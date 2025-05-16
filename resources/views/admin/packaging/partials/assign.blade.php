<div class="modal fade" id="assignModal-{{ $task->id }}" tabindex="-1" aria-labelledby="assignModalLabel-{{ $task->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.packaging.assign', $task->id) }}" method="POST">
                @csrf
                <div class="modal-header bg-gradient-primary text-white">
                    <h5 class="modal-title" id="assignModalLabel-{{ $task->id }}">Assign Task #{{ $task->id }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="user_id">Assign To</label>
                        <select name="user_id" id="user_id" class="form-select" required>
                            <option value="">-- Select Staff --</option>
                            @foreach($packagingStaff as $staff)
                                <option value="{{ $staff->id }}" {{ ($task->assignedTo && $task->assignedTo->id === $staff->id) ? 'selected' : '' }}>
                                    {{ $staff->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="assignment_notes">Assignment Notes</label>
                        <textarea name="assignment_notes" id="assignment_notes" rows="3" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Assign Task</button>
                </div>
            </form>
        </div>
    </div>
</div>
