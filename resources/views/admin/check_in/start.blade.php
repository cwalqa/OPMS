@extends('admin.app')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-primary text-white rounded-top-4">
            <h5 class="mb-0"><i class="fas fa-warehouse me-2"></i> Start Warehouse Check-In</h5>
        </div>
        <div class="card-body">
            @if($estimates->isEmpty())
                <div class="alert alert-warning">No approved estimates with pending items.</div>
            @else
                <form>
                    <div class="mb-3">
                        <label for="estimateSelect" class="form-label fw-semibold">Select Estimate</label>
                        <select id="estimateSelect" class="form-select" required onchange="redirectToCheckIn(this)">
                            <option value="">Choose...</option>
                            @foreach($estimates as $estimate)
                                @php
                                    $pendingCount = $estimate->items->where('check_in_status', '!=', 'checked_in')->count();
                                @endphp
                                <option value="{{ route('admin.check_in.show', $estimate->id) }}" {{ $pendingCount === 0 ? 'disabled' : '' }}>
                                    {{ $estimate->purchase_order_number }} – {{ $estimate->customer_name }}
                                    @if($pendingCount === 0)
                                        (All Checked In)
                                    @else
                                        ({{ $pendingCount }} pending)
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>

<script>
function redirectToCheckIn(select) {
    const url = select.value;
    if (url) window.location.href = url;
}
</script>
@endsection
