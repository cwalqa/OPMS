@extends('admin.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-danger shadow-danger border-radius-lg pt-4 pb-3 d-flex justify-content-between">
                        <h6 class="text-white text-capitalize ps-3">Manage Production Defects</h6>
                        <a href="{{ route('admin.defects.create') }}" class="btn btn-light btn-sm me-3">
                            Report New Defect
                        </a>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="table-responsive p-3">
                        <input type="text" id="defectSearchInput" placeholder="Search Defect Logs..." class="form-control mb-3">
                        
                        <table class="table align-items-center mb-0" id="defectTable">
                            <thead>
                                <tr>
                                    <th>Item Name</th>
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
                                @foreach($defects as $log)
                                    <tr>
                                        <td>{{ $log->estimateItem->name ?? '-' }}</td>
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

                                    <!-- View Modal -->
                                    <div class="modal fade" id="viewModal{{ $log->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Defect Details</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    @include('admin.defects.partials.view', ['defect' => $log])
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Edit Modal -->
                                    <div class="modal fade" id="editModal{{ $log->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Edit Defect</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    @include('admin.defects.partials.edit', ['defect' => $log, 'defectTypes' => $defectTypes, 'severityLevels' => $severityLevels, 'statuses' => $statuses])
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Rework Modal -->
                                    <div class="modal fade" id="reworkModal{{ $log->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form method="POST" action="{{ route('admin.defects.rework', $log->id) }}">
                                                    @csrf
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Mark for Rework</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label>Corrective Action</label>
                                                            <textarea name="corrective_action" class="form-control" required></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" class="btn btn-warning">Confirm</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Discard Modal -->
                                    <div class="modal fade" id="discardModal{{ $log->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form method="POST" action="{{ route('admin.defects.discard', $log->id) }}">
                                                    @csrf
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Mark for Discard</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label>Reason for Discard</label>
                                                            <textarea name="discard_reason" class="form-control" required></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" class="btn btn-danger">Confirm</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="mt-3">
                            {{ $defects->links() }}
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
        const defectSearchInput = document.getElementById('defectSearchInput');
        defectSearchInput.addEventListener('input', function () {
            const filter = defectSearchInput.value.toLowerCase();
            const rows = document.querySelectorAll('#defectTable tbody tr');
            rows.forEach(row => {
                row.style.display = row.textContent.toLowerCase().includes(filter) ? '' : 'none';
            });
        });
    });
</script>
@endsection
