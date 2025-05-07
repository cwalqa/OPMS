/**
 * Production Management UI Enhancement
 * 
 * This script fixes UI rendering issues in the production management interface
 * by properly initializing Bootstrap components and fixing layout problems.
 */

document.addEventListener('DOMContentLoaded', function() {
    // Fix table layout and responsiveness
    const productionTable = document.getElementById('productionItemsTable');
    if (productionTable) {
        // Ensure proper table styling
        productionTable.classList.add('table-striped', 'table-hover');
        
        // Fix any collapsed rows
        const rows = productionTable.querySelectorAll('tr');
        rows.forEach(row => {
            row.style.display = '';
        });
    }
    
    // Fix filter dropdown positioning and functionality
    const filterDropdown = document.getElementById('filterDropdown');
    if (filterDropdown) {
        // Ensure dropdown is properly initialized with Bootstrap
        new bootstrap.Dropdown(filterDropdown);
        
        // Add animation for smoother transitions
        filterDropdown.addEventListener('click', function() {
            this.classList.toggle('active');
        });
    }
    
    // Enhance action buttons with proper spacing and hover effects
    const actionButtons = document.querySelectorAll('.btn-sm');
    actionButtons.forEach(button => {
        button.classList.add('mx-1', 'shadow-sm');
        button.style.minWidth = '80px';
    });
    
    // Fix status badges to ensure consistent styling
    const statusBadges = document.querySelectorAll('.badge');
    statusBadges.forEach(badge => {
        badge.style.padding = '0.35em 0.65em';
        badge.style.fontWeight = '500';
    });
    
    // Ensure modals are properly initialized
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        new bootstrap.Modal(modal);
    });
    
    // Fix modal content to ensure proper display
    const modalContents = document.querySelectorAll('.modal-content');
    modalContents.forEach(content => {
        content.style.borderRadius = '0.5rem';
        content.style.overflow = 'hidden';
    });
    
    // Fix search input functionality
    const searchInput = document.getElementById('productionSearchInput');
    if (searchInput) {
        searchInput.style.maxWidth = '400px';
        searchInput.style.margin = '0 auto 1rem auto';
        
        // Add clear button to search input
        const searchWrapper = document.createElement('div');
        searchWrapper.classList.add('position-relative');
        searchInput.parentNode.insertBefore(searchWrapper, searchInput);
        searchWrapper.appendChild(searchInput);
        
        const clearButton = document.createElement('button');
        clearButton.innerHTML = '&times;';
        clearButton.classList.add('btn', 'btn-sm', 'position-absolute', 'end-0', 'top-0', 'me-2', 'mt-1');
        clearButton.style.display = 'none';
        searchWrapper.appendChild(clearButton);
        
        searchInput.addEventListener('input', function() {
            clearButton.style.display = this.value ? 'block' : 'none';
        });
        
        clearButton.addEventListener('click', function() {
            searchInput.value = '';
            searchInput.dispatchEvent(new Event('keyup'));
            this.style.display = 'none';
        });
    }
    
    // Fix quantity counters in modals
    document.querySelectorAll('[id^="finalDefectiveQuantity"]').forEach(input => {
        input.addEventListener('input', function() {
            const scheduleId = this.id.replace('finalDefectiveQuantity', '');
            const defectValue = parseInt(this.value) || 0;
            const totalQty = parseInt(this.getAttribute('data-total')) || 0;
            const goodQty = totalQty - defectValue;
            
            const defectDisplay = document.getElementById('defectDisplay' + scheduleId);
            const goodQtyDisplay = document.getElementById('goodQty' + scheduleId);
            
            if (defectDisplay) defectDisplay.textContent = defectValue;
            if (goodQtyDisplay) goodQtyDisplay.textContent = goodQty;
        });
    });
    
    // Fix reason selector in pause modals
    document.querySelectorAll('[id^="pauseReasonSelect"]').forEach(select => {
        select.addEventListener('change', function() {
            const scheduleId = this.id.replace('pauseReasonSelect', '');
            const textareaEl = document.getElementById('pauseReason' + scheduleId);
            
            if (this.value === 'custom') {
                textareaEl.value = '';
                textareaEl.placeholder = 'Please specify the reason';
                textareaEl.focus();
            } else {
                textareaEl.value = this.value;
            }
        });
    });
    
    // Add print and export functionality
    function setupReportButtons() {
        document.querySelectorAll('button[onclick^="printProductionReport"]').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const scheduleId = this.getAttribute('onclick').match(/\d+/)[0];
                printProductionReport(scheduleId);
            });
        });
        
        document.querySelectorAll('button[onclick^="exportProductionData"]').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const scheduleId = this.getAttribute('onclick').match(/\d+/)[0];
                exportProductionData(scheduleId);
            });
        });
    }
    
    setupReportButtons();
    
    // Enhanced print functionality
    window.printProductionReport = function(scheduleId) {
        // Create print-friendly version
        const modalContent = document.querySelector(`#viewDetailsModal${scheduleId} .modal-body`).cloneNode(true);
        const printWindow = window.open('', '_blank', 'width=800,height=600');
        
        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>Production Report #${scheduleId}</title>
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
                <style>
                    body { padding: 20px; }
                    .print-header { text-align: center; margin-bottom: 20px; }
                    @media print {
                        .no-print { display: none; }
                    }
                </style>
            </head>
            <body>
                <div class="print-header">
                    <h3>Production Report #${scheduleId}</h3>
                    <p>Generated on ${new Date().toLocaleString()}</p>
                </div>
                <div id="content"></div>
                <div class="text-center mt-4 no-print">
                    <button class="btn btn-primary" onclick="window.print()">Print Now</button>
                </div>
            </body>
            </html>
        `);
        
        printWindow.document.getElementById('content').appendChild(modalContent);
        printWindow.document.close();
    };
    
    // Enhanced export functionality
    window.exportProductionData = function(scheduleId) {
        // Show loading indicator
        Swal.fire({
            title: 'Preparing Export',
            text: 'Generating CSV file...',
            icon: 'info',
            showConfirmButton: false,
            timer: 1500
        });
        
        // Simulate data collection (in real app, this would be an AJAX call)
        setTimeout(() => {
            const modalEl = document.querySelector(`#viewDetailsModal${scheduleId}`);
            if (!modalEl) return;
            
            // Extract data from modal
            const orderNum = modalEl.querySelector('.card-body p:nth-child(1)').innerText.split(': ')[1];
            const product = modalEl.querySelector('.card-body p:nth-child(2)').innerText.split(': ')[1];
            const quantity = modalEl.querySelector('.card-body p:nth-child(3)').innerText.split(': ')[1];
            const goodQty = modalEl.querySelector('.card-body p:nth-child(4)').innerText.split(': ')[1];
            const defectQty = modalEl.querySelector('.card-body p:nth-child(5)').innerText.split(': ')[1];
            
            // Create CSV content
            const csvContent = [
                'Data Type,Value',
                `Order Number,${orderNum}`,
                `Product,${product}`,
                `Scheduled Quantity,${quantity}`,
                `Good Quantity,${goodQty}`,
                `Defective Quantity,${defectQty}`
            ].join('\n');
            
            // Create download link
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.setAttribute('href', url);
            link.setAttribute('download', `production_report_${scheduleId}.csv`);
            link.style.visibility = 'hidden';
            
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            
            Swal.fire({
                title: 'Export Complete',
                text: 'Your data has been exported successfully!',
                icon: 'success',
                showConfirmButton: false,
                timer: 1500
            });
        }, 1000);
    };
    
    // Fix the overall layout
    function fixLayoutIssues() {
        // Fix table container
        const tableContainer = document.querySelector('.table-responsive');
        if (tableContainer) {
            tableContainer.style.overflow = 'visible';
            tableContainer.style.overflowX = 'auto';
        }
        
        // Fix the modal dialog sizes
        document.querySelectorAll('.modal-dialog').forEach(dialog => {
            dialog.style.maxWidth = '95%';
            dialog.style.margin = '1.75rem auto';
        });
        
        // Fix button groups for better mobile display
        document.querySelectorAll('.btn-group').forEach(group => {
            group.classList.add('d-flex', 'flex-wrap');
        });
    }
    
    fixLayoutIssues();
    
    // Add keyboard shortcuts for better usability
    document.addEventListener('keydown', function(e) {
        // Alt+S for search focus
        if (e.altKey && e.key === 's') {
            e.preventDefault();
            if (searchInput) searchInput.focus();
        }
        
        // Esc to close modals
        if (e.key === 'Escape') {
            const openModals = document.querySelectorAll('.modal.show');
            openModals.forEach(modal => {
                const bsModal = bootstrap.Modal.getInstance(modal);
                if (bsModal) bsModal.hide();
            });
        }
    });
    
    // Show success notification for the fix
    setTimeout(() => {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'UI Enhancement Applied',
                text: 'The production management interface has been optimized for better user experience.',
                icon: 'success',
                showConfirmButton: false,
                timer: 2000
            });
        }
    }, 1000);
});