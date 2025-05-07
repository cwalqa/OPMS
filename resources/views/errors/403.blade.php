@extends('layouts.login')

@section('title', '403 - Access Denied')

@section('content')
<div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
    <div class="bg-gradient-warning shadow-warning border-radius-lg py-3 pe-1">
        <h4 class="text-white font-weight-bolder text-center mt-2 mb-0">ACCESS DENIED</h4>
    </div>
</div>

<div class="card-body text-center">
    <h1 class="display-1 text-warning fw-bold">403</h1>
    <p class="mb-4 text-muted">You do not have permission to view this page.</p>
    <a href="{{ url('/') }}" class="btn bg-gradient-primary w-100 my-4">Return to Home</a>
</div>
@endsection
