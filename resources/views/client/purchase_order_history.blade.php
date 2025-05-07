@extends('client.app')

@section('content')

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3 d-flex justify-content-between">
                        <h6 class="text-white text-capitalize ps-3">PURCHASE ORDER HISTORY</h6>
                        <!-- Search Input -->
                        <input type="text" id="purchaseOrderSearchInput" placeholder="Search..." class="form-control w-25 me-3">
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="table-responsive p-3">
                        <table class="table align-items-center mb-0" id="purchaseOrderTable">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-primary text-sm font-weight-bolder opacity-10 ps-2">S/N</th>
                                    <th class="text-uppercase text-primary text-sm font-weight-bolder opacity-10 ps-2">Order Date</th>
                                    <th class="text-uppercase text-primary text-sm font-weight-bolder opacity-10 ps-2">Purchase Order Number</th>
                                    <th class="text-uppercase text-primary text-sm font-weight-bolder opacity-10 ps-2">Total Amount</th>
                                    <th class="text-uppercase text-primary text-sm font-weight-bolder opacity-10 ps-2">Additional Notes</th>
                                    <th class="text-uppercase text-primary text-sm font-weight-bolder opacity-10">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($purchaseOrders->where('status', '!=', 'canceled') as $order)
                                <tr>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">{{ $loop->iteration }}</p>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">{{ $order->created_at->format('Y-m-d') }}</p>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">{{ $order->purchase_order_number }}</p>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">${{ $order->total_amount }}</p>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">{{ $order->customer_memo }}</p>
                                    </td>
                                    <td>
                                        <a href="{{ route('client.viewOrderDetails', $order->id) }}" class="btn btn-secondary btn-sm">
                                            Order Details
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('purchaseOrderSearchInput');
        const table = document.getElementById('purchaseOrderTable');
        const rows = table.getElementsByTagName('tr');

        searchInput.addEventListener('keyup', function () {
            const filter = searchInput.value.toLowerCase();
            for (let i = 1; i < rows.length; i++) { // Skip header row
                const cells = rows[i].getElementsByTagName('td');
                let found = false;
                for (let j = 0; j < cells.length; j++) {
                    if (cells[j].innerText.toLowerCase().includes(filter)) {
                        found = true;
                        break;
                    }
                }
                rows[i].style.display = found ? '' : 'none';
            }
        });
    });
</script>
