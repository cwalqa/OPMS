@extends('admin.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3 d-flex justify-content-between">
                        <h6 class="text-white text-capitalize ps-3">Manage Deliveries</h6>
                        <!-- Button to redirect to the new scheduling delivery page -->
                        <a href="{{ route('admin.deliveries.create') }}" class="btn btn-light btn-sm me-3">
                            Schedule New Delivery
                        </a>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="table-responsive p-3">
                        <!-- Input for table search functionality -->
                        <input type="text" id="deliverySearchInput" placeholder="Search Deliveries..." class="form-control mb-3">
                        
                        <!-- Deliveries management table -->
                        <table class="table align-items-center mb-0" id="deliveryTable">
                            <thead>
                                <tr>
                                    <th>Delivery ID</th>
                                    <th>Order Number</th>
                                    <th>Item Name</th>
                                    <th>Quantity Delivered</th>
                                    <th>Status</th>
                                    <th>Delivery Date</th>
                                    <th>Assigned Dispatch</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($deliveries as $delivery)
                                    <tr>
                                        <td>{{ $delivery->id }}</td>
                                        <td>{{ $delivery->order_number }}</td>
                                        <td>{{ $delivery->item->description }}</td>
                                        <td>{{ $delivery->quantity }}</td>
                                        <td>{{ ucfirst($delivery->status) }}</td>
                                        <td>{{ $delivery->delivery_date }}</td>
                                        <td>{{ $delivery->assignedDispatch ? $delivery->assignedDispatch->name : 'N/A' }}</td>
                                        <td>
                                            <!-- Action Buttons -->
                                            <a href="{{ route('deliveries.edit', ['id' => $delivery->id]) }}" class="btn btn-primary btn-sm">Edit</a>
                                            <button class="btn btn-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#logNotesModal{{ $delivery->id }}">Add Notes</button>
                                        </td>
                                    </tr>

                                    <!-- Notes Modal -->
                                    <div class="modal fade" id="logNotesModal{{ $delivery->id }}" tabindex="-1" aria-labelledby="logNotesModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="logNotesModalLabel">Add Notes</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <form action="{{ route('deliveries.logNotes') }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="delivery_id" value="{{ $delivery->id }}">
                                                        <div class="form-group">
                                                            <label for="notes">Notes</label>
                                                            <textarea name="notes" class="form-control">{{ $delivery->notes }}</textarea>
                                                        </div>
                                                        <button type="submit" class="btn btn-primary mt-3">Save Notes</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </tbody>
                        </table>

                        <!-- Pagination -->
                        <div class="mt-3">
                            {{ $deliveries->links() }}
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
    document.addEventListener('DOMContentLoaded', function () {
        // Optional: Search functionality for the deliveries table
        const searchInput = document.getElementById('deliverySearchInput');
        const tableRows = document.querySelectorAll('#deliveryTable tbody tr');

        searchInput.addEventListener('input', function () {
            const searchTerm = searchInput.value.toLowerCase();
            tableRows.forEach(row => {
                const rowText = row.textContent.toLowerCase();
                row.style.display = rowText.includes(searchTerm) ? '' : 'none';
            });
        });
    });
</script>
@endsection
