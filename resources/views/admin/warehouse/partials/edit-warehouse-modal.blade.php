<!-- Edit Warehouse Modal -->
<div class="modal fade" id="editWarehouseModal{{ $warehouse->id }}" tabindex="-1" aria-labelledby="editWarehouseModalLabel{{ $warehouse->id }}" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg modal-fullscreen-sm-down">
    <div class="modal-content shadow-lg border-0 rounded-3">
      <div class="modal-header bg-gradient-dark text-white">
        <h5 class="modal-title" id="editWarehouseModalLabel{{ $warehouse->id }}">
          <i class="fas fa-edit me-2"></i> Edit Warehouse - {{ $warehouse->name }}
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form action="{{ route('admin.warehouse.update', $warehouse->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-body p-4">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label fw-bold">Warehouse Name</label>
              <input type="text" name="name" value="{{ $warehouse->name }}" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-bold">Code</label>
              <input type="text" name="code" value="{{ $warehouse->code }}" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-bold">Location</label>
              <input type="text" name="location" value="{{ $warehouse->location }}" class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-bold">Status</label>
              <select name="is_active" class="form-select">
                <option value="1" {{ $warehouse->is_active ? 'selected' : '' }}>Active</option>
                <option value="0" {{ !$warehouse->is_active ? 'selected' : '' }}>Inactive</option>
              </select>
            </div>
            <div class="col-md-12">
              <label class="form-label fw-bold">Address</label>
              <textarea name="address" rows="2" class="form-control">{{ $warehouse->address }}</textarea>
            </div>
            <div class="col-md-12">
              <label class="form-label fw-bold">Description</label>
              <textarea name="description" rows="2" class="form-control">{{ $warehouse->description }}</textarea>
            </div>
          </div>
        </div>

        <div class="modal-footer border-top-0">
          <button type="submit" class="btn btn-dark rounded-pill px-4">
            <i class="fas fa-save me-2"></i> Update Warehouse
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
