@extends('admin.app')

@section('title', 'Transfer Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Transfer Details #{{ $transfer->id }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('inventory.transfers') }}" class="btn btn-default btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Transfers
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Item Information</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 30%">Item Name</th>
                                    <td>{{ $transfer->item->name }}</td>
                                </tr>
                                <tr>
                                    <th>SKU</th>
                                    <td>{{ $transfer->item->sku }}</td>
                                </tr>
                                <tr>
                                    <th>Quantity Transferred</th>
                                    <td>{{ $transfer->quantity }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>Transfer Information</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 30%">Date & Time</th>
                                    <td>{{ $transfer->created_at->format('M d, Y H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <th>Executed By</th>
                                    <td>{{ $transfer->user->name }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <h5>Source Information</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 30%">Warehouse</th>
                                    <td>{{ $transfer->sourceWarehouse->name }}</td>
                                </tr>
                                <tr>
                                    <th>Shelf/Lot</th>
                                    <td>{{ $transfer->source_lot_shelf ?: 'Default' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>Destination Information</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 30%">Warehouse</th>
                                    <td>{{ $transfer->destinationWarehouse->name }}</td>
                                </tr>
                                <tr>
                                    <th>Shelf/Lot</th>
                                    <td>{{ $transfer->destination_lot_shelf ?: 'Default (A)' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    @if($transfer->notes)
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>Notes</h5>
                            <div class="card bg-light">
                                <div class="card-body">
                                    {{ $transfer->notes }}
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection