<div class="modal fade" id="reworkModal{{ $defect->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="{{ route('admin.defects.rework', $defect->id) }}" class="ajax-rework-form">
            @csrf
            <div class="modal-content shadow-sm">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title">Mark Defect for Rework</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to mark this defect for rework?</p>
                    <div class="mb-3">
                        <label for="reworkNotes{{ $defect->id }}" class="form-label">Rework Notes (optional)</label>
                        <textarea class="form-control" name="rework_notes" id="reworkNotes{{ $defect->id }}" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-warning">Confirm Rework</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>
