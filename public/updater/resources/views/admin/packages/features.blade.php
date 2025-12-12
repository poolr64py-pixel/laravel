@extends('admin.layout')

@section('content')
    <div class="page-header">
        <h4 class="page-title">{{ __('Package Features') }}</h4>

        <ul class="breadcrumbs">
            <li class="nav-home">
                <a href="{{ route('admin.dashboard') }}">
                    <i class="flaticon-home"></i>
                </a>
            </li>

            <li class="separator">
                <i class="flaticon-right-arrow"></i>
            </li>

            <li class="nav-item">
                <a href="#">{{ __('Packages Management') }}</a>
            </li>

            <li class="separator">
                <i class="flaticon-right-arrow"></i>
            </li>

            <li class="nav-item">
                <a href="#">{{ __('Package Features') }}</a>
            </li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title d-inline-block">
                        {{ __('Package Features') }}
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-8 offset-lg-2">
                            <form id="permissionsForm" action="{{ route('admin.package.features') }}" method="post">

                                @csrf

                                <div class="alert alert-warning">
                                    {{ __('Only these selected features will be visible in frontend Pricing Section') }}
                                </div>

                                <div class="form-group">
                                    <label class="form-label">{{ __('Package Features') }}</label>

                                    <div class="selectgroup selectgroup-pills">
                                        {{-- General Features --}}
                                        @php
                                            $generalFeatures = [
                                                'Custom Domain',
                                                'Subdomain',
                                                'Additional Language',
                                                'Agent',
                                                'Property Management',
                                                'Project Management',
                                                'Blog',
                                                'User',
                                                'Advertisement',
                                                'Additional Page',
                                                'Support Ticket',
                                                'Google Analytics',
                                                'Disqus',
                                                'Google Recaptcha',
                                                'Whatsapp',
                                                'Google Login',
                                            ];
                                        @endphp

                                        @foreach ($generalFeatures as $feature)
                                            <label class="selectgroup-item">
                                                <input type="checkbox" name="features[]" value="{{ $feature }}"
                                                    class="selectgroup-input"
                                                    @if (is_array($features) && in_array($feature, $features)) checked @endif>
                                                <span class="selectgroup-button">
                                                    {{ __($feature) }}
                                                </span>
                                            </label>
                                        @endforeach

                                        {{-- AI Features Section --}}
                                        <div class="w-100 my-4"></div>

                                        <div class="w-100">
                                            <div class="alert alert-info mb-3">
                                                <strong>
                                                    <i class="fas fa-magic"></i>
                                                    {{ __('AI Powered Features') }}
                                                </strong>
                                                <p class="mb-2 mt-2 small">
                                                    {{ __('Advanced AI capabilities to enhance content creation and productivity') }}
                                                </p>
                                                <p class="mb-0 small">
                                                    <i class="fas fa-info-circle"></i>
                                                    <strong>{{ __('Note') . ':' }}</strong>
                                                    {{ __('AI features are available only for Property and Project creation') . '.' }}
                                                </p>
                                            </div>
                                        </div>

                                        <label class="selectgroup-item">
                                            <input type="checkbox" name="features[]" value="AI Content Generation"
                                                class="selectgroup-input" @if (is_array($features) && in_array('AI Content Generation', $features)) checked @endif>
                                            <span class="selectgroup-button">
                                                <i class="fas fa-robot text-primary mr-1"></i>
                                                {{ __('AI Content Generation') }}
                                                <span class="badge badge-warning badge-pill ml-2"
                                                    style="font-size: 0.65rem;">
                                                    <i class="fas fa-coins"></i>
                                                    {{ __('Token-based') }}
                                                </span>
                                            </span>
                                        </label>

                                        <label class="selectgroup-item">
                                            <input type="checkbox" name="features[]" value="AI Image Generation"
                                                class="selectgroup-input" @if (is_array($features) && in_array('AI Image Generation', $features)) checked @endif>
                                            <span class="selectgroup-button">
                                                <i class="fas fa-image text-success mr-1"></i>
                                                {{ __('AI Image Generation') }}
                                                <span class="badge badge-success badge-pill ml-2"
                                                    style="font-size: 0.65rem;">
                                                    <i class="fas fa-infinity"></i>
                                                    {{ __('Unlimited Free') }}
                                                </span>
                                            </span>
                                        </label>

                                        {{-- AI Provider Info --}}
                                        <div class="w-100 mt-3"></div>

                                        <small class="text-warning d-block w-100">
                                            <strong class="text-success">{{ __('AI Technology Powered by') . ':' }}</strong><br>
                                            • {{ __('AI Content Generation via Google Gemini') }}<br>
                                            • {{ __('AI Image Generation via Pollinations.ai Flux Model') }}
                                        </small>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <div class="form">
                        <div class="form-group from-show-notify row">
                            <div class="col-12 text-center">
                                <button type="submit" id="permissionBtn" class="btn btn-success">
                                    {{ __('Update') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
