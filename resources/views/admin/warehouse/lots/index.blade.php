@extends('admin.app')

@section('content')
<div class="container-fluid py-4">
  <div class="card shadow-sm border-0 rounded-4">
    <div class="card-header bg-gradient-primary text-white rounded-top-4 d-flex justify-content-between align-items-center px-4 py-3">
      <h5 class="mb-0"><i class="fas fa-layer-group me-2"></i> Warehouse Lots Management</h5>

      <button class="btn btn-light text-primary fw-bold" data-bs-toggle="modal" data-bs-target="#createLotModal">
        <i class="fas fa-plus me-1"></i> New Lot
      </button>
    </div>

    <div class="card-body px-4 py-3">
      <form method="GET" action="{{ route('admin.warehouse.lots.index', ['warehouse' => request('warehouse', $warehouse->id ?? '')]) }}" class="mb-4 d-flex align-items-end gap-2">
        @php
            $selectedWarehouseId = request('warehouse', $warehouse->id ?? '');
        @endphp

        <div class="flex-grow-1">
          <label class="form-label fw-bold">Filter by Warehouse</label>
          <select name="warehouse" class="form-select" onchange="this.form.submit()">
            <option value="">-- Select Warehouse --</option>
            @foreach($warehouses as $wh)
              <option value="{{ $wh->id }}" {{ (string) $selectedWarehouseId === (string) $wh->id ? 'selected' : '' }}>
                  {{ $wh->name }}
              </option>
            @endforeach
          </select>
        </div>
        <div>
          <button type="submit" class="btn btn-secondary mt-4">Apply</button>
        </div>
      </form>

      <div class="table-responsive">
        @if($lots->isEmpty())
          <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i> No lots found for this warehouse.
          </div>
        @else
          <table class="table table-hover align-middle">
            <thead class="table-light">
              <tr>
                <th>Lot Code</th>
                <th>Description</th>
                <th>Created At</th>
                <th>Status</th>
                <th class="text-end">Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($lots as $lot)
                <tr>
                  <td>{{ $lot->code }}</td>
                  <td>{{ $lot->description ?? '-' }}</td>
                  <td>{{ $lot->created_at->format('Y-m-d') }}</td>
                  <td>
                    <span class="badge {{ $lot->is_active ? 'bg-success' : 'bg-secondary' }}">
                      {{ $lot->is_active ? 'Active' : 'Inactive' }}
                    </span>
                  </td>
                  <td class="text-end">
                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editLotModal{{ $lot->id }}">
                      <i class="fas fa-edit me-1"></i> Edit
                    </button>
                    <form action="{{ route('admin.warehouse.lots.destroy', [$warehouse->id, $lot->id]) }}" method="POST" class="d-inline">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this lot?')">
                        <i class="fas fa-trash-alt me-1"></i> Delete
                      </button>
                    </form>
                  </td>
                </tr>
                @include('admin.warehouse.lots.partials.edit-lot-modal', ['lot' => $lot, 'warehouse' => $warehouse])
              @endforeach
            </tbody>
          </table>
        @endif
      </div>
    </div>
  </div>
</div>

@include('admin.warehouse.lots.partials.create-lot-modal', ['selectedWarehouseId' => $selectedWarehouseId])
@endsection
