@extends('client.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card border-0 shadow rounded-4">
                <div class="card-header bg-gradient-primary text-white rounded-top-4 p-4">
                    <h5 class="mb-0">
                        🔒 Update Your Password
                    </h5>
                </div>

                <div class="card-body p-4">
                    <form method="POST" action="{{ route('client.updatePassword') }}">
                        @csrf

                        <!-- Current Password -->
                        <div class="mb-3">
                            <label for="current_password" class="form-label fw-semibold">Current Password</label>
                            <div class="input-group">
                                <input type="password" name="current_password" id="current_password" class="form-control" required autocomplete="current-password">
                                <button type="button" class="btn btn-outline-secondary toggle-password" data-target="#current_password">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="eye-icon" viewBox="0 0 16 16" width="16" height="16">
                                        <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM8 12c-2.21 0-4-1.79-4-4s1.79-4 4-4
                                            4 1.79 4 4-1.79 4-4 4zm0-1.5a2.5 2.5 0 100-5 2.5 2.5 0 000 5z"/>
                                    </svg>
                                </button>
                            </div>
                            @error('current_password')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- New Password -->
                        <div class="mb-3">
                            <label for="new_password" class="form-label fw-semibold">New Password</label>
                            <div class="input-group">
                                <input type="password" name="new_password" id="new_password" class="form-control" required minlength="8" autocomplete="new-password">
                                <button type="button" class="btn btn-outline-secondary toggle-password" data-target="#new_password">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="eye-icon" viewBox="0 0 16 16" width="16" height="16">
                                        <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM8 12c-2.21 0-4-1.79-4-4s1.79-4 4-4
                                            4 1.79 4 4-1.79 4-4 4zm0-1.5a2.5 2.5 0 100-5 2.5 2.5 0 000 5z"/>
                                    </svg>
                                </button>
                            </div>
                            @error('new_password')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div class="mb-4">
                            <label for="new_password_confirmation" class="form-label fw-semibold">Confirm New Password</label>
                            <div class="input-group">
                                <input type="password" name="new_password_confirmation" id="new_password_confirmation" class="form-control" required autocomplete="new-password">
                                <button type="button" class="btn btn-outline-secondary toggle-password" data-target="#new_password_confirmation">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="eye-icon" viewBox="0 0 16 16" width="16" height="16">
                                        <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM8 12c-2.21 0-4-1.79-4-4s1.79-4 4-4
                                            4 1.79 4 4-1.79 4-4 4zm0-1.5a2.5 2.5 0 100-5 2.5 2.5 0 000 5z"/>
                                    </svg>
                                </button>
                            </div>
                            @error('new_password_confirmation')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Action Buttons -->
                        <div class="row mt-4">
                            <div class="col-md-6 text-start">
                                <a href="{{ route('client.dashboard') }}" class="btn btn-danger">
                                    Return to Dashboard
                                </a>
                            </div>
                            <div class="col-md-6 text-end">
                                <button type="submit" class="btn btn-primary">
                                    Update Password
                                </button>
                            </div>
                        </div>

                        <p class="text-center text-muted mt-3">
                            By updating your password, you agree to our <a href="#" class="text-decoration-underline">Terms of Service</a> and <a href="#" class="text-decoration-underline">Privacy Policy</a>.
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<!-- Show/Hide Password Toggle -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.toggle-password').forEach(button => {
            button.addEventListener('click', function () {
                const input = document.querySelector(this.dataset.target);
                const icon = this.querySelector('svg');

                if (input.type === 'password') {
                    input.type = 'text';
                    icon.innerHTML = `<path d="M13.359 11.238l2.122 2.122-1.061 1.06-2.122-2.121a8.483 8.483 0 01-4.298 1.201C3 13.5 0 8 0 8s.951-1.74 2.545-3.237l-1.3-1.3 1.06-1.061 13 13-1.06 1.06-2.124-2.122a10.402 10.402 0 01-3.122.36C3 13.5 0 8 0 8s3-5.5 8-5.5c1.346 0 2.629.299 3.828.832l-1.57 1.57A5.978 5.978 0 008 3.5c-2.21 0-4 1.79-4 4 0 .754.214 1.457.586 2.061l1.474-1.474a2.5 2.5 0 013.474 3.474l1.474-1.474A5.984 5.984 0 008 11.5c-1.346 0-2.629-.299-3.828-.832l-1.57 1.57A10.403 10.403 0 008 13.5c5 0 8-5.5 8-5.5s-.91 1.82-2.641 3.762z"/>`;
                } else {
                    input.type = 'password';
                    icon.innerHTML = `<path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM8 12c-2.21 0-4-1.79-4-4s1.79-4 4-4
                        4 1.79 4 4-1.79 4-4 4zm0-1.5a2.5 2.5 0 100-5 2.5 2.5 0 000 5z"/>`;
                }
            });
        });
    });
</script>
@endpush

<!-- SweetAlert Feedback -->
@if(session('success') || session('error'))
@push('scripts')
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
@endpush
@endif
@endsection
