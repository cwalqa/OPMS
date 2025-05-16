<div class="modal fade" id="createTaskModal" tabindex="-1" aria-labelledby="createTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.packaging.create') }}" method="POST">
                @csrf
                <div class="modal-header bg-gradient-primary text-white">
                    <h5 class="modal-title" id="createTaskModalLabel">Create New Packaging Task</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- Warehouse Notification --}}
                    <div class="form-group mb-3">
                        <label for="warehouse_notification_id">Warehouse Notification</label>
                        <select name="warehouse_notification_id" id="warehouse_notification_id" class="form-select" required>
                            <option value="">-- Select Notification --</option>
                            @foreach($warehouseNotifications as $notification)
                                <option value="{{ $notification->id }}">
                                    {{ $notification->tracking_id }} - {{ $notification->schedule->item->name ?? 'Unknown Product' }} (Qty: {{ $notification->quantity }})
                                </option>

                            @endforeach
                        </select>
                    </div>

                    {{-- Packaging Type --}}
                    <div class="form-group mb-3">
                        <label for="packaging_type">Packaging Type</label>
                        <input type="text" name="packaging_type" id="packaging_type" class="form-control" placeholder="e.g. Box, Wrap, Custom" required>
                    </div>

                    {{-- Priority --}}
                    <div class="form-group mb-3">
                        <label for="priority">Priority</label>
                        <select name="priority" id="priority" class="form-select" required>
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                            <option value="urgent">Urgent</option>
                        </select>
                    </div>

                    {{-- Packaging Notes --}}
                    <div class="form-group mb-3">
                        <label for="packaging_notes">Packaging Notes</label>
                        <textarea name="packaging_notes" id="packaging_notes" rows="2" class="form-control" placeholder="Optional notes..."></textarea>
                    </div>

                    {{-- Special Instructions --}}
                    <div class="form-group mb-3">
                        <label for="special_instructions">Special Instructions</label>
                        <textarea name="special_instructions" id="special_instructions" rows="2" class="form-control" placeholder="e.g. Fragile handling, custom labeling..."></textarea>
                    </div>

                    {{-- Assign to --}}
                    <div class="form-group mb-3">
                        <label for="assigned_to">Assign To (Optional)</label>
                        <select name="assigned_to" id="assigned_to" class="form-select">
                            <option value="">-- Do not assign now --</option>
                            @foreach($packagingStaff as $staff)
                                <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Create Task</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
