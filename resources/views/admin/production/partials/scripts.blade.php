<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="{{ asset('production-management-fixes.css') }}">
<script src="{{ asset('production-management-fixes.js') }}"></script>

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
    document.addEventListener('DOMContentLoaded', function() {
        // Search functionality
        const searchInput = document.getElementById('productionSearchInput');
        const table = document.getElementById('productionItemsTable');
        const rows = table.getElementsByTagName('tr');

        searchInput.addEventListener('keyup', function() {
            const filter = searchInput.value.toLowerCase();
            for (let i = 1; i < rows.length; i++) { // Skip header row
                const cells = rows[i].getElementsByTagName('td');
                let found = false;
                
                // Only search in visible rows (respect filter)
                if (rows[i].style.display !== 'none') {
                    for (let j = 0; j < cells.length; j++) {
                        if (cells[j].innerText.toLowerCase().includes(filter)) {
                            found = true;
                            break;
                        }
                    }
                    rows[i].style.display = found ? '' : 'none';
                }
            }
        });

        // Filter by status
        const filterLinks = document.querySelectorAll('.filter-status');
        filterLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const status = this.getAttribute('data-status');
                const productionRows = document.querySelectorAll('.production-row');
                
                productionRows.forEach(row => {
                    if (status === 'all' || row.getAttribute('data-status') === status) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
                
                // Update dropdown button text
                document.getElementById('filterDropdown').textContent = 'Filter: ' + (status === 'all' ? 'All' : status.charAt(0).toUpperCase() + status.slice(1));
            });
        });

        // Update good quantity calculation in real-time
        @foreach($scheduledItems as $schedule)
        const defectInput{{ $schedule->id }} = document.getElementById('finalDefectiveQuantity{{ $schedule->id }}');
        if (defectInput{{ $schedule->id }}) {
            defectInput{{ $schedule->id }}.addEventListener('input', function() {
                const defectValue = parseInt(this.value) || 0;
                const totalQty = {{ $schedule->quantity }};
                const goodQty = totalQty - defectValue;
                
                document.getElementById('defectDisplay{{ $schedule->id }}').textContent = defectValue;
                document.getElementById('goodQty{{ $schedule->id }}').textContent = goodQty;
            });
        }
        @endforeach
    });

    // Toggle custom reason field for pause modal
    function toggleCustomReason(scheduleId) {
        const selectEl = document.getElementById('pauseReasonSelect' + scheduleId);
        const textareaEl = document.getElementById('pauseReason' + scheduleId);
        
        if (selectEl.value === 'custom') {
            textareaEl.value = '';
            textareaEl.placeholder = 'Please specify the reason';
        } else {
            textareaEl.value = selectEl.value;
        }
    }

    // Placeholder functions for export and print
    function printProductionReport(scheduleId) {
        Swal.fire({
            title: 'Printing Report',
            text: 'Preparing production report for printing...',
            icon: 'info',
            showConfirmButton: false,
            timer: 1500
        });
        // Actual print functionality would be implemented here
    }

    function exportProductionData(scheduleId) {
        Swal.fire({
            title: 'Exporting Data',
            text: 'Preparing production data for export...',
            icon: 'info',
            showConfirmButton: false,
            timer: 1500
        });
        // Actual export functionality would be implemented here
    }
</script>