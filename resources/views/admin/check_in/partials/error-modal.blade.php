<div class="modal-header bg-danger text-white">
    <h5 class="modal-title"><i class="fas fa-exclamation-circle me-2"></i> Error</h5>
</div>

<div class="modal-body">
    <div class="alert alert-danger">
        {{ $message ?? 'An error occurred. Please try again.' }}
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
</div>
