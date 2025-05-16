<style>
    /* General card improvements */
    .card {
        border: none;
        border-radius: 0.75rem;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
    }

    .card-header {
        border-bottom: none;
        border-radius: 0.75rem 0.75rem 0 0;
    }

    /* Clean tables */
    .table {
        border-collapse: separate;
        border-spacing: 0;
    }

    .table thead {
        background-color: #343a40;
        color: #fff;
    }

    .table tbody tr:hover {
        background-color: #f8f9fa;
        transition: background-color 0.2s ease-in-out;
    }

    .table td, .table th {
        vertical-align: middle;
        padding: 0.75rem;
    }

    /* Rounded search input */
    .form-control {
        border-radius: 2rem;
        box-shadow: none;
        border-color: #ced4da;
    }

    .form-control:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    }

    /* Button adjustments */
    .btn {
        border-radius: 0.5rem;
    }

    .btn-group .btn {
        margin-right: 0.3rem;
    }

    /* SweetAlert consistent styling override */
    .swal2-popup {
        border-radius: 1rem;
    }

    /* Modals */
    .modal-content {
        border-radius: 0.75rem;
        border: none;
    }

    /* Highlight row for paused or defective items */
    .production-row[data-status="paused"] {
        background-color: #fff3cd;
    }

    .production-row[data-status="completed"] {
        background-color: #d1e7dd;
    }

    /* Badge improvements */
    .badge {
        padding: 0.4em 0.8em;
        font-size: 0.75rem;
        border-radius: 0.5rem;
    }

    /* Defect alerts */
    .alert-light {
        background-color: #f8f9fa;
        border-color: #f8f9fa;
        color: #333;
    }
</style>
