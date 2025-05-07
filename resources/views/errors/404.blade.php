@extends('layouts.login')

@section('title', '404 - Page Not Found')

@section('content')
    <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
        <div class="bg-gradient-danger shadow-danger border-radius-lg py-3 pe-1">
            <h4 class="text-white font-weight-bolder text-center mt-2 mb-0">PAGE NOT FOUND</h4>
        </div>
    </div>

    <div class="card-body text-center">
        <h1 class="display-1 text-danger fw-bold">404</h1>
        <p class="mb-4 text-muted">Oops! The page you're looking for doesn't exist.</p>
        <a href="{{ url('/') }}" class="btn bg-gradient-primary w-100 my-4">Return to Home</a>
        <p class="text-sm text-muted">If you believe this is an error, contact support.</p>
    </div>
@endsection

@section('scripts')
    <!-- Optional: SweetAlert or similar toast can be shown -->
    <script>
        // Example optional alert
        // Swal.fire({ icon: 'error', title: '404 - Page Not Found', text: 'You’ve hit a dead end.' });
    </script>
@endsection
