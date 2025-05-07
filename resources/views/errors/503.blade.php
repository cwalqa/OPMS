@extends('layouts.login')

@section('title', '503 - Service Unavailable')

@section('content')
<div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
    <div class="bg-gradient-light shadow-light border-radius-lg py-3 pe-1">
        <h4 class="text-dark font-weight-bolder text-center mt-2 mb-0">SERVICE UNAVAILABLE</h4>
    </div>
</div>

<div class="card-body text-center">
    <h1 class="display-1 text-muted fw-bold">503</h1>
    <p class="mb-4 text-muted">We’re currently down for maintenance. Please check back later.</p>
    <a href="{{ url('/') }}" class="btn bg-gradient-primary w-100 my-4">Try Again</a>
</div>
@endsection
