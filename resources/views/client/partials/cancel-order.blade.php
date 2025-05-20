<div class="modal fade" id="cancelOrderModal" tabindex="-1" aria-labelledby="cancelOrderModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered modal-lg modal-fullscreen-sm-down">

            <div class="modal-content shadow-lg border-0 rounded-3">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="cancelOrderModalLabel">
                        <i class="fas fa-exclamation-triangle me-2"></i> 
                        {{ strtolower($order->status ?? '') == 'approved' ? 'Request Cancellation' : 'Cancel Order Confirmation' }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="cancelOrderForm" action="{{ route('client.cancelOrder', $order->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="cancelReason" class="form-label">Reason for Cancellation</label>
                            <textarea name="cancel_reason" id="cancelReason" class="form-control" rows="4" placeholder="Briefly explain why you wish to cancel this order..." required></textarea>
                        </div>
                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times-circle me-1"></i> No, Keep My Order
                            </button>
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash-alt me-1"></i> 
                                {{ strtolower($order->status ?? '') == 'approved' ? 'Request Cancellation' : 'Yes, Cancel Order' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>