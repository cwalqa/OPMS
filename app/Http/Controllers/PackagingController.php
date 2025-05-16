<?php

namespace App\Http\Controllers;

use App\Models\OrderItemStageLog;
use App\Models\PackagingAssignment;
use App\Models\PackagingTask;
use App\Models\ProductionSchedule;
use App\Models\QuickbooksEstimateItems;
use App\Models\WarehouseNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Carbon\Carbon;


use App\Models\PackagingMaterial;
use App\Models\Supplier;
use App\Models\QuickbooksAdmin;
use App\Notifications\LowPackagingMaterialsNotification;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Log;


class PackagingController extends Controller
{
    /**
     * Display a listing of pending packaging tasks
     */
    public function index(Request $request)
{
    // Build the initial query with eager loading to prevent N+1 problems
    $query = PackagingTask::with(['item.order', 'assignedTo']);

    // Apply status filter if provided
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    // Apply search filter if provided
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('packaging_notes', 'like', "%$search%")
              ->orWhereHas('item', function ($q) use ($search) {
                  $q->where('name', 'like', "%$search%")
                    ->orWhere('sku', 'like', "%$search%");
              });
        });
    }

    // Get paginated tasks
    $packagingTasks = $query->orderBy('created_at', 'desc')->paginate(10);

    // Get all admins as packaging staff (or adjust later when role implemented)
    $packagingStaff = QuickbooksAdmin::all();

    // Get warehouse notifications that are ready (required by the create task modal)
    $warehouseNotifications = WarehouseNotification::with('schedule.item')
                            ->where('status', 'ready')
                            ->get();

    // Define status options for filters and display
    $statuses = [
        'pending' => 'Pending',
        'assigned' => 'Assigned',
        'in_progress' => 'In Progress',
        'completed' => 'Completed',
        'on_hold' => 'On Hold',
    ];

    // Return the view with all required variables
    return view('admin.packaging.index', compact(
        'packagingTasks',
        'statuses',
        'warehouseNotifications',
        'packagingStaff'
    ));
}

    
    /**
     * Show pending items from warehouse notifications that need packaging tasks created
     */
    public function showPendingItems()
    {
        // Find warehouse notifications for items that have completed production but don't have packaging tasks yet
        $pendingItems = WarehouseNotification::where('status', 'pending')
            ->whereDoesntHave('packagingTask')
            ->with(['productionSchedule', 'item.order'])
            ->paginate(10);
            
        return view('admin.packaging.pending', compact('pendingItems'));
    }

    /**
     * Create a new packaging task from a warehouse notification
     */
   public function createTask(Request $request)
{
    try {
        // 1. Validate the request data
        $validated = $request->validate([
            'warehouse_notification_id' => 'required|exists:warehouse_notifications,id',
            'packaging_notes' => 'nullable|string',
            'packaging_type' => 'required|string',
            'priority' => 'required|string|in:low,medium,high,urgent',
            'special_instructions' => 'nullable|string',
            'assigned_to' => 'nullable|exists:quickbooks_admin,id' // Updated to match correct table
        ]);

        // 2. Debug the validation data early
        Log::info('Request data for packaging task:', $request->all());
        Log::info('Validated data for packaging task:', $validated);
        
        // 3. Begin database transaction
        DB::beginTransaction();
        
        // 4. Fetch warehouse notification with proper relationship loading
        $notification = WarehouseNotification::with(['schedule.item'])
            ->findOrFail($validated['warehouse_notification_id']);
        
        if (!$notification) {
            throw new \Exception('Warehouse notification not found');
        }
        
        Log::info('Fetched WarehouseNotification:', [
            'id' => $notification->id,
            'tracking_id' => $notification->tracking_id,
            'estimate_item_sku' => $notification->estimate_item_sku ?? 'null',
            'quantity' => $notification->quantity
        ]);
        
        // 5. Make sure required fields exist
        if (empty($notification->tracking_id)) {
            throw new \Exception('Warehouse notification is missing tracking_id');
        }
        
        // 6. Create the packaging task with explicit null handling
        $task = new PackagingTask();
        $task->warehouse_notification_id = $notification->id;
        $task->tracking_id = $notification->tracking_id;
        $task->estimate_item_sku = $notification->estimate_item_sku ?? null;
        $task->quantity = $notification->quantity ?? 0;
        $task->packaging_type = $validated['packaging_type'];
        $task->packaging_notes = $validated['packaging_notes'] ?? null;
        $task->special_instructions = $validated['special_instructions'] ?? null;
        $task->priority = $validated['priority'];
        $task->status = isset($validated['assigned_to']) ? PackagingTask::STATUS_ASSIGNED : PackagingTask::STATUS_PENDING;
        $task->created_by = auth('admin')->id();
        
        // 7. Save and verify task creation
        $saved = $task->save();
        
        if (!$saved) {
            throw new \Exception('Packaging task could not be saved.');
        }
        
        Log::info('Created PackagingTask:', [
            'id' => $task->id,
            'tracking_id' => $task->tracking_id,
            'status' => $task->status
        ]);
        
        // 8. Handle task assignment if provided
        if (!empty($validated['assigned_to'])) {
            $assignment = new PackagingAssignment();
            $assignment->packaging_task_id = $task->id;
            $assignment->user_id = $validated['assigned_to']; // This should match the field in PackagingAssignment table
            $assignment->assigned_by = auth('admin')->id();
            $assignment->assigned_at = now();
            $assignment->save();
            
            Log::info('Created PackagingAssignment for task', [
                'task_id' => $task->id, 
                'user_id' => $validated['assigned_to']
            ]);
        }
        
        // 9. Update warehouse notification status
        $notification->status = 'processing';
        $notification->save();
        
        Log::info('Updated WarehouseNotification status to processing', [
            'id' => $notification->id,
            'new_status' => 'processing'
        ]);
        
        // 10. Create stage log
        $assignedUser = !empty($validated['assigned_to']) 
            ? QuickbooksAdmin::find($validated['assigned_to'])->name 
            : null;
            
        OrderItemStageLog::create([
            'tracking_id' => $notification->tracking_id,
            'estimate_item_sku' => $notification->estimate_item_sku,
            'stage' => 'packaging_task_created',
            'comments' => 'Packaging task created' . ($assignedUser ? ' and assigned to ' . $assignedUser : ''),
            'meta' => json_encode([
                'packaging_task_id' => $task->id,
                'packaging_type' => $validated['packaging_type'],
                'priority' => $validated['priority'],
                'created_by' => auth('admin')->user()->name,
                'assigned_to' => $assignedUser,
            ]),
            'timestamp' => now(),
        ]);
        
        Log::info('Created OrderItemStageLog for packaging task', [
            'task_id' => $task->id,
            'tracking_id' => $notification->tracking_id
        ]);
        
        // 11. Commit transaction
        DB::commit();
        
        // 12. Return success response
        return redirect()->route('admin.packaging.index')
            ->with('success', 'Packaging task created successfully.');
            
    } catch (\Exception $e) {
        // 13. Roll back transaction on error
        DB::rollBack();
        
        // 14. Enhanced error logging
        Log::error('Error creating packaging task: ' . $e->getMessage(), [
            'exception' => get_class($e),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
            'request_data' => $request->all()
        ]);
        
        // 15. Return error response
        return redirect()->back()
            ->withInput()
            ->with('error', 'Failed to create packaging task: ' . $e->getMessage());
    }
}
    
    /**
     * Assign packaging task to staff
     */
    public function assignTask(Request $request, $id)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'assignment_notes' => 'nullable|string'
        ]);
        
        DB::beginTransaction();
        
        try {
            $task = PackagingTask::findOrFail($id);
            
            // Create or update assignment
            $assignment = PackagingAssignment::updateOrCreate(
                ['packaging_task_id' => $task->id],
                [
                    'user_id' => $validated['user_id'],
                    'assigned_by' => auth('admin')->user()->id,
                    'assigned_at' => now(),
                    'notes' => $validated['assignment_notes']
                ]
            );
            
            // Update task status
            $task->update(['status' => 'assigned']);
            
            // Log this assignment
            OrderItemStageLog::create([
                'tracking_id' => $task->tracking_id,
                'estimate_item_sku' => $task->estimate_item_sku,
                'stage' => 'packaging_assigned',
                'comments' => 'Packaging task assigned to staff',
                'meta' => [
                    'packaging_task_id' => $task->id,
                    'assigned_to' => User::find($validated['user_id'])->name,
                    'assigned_by' => auth('admin')->user()->name,
                    'assignment_notes' => $validated['assignment_notes']
                ],
                'timestamp' => now()
            ]);
            
            DB::commit();
            
            return redirect()->route('admin.packaging.index')->with('success', 'Task assigned successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to assign task: ' . $e->getMessage());
        }
    }
    
    /**
     * Update packaging task status
     */
    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:pending,assigned,in_progress,completed,on_hold',
            'status_notes' => 'nullable|string'
        ]);
        
        DB::beginTransaction();
        
        try {
            $task = PackagingTask::findOrFail($id);
            $oldStatus = $task->status;
            
            // Update task status
            $task->update([
                'status' => $validated['status'],
                'status_notes' => $validated['status_notes'],
                'completed_at' => $validated['status'] === 'completed' ? now() : $task->completed_at
            ]);
            
            // If completed, update the warehouse notification
            if ($validated['status'] === 'completed' && $task->warehouseNotification) {
                $task->warehouseNotification->update(['status' => 'completed']);
            }
            
            // Log status change
            OrderItemStageLog::create([
                'tracking_id' => $task->tracking_id,
                'estimate_item_sku' => $task->estimate_item_sku,
                'stage' => 'packaging_status_updated',
                'comments' => "Packaging status changed from {$oldStatus} to {$validated['status']}",
                'meta' => [
                    'packaging_task_id' => $task->id,
                    'old_status' => $oldStatus,
                    'new_status' => $validated['status'],
                    'updated_by' => auth('admin')->user()->name,
                    'notes' => $validated['status_notes']
                ],
                'timestamp' => now()
            ]);
            
            DB::commit();
            
            return redirect()->route('admin.packaging.index')->with('success', 'Task status updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to update task status: ' . $e->getMessage());
        }
    }
    
    /**
     * Generate a packaging label with QR code
     */
    public function generateLabel($id)
    {
        try {
            $task = PackagingTask::with(['item.order', 'productionSchedule'])->findOrFail($id);
            $item = $task->item;
            
            if (!$item) {
                return redirect()->back()->with('error', 'Item information not found.');
            }
            
            // Get scheduled delivery date if available
            $scheduleData = ProductionSchedule::where('estimate_item_sku', $item->sku)->first();
            $deliveryDate = $scheduleData && $scheduleData->delivery_date 
                ? Carbon::parse($scheduleData->delivery_date)->format('Y-m-d')
                : 'Not scheduled';
            
            // QR code data
            $qrData = json_encode([
                'tracking_id' => $task->tracking_id,
                'item_sku' => $item->sku,
                'purchase_order' => $item->order->purchase_order_number ?? 'N/A',
                'product_name' => $item->name,
                'packaging_task_id' => $task->id
            ]);
            
            // Generate QR code image
            $qrImage = QrCode::format('png')
                ->size(200)
                ->errorCorrection('H')
                ->generate($qrData);
            
            // Store QR image
            $qrFileName = 'packaging_labels/qr_' . $task->id . '_' . time() . '.png';
            Storage::disk('public')->put($qrFileName, $qrImage);
            
            // Prepare label data
            $labelData = [
                'qr_image_url' => Storage::url($qrFileName),
                'tracking_id' => $task->tracking_id,
                'purchase_order' => $item->order->purchase_order_number ?? 'N/A',
                'item_sku' => $item->sku,
                'product_name' => $item->name,
                'client_name' => $item->order->customer_name ?? 'N/A',
                'client_email' => $item->order->customer_email ?? 'N/A',
                'order_quantity' => $item->quantity,
                'finished_quantity' => $task->quantity,
                'completion_date' => $task->created_at->format('Y-m-d'),
                'delivery_date' => $deliveryDate,
                'packaging_type' => $task->packaging_type,
                'special_instructions' => $task->special_instructions
            ];
            
            // Log the label generation
            OrderItemStageLog::create([
                'tracking_id' => $task->tracking_id,
                'estimate_item_sku' => $task->estimate_item_sku,
                'stage' => 'packaging_label_generated',
                'comments' => 'Packaging label generated',
                'meta' => [
                    'packaging_task_id' => $task->id,
                    'generated_by' => auth('admin')->user()->name
                ],
                'timestamp' => now()
            ]);
            
            return view('admin.packaging.label', compact('labelData'));
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to generate packaging label: ' . $e->getMessage());
        }
    }
    
    /**
     * Download packaging label as PDF
     */
    public function downloadLabel($id)
    {
        try {
            $task = PackagingTask::with(['item.order', 'productionSchedule'])->findOrFail($id);
            
            // Generate label HTML (similar logic to generateLabel method)
            // Then use a PDF library (like dompdf) to convert HTML to PDF
            
            // For this example, we'll simulate a PDF download
            $pdf = app()->make('dompdf.wrapper');
            $pdf->loadView('admin.packaging.label_pdf', [
                'task' => $task,
                // Include all necessary data for the PDF
            ]);
            
            return $pdf->download('packaging_label_' . $task->id . '.pdf');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to download packaging label: ' . $e->getMessage());
        }
    }
    
    /**
     * Show packaging task details
     */
    public function show($id)
    {
        $task = PackagingTask::with([
            'item.order', 
            'warehouseNotification', 
            'assignment.user',
            'creator'
        ])->findOrFail($id);
        
        // Get history logs related to this task
        $logs = OrderItemStageLog::where('tracking_id', $task->tracking_id)
            ->where(function($query) {
                $query->where('stage', 'like', 'packaging%')
                    ->orWhere('stage', 'production_completed');
            })
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('admin.packaging.show', compact('task', 'logs'));
    }
    
    /**
     * Show bulk packaging interface for multiple items
     */
    public function bulkPackaging()
    {
        $pendingTasks = PackagingTask::whereIn('status', ['pending', 'assigned'])
            ->orderBy('priority', 'desc')
            ->with(['item.order'])
            ->get()
            ->groupBy('estimate_item_sku');
            
        return view('admin.packaging.bulk', compact('pendingTasks'));
    }
    
    /**
     * Process bulk packaging action
     */
    public function processBulkPackaging(Request $request)
    {
        $validated = $request->validate([
            'task_ids' => 'required|array',
            'task_ids.*' => 'exists:packaging_tasks,id',
            'bulk_packaging_notes' => 'nullable|string',
            'bulk_status' => 'required|in:in_progress,completed,on_hold'
        ]);
        
        DB::beginTransaction();
        
        try {
            // Update all selected tasks
            foreach ($validated['task_ids'] as $taskId) {
                $task = PackagingTask::findOrFail($taskId);
                $oldStatus = $task->status;
                
                $task->update([
                    'status' => $validated['bulk_status'],
                    'status_notes' => $validated['bulk_packaging_notes'] ?? 'Bulk update',
                    'completed_at' => $validated['bulk_status'] === 'completed' ? now() : $task->completed_at
                ]);
                
                // If completed, update the warehouse notification
                if ($validated['bulk_status'] === 'completed' && $task->warehouseNotification) {
                    $task->warehouseNotification->update(['status' => 'completed']);
                }
                
                // Log status change
                OrderItemStageLog::create([
                    'tracking_id' => $task->tracking_id,
                    'estimate_item_sku' => $task->estimate_item_sku,
                    'stage' => 'packaging_bulk_update',
                    'comments' => "Packaging status changed from {$oldStatus} to {$validated['bulk_status']} (bulk update)",
                    'meta' => [
                        'packaging_task_id' => $task->id,
                        'old_status' => $oldStatus,
                        'new_status' => $validated['bulk_status'],
                        'updated_by' => auth('admin')->user()->name,
                        'notes' => $validated['bulk_packaging_notes'] ?? 'Bulk update'
                    ],
                    'timestamp' => now()
                ]);
            }
            
            DB::commit();
            
            return redirect()->route('admin.packaging.index')->with('success', 'Bulk packaging update completed successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to process bulk packaging: ' . $e->getMessage());
        }
    }
    
    /**
     * Generate packaging reports
     */
    public function reports(Request $request)
    {
        // Default to current month
        $startDate = $request->start_date ?? now()->startOfMonth()->format('Y-m-d');
        $endDate = $request->end_date ?? now()->endOfMonth()->format('Y-m-d');
        
        // Packaging tasks by status
        $tasksByStatus = PackagingTask::whereBetween('created_at', [$startDate, $endDate])
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();
            
        // Packaging tasks by packaging type
        $tasksByType = PackagingTask::whereBetween('created_at', [$startDate, $endDate])
            ->select('packaging_type', DB::raw('count(*) as count'))
            ->groupBy('packaging_type')
            ->get();
            
        // Top 5 staff with most completed packages
        $topPackagers = PackagingAssignment::whereBetween('created_at', [$startDate, $endDate])
            ->whereHas('packagingTask', function($query) {
                $query->where('status', 'completed');
            })
            ->with('user:id,name')
            ->select('user_id', DB::raw('count(*) as completed_count'))
            ->groupBy('user_id')
            ->orderBy('completed_count', 'desc')
            ->limit(5)
            ->get();
            
        // Average completion time in hours (from task creation to completion)
        $avgCompletionTime = PackagingTask::whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('completed_at')
            ->select(DB::raw('AVG(TIMESTAMPDIFF(HOUR, created_at, completed_at)) as avg_hours'))
            ->first();
            
        return view('admin.packaging.reports', compact(
            'tasksByStatus', 
            'tasksByType', 
            'topPackagers', 
            'avgCompletionTime',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Display a staff dashboard with assigned tasks
     */
    public function staffDashboard()
    {
        // Show tasks assigned to the logged-in packaging staff member
        $user = auth()->user();
        $assignedTasks = PackagingTask::whereHas('assignment', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->with(['item.order'])
        ->orderBy('priority', 'desc')
        ->paginate(10);
        
        // Get completed tasks for this user
        $completedTasks = PackagingTask::whereHas('assignment', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->where('status', 'completed')
        ->orderBy('completed_at', 'desc')
        ->limit(5)
        ->get();
        
        // Stats for the staff member
        $stats = [
            'completed_today' => PackagingTask::whereHas('assignment', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })->where('status', 'completed')
            ->whereDate('completed_at', Carbon::today())
            ->count(),
            
            'in_progress' => PackagingTask::whereHas('assignment', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })->where('status', 'in_progress')
            ->count(),
            
            'pending' => PackagingTask::whereHas('assignment', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })->where('status', 'assigned')
            ->count()
        ];
        
        return view('packaging.staff-dashboard', compact('assignedTasks', 'completedTasks', 'stats'));
    }

    /**
     * Update task progress by staff
     */
    public function updateTaskProgress(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:in_progress,completed',
            'completion_notes' => 'nullable|string',
            'issues_encountered' => 'nullable|string',
        ]);
        
        DB::beginTransaction();
        
        try {
            $task = PackagingTask::findOrFail($id);
            $oldStatus = $task->status;
            
            // Update the task status
            $task->update([
                'status' => $validated['status'],
                'status_notes' => $validated['completion_notes'],
                'issues_encountered' => $validated['issues_encountered'],
                'completed_at' => $validated['status'] === 'completed' ? now() : null
            ]);
            
            // Update warehouse notification if task is completed
            if ($validated['status'] === 'completed' && $task->warehouseNotification) {
                $task->warehouseNotification->update(['status' => 'completed']);
            }
            
            // Log the status change
            OrderItemStageLog::create([
                'tracking_id' => $task->tracking_id,
                'estimate_item_sku' => $task->estimate_item_sku,
                'stage' => 'packaging_status_updated_by_staff',
                'comments' => "Packaging status changed from {$oldStatus} to {$validated['status']} by staff",
                'meta' => [
                    'packaging_task_id' => $task->id,
                    'old_status' => $oldStatus,
                    'new_status' => $validated['status'],
                    'updated_by' => auth()->user()->name,
                    'notes' => $validated['completion_notes'],
                    'issues' => $validated['issues_encountered']
                ],
                'timestamp' => now()
            ]);
            
            DB::commit();
            
            return redirect()->route('packaging.staff-dashboard')->with('success', 'Task status updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to update task status: ' . $e->getMessage());
        }
    }

    /**
     * Quality control checkpoint for packaging tasks
     */
    public function qualityControl(Request $request, $id)
    {
        $validated = $request->validate([
            'quality_status' => 'required|in:passed,failed',
            'quality_notes' => 'nullable|string',
            'issues' => 'nullable|string',
            'resolution' => 'nullable|string|required_if:quality_status,failed'
        ]);
        
        DB::beginTransaction();
        
        try {
            $task = PackagingTask::findOrFail($id);
            
            // Update task with quality control information
            $task->update([
                'quality_check_status' => $validated['quality_status'],
                'quality_check_notes' => $validated['quality_notes'],
                'quality_issues' => $validated['issues'],
                'quality_resolution' => $validated['resolution'],
                'quality_checked_at' => now(),
                'quality_checked_by' => auth()->user()->id,
                // If failed quality check, reset status to in_progress
                'status' => $validated['quality_status'] === 'failed' ? 'in_progress' : $task->status
            ]);
            
            // Log quality control check
            OrderItemStageLog::create([
                'tracking_id' => $task->tracking_id,
                'estimate_item_sku' => $task->estimate_item_sku,
                'stage' => 'packaging_quality_control',
                'comments' => "Quality control check: " . $validated['quality_status'],
                'meta' => [
                    'packaging_task_id' => $task->id,
                    'status' => $validated['quality_status'],
                    'checked_by' => auth()->user()->name,
                    'notes' => $validated['quality_notes'],
                    'issues' => $validated['issues'],
                    'resolution' => $validated['resolution'] ?? null
                ],
                'timestamp' => now()
            ]);
            
            DB::commit();
            
            return redirect()->route('admin.packaging.show', $id)->with('success', 'Quality control check recorded successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to record quality control check: ' . $e->getMessage());
        }
    }

    /**
     * Prepare packaging task for shipping
     */
    public function prepareForShipping(Request $request, $id)
    {
        $validated = $request->validate([
            'shipping_notes' => 'nullable|string',
            'shipping_method' => 'required|string',
            'shipped_quantity' => 'required|numeric|min:1',
            'package_dimensions' => 'nullable|string',
            'package_weight' => 'nullable|numeric',
            'shipping_cost' => 'nullable|numeric'
        ]);
        
        DB::beginTransaction();
        
        try {
            $task = PackagingTask::findOrFail($id);
            
            // Update task with shipping preparation details
            $task->update([
                'shipping_notes' => $validated['shipping_notes'],
                'shipping_method' => $validated['shipping_method'],
                'shipped_quantity' => $validated['shipped_quantity'],
                'package_dimensions' => $validated['package_dimensions'],
                'package_weight' => $validated['package_weight'],
                'shipping_cost' => $validated['shipping_cost'],
                'ready_for_shipping' => true,
                'prepared_for_shipping_at' => now(),
                'prepared_for_shipping_by' => auth()->user()->id
            ]);
            
            // Log shipping preparation
            OrderItemStageLog::create([
                'tracking_id' => $task->tracking_id,
                'estimate_item_sku' => $task->estimate_item_sku,
                'stage' => 'packaging_ready_for_shipping',
                'comments' => "Item prepared for shipping",
                'meta' => [
                    'packaging_task_id' => $task->id,
                    'shipping_method' => $validated['shipping_method'],
                    'shipped_quantity' => $validated['shipped_quantity'],
                    'prepared_by' => auth()->user()->name
                ],
                'timestamp' => now()
            ]);
            
            DB::commit();
            
            return redirect()->route('admin.packaging.show', $id)->with('success', 'Item prepared for shipping successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to prepare item for shipping: ' . $e->getMessage());
        }
    }

    /**
     * Manage custom packaging materials
     */
    public function customMaterials()
    {
        // Get all suppliers for dropdown
        $suppliers = Supplier::orderBy('name')->get();
        
        // Show a list of available custom packaging materials
        $materials = PackagingMaterial::with('supplier')
            ->orderBy('name')
            ->paginate(15);
            
        return view('admin.packaging.materials', compact('materials', 'suppliers'));
    }

    /**
     * Add a new custom packaging material
     */
    public function addCustomMaterial(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'cost' => 'required|numeric|min:0',
            'inventory_level' => 'required|integer|min:0',
            'reorder_level' => 'required|integer|min:0',
            'supplier_id' => 'nullable|exists:suppliers,id'
        ]);
        
        PackagingMaterial::create($validated);
        
        return redirect()->route('admin.packaging.materials')->with('success', 'Custom packaging material added successfully');
    }

    /**
     * Update an existing packaging material
     */
    public function updateMaterial(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'cost' => 'required|numeric|min:0',
            'inventory_level' => 'required|integer|min:0',
            'reorder_level' => 'required|integer|min:0',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'is_active' => 'boolean'
        ]);
        
        $material = PackagingMaterial::findOrFail($id);
        $material->update($validated);
        
        return redirect()->route('admin.packaging.materials')->with('success', 'Packaging material updated successfully');
    }

    /**
     * Assign packaging materials to a task
     */
    public function assignMaterialsToTask(Request $request, $id)
    {
        $validated = $request->validate([
            'material_ids' => 'required|array',
            'material_ids.*' => 'exists:packaging_materials,id',
            'quantities' => 'required|array',
            'quantities.*' => 'integer|min:1'
        ]);
        
        $task = PackagingTask::findOrFail($id);
        
        DB::beginTransaction();
        
        try {
            // First detach any existing materials if this is a reassignment
            if ($request->has('reassign') && $request->reassign) {
                $task->materials()->detach();
            }
            
            foreach ($validated['material_ids'] as $index => $materialId) {
                $task->materials()->attach($materialId, [
                    'quantity' => $validated['quantities'][$index],
                    'assigned_by' => auth()->user()->id,
                    'assigned_at' => now()
                ]);
                
                // Update inventory levels
                $material = PackagingMaterial::find($materialId);
                $material->decrement('inventory_level', $validated['quantities'][$index]);
                
                // Check if this puts the material below reorder level
                if ($material->isLowStock()) {
                    // Notify admins about low stock
                    $admins = User::where('role', 'admin')->get();
                    foreach ($admins as $admin) {
                        $admin->notify(new LowPackagingMaterialsNotification(collect([$material])));
                    }
                }
            }
            
            // Log the material assignment
            OrderItemStageLog::create([
                'tracking_id' => $task->tracking_id,
                'estimate_item_sku' => $task->estimate_item_sku,
                'stage' => 'packaging_materials_assigned',
                'comments' => 'Packaging materials assigned to task',
                'meta' => [
                    'packaging_task_id' => $task->id,
                    'assigned_by' => auth()->user()->name,
                    'material_count' => count($validated['material_ids'])
                ],
                'timestamp' => now()
            ]);
            
            DB::commit();
            
            return redirect()->route('admin.packaging.show', $id)->with('success', 'Packaging materials assigned successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to assign packaging materials: ' . $e->getMessage());
        }
    }

    /**
     * Check inventory levels of packaging materials
     */
    public function checkInventoryLevels()
    {
        // Get all materials
        $allMaterials = PackagingMaterial::with('supplier')->orderBy('name')->get();
        
        // Check if there are enough packaging materials available 
        $lowStockMaterials = $allMaterials->filter(function($material) {
            return $material->inventory_level <= $material->reorder_level;
        });
        
        // Get materials needing reorder
        $outOfStockMaterials = $allMaterials->filter(function($material) {
            return $material->inventory_level === 0;
        });
        
        // Get sufficient stock materials
        $healthyStockMaterials = $allMaterials->filter(function($material) {
            return $material->inventory_level > $material->reorder_level;
        });
        
        // Statistics
        $stats = [
            'total_materials' => $allMaterials->count(),
            'low_stock_count' => $lowStockMaterials->count(),
            'out_of_stock_count' => $outOfStockMaterials->count(),
            'healthy_stock_count' => $healthyStockMaterials->count(),
            'total_value' => $allMaterials->sum(function($material) {
                return $material->inventory_level * $material->cost;
            })
        ];
        
        return view('admin.packaging.inventory', compact(
            'allMaterials',
            'lowStockMaterials',
            'outOfStockMaterials', 
            'healthyStockMaterials',
            'stats'
        ));
    }

    /**
     * API endpoint to get tasks assigned to the authenticated user
     */
    public function apiGetAssignedTasks()
    {
        $user = auth()->user();
        $tasks = PackagingTask::whereHas('assignment', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->with(['item.order'])
        ->orderBy('priority', 'desc')
        ->get();
        
        return response()->json([
            'success' => true,
            'tasks' => $tasks
        ]);
    }

    /**
     * API endpoint to update task status
     */
    public function apiUpdateTaskStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:in_progress,completed',
            'notes' => 'nullable|string',
            'issues_encountered' => 'nullable|string'
        ]);
        
        try {
            $task = PackagingTask::findOrFail($id);
            $oldStatus = $task->status;
            
            $task->update([
                'status' => $validated['status'],
                'status_notes' => $validated['notes'],
                'issues_encountered' => $validated['issues_encountered'],
                'completed_at' => $validated['status'] === 'completed' ? now() : null
            ]);
            
            // Log the change
            OrderItemStageLog::create([
                'tracking_id' => $task->tracking_id,
                'estimate_item_sku' => $task->estimate_item_sku,
                'stage' => 'packaging_status_updated_via_api',
                'comments' => "Packaging status changed from {$oldStatus} to {$validated['status']} via API",
                'meta' => [
                    'packaging_task_id' => $task->id,
                    'updated_by' => auth()->user()->name,
                    'notes' => $validated['notes'],
                    'issues_encountered' => $validated['issues_encountered']
                ],
                'timestamp' => now()
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Task status updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update task status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API endpoint to get packaging materials
     */
    public function apiGetMaterials()
    {
        $materials = PackagingMaterial::where('is_active', true)
            ->where('inventory_level', '>', 0)
            ->orderBy('name')
            ->get();
            
        return response()->json([
            'success' => true,
            'materials' => $materials
        ]);
    }

    /**
     * API endpoint to scan packaging task QR code
     */
    public function apiScanPackagingQR(Request $request)
    {
        $validated = $request->validate([
            'qr_data' => 'required|string'
        ]);
        
        try {
            // Decode QR data
            $qrData = json_decode($validated['qr_data'], true);
            
            if (!isset($qrData['tracking_id']) || !isset($qrData['packaging_task_id'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid QR code data'
                ], 400);
            }
            
            // Find the task
            $task = PackagingTask::with(['item.order', 'assignment.user'])
                ->where('id', $qrData['packaging_task_id'])
                ->where('tracking_id', $qrData['tracking_id'])
                ->first();
                
            if (!$task) {
                return response()->json([
                    'success' => false,
                    'message' => 'Packaging task not found'
                ], 404);
            }
            
            // Return task details
            return response()->json([
                'success' => true,
                'task' => $task
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to process QR code: ' . $e->getMessage()
            ], 500);
        }
    }

}