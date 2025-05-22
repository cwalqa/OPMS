<!-- Create Shelf Modal -->
<div class="modal fade" id="createShelfModal" tabindex="-1" aria-labelledby="createShelfModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-md modal-fullscreen-sm-down">
    <div class="modal-content shadow-lg border-0 rounded-3">
      <div class="modal-header bg-gradient-primary text-white">
        <h5 class="modal-title" id="createShelfModalLabel">
          <i class="fas fa-columns me-2"></i> Add New Shelf
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form action="{{ route('admin.warehouse.shelves.store', ['warehouse' => $warehouse->id]) }}" method="POST" id="createShelfForm">
        @csrf
        <div class="modal-body p-4">
          <div class="mb-3">
            <label for="shelf_warehouse_id" class="form-label fw-bold">Warehouse <span class="text-danger">*</span></label>
            <select class="form-select" id="shelf_warehouse_id" name="warehouse_id" required>
              <option value="">Select Warehouse</option>
              @foreach($warehouses as $wh)
                <option value="{{ $wh->id }}" {{ $wh->id == $warehouse->id ? 'selected' : '' }}>
                  {{ $wh->name }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="mb-3">
            <label for="code" class="form-label fw-bold">Shelf Code <span class="text-danger">*</span></label>
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
            <i class="fas fa-save me-2"></i> Save Shelf
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
