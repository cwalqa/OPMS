@extends('admin.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3 d-flex justify-content-between">
                        <h6 class="text-white text-capitalize ps-3">Calendar View - Scheduled Orders</h6>
                    </div>
                </div>
                <div class="card-body px-0 pb-2 d-flex justify-content-center align-items-center">
                    <!-- Calendar container -->
                    <div id="calendar"></div>
                </div>

                <style>
                    #calendar {
                        width: 95%;        /* Adjust width to fit nicely inside card */
                        height: auto;      /* Dynamically adjust height */
                        min-height: 400px; /* Minimum height for small screens */
                        max-height: 80vh;  /* Max height to prevent overflow on very large screens */
                    }

                    .card-body {
                        height: 100%;      /* Ensure card's body fills the height */
                    }

                    .card-body.d-flex {
                        min-height: 500px;  /* Ensure minimum height to make it look good */
                    }
                </style>
            </div>
        </div>
    </div>
</div>
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var calendarEl = document.getElementById('calendar');

        // Define a list of colors for different events
        var colors = ['#3788d8', '#28a745', '#dc3545', '#ffc107', '#17a2b8'];

        // Initialize FullCalendar
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            events: [
                @foreach($scheduledOrders as $schedule)
                    @php
                        $product = \App\Models\QuickbooksItem::where('sku', $schedule->item->sku)->first();
                        $productName = $product ? $product->name : 'Unknown Product';
                    @endphp
                    {
                        title: '{{ $productName }} (Order: {{ $schedule->item->order->purchase_order_number }}, Qty: {{ $schedule->quantity }})',
                        start: '{{ $schedule->schedule_date }}',  // Only show the event on the schedule_date
                        description: 'Production Line: {{ $schedule->line->line_name }}',
                        url: '{{ route('admin.scheduledOrders', ['schedule_id' => $schedule->id]) }}',  // Redirect to the scheduled orders page when clicked
                        backgroundColor: colors[{{ $loop->index % count($scheduledOrders) }}],  // Use color array
                        borderColor: colors[{{ $loop->index % count($scheduledOrders) }}],
                        textColor: '#ffffff'
                    },
                @endforeach
            ],
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            editable: false,
            eventDisplay: 'block',
            eventClick: function(info) {
                info.jsEvent.preventDefault();  // Prevent the default click behavior
                if (info.event.url) {
                    window.location.href = info.event.url;  // Redirect to the schedule details page
                }
            },
            eventMaxStack: 3,  // Limit the number of events displayed in a day
            height: 'auto',    // Ensure the calendar fits properly
            eventMaxHeight: 50,  // Adjust event height to ensure proper fit
            dayMaxEventRows: true,  // Allows for 'more' link when events overflow
            views: {
                dayGridMonth: {
                    dayMaxEventRows: 2,  // Limits rows of events per day
                }
            }
        });

        // Render the calendar
        calendar.render();
    });
</script>
