<div class="modal fade" id="inventoryModal" tabindex="-1" aria-labelledby="inventoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-gradient-warning text-dark">
                <h5 class="modal-title" id="inventoryModalLabel">Packaging Inventory</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Review and manage packaging inventory levels across warehouse locations.</p>
                <ul>
                    <li>Track material stock by location.</li>
                    <li>View low stock alerts and reorder needs.</li>
                    <li>Schedule restocking or transfer materials between locations.</li>
                </ul>
            </div>
            <div class="modal-footer">
                <a href="{{ route('admin.packaging.inventory') }}" class="btn btn-warning text-dark">Open Inventory</a>
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
