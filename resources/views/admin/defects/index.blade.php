@extends('admin.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">

            {{-- Alert for schedules with missing defect logs --}}
            @if($schedulesMissingDefectLogs->isNotEmpty())
                <div class="alert alert-warning">
                    <strong>Warning!</strong> There are {{ $schedulesMissingDefectLogs->count() }} schedules with defective quantity but no defect logs.
                    <ul class="mb-0">
                        @foreach($schedulesMissingDefectLogs as $schedule)
                            <li>
                                <strong>Schedule ID:</strong> {{ $schedule->id }},
                                <strong>Item:</strong> {{ $schedule->item->name ?? '-' }},
                                <strong>Defective Qty:</strong> {{ $schedule->defective_quantity }},
                                <strong>Status:</strong> {{ ucfirst($schedule->schedule_status) }}
                                <a href="{{ route('admin.defects.create', ['schedule_id' => $schedule->id]) }}" class="btn btn-sm btn-outline-danger ms-2">Report Now</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card my-4">
                <div class="card-header bg-gradient-danger shadow-danger border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center">
                    <h6 class="text-white text-capitalize ps-3">Manage Production Defects</h6>
                    <a href="{{ route('admin.defects.create') }}" class="btn btn-light btn-sm">Report New Defect</a>
                </div>
                <div class="card-body px-4 pb-4">
                    <input type="text" id="defectSearchInput" placeholder="Search defects..." class="form-control mb-3">

                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle" id="defectTable">
                            <thead class="table-dark">
                                <tr>
                                    <th>Order Number</th>
                                    <th>Item SKU</th>
                                    <th>Product Name</th>
                                    <th>Description</th>
                                    <th>Quantity</th>
                                    <th>Type</th>
                                    <th>Severity</th>
                                    <th>Status</th>
                                    <th>Reported On</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($defects as $log)
                                    @php
                                        $estimateItem = $log->estimateItem;
                                        $orderNumber = $estimateItem?->order?->purchase_order_number ?? '-';
                                        $itemSku = $estimateItem?->sku ?? '-';
                                        $productName = $estimateItem?->name ?? '-';
                                    @endphp
                                    <tr>
                                        <td>{{ $log->estimateItem?->order?->purchase_order_number ?? '-' }}</td>
                                        <td>{{ $log->estimate_item_sku }}</td>
                                        <td>{{ $log->estimateItem?->name ?? '-' }}</td>
                                        <td>{{ $log->description }}</td>
                                        <td>{{ $log->quantity }}</td>
                                        <td>{{ ucfirst($log->defect_type) }}</td>
                                        <td>{{ ucfirst($log->severity) }}</td>
                                        <td>{{ ucfirst($log->status) }}</td>
                                        <td>{{ $log->created_at->format('d M Y') }}</td>
                                        <td>
                                            <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#viewModal{{ $log->id }}">View</button>
                                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editModal{{ $log->id }}">Edit</button>
                                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#reworkModal{{ $log->id }}">Rework</button>
                                            <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#discardModal{{ $log->id }}">Discard</button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center">No defects found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $defects->links() }}
                    </div>

                    {{-- Include modals outside the table --}}
                    @foreach($defects as $log)
                        @include('admin.defects.partials.view', ['defect' => $log])
                        @include('admin.defects.partials.edit', ['defect' => $log, 'defectTypes' => $defectTypes, 'severityLevels' => $severityLevels, 'statuses' => $statuses])
                        @include('admin.defects.partials.rework', ['defect' => $log])
                        @include('admin.defects.partials.discard', ['defect' => $log])
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</div>
@endsection



@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('defectSearchInput').addEventListener('input', function () {
            const filter = this.value.toLowerCase();
            document.querySelectorAll('#defectTable tbody tr').forEach(row => {
                row.style.display = row.textContent.toLowerCase().includes(filter) ? '' : 'none';
            });
        });

        function ajaxFormSubmit(selector, successMessage) {
            document.querySelectorAll(selector).forEach(form => {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();
                    const formData = new FormData(this);

                    fetch(this.action, {
                        method: this.method,
                        headers: { 'X-CSRF-TOKEN': form.querySelector('[name="_token"]').value },
                        body: formData,
                    })
                    .then(response => response.ok ? response.json().catch(() => ({})) : response.json().then(data => Promise.reject(data)))
                    .then(() => {
                        bootstrap.Modal.getInstance(this.closest('.modal')).hide();
                        alert(successMessage);
                        location.reload();
                    })
                    .catch(error => alert(error?.message || 'An error occurred.'));
                });
            });
        }

        ajaxFormSubmit('.ajax-edit-form', 'Defect updated!');
        ajaxFormSubmit('.ajax-rework-form', 'Defect marked for rework!');
        ajaxFormSubmit('.ajax-discard-form', 'Defect marked for discard!');
    });
</script>
@endsection
