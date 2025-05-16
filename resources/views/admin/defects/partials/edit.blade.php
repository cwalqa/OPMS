<div class="modal fade" id="editModal{{ $defect->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="{{ route('admin.defects.update', $defect->id) }}" class="ajax-edit-form">
            @csrf
            @method('PATCH')
            <div class="modal-content shadow-sm">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Edit Defect</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="description{{ $defect->id }}" class="form-label">Description</label>
                        <textarea class="form-control" name="description" id="description{{ $defect->id }}" rows="3" required>{{ $defect->description }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label for="quantity{{ $defect->id }}" class="form-label">Quantity</label>
                        <input type="number" name="quantity" id="quantity{{ $defect->id }}" class="form-control" min="1" value="{{ $defect->quantity }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="type{{ $defect->id }}" class="form-label">Type</label>
                        <select name="defect_type" id="type{{ $defect->id }}" class="form-select" required>
                            @foreach($defectTypes as $type)
                                <option value="{{ $type }}" @selected($defect->defect_type == $type)>{{ ucfirst($type) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="severity{{ $defect->id }}" class="form-label">Severity</label>
                        <select name="severity" id="severity{{ $defect->id }}" class="form-select" required>
                            @foreach($severityLevels as $level)
                                <option value="{{ $level }}" @selected($defect->severity == $level)>{{ ucfirst($level) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="status{{ $defect->id }}" class="form-label">Status</label>
                        <select name="status" id="status{{ $defect->id }}" class="form-select" required>
                            @foreach($statuses as $status)
                                <option value="{{ $status }}" @selected($defect->status == $status)>{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Update Defect</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>
