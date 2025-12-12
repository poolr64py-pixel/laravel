@extends('user.layout')

@section('content')
<div class="page-header">
    <h4 class="page-title">{{ __('Cookie Alert') }}</h4>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">{{ __('Cookie Alert Settings') }}</h4>
            </div>
            <div class="card-body">
                <p>{{ __('Cookie alert settings will be available soon.') }}</p>
                <a href="{{ route('user-dashboard') }}" class="btn btn-primary">{{ __('Back to Dashboard') }}</a>
            </div>
        </div>
    </div>
</div>
@endsection
