@extends('admin.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">Schedule New Delivery</h6>
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

                        <!-- Delivery Form -->
                        <h6>Delivery Form</h6>
                        <form id="deliveryForm" method="POST" action="{{ route('admin.deliveries.store') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="clientNameInput">Client Name:</label>
                                <input type="text" name="client_name" id="clientNameInput" class="form-control" readonly required>
                            </div>
                            <div class="mb-3">
                                <label for="clientIdInput">Client ID:</label>
                                <input type="text" name="client_id" id="clientIdInput" class="form-control" readonly required>
                            </div>
                            <div class="mb-3">
                                <label for="orderNumberInput">Purchase Order ID:</label>
                                <input type="text" name="purchase_order_id" id="orderNumberInput" class="form-control" readonly required>
                            </div>
                            <div class="mb-3">
                                <label for="productNameInput">Product Name:</label>
                                <input type="text" name="product_name" id="productNameInput" class="form-control" readonly required>
                            </div>
                            <div class="mb-3">
                                <label for="productIdInput">Product ID:</label>
                                <input type="text" name="product_id" id="productIdInput" class="form-control" readonly required>
                            </div>
                            <div class="mb-3">
                                <label for="quantityInput">Quantity:</label>
                                <input type="number" name="quantity" id="quantityInput" class="form-control" min="1" required>
                            </div>
                            <div class="mb-3">
                                <label for="statusInput">Status:</label>
                                <input type="text" name="status" id="statusInput" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="deliveryDateInput">Delivery Date:</label>
                                <input type="date" name="delivery_date" id="deliveryDateInput" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="assignedDispatchSelect">Assigned Dispatch:</label>
                                <select name="assigned_dispatch" id="assignedDispatchSelect" class="form-control">
                                    <option value="">Select a dispatch</option>
                                    @foreach($dispatchers as $dispatcher)
                                        <option value="{{ $dispatcher->id }}">{{ $dispatcher->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="deliveryNoteTextarea">Delivery Note:</label>
                                <textarea name="delivery_note" id="deliveryNoteTextarea" class="form-control" rows="3" placeholder="Enter any notes regarding the delivery"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary" id="scheduleDeliveryButton" disabled>Schedule Delivery</button>
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
        const clientNameInput = document.getElementById('clientNameInput');
        const clientIdInput = document.getElementById('clientIdInput');
        const orderNumberInput = document.getElementById('orderNumberInput');
        const productNameInput = document.getElementById('productNameInput');
        const productIdInput = document.getElementById('productIdInput');
        const quantityInput = document.getElementById('quantityInput');
        const statusInput = document.getElementById('statusInput');
        const deliveryDateInput = document.getElementById('deliveryDateInput');
        const assignedDispatchSelect = document.getElementById('assignedDispatchSelect');
        const scheduleDeliveryButton = document.getElementById('scheduleDeliveryButton');
        let html5QrcodeScanner;

        // Function to check if all required fields are filled
        function checkFormFields() {
            if (clientNameInput.value && clientIdInput.value && orderNumberInput.value && productNameInput.value && productIdInput.value && quantityInput.value && statusInput.value && deliveryDateInput.value) {
                scheduleDeliveryButton.disabled = false;
            } else {
                scheduleDeliveryButton.disabled = true;
            }
        }

        // Add event listeners to monitor changes in input fields
        clientNameInput.addEventListener('input', checkFormFields);
        clientIdInput.addEventListener('input', checkFormFields);
        orderNumberInput.addEventListener('input', checkFormFields);
        productNameInput.addEventListener('input', checkFormFields);
        productIdInput.addEventListener('input', checkFormFields);
        quantityInput.addEventListener('input', checkFormFields);
        statusInput.addEventListener('input', checkFormFields);
        deliveryDateInput.addEventListener('input', checkFormFields);
        assignedDispatchSelect.addEventListener('change', checkFormFields);

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
                        // Assuming the QR code contains data in this format
                        const data = JSON.parse(qrCodeMessage);
                        clientNameInput.value = data.client_name || '';
                        clientIdInput.value = data.client_id || '';
                        orderNumberInput.value = data.purchase_order_id || '';
                        productNameInput.value = data.product_name || '';
                        productIdInput.value = data.product_id || '';
                        quantityInput.value = data.quantity || '';
                        document.getElementById('deliveryNoteTextarea').value = data.additional_notes || '';

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
