@extends('admin.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">Start Production Process</h6>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="container">

                        <!-- QR Code Scanner -->
                        <h6>Scan QR Code Using Camera</h6>
                        <button class="btn btn-success mb-3" id="startScannerButton">Start Scanner</button>
                        <div id="qrScannerContainer" class="mt-4" style="display: none;">
                            <div id="reader" style="width: 100%; max-width: 400px;"></div>
                            <button class="btn btn-danger mt-3" id="stopScannerButton">Stop Scanner</button>
                        </div>

                        <hr class="my-4">

                        <!-- Upload QR Code Image -->
                        <h6>Upload QR Code Image</h6>
                        <form id="qrUploadForm" method="POST" action="{{ route('admin.production.uploadQrImage') }}" enctype="multipart/form-data">
                            @csrf
                            <input type="file" name="qr_image" id="qrImageInput" accept="image/*" class="form-control mb-3" required>
                            <button type="submit" class="btn btn-primary">Scan QR</button>
                        </form>

                        <hr class="my-4">

                        <!-- Production Form -->
                        <h6>Production Form</h6>
                        <form id="productionForm" method="POST" action="{{ route('admin.production.start') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="clientName">Client Name:</label>
                                <input type="text" name="client_name" id="clientName" class="form-control" value="{{ session('client_name', old('client_name', '')) }}" readonly>
                            </div>
                            <div class="mb-3">
                                <!-- <label for="clientId">Client ID:</label> -->
                                <input type="hidden" name="client_id" id="clientId" class="form-control"value="{{ session('client_id', old('client_id', '')) }}" readonly>
                            </div>
                            <div class="mb-3">
                                <!-- <label for="clientId">Client Ref:</label> -->
                                <input type="hidden" name="client_ref" id="clientRef" class="form-control" value="{{ session('client_ref', old('client_ref', '')) }}" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="purchaseOrderId">Purchase Order ID:</label>
                                <input type="text" name="purchase_order_id" id="purchaseOrderId" class="form-control" value="{{ session('purchase_order_id', '') }}" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="productName">Product Name:</label>
                                <input type="text" name="product_name" id="productName" class="form-control" value="{{ session('product_name', '') }}" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="productId">Product ID:</label>
                                <input type="text" name="product_id" id="productId" class="form-control" value="{{ session('product_id', '') }}" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="quantity">Quantity:</label>
                                <input type="text" name="quantity" id="quantity" class="form-control" value="{{ session('quantity', '') }}" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="additionalNotes">Additional Notes:</label>
                                <textarea name="additional_notes" id="additionalNotes" class="form-control" readonly>{{ session('additional_notes', '') }}</textarea>
                            </div>
                            <div class="mb-3">
                                <label for="productionLineSelect">Production Line:</label>
                                <select name="production_line_id" id="productionLineSelect" class="form-control" required>
                                    <option value="">Select a production line</option>
                                    @foreach($productionLines as $line)
                                        <option value="{{ $line->id }}">{{ $line->line_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <button type="submit" class="btn btn-primary" id="startProductionButton">Start Production</button>
                        </form>
                        <!-- Logs Modal -->
                        <div class="modal fade" id="stageLogsModal" tabindex="-1" aria-labelledby="stageLogsModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header bg-gradient-dark text-white">
                                        <h5 class="modal-title" id="stageLogsModalLabel">Item Stage History</h5>
                                        <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        @php
                                            $logs = session('tracking_logs', collect());
                                        @endphp
                                        @if($logs->isEmpty())
                                            <p class="text-muted">No logs found for this item.</p>
                                        @else
                                            <table class="table table-striped">
                                                <thead class="table-dark">
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Stage</th>
                                                        <th>Comments</th>
                                                        <th>Status</th>
                                                        <th>Date</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($logs as $index => $log)
                                                        <tr>
                                                            <td>{{ $index + 1 }}</td>
                                                            <td>{{ ucfirst(str_replace('_', ' ', $log->stage)) }}</td>
                                                            <td>{{ $log->comments ?? '-' }}</td>
                                                            <td>{{ optional(json_decode($log->meta))->status ?? '-' }}</td>
                                                            <td>{{ $log->created_at->format('Y-m-d H:i') }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://unpkg.com/html5-qrcode"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        console.log("QR Scanner Script Loaded");

        let qrScanner;

        // Select Elements
        const startScannerButton = document.getElementById('startScannerButton');
        const stopScannerButton = document.getElementById('stopScannerButton');
        const startProductionButton = document.getElementById('startProductionButton');
        const productionLineSelect = document.getElementById('productionLineSelect');
        const productionForm = document.getElementById('productionForm');

        if (!startScannerButton || !stopScannerButton) {
            console.error("Start or Stop Scanner Button NOT found! Check Blade file.");
            return;
        }

        // Prevent Form Submission if Production Line is Not Selected
        productionForm.addEventListener('submit', function (event) {
            if (!productionLineSelect.value) {
                event.preventDefault();
                alert("Please select a production line before starting production.");
                return false;
            }
        });

        startScannerButton.addEventListener('click', function () {
            console.log("Start Scanner button clicked");
            document.getElementById('qrScannerContainer').style.display = 'block';

            if (!qrScanner) {
                console.log("Initializing QR Scanner...");
                qrScanner = new Html5Qrcode("reader");

                qrScanner.start(
                    { facingMode: "environment" }, 
                    { fps: 10, qrbox: { width: 250, height: 250 } },
                    function (decodedText) {
                        console.log("QR Code Scanned:", decodedText);
                        try {
                            const data = JSON.parse(decodedText);
                            document.getElementById('clientName').value = data.client_name || '';
                            document.getElementById('clientId').value = data.client_id || '';
                            document.getElementById('clientRef').value = data.client_ref || '';
                            document.getElementById('purchaseOrderId').value = data.purchase_order_id || '';
                            document.getElementById('productName').value = data.product_name || '';
                            document.getElementById('productId').value = data.product_id || '';
                            document.getElementById('quantity').value = data.quantity || '';
                            document.getElementById('additionalNotes').value = data.additional_notes || '';
                            stopScanner();
                        } catch (e) {
                            console.error("Invalid QR Code:", decodedText);
                        }
                    },
                    function (errorMessage) {
                        console.warn("QR Scan Error:", errorMessage);
                    }
                ).catch(err => {
                    console.error("Scanner Initialization Error:", err);
                });
            }
        });

        function stopScanner() {
            if (qrScanner) {
                qrScanner.stop().then(() => {
                    console.log("Scanner Stopped");
                    document.getElementById('qrScannerContainer').style.display = 'none';
                }).catch(err => console.error("Error Stopping Scanner:", err));
            }
        }

        stopScannerButton.addEventListener('click', stopScanner);
    });
</script>

<script>
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: '{{ session('success') }}'
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Oops!',
            text: '{{ session('error') }}'
        });
    @endif
</script>

@if(session('show_logs_modal'))
<script>
    window.addEventListener('load', () => {
        const logsModal = new bootstrap.Modal(document.getElementById('stageLogsModal'));
        logsModal.show();
        document.getElementById('viewLogsButton')?.classList.remove('d-none');
    });
</script>
@endif

@endsection
