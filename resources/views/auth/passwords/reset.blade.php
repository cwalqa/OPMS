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

        <div class="text-center mb-3">
            <p class="text-muted">Please create a new secure password for your account</p>
        </div>

        <form method="POST" action="{{ route('password.update') }}" novalidate>
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            
            <div class="input-group input-group-outline my-3 @error('email') is-invalid @enderror @if(old('email') || $email) is-filled @endif">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') ?? $email }}" required autocomplete="email" readonly>
            </div>
            @error('email')
                <span class="text-danger text-xs">{{ $message }}</span>
            @enderror

            <div class="input-group input-group-outline my-3 @error('password') is-invalid @enderror">
                <label class="form-label">New Password</label>
                <input type="password" name="password" class="form-control" required autocomplete="new-password">
            </div>
            @error('password')
                <span class="text-danger text-xs">{{ $message }}</span>
            @enderror

            <div class="input-group input-group-outline my-3">
                <label class="form-label">Confirm Password</label>
                <input type="password" name="password_confirmation" class="form-control" required autocomplete="new-password">
            </div>

            <div class="password-requirements mt-3 mb-2">
                <p class="text-xs text-muted mb-1">Password must meet the following requirements:</p>
                <ul class="text-xs text-muted ps-3">
                    <li>At least 8 characters in length</li>
                    <li>At least one uppercase letter</li>
                    <li>At least one lowercase letter</li>
                    <li>At least one number</li>
                    <li>At least one special character (e.g. !@#$%^&*)</li>
                </ul>
            </div>

            <div class="text-center">
                <button type="submit" class="btn bg-gradient-primary w-100 my-4 mb-2">Reset Password</button>
            </div>

            <div class="text-center">
                <a href="{{ route('login.form') }}" class="text-primary text-gradient text-sm">Return to login</a>
            </div>
        </form>
    </div>
@endsection