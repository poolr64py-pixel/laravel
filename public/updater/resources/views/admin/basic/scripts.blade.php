@extends('admin.layout')
@section('content')
    <div class="page-header">
        <h4 class="page-title">{{ __('Plugins') }}</h4>
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
                <a href="#">{{ __('Settings') }}</a>
            </li>
            <li class="separator">
                <i class="flaticon-right-arrow"></i>
            </li>
            <li class="nav-item">
                <a href="#">{{ __('Plugins') }}</a>
            </li>
        </ul>
    </div>
    <div class="row">
        <div class="col-md-12">
            <form id="scriptForm" class="" action="{{ route('admin.script.update') }}" method="post">
                @csrf
                <div class="row">

                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div class="card-title mb-0">{{ __('Gemini AI') }}</div>
                                <button type="button" class="btn btn-sm btn-info" data-toggle="modal"
                                    data-target="#geminiInfoModal">
                                    <i class="fas fa-info-circle"></i> {{ __('Info') }}
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label>{{ __('Status') }}</label>
                                    <div class="selectgroup w-100">
                                        <label class="selectgroup-item">
                                            <input type="radio" name="ai_generate_status" value="1"
                                                class="selectgroup-input"
                                                {{ $data->ai_generate_status == 1 ? 'checked' : '' }}>
                                            <span class="selectgroup-button">{{ __('Active') }}</span>
                                        </label>
                                        <label class="selectgroup-item">
                                            <input type="radio" name="ai_generate_status" value="0"
                                                class="selectgroup-input"
                                                {{ $data->ai_generate_status == 0 ? 'checked' : '' }}>
                                            <span class="selectgroup-button">{{ __('Deactive') }}</span>
                                        </label>
                                    </div>
                                    @if ($errors->has('ai_generate_status'))
                                        <p class="mb-0 text-danger">{{ $errors->first('ai_generate_status') }}</p>
                                    @endif
                                </div>

                                <div class="form-group">
                                    <label>{{ __('Gemini Model') }}</label>
                                    <select class="form-control" name="gemini_model">
                                        <option value="gemini-2.5-flash"
                                            {{ ($data->gemini_model ?? 'gemini-2.5-flash') == 'gemini-2.5-flash' ? 'selected' : '' }}>
                                            {{ __('Gemini 2.5 Flash')  . ' ('. __('Fast & Recommended') . ')'}}
                                        </option>
                                        <option value="gemini-2.5-pro"
                                            {{ ($data->gemini_model ?? '') == 'gemini-2.5-pro' ? 'selected' : '' }}>
                                            {{ __('Gemini 2.5 Pro')  . ' ('. __('Advanced Reasoning') . ')' }}
                                        </option>
                                        <option value="gemini-2.5-flash-lite"
                                            {{ ($data->gemini_model ?? '') == 'gemini-2.5-flash-lite' ? 'selected' : '' }}>
                                            {{  __('Gemini 2.5 Flash-Lite')  . ' ('. __('Cost Efficient') . ')' }}
                                            
                                        </option>
                                        <option value="gemini-2.0-flash"
                                            {{ ($data->gemini_model ?? '') == 'gemini-2.0-flash' ? 'selected' : '' }}>
                                            {{   __('Gemini 2.0 Flash')  . ' ('. __('Stable') . ')' }}
                                        </option>
                                    </select>
                                    @if ($errors->has('gemini_model'))
                                        <p class="mb-0 text-danger">{{ $errors->first('gemini_model') }}</p>
                                    @endif
                                    <small class="form-text text-primary">
                                        <i class="fas fa-lightbulb"></i>
                                        {{ __('Select AI model based on your needs. Flash is recommended for general content generation') . '.' }}
                                    </small>
                                </div>

                                <div class="form-group">
                                    <label>{{ __('API Key') }}</label>
                                    <input class="form-control" name="gemini_apikey" value="{{ $data->gemini_apikey }}"
                                        placeholder="AIzaSy...">
                                    @if ($errors->has('gemini_apikey'))
                                        <p class="mb-0 text-danger">{{ $errors->first('gemini_apikey') }}</p>
                                    @endif

                                    <small class="form-text  ">
                                        <div class="text-info">
                                            <i class="fas fa-info-circle"></i>
                                            {{ __('This API key is used only for AI content generation. Get your API key from') }}
                                        </div>
                                        <a  href="https://aistudio.google.com/app/apikey" target="_blank"
                                            class="btn btn-sm btn-primary text-white rv-btn-1 ml-1" >
                                            <i class="fas fa-key"></i> {{ __('Google AI Studio') }} <i
                                                class="fas fa-external-link-alt"></i>
                                        </a>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Gemini Info Modal -->
                    <div class="modal fade" id="geminiInfoModal" tabindex="-1" role="dialog"
                        aria-labelledby="geminiInfoModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header bg-info text-white">
                                    <h5 class="modal-title" id="geminiInfoModalLabel">
                                        <i class="fas fa-info-circle"></i> {{ __('Gemini AI Information') }}
                                    </h5>
                                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="alert alert-light border-left border-info"
                                        style="border-left-width: 4px !important;">
                                        <h6 class="font-weight-bold text-info">
                                            <i class="fas fa-key"></i> {{ __('API Key Usage') }}
                                        </h6>
                                        <p class="mb-0">
                                            {{ __('Gemini API key will be used exclusively for generating content such as title, descriptions and other text-based materials') . '.' }}
                                        </p>
                                    </div>

                                    <div class="mt-3">
                                        <h6 class="font-weight-bold"><i class="fas fa-check-circle text-success"></i>
                                            {{ __('Supported Content Types:') }}</h6>
                                        <ul class="mb-0">
                                            <li>{{ __('Property and Project titles and Description') }}</li>
                                            <li>{{ __('Meta Keywords and descriptions') }}</li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                        <i class="fas fa-times"></i> {{ __('Close') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <div class="card-title">
                                    {{ __('Google Recaptcha') }}
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label>{{ __('Google Recaptcha Status') }}</label>
                                    <div class="selectgroup w-100">
                                        <label class="selectgroup-item">
                                            <input type="radio" name="is_recaptcha" value="1"
                                                class="selectgroup-input" {{ $data->is_recaptcha == 1 ? 'checked' : '' }}>
                                            <span class="selectgroup-button">{{ __('Active') }}</span>
                                        </label>
                                        <label class="selectgroup-item">
                                            <input type="radio" name="is_recaptcha" value="0"
                                                class="selectgroup-input" {{ $data->is_recaptcha == 0 ? 'checked' : '' }}>
                                            <span class="selectgroup-button">{{ __('Deactive') }}</span>
                                        </label>
                                    </div>
                                    @if ($errors->has('is_recaptcha'))
                                        <p class="mb-0 text-danger">{{ $errors->first('is_recaptcha') }}</p>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>{{ __('Google Recaptcha Site key') }}</label>
                                    <input class="form-control" name="google_recaptcha_site_key"
                                        value="{{ $data->google_recaptcha_site_key }}">
                                    @if ($errors->has('google_recaptcha_site_key'))
                                        <p class="mb-0 text-danger">{{ $errors->first('google_recaptcha_site_key') }}</p>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>{{ __('Google Recaptcha Secret key') }}</label>
                                    <input class="form-control" name="google_recaptcha_secret_key"
                                        value="{{ $data->google_recaptcha_secret_key }}">
                                    @if ($errors->has('google_recaptcha_secret_key'))
                                        <p class="mb-0 text-danger">{{ $errors->first('google_recaptcha_secret_key') }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <div class="card-title">{{ __('Disqus') }}</div>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label>{{ __('Disqus Status') }} ({{ __('Website Blog Details') }})</label>
                                    <div class="selectgroup w-100">
                                        <label class="selectgroup-item">
                                            <input type="radio" name="is_disqus" value="1"
                                                class="selectgroup-input" {{ $data->is_disqus == 1 ? 'checked' : '' }}>
                                            <span class="selectgroup-button">{{ __('Active') }}</span>
                                        </label>
                                        <label class="selectgroup-item">
                                            <input type="radio" name="is_disqus" value="0"
                                                class="selectgroup-input" {{ $data->is_disqus == 0 ? 'checked' : '' }}>
                                            <span class="selectgroup-button">{{ __('Deactive') }}</span>
                                        </label>
                                    </div>
                                    @if ($errors->has('is_disqus'))
                                        <p class="mb-0 text-danger">{{ $errors->first('is_disqus') }}</p>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>{{ __('Disqus Status') }}({{ __('User Profile Blog Details') }})</label>
                                    <div class="selectgroup w-100">
                                        <label class="selectgroup-item">
                                            <input type="radio" name="is_user_disqus" value="1"
                                                class="selectgroup-input"
                                                {{ $data->is_user_disqus == 1 ? 'checked' : '' }}>
                                            <span class="selectgroup-button">{{ __('Active') }}</span>
                                        </label>
                                        <label class="selectgroup-item">
                                            <input type="radio" name="is_user_disqus" value="0"
                                                class="selectgroup-input"
                                                {{ $data->is_user_disqus == 0 ? 'checked' : '' }}>
                                            <span class="selectgroup-button">{{ __('Deactive') }}</span>
                                        </label>
                                    </div>
                                    @if ($errors->has('is_user_disqus'))
                                        <p class="mb-0 text-danger">{{ $errors->first('is_user_disqus') }}</p>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>{{ __('Disqus Shortname') }}</label>
                                    <input class="form-control" name="disqus_shortname"
                                        value="{{ $data->disqus_shortname }}">
                                    @if ($errors->has('disqus_shortname'))
                                        <p class="mb-0 text-danger">{{ $errors->first('disqus_shortname') }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <div class="card-title">{{ __('Tawk.to') }}</div>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label>{{ __('Tawk.to Status') }}</label>
                                    <div class="selectgroup w-100">
                                        <label class="selectgroup-item">
                                            <input type="radio" name="is_tawkto" value="1"
                                                class="selectgroup-input" {{ $data->is_tawkto == 1 ? 'checked' : '' }}>
                                            <span class="selectgroup-button">{{ __('Active') }}</span>
                                        </label>
                                        <label class="selectgroup-item">
                                            <input type="radio" name="is_tawkto" value="0"
                                                class="selectgroup-input" {{ $data->is_tawkto == 0 ? 'checked' : '' }}>
                                            <span class="selectgroup-button">{{ __('Deactive') }}</span>
                                        </label>
                                    </div>
                                    <p class="mb-0 text-warning">
                                        {{ __('If you enable Tawk.to, then WhatsApp must be disabled') }}</p>
                                    @if ($errors->has('is_tawkto'))
                                        <p class="mb-0 text-danger">{{ $errors->first('is_tawkto') }}</p>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>{{ __('Tawk.to Direct Chat Link') }}</label>
                                    <input class="form-control" name="tawkto_chat_link"
                                        value="{{ $data->tawkto_chat_link }}">
                                    @if ($errors->has('tawkto_chat_link'))
                                        <p class="mb-0 text-danger">{{ $errors->first('tawkto_chat_link') }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <div class="card-title">{{ __('WhatsApp Chat Button') }}</div>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label>{{ __('Status') }}</label>
                                    <div class="selectgroup w-100">
                                        <label class="selectgroup-item">
                                            <input type="radio" name="is_whatsapp" value="1"
                                                class="selectgroup-input" {{ $data->is_whatsapp == 1 ? 'checked' : '' }}>
                                            <span class="selectgroup-button">{{ __('Active') }}</span>
                                        </label>
                                        <label class="selectgroup-item">
                                            <input type="radio" name="is_whatsapp" value="0"
                                                class="selectgroup-input" {{ $data->is_whatsapp == 0 ? 'checked' : '' }}>
                                            <span class="selectgroup-button">{{ __('Deactive') }}</span>
                                        </label>
                                    </div>
                                    <p class="text-warning mb-0">
                                        {{ __('If you enable WhatsApp, then Tawk.to must be disabled') }}</p>
                                </div>
                                <div class="form-group">
                                    <label>{{ __('WhatsApp Number') }}</label>
                                    <input class="form-control" type="text" name="whatsapp_number"
                                        value="{{ $data->whatsapp_number }}">
                                    <p class="text-warning mb-0">{{ __('Enter Phone number with Country Code') }}</p>
                                </div>
                                <div class="form-group">
                                    <label>{{ __('WhatsApp Header Title') }}</label>
                                    <input class="form-control" type="text" name="whatsapp_header_title"
                                        value="{{ $data->whatsapp_header_title }}">
                                    @if ($errors->has('whatsapp_header_title'))
                                        <p class="mb-0 text-danger">{{ $errors->first('whatsapp_header_title') }}</p>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>{{ __('WhatsApp Popup Message') }}</label>
                                    <textarea class="form-control" name="whatsapp_popup_message" rows="2">{{ $data->whatsapp_popup_message }}</textarea>
                                    @if ($errors->has('whatsapp_popup_message'))
                                        <p class="mb-0 text-danger">{{ $errors->first('whatsapp_popup_message') }}</p>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>{{ __('Popup') }}</label>
                                    <div class="selectgroup w-100">
                                        <label class="selectgroup-item">
                                            <input type="radio" name="whatsapp_popup" value="1"
                                                class="selectgroup-input"
                                                {{ $data->whatsapp_popup == 1 ? 'checked' : '' }}>
                                            <span class="selectgroup-button">{{ __('Active') }}</span>
                                        </label>
                                        <label class="selectgroup-item">
                                            <input type="radio" name="whatsapp_popup" value="0"
                                                class="selectgroup-input"
                                                {{ $data->whatsapp_popup == 0 ? 'checked' : '' }}>
                                            <span class="selectgroup-button">{{ __('Deactive') }}</span>
                                        </label>
                                    </div>
                                    @if ($errors->has('whatsapp_popup'))
                                        <p class="mb-0 text-danger">{{ $errors->first('whatsapp_popup') }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-footer">
                        <div class="form">
                            <div class="form-group from-show-notify row">
                                <div class="col-12 text-center">
                                    <button type="submit" form="scriptForm"
                                        class="btn btn-success">{{ __('Update') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
