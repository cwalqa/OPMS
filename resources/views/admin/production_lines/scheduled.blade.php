@extends('admin.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3 d-flex justify-content-between">
                        <h6 class="text-white text-capitalize ps-3">Scheduled Production Orders</h6>
                        <!-- Button to trigger Add Schedule Modal -->
                        <button class="btn btn-light btn-sm me-3" data-bs-toggle="modal" data-bs-target="#addScheduleModal">
                            Add New Schedule
                        </button>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="table-responsive p-3">
                        <input type="text" id="scheduleSearchInput" placeholder="Search..." class="form-control mb-3">
                        <table class="table align-items-center mb-0" id="scheduleTable">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-sm font-weight-bolder opacity-10 ps-2">Order Number</th>
                                    <th class="text-uppercase text-secondary text-sm font-weight-bolder opacity-10 ps-2">Item Name</th>
                                    <th class="text-uppercase text-secondary text-sm font-weight-bolder opacity-10 ps-2">Quantity</th>
                                    <th class="text-uppercase text-secondary text-sm font-weight-bolder opacity-10 ps-2">Production Line</th>
                                    <th class="text-uppercase text-secondary text-sm font-weight-bolder opacity-10 ps-2">Schedule Date</th>
                                    <th class="text-uppercase text-secondary text-sm font-weight-bolder opacity-10 ps-2">Deadline Date</th>
                                    <th class="text-uppercase text-secondary text-sm font-weight-bolder opacity-10 ps-2">Schedule Status</th>
                                    <th class="text-uppercase text-secondary text-sm font-weight-bolder opacity-10 ps-2">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($scheduledOrders as $schedule)
                                <tr>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">{{ $schedule->item->order->purchase_order_number }}</p>
                                    </td>
                                    <td>
                                        @php
                                            $product = \App\Models\QuickbooksItem::where('sku', $schedule->item->sku)->first();
                                            $productName = $product ? $product->name : 'Unknown Product';
                                        @endphp
                                        <p class="text-xs font-weight-bold mb-0">{{ $productName }}</p>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">{{ number_format($schedule->quantity) }}</p>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">{{ $schedule->line->line_name }}</p>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">{{ $schedule->schedule_date }}</p>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">{{ $schedule->deadline_date }}</p>
                                    </td>
                                    <td>
                                        <span class="badge {{ $schedule->schedule_status == 'in production' ? 'bg-warning' : 'bg-success' }}">
                                            {{ ucfirst($schedule->schedule_status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <!-- Edit Button -->
                                        <button class="btn btn-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#editScheduleModal{{ $schedule->id }}">
                                            Edit
                                        </button>
                                        <!-- Delete Button -->
                                        <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteScheduleModal{{ $schedule->id }}">
                                            Delete
                                        </button>
                                    </td>
                                </tr>

                                <!-- Edit Schedule Modal -->
                                <div class="modal fade" id="editScheduleModal{{ $schedule->id }}" tabindex="-1" aria-labelledby="editScheduleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="editScheduleModalLabel">Edit Production Schedule</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <form action="{{ route('admin.editSchedule', $schedule->id) }}" method="POST">
                                                    @csrf
                                                    @if($errors->any())
                                                        <div class="alert alert-danger">
                                                            <ul>
                                                                @foreach($errors->all() as $error)
                                                                    <li>{{ $error }}</li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                    @endif
                                                    <div class="form-group mb-3">
                                                        <label for="schedule_date">Schedule Date</label>
                                                        <input type="date" class="form-control" id="schedule_date" name="schedule_date" value="{{ $schedule->schedule_date }}" required>
                                                    </div>
                                                    <div class="form-group mb-3">
                                                        <label for="deadline_date">Deadline Date</label>
                                                        <input type="date" class="form-control" id="deadline_date" name="deadline_date" value="{{ $schedule->deadline_date }}" required>
                                                    </div>
                                                    <div class="form-group mb-3">
                                                        <label for="schedule_status">Schedule Status</label>
                                                        <select class="form-select" id="schedule_status" name="schedule_status" required>
                                                            <option value="scheduled" {{ $schedule->schedule_status == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                                            <option value="in production" {{ $schedule->schedule_status == 'in production' ? 'selected' : '' }}>In Production</option>
                                                        </select>
                                                    </div>
                                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Delete Schedule Modal -->
                                <div class="modal fade" id="deleteScheduleModal{{ $schedule->id }}" tabindex="-1" aria-labelledby="deleteScheduleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="deleteScheduleModalLabel">Delete Production Schedule</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                Are you sure you want to delete the schedule for order: <b>{{ $schedule->item->order->purchase_order_number }}</b>?
                                            </div>
                                            <div class="modal-footer">
                                                <form action="{{ route('admin.deleteSchedule', $schedule->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">Delete</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-3">
                        {{ $scheduledOrders->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add New Production Schedule Modal -->
<div class="modal fade" id="addScheduleModal" tabindex="-1" aria-labelledby="addScheduleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addScheduleModalLabel">Add New Production Schedule</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addScheduleForm" action="{{ route('admin.addSchedule') }}" method="POST">
                    @csrf

                    <!-- Select Item from Order -->
                    <div class="form-group mb-3">
                        <label for="item_id">Select Item</label>
                        <select class="form-control @error('item_id') is-invalid @enderror" id="item_id" name="item_id" required>
                            <option value="">Select an item</option>
                            @foreach($orders as $order)
                                @foreach($order->items as $item)
                                    @php
                                        $product = \App\Models\QuickbooksItem::where('sku', $item->sku)->first();
                                        $productName = $product ? $product->name : 'Unknown Product';
                                    @endphp
                                    <option value="{{ $item->id }}" data-quantity="{{ $item->quantity }}">
                                        {{ $order->purchase_order_number }} - {{ $productName }} - {{ $item->sku }}
                                    </option>
                                @endforeach
                            @endforeach
                        </select>
                        @error('item_id')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <!-- Quantity Field -->
                    <div class="form-group mb-3">
                        <label for="quantity">Quantity</label>
                        <input type="number" class="form-control @error('quantity') is-invalid @enderror" id="quantity" name="quantity" readonly required>
                        @error('quantity')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <!-- Select Production Line -->
                    <div class="form-group mb-3">
                        <label for="line_id">Select Production Line</label>
                        <select class="form-control @error('line_id') is-invalid @enderror" id="line_id" name="line_id" required>
                            <option value="">Select a production line</option>
                            @foreach($productionLines as $line)
                                <option value="{{ $line->id }}">{{ $line->line_name }}</option>
                            @endforeach
                        </select>
                        @error('line_id')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <!-- Schedule Date -->
                    <div class="form-group mb-3">
                        <label for="schedule_date">Schedule Date</label>
                        <input type="date" class="form-control @error('schedule_date') is-invalid @enderror" id="schedule_date" name="schedule_date" required>
                        @error('schedule_date')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <!-- Deadline Date -->
                    <div class="form-group mb-3">
                        <label for="deadline_date">Deadline Date</label>
                        <input type="date" class="form-control @error('deadline_date') is-invalid @enderror" id="deadline_date" name="deadline_date" required>
                        @error('deadline_date')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">Add Schedule</button>
                </form>
            </div>
        </div>
    </div>
</div>




@endsection

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if(session('success'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: "{{ session('success') }}",
            showConfirmButton: false,
            timer: 2000
        });
    });
</script>
@endif

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Search functionality for schedules
        const searchInput = document.getElementById('scheduleSearchInput');
        const table = document.getElementById('scheduleTable');
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

        // Disable past dates
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('schedule_date').setAttribute('min', today);
        document.getElementById('deadline_date').setAttribute('min', today);

        // Update quantity field on item selection
        const itemDropdown = document.getElementById('item_id');
        const quantityInput = document.getElementById('quantity');

        itemDropdown.addEventListener('change', function () {
            const selectedItem = itemDropdown.options[itemDropdown.selectedIndex];
            const itemQuantity = selectedItem.getAttribute('data-quantity');
            quantityInput.value = parseInt(itemQuantity);
        });

        // Ensure deadline date is after schedule date
        const scheduleDateInput = document.getElementById('schedule_date');
        const deadlineDateInput = document.getElementById('deadline_date');

        scheduleDateInput.addEventListener('change', function () {
            const scheduleDate = scheduleDateInput.value;
            deadlineDateInput.setAttribute('min', scheduleDate);
        });
    });
</script>
