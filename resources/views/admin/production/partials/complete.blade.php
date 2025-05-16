<div class="modal fade" id="completeModal{{ $schedule->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="{{ route('admin.production.complete', $schedule->id) }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Complete Production</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info mb-3">
                        <p><strong>Order:</strong> {{ $schedule->item->order->purchase_order_number }}</p>
                        <p><strong>Product:</strong> {{ $productName }}</p>
                        <p><strong>Planned Quantity:</strong> {{ $schedule->quantity }}</p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Final Defective Quantity</label>
                        <input type="number" id="finalDefectiveQuantity{{ $schedule->id }}"
                               data-total="{{ $schedule->quantity }}"
                               name="defective_quantity" class="form-control"
                               min="0" max="{{ $schedule->quantity }}"
                               value="{{ $schedule->defective_quantity ?? 0 }}">
                    </div>

                    <div class="alert alert-success">
                        <div class="row text-center">
                            <div class="col-4 border-end">
                                <small>Total</small>
                                <h5>{{ $schedule->quantity }}</h5>
                            </div>
                            <div class="col-4 border-end">
                                <small>Defective</small>
                                <h5 id="defectDisplay{{ $schedule->id }}">{{ $schedule->defective_quantity ?? 0 }}</h5>
                            </div>
                            <div class="col-4">
                                <small>Good</small>
                                <h5 id="goodQty{{ $schedule->id }}">{{ $schedule->quantity - ($schedule->defective_quantity ?? 0) }}</h5>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Completion Notes</label>
                        <textarea class="form-control" name="completion_notes" rows="3" placeholder="Add any important notes about the production run..."></textarea>
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" value="1" id="confirmCompletion{{ $schedule->id }}" required>
                        <label class="form-check-label" for="confirmCompletion{{ $schedule->id }}">
                            I confirm that production is complete and quality checks have been performed
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-check me-1"></i> Complete Production
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>
