@extends('admin.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">Report Defect</h6>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="container">

                        <!-- Option 1: Scan QR Code -->
                        <h6>Option 1: Scan QR Code</h6>
                        <button class="btn btn-primary mb-3" id="startScannerButton">
                            Scan QR Code
                        </button>
                        <div id="qrScannerContainer" class="mt-4" style="display: none;">
                            <div id="qrVideoContainer" style="width: 100%;"></div>
                            <button class="btn btn-danger mt-3" id="stopScannerButton">Stop Scanner</button>
                        </div>

                        <hr class="my-4">

                        <!-- Defect Report Form -->
                        <h6>Defect Report Form</h6>
                        <form id="defectForm" method="POST" action="{{ route('admin.reportDefect') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="orderNumberInput">Purchase Order Number:</label>
                                <input type="text" name="purchase_order_number" id="orderNumberInput" class="form-control" readonly required>
                            </div>
                            <div class="mb-3">
                                <label for="itemIdInput">Product ID:</label>
                                <input type="text" name="item_id" id="itemIdInput" class="form-control" readonly required>
                            </div>
                            <div class="mb-3">
                                <label for="descriptionInput">Defect Description:</label>
                                <input type="text" name="description" id="descriptionInput" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="quantityInput">Affected Quantity:</label>
                                <input type="number" name="quantity" id="quantityInput" class="form-control" min="1" required>
                            </div>
                            <div class="mb-3">
                                <label for="defectTypeSelect">Defect Type:</label>
                                <select name="defect_type" id="defectTypeSelect" class="form-control" required>
                                    <option value="">Select a defect type</option>
                                    <option value="minor">Minor</option>
                                    <option value="major">Major</option>
                                    <option value="critical">Critical</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="severity">Severity</label>
                                <select name="severity" id="severity" class="form-control" required>
                                    <option value="">Select a defect severity</option>
                                    <option value="low">Low</option>
                                    <option value="medium">Medium</option>
                                    <option value="high">High</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary" id="reportDefectButton" disabled>Report Defect</button>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://unpkg.com/html5-qrcode/minified/html5-qrcode.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const startScannerButton = document.getElementById('startScannerButton');
        const stopScannerButton = document.getElementById('stopScannerButton');
        const qrScannerContainer = document.getElementById('qrScannerContainer');
        const orderNumberInput = document.getElementById('orderNumberInput');
        const itemIdInput = document.getElementById('itemIdInput');
        const descriptionInput = document.getElementById('descriptionInput');
        const quantityInput = document.getElementById('quantityInput');
        const defectTypeSelect = document.getElementById('defectTypeSelect');
        const reportDefectButton = document.getElementById('reportDefectButton');
        let html5QrcodeScanner;

        // Function to check if all required fields are filled
        function checkFormFields() {
            if (orderNumberInput.value && itemIdInput.value && descriptionInput.value && quantityInput.value && defectTypeSelect.value) {
                reportDefectButton.disabled = false;
            } else {
                reportDefectButton.disabled = true;
            }
        }

        // Add event listeners to monitor changes in input fields
        orderNumberInput.addEventListener('input', checkFormFields);
        itemIdInput.addEventListener('input', checkFormFields);
        descriptionInput.addEventListener('input', checkFormFields);
        quantityInput.addEventListener('input', checkFormFields);
        defectTypeSelect.addEventListener('change', checkFormFields);

        startScannerButton.addEventListener('click', function () {
            qrScannerContainer.style.display = 'block';

            html5QrcodeScanner = new Html5Qrcode("qrVideoContainer");
            html5QrcodeScanner.start(
                { facingMode: "environment" }, // Adjust to "user" for front-facing camera
                {
                    fps: 10,
                    qrbox: { width: 250, height: 250 }
                },
                qrCodeMessage => {
                    try {
                        // Assuming the QR code data is in a format like: {"purchase_order_number": "PO12345", "item_id": "67890"}
                        const data = JSON.parse(qrCodeMessage);
                        orderNumberInput.value = data.purchase_order_number || '';
                        itemIdInput.value = data.item_id || '';
                        checkFormFields(); // Check if fields are filled after scanning
                    } catch (e) {
                        console.error('Invalid QR code data:', qrCodeMessage);
                        alert('Scanned data is not valid.');
                    }

                    html5QrcodeScanner.stop().then(() => {
                        qrScannerContainer.style.display = 'none';
                    }).catch(err => {
                        console.error('Error stopping QR code scanner:', err);
                    });
                },
                errorMessage => {
                    console.warn(`QR Code scanning error: ${errorMessage}`);
                }
            ).catch(err => {
                console.error('Error starting QR code scanner:', err);
                alert('Could not start the QR scanner. Please check your camera permissions.');
            });
        });

        stopScannerButton.addEventListener('click', function () {
            if (html5QrcodeScanner) {
                html5QrcodeScanner.stop().then(() => {
                    qrScannerContainer.style.display = 'none';
                }).catch(err => {
                    console.error('Error stopping QR code scanner:', err);
                });
            }
        });
    });
</script>
@endsection
