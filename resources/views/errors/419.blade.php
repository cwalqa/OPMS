@extends('layouts.login')

@section('title', '419 - Session Expired')

@section('content')
<div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
    <div class="bg-gradient-secondary shadow-secondary border-radius-lg py-3 pe-1">
        <h4 class="text-white font-weight-bolder text-center mt-2 mb-0">SESSION EXPIRED</h4>
    </div>
</div>

<div class="card-body text-center">
    <h1 class="display-1 text-secondary fw-bold">419</h1>
    <p class="mb-4 text-muted">Your session has expired. Please refresh and try again.</p>
    <a href="{{ url()->previous() }}" class="btn bg-gradient-primary w-100 my-4">Go Back</a>
</div>
@endsection
