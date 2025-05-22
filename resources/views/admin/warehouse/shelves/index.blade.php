@extends('admin.app')

@section('content')
<div class="container-fluid py-4">
  <div class="card shadow-sm border-0 rounded-4">
    <div class="card-header bg-gradient-primary text-white rounded-top-4 d-flex justify-content-between align-items-center px-4 py-3">
      <h5 class="mb-0">
        <i class="fas fa-boxes-stacked me-2"></i> Shelves for Warehouse: {{ $warehouse->name }}
      </h5>
      <button class="btn btn-light text-primary fw-bold" data-bs-toggle="modal" data-bs-target="#createShelfModal">
        <i class="fas fa-plus me-1"></i> New Shelf
      </button>
    </div>

    <div class="card-body px-4 py-3">
      {{-- Filter by warehouse --}}
      <form id="warehouseFilterForm" class="mb-4 d-flex align-items-end gap-2">
        <div class="flex-grow-1">
          <label class="form-label fw-bold">Filter by Warehouse</label>
          <select id="warehouseFilterSelect" class="form-select">
            <option value="">-- Select Warehouse --</option>
            @foreach($warehouses as $wh)
              <option value="{{ $wh->id }}" {{ $warehouse->id == $wh->id ? 'selected' : '' }}>
                {{ $wh->name }}
              </option>
            @endforeach
          </select>
        </div>
        <div>
          <button type="button" class="btn btn-secondary mt-4" onclick="redirectToWarehouse()">Apply</button>
        </div>
      </form>

      {{-- Shelves Table --}}
      <div class="table-responsive">
        @if($shelves->isEmpty())
          <div class="alert alert-info mb-0">
            <i class="fas fa-info-circle me-2"></i> No shelves found for this warehouse.
          </div>
        @else
          <table class="table table-hover align-middle">
            <thead class="table-light">
              <tr>
                <th>Shelf Code</th>
                <th>Description</th>
                <th>Created At</th>
                <th>Status</th>
                <th class="text-end">Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($shelves as $shelf)
                <tr>
                  <td>{{ $shelf->code }}</td>
                  <td>{{ $shelf->description ?? '-' }}</td>
                  <td>{{ $shelf->created_at->format('Y-m-d') }}</td>
                  <td>
                    <span class="badge {{ $shelf->is_active ? 'bg-success' : 'bg-secondary' }}">
                      {{ $shelf->is_active ? 'Active' : 'Inactive' }}
                    </span>
                  </td>
                  <td class="text-end">
                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editShelfModal{{ $shelf->id }}">
                      <i class="fas fa-edit me-1"></i> Edit
                    </button>
                    <form action="{{ route('admin.warehouse.shelves.destroy', [$warehouse->id, $shelf->id]) }}" method="POST">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this shelf?')">
                        <i class="fas fa-trash-alt me-1"></i> Delete
                      </button>
                    </form>
                  </td>
                </tr>

                {{-- Edit Shelf Modal --}}
                @include('admin.warehouse.shelves.partials.edit-shelf-modal', [
                    'shelf' => $shelf,
                    'warehouse' => $warehouse,
                    'warehouses' => $warehouses
                ])
              @endforeach
            </tbody>
          </table>
        @endif
      </div>
    </div>
  </div>
</div>

{{-- Create Shelf Modal --}}
@include('admin.warehouse.shelves.partials.create-shelf-modal', [
    'warehouse' => $warehouse,
    'warehouses' => $warehouses
])
@endsection

@section('scripts')
<script>
// Pre-generate warehouse URLs in Blade and pass to JavaScript
const warehouseUrls = {
    @foreach($warehouses as $wh)
        {{ $wh->id }}: "{{ route('admin.warehouse.shelves.index', $wh->id) }}",
    @endforeach
};

function redirectToWarehouse() {
    const selectedId = document.getElementById('warehouseFilterSelect').value;
    console.log('Selected ID:', selectedId); // Debug line

    if (selectedId && warehouseUrls[selectedId]) {
      console.log('Target URL:', targetUrl); // Debug line
        window.location.href = warehouseUrls[selectedId];
    } else if (selectedId) {
        // Fallback if URL not found in pre-generated list
        const baseUrl = "{{ url('warehouse') }}";
        window.location.href = `${baseUrl}/${selectedId}/shelves`;
    } else {
        alert("Please select a warehouse.");
    }
}
</script>
@endsection
