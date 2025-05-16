<div class="modal fade" id="materialsModal" tabindex="-1" aria-labelledby="materialsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-gradient-success text-white">
                <h5 class="modal-title" id="materialsModalLabel">Packaging Materials</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Manage and view all available packaging materials, including boxes, wraps, fillers, tapes, and labels.</p>
                <ul>
                    <li>Check available materials.</li>
                    <li>Monitor stock levels and reorder thresholds.</li>
                    <li>Add or edit material details.</li>
                </ul>
            </div>
            <div class="modal-footer">
                <a href="{{ route('admin.packaging.materials') }}" class="btn btn-success">Go to Materials</a>
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
