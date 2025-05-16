<div class="modal fade" id="startModal{{ $schedule->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="{{ route('admin.production.start.process', $schedule->id) }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Start Production</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <p class="mb-1"><strong>Order:</strong> {{ $schedule->item->order->purchase_order_number }}</p>
                        <p class="mb-1"><strong>Product:</strong> {{ $productName }}</p>
                        <p class="mb-1"><strong>Quantity:</strong> {{ $schedule->quantity }}</p>
                        <p class="mb-1"><strong>Line:</strong> {{ $schedule->line->line_name }}</p>
                        <p class="mb-0"><strong>Schedule Date:</strong> {{ $schedule->schedule_date }}</p>
                    </div>
                    <p class="mt-3">Are you ready to begin production for this item?</p>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" value="1" id="confirmStart{{ $schedule->id }}" required>
                        <label class="form-check-label" for="confirmStart{{ $schedule->id }}">
                            I confirm that all materials are ready for production
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-play me-1"></i> Start Production
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>
