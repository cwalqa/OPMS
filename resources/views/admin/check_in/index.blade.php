@extends('admin.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow rounded-4">
                <div class="card-header bg-gradient-primary text-white rounded-top-4 p-4 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-tags me-2"></i> Warehouse Item Check-In</h5>

                    {{-- Updated New Check-In button--}}
                    <a href="{{ route('admin.check_in.start') }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-plus me-1"></i> New Check-In
                    </a>
                </div>

                <div class="card-body p-4">
                    {{-- Filter --}}
                    <form method="GET" class="d-flex flex-wrap gap-2 mb-3">
                        <select name="printed" class="form-select form-select-sm w-auto">
                            <option value="">Printed</option>
                            <option value="1" {{ request('printed') === '1' ? 'selected' : '' }}>Yes</option>
                            <option value="0" {{ request('printed') === '0' ? 'selected' : '' }}>No</option>
                        </select>
                        <select name="packed" class="form-select form-select-sm w-auto">
                            <option value="">Packed</option>
                            <option value="1" {{ request('packed') === '1' ? 'selected' : '' }}>Yes</option>
                            <option value="0" {{ request('packed') === '0' ? 'selected' : '' }}>No</option>
                        </select>
                        <select name="check_in_status" class="form-select form-select-sm w-auto">
                            <option value="">Check-In Status</option>
                            <option value="checked_in" {{ request('check_in_status') === 'checked_in' ? 'selected' : '' }}>Checked In</option>
                            <option value="pending" {{ request('check_in_status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        </select>
                        <button class="btn btn-sm btn-light" type="submit">
                            <i class="fas fa-filter me-1"></i> Filter
                        </button>
                    </form>

                    <form method="POST" action="{{ route('admin.check_in.toggle_status') }}">
                        @csrf
                        <input type="hidden" name="value" value="1">
                        <div class="mb-3 d-flex gap-2">
                            <button type="submit" name="field" value="is_printed" class="btn btn-sm btn-success">
                                <i class="fas fa-print me-1"></i> Mark Printed
                            </button>
                            <button type="submit" name="field" value="is_packed" class="btn btn-sm btn-info">
                                <i class="fas fa-box-open me-1"></i> Mark Packed
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle table-sm mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th><input type="checkbox" onclick="toggleAll(this)"></th>
                                        <th>QR Tag</th>
                                        <th>Product</th>
                                        <th>SKU</th>
                                        <th>Lot/Shelf</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($items as $item)
                                        <tr>
                                            <td><input type="checkbox" name="ids[]" value="{{ $item->id }}"></td>
                                            <td>{{ $item->tag }}</td>
                                            <td>{{ $item->estimateItem->product_name }}</td>
                                            <td>{{ $item->estimateItem->sku }}</td>
                                            <td>{{ $item->lot }} / {{ $item->shelf }}</td>
                                            <td>
                                                <span class="badge {{ $item->is_printed ? 'bg-success' : 'bg-secondary' }}">
                                                    {{ $item->is_printed ? 'Printed' : 'Not Printed' }}
                                                </span>
                                                <span class="badge {{ $item->is_packed ? 'bg-info' : 'bg-secondary' }}">
                                                    {{ $item->is_packed ? 'Packed' : 'Not Packed' }}
                                                </span>
                                                <span class="badge {{ $item->estimateItem->check_in_status === 'checked_in' ? 'bg-primary' : 'bg-warning text-dark' }}">
                                                    {{ ucfirst($item->estimateItem->check_in_status ?? 'pending') }}
                                                </span>
                                            </td>
                                            <td>
                                                {{-- Updated Action Buttons with consistent onclick handlers --}}
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-primary"
                                                        onclick="loadModalContent('previewModal', '{{ route('admin.check_in.preview_modal', $item->estimate_id) }}')">
                                                    <i class="fas fa-eye me-1"></i> Preview
                                                </button>
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-dark"
                                                        onclick="loadModalContent('printModal', '{{ route('admin.check_in.print_labels_modal', $item->estimate_id) }}')">
                                                    <i class="fas fa-print me-1"></i> Print
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="7" class="text-center text-muted">No items found.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center mt-4">
                            {{ $items->appends(request()->query())->links() }}
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modals with explicit IDs --}}
<div class="modal fade" id="checkInModal" tabindex="-1" aria-labelledby="checkInModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            {{-- Content will be loaded via AJAX --}}
        </div>
    </div>
</div>

<div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            {{-- Content will be loaded via AJAX --}}
        </div>
    </div>
</div>

<div class="modal fade" id="printModal" tabindex="-1" aria-labelledby="printModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            {{-- Content will be loaded via AJAX --}}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // Toggle all checkboxes
    function toggleAll(source) {
        document.querySelectorAll('input[name="ids[]"]').forEach(cb => cb.checked = source.checked);
    }
    
    // Generic function to load modal content with proper error handling
    function loadModalContent(modalId, url) {
        const modalElement = document.getElementById(modalId);
        if (!modalElement) {
            console.error(`Modal with id ${modalId} not found`);
            return;
        }
        
        const modalContent = modalElement.querySelector('.modal-content');
        
        // Set loading state
        modalContent.innerHTML = '<div class="modal-body text-center py-5"><div class="spinner-border text-primary"></div></div>';
        
        // Initialize Bootstrap modal if not already done
        const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
        modal.show();
        
        // Fetch content
        fetch(url)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Network response failed: ${response.status}`);
                }
                return response.text();
            })
            .then(html => {
                modalContent.innerHTML = html;
            })
            .catch(error => {
                console.error('Error loading modal content:', error);
                modalContent.innerHTML = `
                    <div class="modal-body text-danger p-4">
                        <div class="alert alert-danger">
                            <h5><i class="fas fa-exclamation-triangle me-2"></i>Error</h5>
                            <p>Failed to load content. Please try again or contact support.</p>
                        </div>
                        <div class="text-center mt-3">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                `;
            });
    }
    
    
</script>
@endpush