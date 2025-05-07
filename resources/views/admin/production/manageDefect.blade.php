@extends('admin.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-danger shadow-danger border-radius-lg pt-4 pb-3 d-flex justify-content-between">
                        <h6 class="text-white text-capitalize ps-3">Manage Production Defects</h6>
                        <!-- Button to trigger defect reporting -->
                        <a href="{{ route('admin.reportDefect') }}" class="btn btn-light btn-sm me-3">
                            Report New Defect
                        </a>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="table-responsive p-3">
                        <!-- Input for table search functionality -->
                        <input type="text" id="defectSearchInput" placeholder="Search Defect Logs..." class="form-control mb-3">
                        
                        <!-- Defects management table -->
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
                                @foreach($defectLogs as $log)
                                    <tr>
                                        <td>{{ $log->item->description }}</td>
                                        <td>{{ $log->description }}</td>
                                        <td>{{ $log->quantity }}</td>
                                        <td>{{ ucfirst($log->defect_type) }}</td>
                                        <td>{{ ucfirst($log->severity) }}</td>
                                        <td>{{ ucfirst($log->status) }}</td>
                                        <td>{{ $log->created_at }}</td>
                                        <td>
                                            <!-- Action Buttons -->
                                            <a href="{{ route('defects.edit', ['id' => $log->id]) }}" class="btn btn-primary btn-sm">Edit</a>
                                            <button class="btn btn-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#logNotesModal{{ $log->id }}">Add Notes</button>
                                        </td>
                                    </tr>

                                    <!-- Notes Modal -->
                                    <div class="modal fade" id="logNotesModal{{ $log->id }}" tabindex="-1" aria-labelledby="logNotesModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="logNotesModalLabel">Add Notes</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <form action="{{ route('defects.logNotes') }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="defect_id" value="{{ $log->id }}">
                                                        <div class="form-group">
                                                            <label for="notes">Notes</label>
                                                            <textarea name="notes" class="form-control">{{ $log->notes }}</textarea>
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
                            {{ $defectLogs->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://unpkg.com/html5-qrcode/minified/html5-qrcode.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Search functionality for defect logs
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
