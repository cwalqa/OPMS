<div class="modal fade" id="pauseModal{{ $schedule->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="{{ route('admin.production.pause', $schedule->id) }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Pause Production</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Reason for Pausing</label>
                        <select class="form-select mb-2" id="pauseReasonSelect{{ $schedule->id }}" onchange="toggleCustomReason({{ $schedule->id }})">
                            <option value="">Select a reason</option>
                            <option value="Equipment failure">Equipment failure</option>
                            <option value="Material shortage">Material shortage</option>
                            <option value="Staff shortage">Staff shortage</option>
                            <option value="Quality concerns">Quality concerns</option>
                            <option value="Scheduled maintenance">Scheduled maintenance</option>
                            <option value="custom">Other (please specify)</option>
                        </select>
                        <textarea class="form-control" id="pauseReason{{ $schedule->id }}" name="pause_reason" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Defective Quantity (if any)</label>
                        <input type="number" class="form-control" name="defective_quantity" min="0" max="{{ $schedule->quantity }}" value="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Defect Notes</label>
                        <textarea class="form-control" name="defect_notes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-pause me-1"></i> Pause Production
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>
