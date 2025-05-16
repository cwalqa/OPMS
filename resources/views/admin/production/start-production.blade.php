@extends('admin.app')


@push('styles')
    @include('admin.production.partials.style')
@endpush


@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center rounded-top">
                    <h6 class="mb-0 ps-2">Production Management</h6>
                    <div class="dropdown">
                        <button class="btn btn-light btn-sm dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            Filter by Status
                        </button>
                        <ul class="dropdown-menu shadow-sm" aria-labelledby="filterDropdown">
                            <li><a class="dropdown-item filter-status" href="#" data-status="all">All</a></li>
                            <li><a class="dropdown-item filter-status" href="#" data-status="scheduled">Scheduled</a></li>
                            <li><a class="dropdown-item filter-status" href="#" data-status="in production">In Production</a></li>
                            <li><a class="dropdown-item filter-status" href="#" data-status="paused">Paused</a></li>
                            <li><a class="dropdown-item filter-status" href="#" data-status="completed">Completed</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body px-4 pb-4">
                    <input type="text" id="productionSearchInput" placeholder="Search schedules..." class="form-control mb-3 rounded-pill shadow-sm">

                    <div class="table-responsive shadow-sm">
                        <table class="table table-striped table-hover table-borderless align-middle" id="productionItemsTable">
                            <thead class="table-dark">
                                <tr>
                                    <th>Order No.</th>
                                    <th>Product</th>
                                    <th>Line</th>
                                    <th>Qty</th>
                                    <th>Schedule Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($scheduledItems as $schedule)
                                    @php
                                        $product = \App\Models\QuickbooksItem::where('sku', $schedule->item->sku)->first();
                                        $productName = $product ? $product->name : 'Unknown Product';
                                        $badgeMap = [
                                            'scheduled' => ['bg-warning text-dark', 'fa-calendar'],
                                            'in production' => ['bg-success text-white', 'fa-cogs'],
                                            'paused' => ['bg-danger text-white', 'fa-pause'],
                                            'completed' => ['bg-primary text-white', 'fa-check'],
                                        ];
                                        [$statusClass, $iconClass] = $badgeMap[$schedule->schedule_status] ?? ['bg-secondary', 'fa-question'];
                                    @endphp
                                    <tr class="production-row" data-status="{{ $schedule->schedule_status }}">
                                        <td>{{ $schedule->item->order->purchase_order_number }}</td>
                                        <td>{{ $productName }}</td>
                                        <td>{{ $schedule->line->line_name }}</td>
                                        <td>{{ $schedule->quantity - ($schedule->defective_quantity ?? 0) }} / {{ $schedule->quantity }}</td>
                                        <td>{{ $schedule->schedule_date }}</td>
                                        <td>
                                            <span class="badge {{ $statusClass }}">
                                                <i class="fas {{ $iconClass }} me-1"></i>{{ ucfirst($schedule->schedule_status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                @if($schedule->schedule_status == 'scheduled')
                                                    <button class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#startModal{{ $schedule->id }}">
                                                        <i class="fas fa-play"></i>
                                                    </button>
                                                @elseif($schedule->schedule_status == 'in production')
                                                    <button class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#pauseModal{{ $schedule->id }}">
                                                        <i class="fas fa-pause"></i>
                                                    </button>
                                                    <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#completeModal{{ $schedule->id }}">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                @elseif($schedule->schedule_status == 'paused')
                                                    <button class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#resumeModal{{ $schedule->id }}">
                                                        <i class="fas fa-play"></i>
                                                    </button>
                                                @elseif($schedule->schedule_status == 'completed')
                                                    <button class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#viewDetailsModal{{ $schedule->id }}">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-4">
                        {{ $scheduledItems->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modals outside table -->
@foreach($scheduledItems as $schedule)
    @php
        $product = \App\Models\QuickbooksItem::where('sku', $schedule->item->sku)->first();
        $productName = $product ? $product->name : 'Unknown Product';
        $statusClass = match($schedule->schedule_status) {
            'scheduled' => 'bg-warning text-dark',
            'in production' => 'bg-success text-white',
            'paused' => 'bg-danger text-white',
            'completed' => 'bg-primary text-white',
            default => 'bg-secondary',
        };
    @endphp

    @include('admin.production.partials.start', ['schedule' => $schedule, 'productName' => $productName])
    @include('admin.production.partials.pause', ['schedule' => $schedule, 'productName' => $productName])
    @include('admin.production.partials.resume', ['schedule' => $schedule, 'productName' => $productName])
    @include('admin.production.partials.complete', ['schedule' => $schedule, 'productName' => $productName])
    @include('admin.production.partials.viewDetails', ['schedule' => $schedule, 'productName' => $productName, 'statusClass' => $statusClass])
@endforeach
@endsection

@push('scripts')
    @include('admin.production.partials.scripts')
@endpush

