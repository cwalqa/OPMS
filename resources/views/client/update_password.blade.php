@extends('client.app')

@section('content')
<div class="container d-flex justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="card shadow-lg p-4" style="max-width: 500px; width: 100%;">
        <div class="card-body">
            <h3 class="card-title text-center mb-3">Update Password</h3>

            <form method="POST" action="{{ route('client.updatePassword') }}">
                @csrf
                
                <div class="mb-3">
                    <label for="current_password" class="form-label">Current Password</label>
                    <input type="password" name="current_password" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="new_password" class="form-label">New Password</label>
                    <input type="password" name="new_password" class="form-control" required minlength="8">
                </div>

                <div class="mb-3">
                    <label for="new_password_confirmation" class="form-label">Confirm New Password</label>
                    <input type="password" name="new_password_confirmation" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-primary w-100">Update Password</button>
            </form>
        </div>
    </div>
</div>

<!-- SweetAlert Scripts -->
@if(session('success') || session('error'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        let successMessage = {!! json_encode(session('success')) !!};
        let errorMessage = {!! json_encode(session('error')) !!};

        if (successMessage) {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: successMessage,
                confirmButtonText: 'Go to Dashboard',
            }).then(() => {
                window.location.href = "{{ route('client.dashboard') }}";
            });
        } else if (errorMessage) {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: errorMessage,
                confirmButtonText: 'Try Again',
            });
        }
    });
</script>
@endif

@endsection
