<div class="modal fade" id="discardModal{{ $defect->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="{{ route('admin.defects.discard', $defect->id) }}" class="ajax-discard-form">
            @csrf
            <div class="modal-content shadow-sm">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Mark Defect as Discarded</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>This action will mark the defect as discarded. Are you sure?</p>
                    <div class="mb-3">
                        <label for="discardNotes{{ $defect->id }}" class="form-label">Discard Notes (optional)</label>
                        <textarea class="form-control" name="discard_notes" id="discardNotes{{ $defect->id }}" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger">Confirm Discard</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>
