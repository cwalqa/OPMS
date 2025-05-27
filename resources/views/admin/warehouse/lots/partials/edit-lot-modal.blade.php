<!-- Edit Lot Modal -->
<div class="modal fade" id="editLotModal{{ $lot->id }}" tabindex="-1" aria-labelledby="editLotModalLabel{{ $lot->id }}" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-md modal-fullscreen-sm-down">
    <div class="modal-content shadow-lg border-0 rounded-3">
      <div class="modal-header bg-gradient-primary text-white">
        <h5 class="modal-title" id="editLotModalLabel{{ $lot->id }}">
          <i class="fas fa-layer-group me-2"></i> Edit Lot ({{ $lot->code }})
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form action="{{ route('admin.lots.update', [$lot->warehouse_id, $lot->id]) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="modal-body p-4">
          <div class="mb-3">
            <label for="warehouse_id_{{ $lot->id }}" class="form-label fw-bold">Warehouse</label>
            <select class="form-select" id="warehouse_id_{{ $lot->id }}" name="warehouse_id" required>
              @foreach($warehouses as $wh)
                <option value="{{ $wh->id }}" {{ $lot->warehouse_id == $wh->id ? 'selected' : '' }}>{{ $wh->name }}</option>
              @endforeach
            </select>
          </div>

          <div class="mb-3">
            <label for="code_{{ $lot->id }}" class="form-label fw-bold">Lot Code</label>
            <input type="text" class="form-control" id="code_{{ $lot->id }}" name="code" value="{{ $lot->code }}" required>
          </div>

          <div class="mb-3">
            <label for="description_{{ $lot->id }}" class="form-label fw-bold">Description</label>
            <textarea class="form-control" id="description_{{ $lot->id }}" name="description" rows="2">{{ $lot->description }}</textarea>
          </div>

          <div class="mb-3">
            <label for="is_active_{{ $lot->id }}" class="form-label fw-bold">Status</label>
            <select class="form-select" id="is_active_{{ $lot->id }}" name="is_active">
              <option value="1" {{ $lot->is_active ? 'selected' : '' }}>Active</option>
              <option value="0" {{ !$lot->is_active ? 'selected' : '' }}>Inactive</option>
            </select>
          </div>
        </div>

        <div class="modal-footer border-top-0">
          <button type="submit" class="btn btn-primary rounded-pill px-4">
            <i class="fas fa-save me-2"></i> Update Lot
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
