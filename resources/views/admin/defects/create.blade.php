@extends('admin.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                    <h6 class="text-white text-capitalize ps-3">Report New Defect</h6>
                </div>
                <div class="card-body px-4 pb-2">
                    <form method="POST" action="{{ route('admin.defects.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="production_schedule_id">Production Schedule</label>
                            <select name="production_schedule_id" id="production_schedule_id" class="form-control" required>
                                <option value="">Select Schedule</option>
                                @foreach($productionSchedules as $schedule)
                                    <option value="{{ $schedule->id }}"
                                        data-product-id="{{ $schedule->item->sku }}"
                                        data-product-name="{{ \App\Models\QuickbooksItem::where('sku', $schedule->item->sku)->first()->name ?? '-' }}"
                                        data-quantity="{{ $schedule->quantity }}"
                                        data-defective="{{ $schedule->defective_quantity ?? 0 }}"
                                        data-order-id="{{ $schedule->item->estimate_id }}"
                                        {{ (isset($selectedScheduleId) && $selectedScheduleId == $schedule->id) ? 'selected' : '' }}>
                                        {{ $schedule->item->sku ?? '-' }} - {{ $schedule->item->name ?? 'N/A' }} | Line: {{ $schedule->line->line_name ?? '-' }} | Status: {{ ucfirst($schedule->schedule_status) }} | Qty: {{ $schedule->quantity }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div id="scheduleDetails" style="display: none;" class="mb-3 border p-3 bg-light">
                            <h6 class="mb-2">Auto-Filled Schedule Details:</h6>
                            <div class="mb-2">
                                <label>Order ID:</label>
                                <input type="text" id="order_id_display" class="form-control" readonly>
                            </div>
                            <div class="mb-2">
                                <label>Product ID (SKU):</label>
                                <input type="text" id="product_id_display" class="form-control" readonly>
                            </div>
                            <div class="mb-2">
                                <label>Product Name:</label>
                                <input type="text" id="product_name_display" class="form-control" readonly>
                            </div>
                            <div class="mb-2">
                                <label>Scheduled Quantity:</label>
                                <input type="text" id="quantity_display" class="form-control" readonly>
                            </div>
                            <div class="mb-2">
                                <label>Defective Quantity Recorded:</label>
                                <input type="text" id="defective_display" class="form-control" readonly>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description">Defect Description</label>
                            <input type="text" name="description" id="description" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="quantity">Affected Quantity</label>
                            <input type="number" name="quantity" id="quantity" class="form-control" min="1" required>
                        </div>
                        <div class="mb-3">
                            <label for="defect_type">Defect Type</label>
                            <select name="defect_type" id="defect_type" class="form-control" required>
                                @foreach($defectTypes as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="severity">Severity</label>
                            <select name="severity" id="severity" class="form-control" required>
                                @foreach($severityLevels as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="corrective_action">Corrective Action (optional)</label>
                            <textarea name="corrective_action" id="corrective_action" class="form-control"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Report Defect</button>
                        <button type="button" id="resetFormButton" class="btn btn-secondary ms-2">Reset Form</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const scheduleSelect = document.getElementById('production_schedule_id');
        const scheduleDetails = document.getElementById('scheduleDetails');
        const orderIdDisplay = document.getElementById('order_id_display');
        const productIdDisplay = document.getElementById('product_id_display');
        const productNameDisplay = document.getElementById('product_name_display');
        const quantityDisplay = document.getElementById('quantity_display');
        const defectiveDisplay = document.getElementById('defective_display');
        const quantityInput = document.getElementById('quantity');

        const descriptionInput = document.getElementById('description');

        const resetFormButton = document.getElementById('resetFormButton');

        resetFormButton.addEventListener('click', function () {
            scheduleSelect.value = '';
            orderIdDisplay.value = '';
            productIdDisplay.value = '';
            productNameDisplay.value = '';
            quantityDisplay.value = '';
            defectiveDisplay.value = '';
            quantityInput.value = '';
            descriptionInput.value = '';
            scheduleDetails.style.display = 'none';
            // Optionally reset defect type, severity, corrective action
            document.getElementById('defect_type').selectedIndex = 0;
            document.getElementById('severity').selectedIndex = 0;
            document.getElementById('corrective_action').value = '';
        });

        function updateScheduleDetails() {
            const selectedOption = scheduleSelect.options[scheduleSelect.selectedIndex];
            if (selectedOption.value) {
                const orderId = selectedOption.getAttribute('data-order-id');
                const productId = selectedOption.getAttribute('data-product-id');
                const productName = selectedOption.getAttribute('data-product-name');
                const defective = selectedOption.getAttribute('data-defective') || '';

                orderIdDisplay.value = orderId;
                productIdDisplay.value = productId;
                productNameDisplay.value = productName;
                quantityDisplay.value = selectedOption.getAttribute('data-quantity');
                defectiveDisplay.value = defective;
                scheduleDetails.style.display = 'block';

                // Auto-prefill affected quantity as current defective quantity
                quantityInput.value = defective;

                // Smartly prefill description
                descriptionInput.value = `Defect found on ${productName} (SKU: ${productId}) - Schedule #${selectedOption.value}`;
            } else {
                scheduleDetails.style.display = 'none';
                quantityInput.value = '';
                descriptionInput.value = '';
            }
        }


        scheduleSelect.addEventListener('change', updateScheduleDetails);

        // Auto-trigger change if pre-selected
        if (scheduleSelect.value) {
            updateScheduleDetails();
        }
    });
</script>
@endsection
