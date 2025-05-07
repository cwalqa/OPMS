@extends('admin.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3 d-flex justify-content-between">
                        <h6 class="text-white text-capitalize ps-3">Manage Production</h6>
                        <a href="{{ route('admin.production.start') }}" class="btn btn-light btn-sm me-3">
                            Start Production Process
                        </a>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="table-responsive p-3">
                        <!-- Search Input -->
                        <input type="text" id="productionSearchInput" placeholder="Search Production Logs..." class="form-control mb-3">
                        
                        <!-- Production Log Table -->
                        <table class="table align-items-center mb-0" id="productionTable">
                            <thead>
                                <tr>
                                    <th>Customer Name</th>
                                    <th>Purchase Order ID</th>
                                    <th>Product Name</th>
                                    <th>Order Quantity</th>
                                    <!-- <th>Production Line</th> -->
                                    <th>Status</th>
                                    <th>Start Time</th>
                                    <!-- <th>End Time</th> -->
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($productionLogs as $log)
                                    <tr>
                                        <td>{{ $log->customer_name }}</td>
                                        <td>{{ $log->qb_estimate_id }}</td>
                                        <td>{{ $log->product_name }}</td>
                                        <td>{{ $log->order_quantity }}</td>
                                        <!-- <td>{{ $log->productionLine->line_name ?? 'N/A' }}</td> -->
                                        <td>
                                            <span class="badge 
                                                @if($log->status == 'in-progress') bg-warning
                                                @elseif($log->status == 'completed') bg-success
                                                @else bg-secondary
                                                @endif">
                                                {{ ucfirst($log->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $log->start_time ?? 'Not Started' }}</td>
                                        <!-- <td>{{ $log->end_time ?? 'In Progress' }}</td> -->
                                        <td>
                                            <!-- Update Button -->
                                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#updateProductionModal{{ $log->id }}">
                                                Update
                                            </button>

                                            <!-- Download QR Code Button -->
                                            <a href="{{ route('production.downloadQr', ['log_id' => $log->id]) }}" class="btn btn-success btn-sm">
                                                Download QR Code
                                            </a>
                                        </td>
                                    </tr>

                                    <!-- Update Production Modal -->
                                    <div class="modal fade" id="updateProductionModal{{ $log->id }}" tabindex="-1" aria-labelledby="updateProductionModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Update Production</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <form action="{{ route('production.update', $log->id) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        
                                                        <!-- Update Production Status -->
                                                        <div class="mb-3">
                                                            <label for="status">Production Status</label>
                                                            <select name="status" class="form-control" required>
                                                                <option value="in-progress" {{ $log->status == 'in-progress' ? 'selected' : '' }}>In Progress</option>
                                                                <option value="completed" {{ $log->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                                                <option value="paused" {{ $log->status == 'paused' ? 'selected' : '' }}>Paused</option>
                                                            </select>
                                                        </div>

                                                        <!-- Additional Notes -->
                                                        <div class="mb-3">
                                                            <label for="notes">Additional Notes</label>
                                                            <textarea name="notes" class="form-control">{{ $log->notes }}</textarea>
                                                        </div>

                                                        <!-- Defects Report -->
                                                        <div class="mb-3">
                                                            <label for="defects">Report Defects</label>
                                                            <textarea name="defects" class="form-control">{{ $log->defects }}</textarea>
                                                        </div>

                                                        <!-- End Time (if Completed) -->
                                                        <div class="mb-3">
                                                            <label for="end_time">End Time (if completed)</label>
                                                            <input type="datetime-local" name="end_time" class="form-control" value="{{ $log->end_time }}">
                                                        </div>

                                                        <button type="submit" class="btn btn-primary">Save Changes</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div> <!-- End Modal -->
                                @endforeach
                            </tbody>
                        </table>

                        <!-- Pagination -->
                        <div class="mt-3">
                            {{ $productionLogs->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const searchInput = document.getElementById("productionSearchInput");
        const tableRows = document.querySelectorAll("#productionTable tbody tr");

        searchInput.addEventListener("keyup", function () {
            const filter = searchInput.value.toLowerCase();
            tableRows.forEach(row => {
                const textContent = row.innerText.toLowerCase();
                row.style.display = textContent.includes(filter) ? "" : "none";
            });
        });
    });
</script>
@endsection
