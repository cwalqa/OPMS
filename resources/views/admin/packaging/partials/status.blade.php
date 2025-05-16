<div class="modal fade" id="statusModal-{{ $task->id }}" tabindex="-1" aria-labelledby="statusModalLabel-{{ $task->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.packaging.update-status', $task->id) }}" method="POST">
                @csrf
                <div class="modal-header bg-gradient-warning text-dark">
                    <h5 class="modal-title" id="statusModalLabel-{{ $task->id }}">Update Status for Task #{{ $task->id }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="status">Status</label>
                        <select name="status" id="status" class="form-select" required>
                            @foreach($statuses as $value => $label)
                                <option value="{{ $value }}" {{ $task->status === $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="status_notes">Status Notes</label>
                        <textarea name="status_notes" id="status_notes" rows="3" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>
