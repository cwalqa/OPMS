@extends('admin.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card my-4">
        <div class="card-header bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
            <h6 class="text-white text-capitalize">Defect Reports</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.defects.reports') }}" class="row mb-4">
                <div class="col-md-4">
                    <label>Start Date</label>
                    <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                </div>
                <div class="col-md-4">
                    <label>End Date</label>
                    <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">Filter</button>
                </div>
            </form>

            <div class="row">
                <div class="col-md-6">
                    <h6>Defects by Type</h6>
                    <canvas id="defectsByTypeChart"></canvas>
                </div>
                <div class="col-md-6">
                    <h6>Defects by Severity</h6>
                    <canvas id="defectsBySeverityChart"></canvas>
                </div>
            </div>

            <div class="mt-5">
                <h6>Top Defective Products</h6>
                <table class="table table-striped">
                    <thead><tr><th>SKU</th><th>Name</th><th>Total Defects</th></tr></thead>
                    <tbody>
                        @foreach($topDefectiveProducts as $product)
                            <tr>
                                <td>{{ $product->estimate_item_sku }}</td>
                                <td>{{ $product->estimateItem->name ?? '-' }}</td>
                                <td>{{ $product->total_defects }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-5">
                <h6>Monthly Defect Trends (Last 6 Months)</h6>
                <canvas id="monthlyTrendChart"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const defectsByTypeChart = new Chart(document.getElementById('defectsByTypeChart'), {
        type: 'pie',
        data: {
            labels: @json($defectsByType->pluck('defect_type')),
            datasets: [{ data: @json($defectsByType->pluck('count')) }]
        }
    });

    const defectsBySeverityChart = new Chart(document.getElementById('defectsBySeverityChart'), {
        type: 'pie',
        data: {
            labels: @json($defectsBySeverity->pluck('severity')),
            datasets: [{ data: @json($defectsBySeverity->pluck('count')) }]
        }
    });

    const monthlyTrendChart = new Chart(document.getElementById('monthlyTrendChart'), {
        type: 'line',
        data: {
            labels: @json($monthlyTrend->map(fn($d) => $d->year . '-' . str_pad($d->month, 2, '0', STR_PAD_LEFT))),
            datasets: [{
                label: 'Defects',
                data: @json($monthlyTrend->pluck('count')),
                fill: true,
                tension: 0.3
            }]
        }
    });
</script>
@endsection
