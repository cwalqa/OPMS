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
                                            @switch($log->action)
                                                @case('start')
                                                    <span class="badge bg-success">Start</span>
                                                    @break
                                                @case('pause')
                                                    <span class="badge bg-warning">Pause</span>
                                                    @break
                                                @case('resume')
                                                    <span class="badge bg-success">Resume</span>
                                                    @break
                                                @case('complete')
                                                    <span class="badge bg-primary">Complete</span>
                                                    @break
                                                @default
                                                    <span class="badge bg-secondary">{{ ucfirst($log->action) }}</span>
                                            @endswitch
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
