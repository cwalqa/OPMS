@extends('layouts.login')

@section('title', '500 - Server Error')

@section('content')
<div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
    <div class="bg-gradient-dark shadow-dark border-radius-lg py-3 pe-1">
        <h4 class="text-white font-weight-bolder text-center mt-2 mb-0">SERVER ERROR</h4>
    </div>
</div>

<div class="card-body text-center">
    <h1 class="display-1 text-dark fw-bold">500</h1>
    <p class="mb-4 text-muted">Oops! Something went wrong on our side.</p>
    <a href="{{ url('/') }}" class="btn bg-gradient-primary w-100 my-4">Return to Home</a>
</div>
@endsection
