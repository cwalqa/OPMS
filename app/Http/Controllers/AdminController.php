<?php

namespace App\Http\Controllers;

use App\Models\QuickbooksCustomer;
use Illuminate\Http\Request;
use App\Models\QuickbooksAdmin;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\Hash;

use App\Models\QuickbooksEstimates;
use App\Models\QuickbooksEstimateItems;
use App\Notifications\OrderApproved;

use App\Models\ProductionLine;
use App\Models\ProductionSchedule;

use App\Mail\OrderApprovedMail;
use App\Mail\OrderDeclinedMail;
use App\Mail\AdminOrderApprovedMail;
use App\Mail\AdminOrderDeclinedMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

use App\Models\OrderItemStageLog;


class AdminController extends Controller
{

    /**
     * Display the list of system administrators.
     */
    public function index()
    {
        // Retrieve all admins, along with their roles and permissions
        $admins = QuickbooksAdmin::with('roles.permissions')->paginate(10);

        // Get all roles and permissions for the forms
        $roles = Role::all();
        $permissions = Permission::all();

        return view('admin.users.admins', compact('admins', 'roles', 'permissions'));
    }

    /**
     * Add a new administrator.
     */
    public function addAdmin(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:quickbooks_admin',
            'roles' => 'required|array',
        ]);

        // Create a new admin with the default password
        $admin = QuickbooksAdmin::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make('defaultpassword'), // Default password (make sure admins change it later)
        ]);

        // Assign roles to the admin
        $admin->roles()->sync($request->roles);

        return redirect()->route('admin.admins')->with('success', 'Admin added successfully.');
    }

    /**
     * Edit an existing administrator.
     */
    public function editAdmin(Request $request, $id)
    {
        $admin = QuickbooksAdmin::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:quickbooks_admin,email,' . $admin->id,
            'roles' => 'required|array',
        ]);

        // Update admin details
        $admin->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        // Update roles for the admin
        $admin->roles()->sync($request->roles);

        return redirect()->route('admin.admins')->with('success', 'Admin updated successfully.');
    }

    /**
     * Delete an administrator.
     */
    public function deleteAdmin($id)
    {
        $admin = QuickbooksAdmin::findOrFail($id);

        // Remove roles and delete the admin
        $admin->roles()->detach();
        $admin->delete();

        return redirect()->route('admin.admins')->with('success', 'Admin deleted successfully.');
    }

    public function update(Request $request, $id)
    {
        $admin = QuickbooksAdmin::findOrFail($id);
        $role = Role::findOrFail($request->role_id);

        // Assign the selected role to the admin
        $admin->roles()->sync([$role->id]);

        // Assign the selected permissions to the role
        if ($request->has('permissions')) {
            $role->permissions()->sync($request->permissions);
        }

        return redirect()->back()->with('success', 'Admin roles and permissions updated successfully.');
    }

    
    public function customers()
    {
        // Retrieve paginated customers
        $customers = QuickbooksCustomer::paginate(10); // Adjust pagination as needed
        return view('admin.users.customers', compact('customers'));
    }


    // List all purchase orders for review
    public function reviewOrders()
    {
        $orders = QuickbooksEstimates::with('customer', 'items')->where('status', 'pending')->paginate(10);
        return view('admin.orders.review', compact('orders'));
    }

    // Approve an order
    public function approveOrder($id)
    {
        $order = QuickbooksEstimates::findOrFail($id);

        //Update order status
        $order->status = 'approved';
        $order->approved_by = auth('admin')->user()->id ?? null;
        $order->save();

        //Notify Line Schedulers
        $lineSchedulers = Role::where('name', 'Line Scheduler')->first()?->admins ?? collect();
        foreach ($lineSchedulers as $scheduler) {
            $scheduler->notify(new OrderApproved($order));
        }

        //Send email to client
        if (!empty($order->bill_email)) {
            try {
                Mail::to($order->bill_email)
                    ->later(now()->addSeconds(5), new OrderApprovedMail($order));
            } catch (\Exception $e) {
                Log::error('Failed to send order approval email to client.', [
                    'order_id' => $order->id,
                    'email' => $order->bill_email,
                    'error' => $e->getMessage(),
                ]);
            }
        } else {
            Log::warning('No customer email found for order approval notification.', [
                'order_id' => $order->id,
            ]);
        }

        // Notify Admin(s)
        $fallbackAdminEmail = config('mail.admin_notification_email', 'jospk.walker@gmail.com');
        $recipients = [$fallbackAdminEmail];

        $adminEmail = auth('admin')->user()->email ?? null;
        if ($adminEmail && $adminEmail !== $fallbackAdminEmail) {
            $recipients[] = $adminEmail;
        }

        try {
            Mail::to($recipients)
                ->later(now()->addSeconds(5), new AdminOrderApprovedMail($order));
        } catch (\Exception $e) {
            Log::error('Failed to send order approval notification to admin.', [
                'order_id' => $order->id,
                'emails' => $recipients,
                'error' => $e->getMessage(),
            ]);
        }

        // Log Approval in Stage Logs using the correct SKU (product_id)
        foreach ($order->items as $item) {
            if ($item->tracking_id) {
                \App\Models\OrderItemStageLog::create([
                    'estimate_item_sku' => $item->sku, 
                    'tracking_id' => $item->tracking_id,
                    'stage' => 'approved',
                    'comments' => 'Order approved by admin.',
                    'meta' => [
                        'approved_by' => auth('admin')->user()?->name,
                        'admin_id' => auth('admin')->id(),
                    ],
                ]);
            }
        }

        return redirect()->route('admin.reviewOrders')->with('success', 'Order approved successfully.');
    }




    // Decline an order
    public function declineOrder(Request $request, $id)
    {
        $order = QuickbooksEstimates::findOrFail($id);

        // Validate and fetch reason
        $request->validate([
            'decline_reason' => 'required|string|max:500',
        ]);
        $reason = $request->input('decline_reason');

        // Update order
        $order->status = 'declined';
        $order->approved_by = auth('admin')->user()->id ?? null;
        $order->decline_reason = $reason; // Make sure this column exists in your DB
        $order->save();

        // Send email to client
        if (!empty($order->bill_email)) {
            try {
                Mail::to($order->bill_email)
                    ->later(now()->addSeconds(5), new OrderDeclinedMail($order, $reason));
            } catch (\Exception $e) {
                Log::error('Failed to send order decline email to client.', [
                    'order_id' => $order->id,
                    'email' => $order->bill_email,
                    'error' => $e->getMessage(),
                ]);
            }
        } else {
            Log::warning('No customer email found for order decline notification.', [
                'order_id' => $order->id,
            ]);
        }

        // Build recipients list for admin
        $fallbackAdminEmail = config('mail.admin_notification_email', 'jospk.walker@gmail.com');
        $recipients = [$fallbackAdminEmail];

        $adminEmail = auth('admin')->user()->email ?? null;
        if ($adminEmail && $adminEmail !== $fallbackAdminEmail) {
            $recipients[] = $adminEmail;
        }

        // Send email to admin
        try {
            // Use a collection to ensure proper handling of multiple recipients
            Mail::to(collect($recipients))
                ->send(new AdminOrderDeclinedMail($order, $reason));
                
            // Alternatively, for immediate sending instead of delayed:
            // Mail::to(collect($recipients))->send(new AdminOrderDeclinedMail($order, $reason));
            
            Log::info('Admin decline notification sent successfully', [
                'order_id' => $order->id,
                'recipients' => $recipients
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send order decline notification to admin.', [
                'order_id' => $order->id,
                'emails' => $recipients,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString() // Add stack trace for better debugging
            ]);
        }

        foreach ($order->items as $item) {
            if ($item->tracking_id) {
                \App\Models\OrderItemStageLog::create([
                    'estimate_item_sku' => $item->sku,
                    'tracking_id' => $item->tracking_id,
                    'stage' => 'approved',
                    'comments' => 'Order declined by admin.',
                ]);
            }
        }

        return redirect()->route('admin.reviewOrders')->with('success', 'Order declined successfully.');
    }

    

    public function approvedOrders()
    {
        $orders = QuickbooksEstimates::with('customer', 'items')->where('status', 'approved')->paginate(10);
        return view('admin.orders.approved', compact('orders'));
    }

    public function declinedOrders()
    {
        $orders = QuickbooksEstimates::with('customer', 'items')->where('status', 'declined')->paginate(10);
        return view('admin.orders.declined', compact('orders'));
    }

    public function canceledOrders()
    {
        $orders = QuickbooksEstimates::with('customer', 'items')->where('status', 'canceled')->paginate(10);
        return view('admin.orders.canceled', compact('orders'));
    }

    public function viewOrderDetails($id)
    {
        $order = QuickbooksEstimates::with('items')->findOrFail($id);
        return view('admin.orders.viewOrderDetails', compact('order'));
    }

    public function viewApprovedOrderDetails($id)
    {
        $order = QuickbooksEstimates::with('items', 'customer')->findOrFail($id);
        return view('admin.orders.viewApprovedOrderDetails', compact('order'));
    }

    public function viewDeclinedOrderDetails($id)
    {
        $order = QuickbooksEstimates::with('items', 'customer')->findOrFail($id);
        return view('admin.orders.viewDeclinedOrderDetails', compact('order'));
    }

    public function viewCanceledOrderDetails($id)
    {
        $order = QuickbooksEstimates::with('items', 'customer')->findOrFail($id);
        return view('admin.orders.viewCanceledOrderDetails', compact('order'));
    }

    // Function to manage production lines
    public function manageProductionLines()
    {
        $productionLines = ProductionLine::with(['lineManager', 'assignedOrder'])->paginate(10);
        $orders = QuickbooksEstimates::where('status', 'approved')->get(); // Orders available for assignment
        $lineManagers = QuickbooksAdmin::all(); // All admins to select as line managers

        return view('admin.production_lines.index', compact('productionLines', 'orders', 'lineManagers'));
    }

    // Assign an order to a production line
    public function assignOrderToLine(Request $request, $id)
    {
        $line = ProductionLine::findOrFail($id);
        $line->assigned_order_id = $request->input('assigned_order_id');
        $line->line_status = 'busy'; // Set status to busy
        $line->order_deadline = $request->input('order_deadline');
        $line->save();

        foreach ($order->items as $item) {
            if ($item->tracking_id) {
                \App\Models\OrderItemStageLog::create([
                    'estimate_item_sku' => $item->sku,
                    'tracking_id' => $item->tracking_id,
                    'stage' => 'approved',
                    'comments' => 'Order assigned to Production line.',
                ]);
            }
        }

        return redirect()->route('admin.productionLines')->with('success', 'Order assigned to production line.');
    }

    // Update the production line status
    public function updateLineStatus(Request $request, $id)
    {
        $line = ProductionLine::findOrFail($id);
        $line->line_status = $request->input('line_status');
        $line->save();

        return redirect()->route('admin.productionLines')->with('success', 'Production line status updated.');
    }


    public function addProductionLine(Request $request)
    {
        $request->validate([
            'line_name' => 'required|string|max:255',
            'max_quantity' => 'required|integer|min:1',
            'line_manager_id' => 'required|exists:quickbooks_admin,id',
        ]);

        ProductionLine::create([
            'line_name' => $request->line_name,
            'max_quantity' => $request->max_quantity,
            'line_manager_id' => $request->line_manager_id,
            'line_status' => 'available',
        ]);

        return redirect()->route('admin.productionLines')->with('success', 'Production line added successfully.');
    }

    public function editProductionLine(Request $request, $id)
    {
        // Validate the input
        $request->validate([
            'line_name' => 'required|string|max:255',
            'max_quantity' => 'required|integer',
            'line_manager_id' => 'required|exists:users,id', // Assuming line managers are stored in 'users' table
            'line_status' => 'required|string|in:available,in production,offline',
        ]);

        // Find the production line by ID
        $productionLine = ProductionLine::findOrFail($id);

        // Update production line details
        $productionLine->line_name = $request->input('line_name');
        $productionLine->max_quantity = $request->input('max_quantity');
        $productionLine->line_manager_id = $request->input('line_manager_id');
        $productionLine->line_status = $request->input('line_status'); // Update the status

        // Save the changes
        $productionLine->save();

        return redirect()->back()->with('success', 'Production Line updated successfully.');
    }


    public function deleteProductionLine($id)
    {
        $line = ProductionLine::findOrFail($id);
        $line->delete();

        return redirect()->route('admin.productionLines')->with('success', 'Production line deleted successfully.');
    }

    public function viewScheduledOrders()
    {
        // Fetch all scheduled orders with their items and associated production lines
        $scheduledOrders = ProductionSchedule::with(['item.order.customer', 'line'])->paginate(10);

        // Fetch all available production lines for scheduling
        $productionLines = ProductionLine::all();

        // Fetch orders that have been approved but have not been fully scheduled yet
        $orders = QuickbooksEstimates::with(['items' => function ($query) {
            $query->whereDoesntHave('productionSchedules');
        }])
        ->where('status', 'approved')
        ->get();

        // Pass the variables to the view
        return view('admin.production_lines.scheduled', compact('scheduledOrders', 'productionLines', 'orders'));
    }


    public function viewScheduledOrdersCalendar()
    {
        $scheduledOrders = ProductionSchedule::with('item.order', 'line')->get();
        
        return view('admin.production_lines.calendar', compact('scheduledOrders'));
    }



    public function addSchedule(Request $request)
    {
        // Validate the request data
        $request->validate([
            'item_id' => 'required|exists:quickbooks_estimate_items,id',
            'quantity' => 'required|numeric|min:1',
            'line_id' => 'required|exists:production_lines,id',
            'schedule_date' => 'required|date',
            'deadline_date' => 'required|date|after_or_equal:schedule_date',
        ]);

        // Check if the item is already scheduled for the selected date and line
        $existingSchedule = ProductionSchedule::where('item_id', $request->item_id)
            ->where('line_id', $request->line_id)
            ->where('schedule_date', $request->schedule_date)
            ->first();

        if ($existingSchedule) {
            return redirect()->back()->withErrors([
                'item_id' => 'This item is already scheduled for the selected date and line.'
            ])->withInput();
        }

        // Find the selected production line
        $line = ProductionLine::findOrFail($request->line_id);

        // Check if the item quantity exceeds the line's max production capacity
        if ($request->quantity > $line->max_quantity) {
            return redirect()->back()->withErrors([
                'quantity' => 'The item quantity exceeds the maximum production quantity for the selected line.'
            ])->withInput();
        }

        // Create a new production schedule
        ProductionSchedule::create([
            'item_id' => $request->item_id,
            'line_id' => $request->line_id,
            'quantity' => $request->quantity,
            'schedule_date' => $request->schedule_date,
            'deadline_date' => $request->deadline_date,
        ]);

        $item = QuickbooksEstimateItems::findOrFail($request->item_id);

        OrderItemStageLog::create([
            'tracking_id' => $item->tracking_id,
            'estimate_item_sku' => $item->sku,
            'stage' => 'scheduled',
            'comments' => 'Item scheduled for production.',
            'meta' => [
                'scheduled_by' => auth('admin')->user()?->name ?? 'System',
                'line_id' => $request->line_id,
                'schedule_date' => $request->schedule_date,
                'deadline_date' => $request->deadline_date,
            ],
            'timestamp' => now(),
        ]);

        // Retrieve all unscheduled items
        $unscheduledItems = QuickbooksEstimateItems::whereDoesntHave('productionSchedules')->get();

        // Redirect back with success message and pass the unscheduled items for display
        return redirect()->back()->with([
            'success' => 'Production schedule added successfully.',
            'unscheduledItems' => $unscheduledItems,
        ]);
    }

    public function editSchedule(Request $request, $id)
    {
        $request->validate([
            'schedule_date' => 'required|date',
            'schedule_status' => 'required|string'
        ]);

        $order = QuickbooksEstimates::findOrFail($id);
        $order->schedule_date = $request->schedule_date;
        $order->schedule_status = $request->schedule_status;
        $order->save();

        return redirect()->route('admin.scheduledOrders')->with('success', 'Production schedule updated successfully.');
    }

    public function deleteSchedule($id)
    {
        $order = QuickbooksEstimates::findOrFail($id);
        $order->schedule_date = null;
        $order->schedule_status = null;
        $order->save();

        return redirect()->route('admin.scheduledOrders')->with('success', 'Production schedule deleted successfully.');
    }

    public function updateCustomer(Request $request, $id)
{
    $request->validate([
        'customer_name' => 'required|string|max:255',
        'company_name' => 'nullable|string|max:255',
        'email' => 'required|email|max:255',
        'status' => 'required|in:0,1',
    ]);

    $customer = \App\Models\QuickbooksCustomer::where('customer_id', $id)->firstOrFail();

    $customer->fully_qualified_name = $request->customer_name;
    $customer->company_name = $request->company_name;
    $customer->email = $request->email;
    $customer->is_active = $request->status;
    $customer->save();

    return redirect()->route('admin.customers')->with('success', 'Customer updated successfully.');
}


}
