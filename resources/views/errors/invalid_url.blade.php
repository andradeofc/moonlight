@extends('layouts.app')

@section('title', 'Invalid URL')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h4 class="mb-0">Error</h4>
                </div>
                <div class="card-body">
                    <p>The URL you are trying to access is invalid or has expired.</p>
                    <p>Please check the URL and try again, or contact support if you believe this is an error.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection