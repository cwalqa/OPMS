@extends('layouts.login')

@section('content')

    <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
        <div class="bg-gradient-primary shadow-primary border-radius-lg py-3 pe-1">
            <h4 class="text-white font-weight-bolder text-center mt-2 mb-0">CLIENT PORTAL</h4>
        </div>
    </div>
    <div class="card-body">
        <form role="form" class="text-start" method="POST" action="{{ route('login') }}">
        @csrf
            <div class="input-group input-group-outline my-3 mt-4" data-validate="Valid email is required: ex@abc.xyz">
                <input type="email" name="email" placeholder="Email Address" class="form-control" required>
            </div>
            <div class="input-group input-group-outline mb-4 mt-4">
                <input type="password" name="password" placeholder="Password" class="form-control" required>
            </div>
            <div class="form-check form-switch d-flex align-items-center mb-4">
                <input class="form-check-input" type="checkbox" id="rememberMe" checked>
                <label class="form-check-label mb-0 ms-3" for="rememberMe">Remember me</label>
            </div>
            <div class="text-center">
                <button type="submit" class="btn bg-gradient-primary w-100 my-4 mb-2">Sign in</button>
            </div>
            <p class="mt-4 text-sm text-center">
                Forgot Password?
                <a href="{{ route('password.request') }}" class="text-primary text-gradient font-weight-bold">Reset your password</a>
            </p>
        </form>
    </div>

@endsection

@section('scripts')
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.16/dist/sweetalert2.all.min.js"></script>

    <!-- SweetAlert for Success, Error, or Validation Errors -->
    <script>
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Login Successful',
                text: @json(session('success')),
            });
        @elseif(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Login Failed',
                text: @json(session('error')),
            });
        @elseif($errors->has('email'))
            Swal.fire({
                icon: 'error',
                title: 'Authentication Error',
                text: @json($errors->first('email')),
            });
        @endif
    </script>

    @if (session('logout_message'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Logout Successful',
                text: '{{ session('logout_message') }}'
            });
        </script>
    @endif
@endsection
