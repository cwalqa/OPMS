@extends('admin.app')



@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center rounded-top">
                    <h6 class="mb-0 ps-2">Packaging Tasks</h6>
                    <div>
                        <button class="btn btn-light btn-sm me-2" data-bs-toggle="modal" data-bs-target="#createTaskModal">Create New Task</button>
                        <button class="btn btn-light btn-sm me-2" data-bs-toggle="modal" data-bs-target="#bulkPackagingModal">Bulk Packaging</button>
                        <button class="btn btn-light btn-sm me-2" data-bs-toggle="modal" data-bs-target="#reportsModal">Reports</button>
                        <button class="btn btn-light btn-sm me-2" data-bs-toggle="modal" data-bs-target="#materialsModal">Materials</button>
                        <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#inventoryModal">Inventory</button>
                    </div>

                </div>
                <div class="card-body px-4 pb-4">
                    {{-- Filters --}}
                    <form method="GET" action="{{ route('admin.packaging.index') }}" class="row g-3 mb-3">
                        <div class="col-md-3">
                            <select name="status" id="status" class="form-select">
                                <option value="">-- All Statuses --</option>
                                @foreach($statuses as $value => $label)
                                    <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-5">
                            <input type="text" class="form-control" name="search" placeholder="Search by name, SKU, or notes" value="{{ request('search') }}">
                        </div>
                        <div class="col-md-4 d-flex">
                            <button type="submit" class="btn btn-primary me-2">Apply Filters</button>
                            <a href="{{ route('admin.packaging.index') }}" class="btn btn-secondary">Clear</a>
                        </div>
                    </form>

                    {{-- Tasks Table --}}
                    <div class="table-responsive shadow-sm">
                        <table class="table table-striped table-hover table-borderless align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Tracking ID</th>
                                    <th>Item</th>
                                    <th>PO Number</th>
                                    <th>Quantity</th>
                                    <th>Status</th>
                                    <th>Priority</th>
                                    <th>Assigned To</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($packagingTasks as $task)
                                    <tr>
                                        <td>{{ $task->id }}</td>
                                        <td>{{ $task->tracking_id }}</td>
                                        <td>
                                            {{ $task->item->name ?? 'N/A' }}<br>
                                            <small class="text-muted">SKU: {{ $task->estimate_item_sku }}</small>
                                        </td>
                                        <td>{{ $task->item->order->purchase_order_number ?? 'N/A' }}</td>
                                        <td>{{ $task->quantity }}</td>
                                        <td>
                                            <span class="badge {{ $task->status === 'completed' ? 'bg-success' : ($task->status === 'in_progress' ? 'bg-primary' : ($task->status === 'on_hold' ? 'bg-warning text-dark' : 'bg-secondary')) }}">
                                                {{ ucfirst($task->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge {{ $task->priority === 'urgent' ? 'bg-danger' : ($task->priority === 'high' ? 'bg-warning text-dark' : ($task->priority === 'medium' ? 'bg-info text-dark' : 'bg-secondary')) }}">
                                                {{ ucfirst($task->priority) }}
                                            </span>
                                        </td>
                                        <td>{{ $task->assignedTo->name ?? 'Not Assigned' }}</td>
                                        <td>{{ $task->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('admin.packaging.show', $task->id) }}" class="btn btn-outline-info"><i class="fas fa-eye"></i></a>
                                                <a href="{{ route('admin.packaging.label', $task->id) }}" class="btn btn-outline-success"><i class="fas fa-tag"></i></a>
                                                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#assignModal-{{ $task->id }}"><i class="fas fa-user-plus"></i></button>
                                                <button type="button" class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#statusModal-{{ $task->id }}"><i class="fas fa-edit"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center">No packaging tasks found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-4">
                        {{ $packagingTasks->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modals outside table --}}
@foreach($packagingTasks as $task)
    @include('admin.packaging.partials.assign', ['task' => $task])
    @include('admin.packaging.partials.status', ['task' => $task])
@endforeach

{{-- Global modals only once --}}
@include('admin.packaging.partials.create_task')
@include('admin.packaging.partials.bulk_packaging')
@include('admin.packaging.partials.reports_modal')
@include('admin.packaging.partials.materials_modal')
@include('admin.packaging.partials.inventory_modal')
@endsection

@push('scripts')
    @include('admin.packaging.partials.scripts')
@endpush
