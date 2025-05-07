@extends('admin.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3 d-flex justify-content-between">
                        <h6 class="text-white text-capitalize ps-3">Production Management</h6>
                        <!-- Filter dropdown -->
                        <div class="dropdown me-3">
                            <button class="btn btn-light btn-sm dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                Filter by Status
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="filterDropdown">
                                <li><a class="dropdown-item filter-status" href="#" data-status="all">All</a></li>
                                <li><a class="dropdown-item filter-status" href="#" data-status="scheduled">Scheduled</a></li>
                                <li><a class="dropdown-item filter-status" href="#" data-status="in production">In Production</a></li>
                                <li><a class="dropdown-item filter-status" href="#" data-status="paused">Paused</a></li>
                                <li><a class="dropdown-item filter-status" href="#" data-status="completed">Completed</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="table-responsive p-3">
                        <input type="text" id="productionSearchInput" placeholder="Search..." class="form-control mb-3">
                        <table class="table align-items-center mb-0" id="productionItemsTable">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-sm font-weight-bolder opacity-10 ps-2">Order Number</th>
                                    <th class="text-uppercase text-secondary text-sm font-weight-bolder opacity-10 ps-2">Product Name</th>
                                    <th class="text-uppercase text-secondary text-sm font-weight-bolder opacity-10 ps-2">Line</th>
                                    <th class="text-uppercase text-secondary text-sm font-weight-bolder opacity-10 ps-2">Quantity</th>
                                    <th class="text-uppercase text-secondary text-sm font-weight-bolder opacity-10 ps-2">Schedule Date</th>
                                    <th class="text-uppercase text-secondary text-sm font-weight-bolder opacity-10 ps-2">Status</th>
                                    <th class="text-uppercase text-secondary text-sm font-weight-bolder opacity-10 ps-2">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($scheduledItems as $schedule)
                                @php
                                    $product = \App\Models\QuickbooksItem::where('sku', $schedule->item->sku)->first();
                                    $productName = $product ? $product->name : 'Unknown Product';
                                    
                                    if($schedule->schedule_status == 'scheduled') {
                                        $statusClass = 'bg-warning text-dark';
                                        $iconClass = 'fa-calendar';
                                    } elseif($schedule->schedule_status == 'in production') {
                                        $statusClass = 'bg-success text-white';
                                        $iconClass = 'fa-cogs';
                                    } elseif($schedule->schedule_status == 'paused') {
                                        $statusClass = 'bg-danger text-white';
                                        $iconClass = 'fa-pause';
                                    } elseif($schedule->schedule_status == 'completed') {
                                        $statusClass = 'bg-primary text-white';
                                        $iconClass = 'fa-check';
                                    }
                                @endphp
                                <tr class="production-row" data-status="{{ $schedule->schedule_status }}">
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">{{ $schedule->item->order->purchase_order_number }}</p>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">{{ $productName }}</p>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">{{ $schedule->line->line_name }}</p>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">{{ $schedule->quantity - ($schedule->defective_quantity ?? 0) }} / {{ $schedule->quantity }}</p>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">{{ $schedule->schedule_date }}</p>
                                    </td>
                                    <td>
                                        <span class="badge {{ $statusClass }} px-2 py-1">
                                            <i class="fas {{ $iconClass }} me-1"></i>
                                            {{ ucfirst($schedule->schedule_status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($schedule->schedule_status == 'scheduled')
                                        <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#startModal{{ $schedule->id }}">
                                            <i class="fas fa-play me-1"></i> Start
                                        </button>
                                        @elseif($schedule->schedule_status == 'in production')
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#pauseModal{{ $schedule->id }}">
                                                <i class="fas fa-pause me-1"></i> Pause
                                            </button>
                                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#completeModal{{ $schedule->id }}">
                                                <i class="fas fa-check me-1"></i> Complete
                                            </button>
                                        </div>
                                        @elseif($schedule->schedule_status == 'paused')
                                        <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#resumeModal{{ $schedule->id }}">
                                            <i class="fas fa-play me-1"></i> Resume
                                        </button>
                                        @elseif($schedule->schedule_status == 'completed')
                                        <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#viewDetailsModal{{ $schedule->id }}">
                                            <i class="fas fa-eye me-1"></i> Details
                                        </button>
                                        @endif
                                    </td>
                                </tr>

                                <!-- Start Modal -->
                                <div class="modal fade" id="startModal{{ $schedule->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <form method="POST" action="{{ route('admin.production.start.process', $schedule->id) }}">
                                            @csrf
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Start Production</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="alert alert-info">
                                                        <p class="mb-1"><strong>Order:</strong> {{ $schedule->item->order->purchase_order_number }}</p>
                                                        <p class="mb-1"><strong>Product:</strong> {{ $productName }}</p>
                                                        <p class="mb-1"><strong>Quantity:</strong> {{ $schedule->quantity }}</p>
                                                        <p class="mb-1"><strong>Line:</strong> {{ $schedule->line->line_name }}</p>
                                                        <p class="mb-0"><strong>Schedule Date:</strong> {{ $schedule->schedule_date }}</p>
                                                    </div>
                                                    <p class="mt-3">Are you ready to begin production for this item?</p>
                                                    <div class="form-check mb-3">
                                                        <input class="form-check-input" type="checkbox" value="1" id="confirmStart{{ $schedule->id }}" required>
                                                        <label class="form-check-label" for="confirmStart{{ $schedule->id }}">
                                                            I confirm that all materials are ready for production
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" class="btn btn-success">
                                                        <i class="fas fa-play me-1"></i> Start Production
                                                    </button>
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <!-- Pause Modal -->
                                <div class="modal fade" id="pauseModal{{ $schedule->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <form method="POST" action="{{ route('admin.production.pause', $schedule->id) }}">
                                            @csrf
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Pause Production</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label for="pauseReason" class="form-label">Reason for Pausing</label>
                                                        <select class="form-select mb-2" id="pauseReasonSelect{{ $schedule->id }}" onchange="toggleCustomReason({{ $schedule->id }})">
                                                            <option value="">Select a reason</option>
                                                            <option value="Equipment failure">Equipment failure</option>
                                                            <option value="Material shortage">Material shortage</option>
                                                            <option value="Staff shortage">Staff shortage</option>
                                                            <option value="Quality concerns">Quality concerns</option>
                                                            <option value="Scheduled maintenance">Scheduled maintenance</option>
                                                            <option value="custom">Other (please specify)</option>
                                                        </select>
                                                        <textarea class="form-control" id="pauseReason{{ $schedule->id }}" name="pause_reason" rows="3" required></textarea>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="defectiveQuantity{{ $schedule->id }}" class="form-label">Defective Quantity (if any)</label>
                                                        <input type="number" class="form-control" id="defectiveQuantity{{ $schedule->id }}" name="defective_quantity" min="0" max="{{ $schedule->quantity }}" value="0">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="defectNotes{{ $schedule->id }}" class="form-label">Defect Notes</label>
                                                        <textarea class="form-control" id="defectNotes{{ $schedule->id }}" name="defect_notes" rows="3"></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" class="btn btn-warning">
                                                        <i class="fas fa-pause me-1"></i> Pause Production
                                                    </button>
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <!-- Resume Modal -->
                                <div class="modal fade" id="resumeModal{{ $schedule->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <form method="POST" action="{{ route('admin.production.resume', $schedule->id) }}">
                                            @csrf
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Resume Production</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="alert alert-warning">
                                                        <p><strong>Currently Paused:</strong> {{ $productName }} for order {{ $schedule->item->order->purchase_order_number }}</p>
                                                        <p class="mb-0"><strong>Progress:</strong> {{ $schedule->quantity - ($schedule->defective_quantity ?? 0) }} / {{ $schedule->quantity }}</p>
                                                    </div>
                                                    <p>Are you ready to resume production for this item?</p>
                                                    <div class="mb-3">
                                                        <label for="resumeNotes{{ $schedule->id }}" class="form-label">Notes (optional)</label>
                                                        <textarea class="form-control" id="resumeNotes{{ $schedule->id }}" name="resume_notes" rows="3"></textarea>
                                                    </div>
                                                    <div class="form-check mb-3">
                                                        <input class="form-check-input" type="checkbox" value="1" id="confirmResume{{ $schedule->id }}" required>
                                                        <label class="form-check-label" for="confirmResume{{ $schedule->id }}">
                                                            I confirm that the pause issue has been resolved
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" class="btn btn-success">
                                                        <i class="fas fa-play me-1"></i> Resume Production
                                                    </button>
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <!-- Complete Modal -->
                                <div class="modal fade" id="completeModal{{ $schedule->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <form method="POST" action="{{ route('admin.production.complete', $schedule->id) }}">
                                            @csrf
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Complete Production</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="alert alert-info mb-3">
                                                        <p class="mb-1"><strong>Order:</strong> {{ $schedule->item->order->purchase_order_number }}</p>
                                                        <p class="mb-1"><strong>Product:</strong> {{ $productName }}</p>
                                                        <p class="mb-0"><strong>Planned Quantity:</strong> {{ $schedule->quantity }}</p>
                                                    </div>
                                                    
                                                    <div class="mb-3">
                                                        <label for="finalDefectiveQuantity{{ $schedule->id }}" class="form-label">Final Defective Quantity</label>
                                                        <input type="number" id="finalDefectiveQuantity{{ $schedule->id }}"
                                                            data-total="{{ $schedule->quantity }}"
                                                            name="defective_quantity" class="form-control"
                                                            min="0" max="{{ $schedule->quantity }}"
                                                            value="{{ $schedule->defective_quantity ?? 0 }}">
                                                    </div>
                                                    
                                                    <div class="alert alert-success">
                                                        <div class="row text-center">
                                                            <div class="col-4 border-end">
                                                                <small>Total</small>
                                                                <h5>{{ $schedule->quantity }}</h5>
                                                            </div>
                                                            <div class="col-4 border-end">
                                                                <small>Defective</small>
                                                                <h5 id="defectDisplay{{ $schedule->id }}">{{ $schedule->defective_quantity ?? 0 }}</h5>
                                                            </div>
                                                            <div class="col-4">
                                                                <small>Good</small>
                                                                <h5 id="goodQty{{ $schedule->id }}">{{ $schedule->quantity - ($schedule->defective_quantity ?? 0) }}</h5>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="mb-3">
                                                        <label for="completionNotes{{ $schedule->id }}" class="form-label">Completion Notes</label>
                                                        <textarea class="form-control" id="completionNotes{{ $schedule->id }}" name="completion_notes" rows="3" placeholder="Add any important notes about the production run..."></textarea>
                                                    </div>
                                                    
                                                    <div class="form-check mb-3">
                                                        <input class="form-check-input" type="checkbox" value="1" id="confirmCompletion{{ $schedule->id }}" required>
                                                        <label class="form-check-label" for="confirmCompletion{{ $schedule->id }}">
                                                            I confirm that production is complete and quality checks have been performed
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="fas fa-check me-1"></i> Complete Production
                                                    </button>
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <!-- View Details Modal -->
                                <div class="modal fade" id="viewDetailsModal{{ $schedule->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Production Details</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <div class="card shadow-sm">
                                                            <div class="card-header bg-light">
                                                                <h6 class="mb-0">Order Information</h6>
                                                            </div>
                                                            <div class="card-body">
                                                                <p><strong>Order Number:</strong> {{ $schedule->item->order->purchase_order_number }}</p>
                                                                <p><strong>Product:</strong> {{ $productName }}</p>
                                                                <p><strong>Scheduled Quantity:</strong> {{ $schedule->quantity }}</p>
                                                                <p><strong>Good Quantity:</strong> {{ $schedule->quantity - ($schedule->defective_quantity ?? 0) }}</p>
                                                                <p class="mb-0"><strong>Defective Quantity:</strong> {{ $schedule->defective_quantity ?? 0 }}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="card shadow-sm">
                                                            <div class="card-header bg-light">
                                                                <h6 class="mb-0">Production Timeline</h6>
                                                            </div>
                                                            <div class="card-body">
                                                                <p><strong>Scheduled Date:</strong> {{ $schedule->schedule_date }}</p>
                                                                <p><strong>Start Date:</strong> {{ $schedule->start_date ?? 'N/A' }}</p>
                                                                <p><strong>Completion Date:</strong> {{ $schedule->completion_date ?? 'N/A' }}</p>
                                                                <p class="mb-0"><strong>Current Status:</strong> <span class="badge {{ $statusClass }}">{{ ucfirst($schedule->schedule_status) }}</span></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="card shadow-sm mt-3">
                                                    <div class="card-header bg-light">
                                                        <h6 class="mb-0">Production Log</h6>
                                                    </div>
                                                    <div class="card-body p-0">
                                                        <div class="table-responsive">
                                                            <table class="table table-sm table-hover mb-0">
                                                                <thead class="bg-light">
                                                                    <tr>
                                                                        <th>Date</th>
                                                                        <th>Action</th>
                                                                        <th>Notes</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach($schedule->logs()->orderBy('created_at', 'desc')->get() as $log)
                                                                    <tr>
                                                                        <td class="text-xs">{{ $log->created_at }}</td>
                                                                        <td>
                                                                            @if($log->action == 'start')
                                                                                <span class="badge bg-success">Start</span>
                                                                            @elseif($log->action == 'pause')
                                                                                <span class="badge bg-warning">Pause</span>
                                                                            @elseif($log->action == 'resume')
                                                                                <span class="badge bg-success">Resume</span>
                                                                            @elseif($log->action == 'complete')
                                                                                <span class="badge bg-primary">Complete</span>
                                                                            @else
                                                                                <span class="badge bg-secondary">{{ ucfirst($log->action) }}</span>
                                                                            @endif
                                                                        </td>
                                                                        <td class="text-xs">{{ $log->notes }}</td>
                                                                    </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="d-flex justify-content-center mt-3">
                                                    <button type="button" class="btn btn-info me-2" onclick="printProductionReport({{ $schedule->id }})">
                                                        <i class="fas fa-print me-1"></i> Print Report
                                                    </button>
                                                    <button type="button" class="btn btn-primary" onclick="exportProductionData({{ $schedule->id }})">
                                                        <i class="fas fa-file-export me-1"></i> Export Data
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-3">
                        {{ $scheduledItems->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="{{ asset('production-management-fixes.css') }}">
<script src="{{ asset('production-management-fixes.js') }}"></script>

@if(session('success'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: "{{ session('success') }}",
            showConfirmButton: false,
            timer: 2000
        });
    });
</script>
@endif

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Search functionality
        const searchInput = document.getElementById('productionSearchInput');
        const table = document.getElementById('productionItemsTable');
        const rows = table.getElementsByTagName('tr');

        searchInput.addEventListener('keyup', function() {
            const filter = searchInput.value.toLowerCase();
            for (let i = 1; i < rows.length; i++) { // Skip header row
                const cells = rows[i].getElementsByTagName('td');
                let found = false;
                
                // Only search in visible rows (respect filter)
                if (rows[i].style.display !== 'none') {
                    for (let j = 0; j < cells.length; j++) {
                        if (cells[j].innerText.toLowerCase().includes(filter)) {
                            found = true;
                            break;
                        }
                    }
                    rows[i].style.display = found ? '' : 'none';
                }
            }
        });

        // Filter by status
        const filterLinks = document.querySelectorAll('.filter-status');
        filterLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const status = this.getAttribute('data-status');
                const productionRows = document.querySelectorAll('.production-row');
                
                productionRows.forEach(row => {
                    if (status === 'all' || row.getAttribute('data-status') === status) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
                
                // Update dropdown button text
                document.getElementById('filterDropdown').textContent = 'Filter: ' + (status === 'all' ? 'All' : status.charAt(0).toUpperCase() + status.slice(1));
            });
        });

        // Update good quantity calculation in real-time
        @foreach($scheduledItems as $schedule)
        const defectInput{{ $schedule->id }} = document.getElementById('finalDefectiveQuantity{{ $schedule->id }}');
        if (defectInput{{ $schedule->id }}) {
            defectInput{{ $schedule->id }}.addEventListener('input', function() {
                const defectValue = parseInt(this.value) || 0;
                const totalQty = {{ $schedule->quantity }};
                const goodQty = totalQty - defectValue;
                
                document.getElementById('defectDisplay{{ $schedule->id }}').textContent = defectValue;
                document.getElementById('goodQty{{ $schedule->id }}').textContent = goodQty;
            });
        }
        @endforeach
    });

    // Toggle custom reason field for pause modal
    function toggleCustomReason(scheduleId) {
        const selectEl = document.getElementById('pauseReasonSelect' + scheduleId);
        const textareaEl = document.getElementById('pauseReason' + scheduleId);
        
        if (selectEl.value === 'custom') {
            textareaEl.value = '';
            textareaEl.placeholder = 'Please specify the reason';
        } else {
            textareaEl.value = selectEl.value;
        }
    }

    // Placeholder functions for export and print
    function printProductionReport(scheduleId) {
        Swal.fire({
            title: 'Printing Report',
            text: 'Preparing production report for printing...',
            icon: 'info',
            showConfirmButton: false,
            timer: 1500
        });
        // Actual print functionality would be implemented here
    }

    function exportProductionData(scheduleId) {
        Swal.fire({
            title: 'Exporting Data',
            text: 'Preparing production data for export...',
            icon: 'info',
            showConfirmButton: false,
            timer: 1500
        });
        // Actual export functionality would be implemented here
    }
</script>
@endpush
@endsection