@extends('layouts.login')

@section('title', '429 - Too Many Requests')

@section('content')
<div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
    <div class="bg-gradient-info shadow-info border-radius-lg py-3 pe-1">
        <h4 class="text-white font-weight-bolder text-center mt-2 mb-0">TOO MANY REQUESTS</h4>
    </div>
</div>

<div class="card-body text-center">
    <h1 class="display-1 text-info fw-bold">429</h1>
    <p class="mb-4 text-muted">You have made too many requests in a short time. Please wait and try again.</p>
    <a href="{{ url()->previous() }}" class="btn bg-gradient-primary w-100 my-4">Retry</a>
</div>
@endsection
