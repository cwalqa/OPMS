<div class="modal fade" id="resumeModal{{ $schedule->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="{{ route('admin.production.resume', $schedule->id) }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Resume Production</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <p><strong>Currently Paused:</strong> {{ $productName }} for order {{ $schedule->item->order->purchase_order_number }}</p>
                        <p class="mb-0"><strong>Progress:</strong> {{ $schedule->quantity - ($schedule->defective_quantity ?? 0) }} / {{ $schedule->quantity }}</p>
                    </div>
                    <p>Are you ready to resume production for this item?</p>
                    <div class="mb-3">
                        <label class="form-label">Notes (optional)</label>
                        <textarea class="form-control" name="resume_notes" rows="3"></textarea>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" value="1" id="confirmResume{{ $schedule->id }}" required>
                        <label class="form-check-label" for="confirmResume{{ $schedule->id }}">
                            I confirm that the pause issue has been resolved
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-play me-1"></i> Resume Production
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>
