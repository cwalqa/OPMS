<!-- Create Warehouse Modal -->
<div class="modal fade" id="createWarehouseModal" tabindex="-1" aria-labelledby="createWarehouseModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg modal-fullscreen-sm-down">
    <div class="modal-content shadow-lg border-0 rounded-3">
      <div class="modal-header bg-gradient-primary text-white">
        <h5 class="modal-title" id="createWarehouseModalLabel">
          <i class="fas fa-warehouse me-2"></i> Add New Warehouse
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form action="{{ route('admin.warehouse.store') }}" method="POST">
        @csrf
        <div class="modal-body p-4">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label fw-bold">Warehouse Name <span class="text-danger">*</span></label>
              <input type="text" name="name" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-bold">Code <span class="text-danger">*</span></label>
              <input type="text" name="code" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-bold">Location</label>
              <input type="text" name="location" class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-bold">Status</label>
              <select name="is_active" class="form-select">
                <option value="1" selected>Active</option>
                <option value="0">Inactive</option>
              </select>
            </div>
            <div class="col-md-12">
              <label class="form-label fw-bold">Address</label>
              <textarea name="address" rows="2" class="form-control"></textarea>
            </div>
            <div class="col-md-12">
              <label class="form-label fw-bold">Description</label>
              <textarea name="description" rows="2" class="form-control"></textarea>
            </div>
          </div>
        </div>

        <div class="modal-footer border-top-0">
          <button type="submit" class="btn btn-primary rounded-pill px-4">
            <i class="fas fa-save me-2"></i> Save Warehouse
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
