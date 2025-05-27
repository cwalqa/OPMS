<!-- Edit Shelf Modal -->
<div class="modal fade" id="editShelfModal{{ $shelf->id }}" tabindex="-1" aria-labelledby="editShelfModalLabel{{ $shelf->id }}" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-md modal-fullscreen-sm-down">
    <div class="modal-content shadow-lg border-0 rounded-3">
      <div class="modal-header bg-gradient-primary text-white">
        <h5 class="modal-title" id="editShelfModalLabel{{ $shelf->id }}">
          <i class="fas fa-columns me-2"></i> Edit Shelf ({{ $shelf->code }})
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form action="{{ route('admin.shelves.update', [$shelf->warehouse_id, $shelf->id]) }}" method="POST" id="editShelfForm{{ $shelf->id }}">
        @csrf
        @method('PUT')

        <div class="modal-body p-4">
          <div class="mb-3">
            <label for="warehouse_id_{{ $shelf->id }}" class="form-label fw-bold">Warehouse</label>
            <select class="form-select" name="warehouse_id" id="warehouse_id_{{ $shelf->id }}" required>
              <option value="">Select Warehouse</option>
              @foreach($warehouses as $wh)
                <option value="{{ $wh->id }}" {{ $shelf->warehouse_id == $wh->id ? 'selected' : '' }}>{{ $wh->name }}</option>
              @endforeach
            </select>
          </div>

          <div class="mb-3">
            <label for="code_{{ $shelf->id }}" class="form-label fw-bold">Shelf Code</label>
            <input type="text" class="form-control" id="code_{{ $shelf->id }}" name="code" value="{{ $shelf->code }}" required>
          </div>

          <div class="mb-3">
            <label for="description_{{ $shelf->id }}" class="form-label fw-bold">Description</label>
            <textarea class="form-control" id="description_{{ $shelf->id }}" name="description" rows="2">{{ $shelf->description }}</textarea>
          </div>

          <div class="mb-3">
            <label for="is_active_{{ $shelf->id }}" class="form-label fw-bold">Status</label>
            <select class="form-select" id="is_active_{{ $shelf->id }}" name="is_active">
              <option value="1" {{ $shelf->is_active ? 'selected' : '' }}>Active</option>
              <option value="0" {{ !$shelf->is_active ? 'selected' : '' }}>Inactive</option>
            </select>
          </div>
        </div>

        <div class="modal-footer border-top-0">
          <button type="submit" class="btn btn-primary rounded-pill px-4">
            <i class="fas fa-save me-2"></i> Update Shelf
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
