<!-- Create Lot Modal -->
<div class="modal fade" id="createLotModal" tabindex="-1" aria-labelledby="createLotModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-md modal-fullscreen-sm-down">
    <div class="modal-content shadow-lg border-0 rounded-3">
      <div class="modal-header bg-gradient-primary text-white">
        <h5 class="modal-title" id="createLotModalLabel">
          <i class="fas fa-layer-group me-2"></i> Add New Lot
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form action="{{ route('admin.warehouse.lots.store', ['warehouse' => 0]) }}" method="POST" id="createLotForm">
        @csrf
        <div class="modal-body p-4">

          <div class="mb-3">
            <label for="warehouse_id" class="form-label fw-bold">Assign to Warehouse <span class="text-danger">*</span></label>
            <select class="form-select" name="warehouse_id" id="warehouse_id" required>
              <option value="">Select Warehouse</option>
              @foreach($warehouses as $wh)
                <option value="{{ $wh->id }}">{{ $wh->name }}</option>
              @endforeach
            </select>
          </div>

          <div class="mb-3">
            <label for="code" class="form-label fw-bold">Lot Code <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="code" name="code" required>
          </div>

          <div class="mb-3">
            <label for="description" class="form-label fw-bold">Description</label>
            <textarea class="form-control" id="description" name="description" rows="2"></textarea>
          </div>

          <div class="mb-3">
            <label for="is_active" class="form-label fw-bold">Status</label>
            <select class="form-select" id="is_active" name="is_active">
              <option value="1" selected>Active</option>
              <option value="0">Inactive</option>
            </select>
          </div>
        </div>

        <div class="modal-footer border-top-0">
          <button type="submit" class="btn btn-primary rounded-pill px-4">
            <i class="fas fa-save me-2"></i> Save Lot
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  // Dynamically update form action to match selected warehouse ID
  document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('createLotForm');
    const warehouseSelect = document.getElementById('warehouse_id');

    warehouseSelect.addEventListener('change', () => {
      const selectedId = warehouseSelect.value;
      if (selectedId) {
        form.action = form.action.replace(/\/\d+$/, `/${selectedId}`);
      }
    });
  });
</script>
