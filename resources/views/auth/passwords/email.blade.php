@extends('layouts.login')

@section('content')
    <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
        <div class="bg-gradient-primary shadow-primary border-radius-lg py-3 pe-1">
            <h4 class="text-white font-weight-bolder text-center mt-2 mb-0">Reset Password</h4>
        </div>
    </div>
    <div class="card-body">
        @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('status') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        
        @if ($errors->has('email_error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ $errors->first('email_error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="text-center mb-3">
            <p class="text-muted">Enter your email address and we'll send you a link to reset your password.</p>
        </div>

        <form method="POST" action="{{ route('password.email') }}" novalidate>
            @csrf
            <div class="input-group input-group-outline my-3 @error('email') is-invalid @enderror @if(old('email')) is-filled @endif">
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required autocomplete="email" autofocus>
            </div>
            @error('email')
                <span class="text-danger text-xs">{{ $message }}</span>
            @enderror

            <div class="text-center">
                <button type="submit" class="btn bg-gradient-primary w-100 my-4 mb-2">
                    <span class="btn-inner--text">Send Password Reset Link</span>
                </button>
            </div>

            <div class="text-center">
                <a href="{{ route('login.form') }}" class="text-primary text-gradient text-sm">Return to login</a>
            </div>
        </form>
    </div>
@endsection