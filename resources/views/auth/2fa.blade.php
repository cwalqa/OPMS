@extends('layouts.login')

@section('content')

    <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
        <div class="bg-gradient-primary shadow-primary border-radius-lg py-3 pe-1">
            <h4 class="text-white font-weight-bolder text-center mt-2 mb-0">Client Authentication</h4>
        </div>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success text-center">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger text-center">{{ $errors->first() }}</div>
        @endif

        <form role="form" class="text-start" method="POST" action="{{ route('customer.verify2fa') }}">
        @csrf
            <div class="input-group input-group-outline my-3 mt-4">
                <input type="text" name="two_factor_code" placeholder="Enter Authentication Code" class="form-control" required>
            </div>
            <div class="text-center">
                <button type="submit" class="btn bg-gradient-primary w-100 my-4 mb-2">Verify Code</button>
            </div>
        </form>

        <!-- Resend 2FA Code Button -->
        <form role="form" class="text-center" method="POST" action="{{ route('customer.resend2fa') }}">
        @csrf
            <button type="submit" class="btn btn-outline-primary w-100">Resend 2FA Code</button>
        </form>
    </div>

@endsection

@push('scripts')
    <!-- SweetAlert2 Script -->
    @if(session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: '2FA Verification Successful',
                text: '{{ session('success') }}',
            });
        </script>
    @elseif(session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: '2FA Verification Failed',
                text: '{{ session('error') }}',
            });
        </script>
    @endif
@endpush

    @if (session('logout_message'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Logout Successful',
                text: '{{ session('logout_message') }}'
            });
        </script>
    @endif