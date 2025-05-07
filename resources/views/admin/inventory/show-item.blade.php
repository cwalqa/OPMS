@extends('admin.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-3 d-flex justify-content-between">
                    <h6 class="text-dark text-capitalize">{{ $item->name }}</h6>
                    <a href="{{ route('inventory.items.edit', $item->id) }}" class="btn btn-light btn-sm">
                        <i class="material-icons">edit</i> Edit Item
                    </a>
                </div>
                <div class="card-body px-4 pb-4">
                    
                    <!-- Stock & Financial Summary -->
                    <div class="row">
                        <!-- Stock Card -->
                        <div class="col-lg-3 col-md-6">
                            <div class="card text-center">
                                <div class="card-header bg-gradient-dark text-white">
                                    <i class="material-icons">inventory</i> Stock
                                </div>
                                <div class="card-body">
                                    <h3 class="mb-0">{{ $item->stock }}</h3>
                                    <p class="text-muted">Current Stock</p>
                                </div>
                            </div>
                        </div>

                        <!-- Total Income -->
                        <div class="col-lg-3 col-md-6">
                            <div class="card text-center">
                                <div class="card-header bg-gradient-primary text-white">
                                    <i class="material-icons">trending_up</i> Total Income
                                </div>
                                <div class="card-body">
                                    <h3 class="mb-0">${{ number_format($item->total_sold, 2) }}</h3>
                                    <p class="text-muted">Revenue from Sales</p>
                                </div>
                            </div>
                        </div>

                        <!-- Total Expense -->
                        <div class="col-lg-3 col-md-6">
                            <div class="card text-center">
                                <div class="card-header bg-gradient-danger text-white">
                                    <i class="material-icons">money_off</i> Total Expense
                                </div>
                                <div class="card-body">
                                    <h3 class="mb-0">${{ number_format($item->total_purchased, 2) }}</h3>
                                    <p class="text-muted">Purchase Cost</p>
                                </div>
                            </div>
                        </div>

                        <!-- Quantity Summary -->
                        <div class="col-lg-3 col-md-6">
                            <div class="card text-center">
                                <div class="card-header bg-gradient-warning text-white">
                                    <i class="material-icons">shopping_cart</i> Quantity Stats
                                </div>
                                <div class="card-body">
                                    <h5>Sold: {{ $item->total_sold_qty }}</h5>
                                    <h5>Purchased: {{ $item->total_purchased_qty }}</h5>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Item Details -->
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <p><strong>Sale Price:</strong> ${{ number_format($item->sale_price, 2) }}</p>
                            <p><strong>Purchase Price:</strong> ${{ number_format($item->purchase_price, 2) }}</p>
                            <p><strong>Opening Stock:</strong> {{ $item->stock }}</p>
                            <p><strong>SKU:</strong> {{ $item->sku }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Category:</strong> {{ $item->category->name ?? 'N/A' }}</p>
                            <p><strong>Brand:</strong> {{ $item->brand->name ?? 'N/A' }}</p>
                            <p><strong>Default Warehouse:</strong> {{ $item->defaultWarehouse->name ?? 'N/A' }}</p>
                            <p><strong>Location (Lot/Shelf):</strong> 
                                @if($item->warehouseItems && $item->warehouseItems->count() > 0)
                                    {{ $item->warehouseItems->pluck('lot_shelf')->implode(', ') }}
                                @else
                                    N/A
                                @endif
                            </p>
                        </div>
                    </div>

                    <!-- Warehouse Inventory Section (New) -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5><i class="material-icons">warehouse</i> Warehouse Inventory</h5>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Warehouse</th>
                                            <th>Stock</th>
                                            <th>Lot/Shelf</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if($item->warehouseItems && $item->warehouseItems->count() > 0)
                                            @foreach($item->warehouseItems as $warehouseItem)
                                                <tr>
                                                    <td>{{ $warehouseItem->warehouse->name }}</td>
                                                    <td>{{ $warehouseItem->stock }}</td>
                                                    <td>{{ $warehouseItem->lot_shelf ?? 'N/A' }}</td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="3" class="text-center">No warehouse data available</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Tabs for Overview & History -->
                    <ul class="nav nav-tabs mt-4" id="itemTabs">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#overview">
                                <i class="material-icons">bar_chart</i> Overview
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#history">
                                <i class="material-icons">history</i> History
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#transfers">
                                <i class="material-icons">swap_horiz</i> Transfers
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content mt-3">
                        <!-- Overview Tab -->
                        <div class="tab-pane fade show active" id="overview">
                            <h5>Sales Quantity By Warehouse</h5>
                            <p class="text-muted">Track incoming & outgoing inventory</p>
                            <canvas id="salesChart"></canvas>
                        </div>

                        <!-- History Tab -->
                        <div class="tab-pane fade" id="history">
                            <h5><i class="material-icons">history</i> Transaction History</h5>
                            <p class="text-muted">Logs of stock changes, sales, purchases, and transfers</p>

                            <div class="table-responsive">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Action</th>
                                            <th>Warehouse</th>
                                            <th>Lot/Shelf</th>
                                            <th>Quantity</th>
                                            <th>Amount</th>
                                            <th>Note</th>
                                        </tr>
                                    </thead>
                                    <tbody>
    @if($item->histories && $item->histories->count() > 0)
        @foreach($item->histories as $history)
            <tr>
                <td><strong>{{ $history->created_at->format('M d, Y') }}</strong></td>
                <td>
                    @if($history->action === 'Stock Added')
                        <span class="badge bg-success">{{ $history->action }}</span>
                    @elseif($history->action === 'Stock Reduced')
                        <span class="badge bg-danger">{{ $history->action }}</span>
                    @elseif($history->action === 'Sale')
                        <span class="badge bg-primary">{{ $history->action }}</span>
                    @elseif($history->action === 'Return')
                        <span class="badge bg-warning text-dark">{{ $history->action }}</span>
                    @else
                        <span class="badge bg-secondary">{{ $history->action }}</span>
                    @endif
                </td>
                <td>{{ $history->warehouse->name ?? 'N/A' }}</td> <!-- FIXED -->
                <td>{{ $history->lot_shelf ?? 'N/A' }}</td> <!-- FIXED -->
                <td>{{ number_format($history->quantity) }}</td>
                <td>${{ number_format($history->amount, 2) }}</td>
                <td>{{ $history->note ?? 'N/A' }}</td>
            </tr>
        @endforeach
    @else
        <tr>
            <td colspan="7" class="text-center">No transaction history available.</td>
        </tr>
    @endif
</tbody>

                                </table>
                            </div>
                        </div>

                        <!-- Transfers Tab (New) -->
                        <div class="tab-pane fade" id="transfers">
                            <h5>Warehouse Transfers</h5>
                            <p class="text-muted">Movement of stock between warehouses</p>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>From Warehouse</th>
                                            <th>To Warehouse</th>
                                            <th>Quantity</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- You'll need to implement a transfer model and relationship -->
                                        <tr>
                                            <td colspan="5" class="text-center">No transfer history available.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div> <!-- Card Body -->
            </div> <!-- Card -->
        </div> <!-- Col -->
    </div> <!-- Row -->
</div> <!-- Container -->

@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Get warehouse names and stock data (assuming it's available in a relationship)
        const warehouseNames = [];
        const warehouseStock = [];
        
        @if($item->warehouseItems && $item->warehouseItems->count() > 0)
            @foreach($item->warehouseItems as $warehouseItem)
                warehouseNames.push("{{ $warehouseItem->warehouse->name }}");
                warehouseStock.push({{ $warehouseItem->stock }});
            @endforeach
        @else
            // Default data if no warehouse items
            warehouseNames.push('No Data');
            warehouseStock.push(0);
        @endif
        
        const ctx = document.getElementById('salesChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: warehouseNames,
                datasets: [{
                    label: 'Stock By Warehouse',
                    data: warehouseStock,
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(75, 192, 192, 0.7)',
                        'rgba(255, 206, 86, 0.7)',
                        'rgba(255, 99, 132, 0.7)',
                        'rgba(153, 102, 255, 0.7)'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: true }
                },
                scales: {
                    y: { beginAtZero: true },
                    x: { beginAtZero: true }
                }
            }
        });
    });
</script>
@endsection