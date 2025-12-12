@extends('agent.layout')

@section('content')
    <div class="page-header">
        <h4 class="page-title">{{ __('Add Property') }}</h4>
        <ul class="breadcrumbs">
            <li class="nav-home">
                <a href="{{ route('admin.dashboard', getParam()) }}">
                    <i class="flaticon-home"></i>
                </a>
            </li>
            <li class="separator">
                <i class="flaticon-right-arrow"></i>
            </li>
            <li class="nav-item">
                <a href="#">{{ __('Property Management') }}</a>
            </li>
            <li class="separator">
                <i class="flaticon-right-arrow"></i>
            </li>
            <li class="nav-item">
                <a href="#">{{ __('Add Porperty') }}
                    @if (request('type') == 'residential')
                        {{ __('Residential') }}
                    @else
                        {{ __('Commercial') }}
                    @endif
                </a>
            </li>
        </ul>
    </div>
    @php
        use App\Http\Helpers\UserPermissionHelper;

        $tenantId = null;
        if (Auth::guard('agent')->check() && Auth::guard('agent')->user()) {
            $tenantId = Auth::guard('agent')->user()->user_id;
        }

        $package = UserPermissionHelper::currentPackage($tenantId);
        $permissions = [];

        if (!empty($tenantId)) {
            $permissions = UserPermissionHelper::packagePermission($tenantId);
            $permissions = json_decode($permissions, true) ?? [];
        }

        // Helper booleans – you can now use these anywhere
        $canAiContent = !empty($package) && in_array('AI Content Generation', $permissions);
        $canAiImage = !empty($package) && in_array('AI Image Generation', $permissions);
    @endphp

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title d-inline-block">{{ __('Add Porperty') }}</div>
                    @if($canAiContent)
                    <button class="btn btn-primary float-right" data-toggle="modal" data-target="#aiContentModal"
                        id="generate-all-btn">
                        <i class="fas fa-magic"></i> {{ __('Generate All Content') }}
                    </button>
                    @endif
                </div>

                <div class="card-body">
                    <div class="row">
                        @if ($agent->vendor_id != 0)
                            <div class="col-lg-10 offset-lg-1">
                                <div class="alert alert-warning">
                                    {{ __('You can upload maximum ' . $currentPackage->number_of_property_gallery_images . ' gallery images under one property') }}
                                </div>
                            </div>
                        @endif
                        <div class="col-lg-10 offset-lg-1">
                            <div class="alert alert-danger pb-1 dis-none" id="propertyErrors">
                                <button type="button" class="close" data-dismiss="alert">×</button>
                                <ul></ul>
                            </div>
                            <div class="col-lg-12">

                                <div class="d-flex justify-content-between mb-2">
                                    <label for=""><strong> {{ __('Gallery Images') }}
                                        </strong> <span class="text-danger">{{ '*' }}</span>
                                    </label>
@if ($canAiImage)
                                    <button class="btn btn-primary btn-sm" type="button" data-toggle="modal"
                                        data-target="#aiImageModal" data-image-type="gallery" data-field-name="Gallery"><i
                                            class="fas fa-magic"></i> {{ __('Generate') }}</button>
                                            @endif

                                </div>
                                <form action="{{ route('agent.property.imagesstore', getParam()) }}" id="my-dropzone"
                                    enctype="multipart/form-data" class="dropzone create">
                                    @csrf
                                    <div class="fallback">
                                        <input name="file" type="file" multiple />
                                    </div>
                                </form>
                                <p class="em text-danger mb-0" id="errslider_images"></p>
                            </div>


                            <form id="propertyForm"
                                action=" {{ route('agent.property_management.store_property', getParam()) }} "
                                method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="type" value="{{ request()->type }}">
                                <div id="sliders"></div>
                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="">{{ __('Thumbnail Image') }} <span
                                                    class="text-danger">{{ '*' }}</span> </label>
                                            <br>
                                            <div class="thumb-preview ">
                                                <img src="{{ asset('assets/img/noimage.jpg') }}" alt="..."
                                                    class="uploaded-img">
                                            </div>

                                            <div class="mt-3">
                                                <div role="button" class="btn btn-primary btn-sm upload-btn">
                                                    {{ __('Choose Image') }}
                                                    <input type="file" class="img-input" name="featured_image">
                                                </div>
@if ($canAiImage)
                                                <button class="btn btn-primary btn-sm ml-2" type="button" data-toggle="modal"
                                                    data-target="#aiImageModal" data-image-type="thumbnail"
                                                    data-field-name="Thumbnail"><i class="fas fa-magic"></i>
                                                    {{ __('Generate') }}</button>
                                                    @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="">
                                                {{ __('Floor Planning Image') }}
                                            </label>
                                            <br>
                                            <div class="thumb-preview remove">
                                                <img src="{{ asset('assets/img/noimage.jpg') }}" alt="..."
                                                    class="uploaded-img2">
                                            </div>

                                            <div class="mt-3">
                                                <div role="button" class="btn btn-primary btn-sm upload-btn">
                                                    {{ __('Choose Image') }}
                                                    <input type="file" class="img-input2" name="floor_planning_image">
                                                </div>
@if($canAiImage)
                                                <button class="btn btn-primary btn-sm ml-2" type="button" data-toggle="modal"
                                                    data-target="#aiImageModal" data-image-type="floor_plan"
                                                    data-field-name="Floor Plan"><i class="fas fa-magic"></i>
                                                    {{ __('Generate') }}</button>
                                                    @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for=""> {{ __('Video Image') }}
                                            </label>
                                            <br>
                                            <div class="thumb-preview remove">
                                                <img src="{{ asset('assets/img/noimage.jpg') }}" alt="..."
                                                    class="uploaded-img3">
                                            </div>

                                            <div class="mt-3">
                                                <div role="button" class="btn btn-primary btn-sm upload-btn">
                                                    {{ __('Choose Image') }}
                                                    <input type="file" class="img-input3" name="video_image">
                                                </div>
@if($canAiImage)
                                                <button class="btn btn-primary btn-sm ml-2" type="button"
                                                    data-toggle="modal" data-target="#aiImageModal"
                                                    data-image-type="video_poster" data-field-name="Video Poster"><i
                                                        class="fas fa-magic"></i> {{ __('Generate') }}</button>
                                                        @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label>{{ __('Video Url') }}</label>
                                            <input type="text" class="form-control" name="video_url"
                                                placeholder="{{ __('Enter video url') }}">
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label> {{ __('Purpose') }} <span
                                                    class="text-danger">{{ '*' }}</span> </label>

                                            <select name="purpose" class="form-control">
                                                <option selected disabled>
                                                    {{ __('Select Purpose') }} </option>
                                                <option value="rent">{{ __('Rent') }}</option>
                                                <option value="sale">{{ __('Sale') }}</option>
                                            </select>
                                        </div>

                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group ">
                                            <label>{{ __('Category') }} <span
                                                    class="text-danger">{{ '*' }}</span> </label>
                                            <select name="category_id" class="form-control category">
                                                <option disabled selected>
                                                    {{ __('Select Category') }}
                                                </option>

                                                @foreach ($propertyCategories as $category)
                                                    <option value="{{ $category->id }}">
                                                        {{ $category->categoryContent->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    @if ($settings->property_country_status == 1)
                                        <div class="col-lg-3">
                                            <div class="form-group">


                                                <label>{{ __('Country') }} <span
                                                        class="text-danger">{{ '*' }}</span> </label>
                                                <select name="country_id"
                                                    class="form-control country js-example-basic-single3">
                                                    <option disabled selected>
                                                        {{ __('Select Country') }}
                                                    </option>

                                                    @foreach ($propertyCountries as $country)
                                                        <option value="{{ $country->id }}">
                                                            {{ $country->countryContent->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    @endif
                                    @if ($settings->property_state_status == 1)
                                        <div class="col-lg-3 state">
                                            <div class="form-group  ">

                                                <label>{{ __('State') }} <span
                                                        class="text-danger">{{ '*' }}</span> </label>
                                                <select onchange="getCities(event)" name="state_id"
                                                    class="form-control state_id states js-example-basic-single3">
                                                    <option selected disabled>{{ __('Select State') }}
                                                    </option>
                                                    @foreach ($states as $state)
                                                        <option value="{{ $state->id }}">
                                                            {{ $state->stateContent->name }}</option>
                                                    @endforeach

                                                </select>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="col-lg-3 city">
                                        <div class="form-group ">


                                            <label>{{ __('City') }} <span
                                                    class="text-danger">{{ '*' }}</span></label>
                                            <select name="city_id" class="form-control city_id js-example-basic-single3">
                                                <option selected disabled>
                                                    {{ __('Select City') }}
                                                </option>
                                                @if ($settings->property_state_status == 0 && $settings->property_country_status == 0)
                                                    @foreach ($cities as $city)
                                                        <option value="{{ $city->id }}">
                                                            {{ $city->cityContent->name }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label for="">{{ __('Amenities') }} <span
                                                    class="text-danger">{{ '*' }}</span> </label>
                                            <select name="amenities[]" class="form-control js-example-basic-single2"
                                                multiple="multiple">
                                                @foreach ($amenities as $amenity)
                                                    <option value="{{ $amenity->id }}">
                                                        {{ $amenity->amenityContent->name }}</option>
                                                @endforeach
                                            </select>

                                        </div>
                                    </div>

                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label>{{ __('Price') . ' (' . $settings->base_currency_text . ')' }}
                                            </label>
                                            <input type="number" class="form-control" name="price"
                                                placeholder="{{ __('Enter Current Price') }}">

                                            <p class="text-warning">
                                                {{ __('If you leave it blank, price will be negotiable') }}
                                            </p>
                                        </div>
                                    </div>

                                    @if (request('type') == 'residential')
                                        <div class="col-lg-3">
                                            <div class="form-group">
                                                <label>{{ __('Beds') }} <span
                                                        class="text-danger">{{ '*' }}</span> </label>
                                                <input type="text" class="form-control" name="beds"
                                                    placeholder="{{ __('Enter number of bed') }}">
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <div class="form-group">
                                                <label>{{ __('Baths') }} <span
                                                        class="text-danger">{{ '*' }}</span> </label>
                                                <input type="text" class="form-control" name="bath"
                                                    placeholder="{{ __('Enter number of bath') }}">
                                            </div>
                                        </div>
                                    @endif
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label>{{ __('Area (sqft)') }} <span
                                                    class="text-danger">{{ '*' }}</span> </label>
                                            <input type="text" class="form-control" name="area"
                                                placeholder="{{ __('Enter area (sqft)') }} ">
                                        </div>
                                    </div>

                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label>{{ __('Status') }} <span
                                                    class="text-danger">{{ '*' }}</span> </label>
                                            <select name="status" id="" class="form-control">
                                                <option value="1">{{ __('Active') }}</option>
                                                <option value="0">{{ __('Deactive') }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label>{{ __('Latitude') }} <span
                                                    class="text-danger">{{ '*' }}</span> </label>
                                            <input type="text" class="form-control" name="latitude"
                                                placeholder="{{ __('Enter Latitude') }}">
                                            <p> <span class="text-warning">
                                                    {{ __('The Latitude must be between -90 to 90') }}
                                                </span> <br>
                                                <a href="https://tinyurl.com/2hjtmtvc "> <i class="fas fa-eye"> </i>
                                                    {{ __('See Example') }}</a>
                                            </p>

                                        </div>
                                    </div>

                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label>{{ __('Longitude') }} <span
                                                    class="text-danger">{{ '*' }}</span> </label>
                                            <input type="text" class="form-control" name="longitude"
                                                placeholder="{{ __('Enter Longitude') }}">
                                            <p>
                                                <span class="text-warning">
                                                    {{ __('The Longitude must be between -180 to 180') }}
                                                </span> <br>
                                                <a href="https://tinyurl.com/2hjtmtvc "> <i class="fas fa-eye"> </i>
                                                    {{ __('See Example') }} </a>
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div id="accordion" class="mt-3">
                                    @foreach ($tenantLangs as $language)
                                        <div class="version">
                                            <div class="version-header" id="heading{{ $language->id }}">
                                                <h5 class="mb-0">
                                                    <button type="button" class="btn btn-link" data-toggle="collapse"
                                                        data-target="#collapse{{ $language->id }}"
                                                        aria-expanded="{{ $language->is_default == 1 ? 'true' : 'false' }}"
                                                        aria-controls="collapse{{ $language->id }}">
                                                        {{ $language->name . ' ' . __('Language') }}
                                                        {{ $language->is_default == 1 ? '('.__('Default') . ')' : '' }}
                                                    </button>
                                                </h5>
                                            </div>

                                            <div id="collapse{{ $language->id }}"
                                                class="collapse {{ $language->is_default == 1 ? 'show' : '' }}"
                                                aria-labelledby="heading{{ $language->id }}" data-parent="#accordion">
                                                <div class="version-body">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div
                                                                class="form-group {{ $language->direction == 1 ? 'rtl text-right' : '' }}">
                                                                <label>{{ __('Title') }} <span
                                                                        class="text-danger">{{ '*' }}</span>
                                                                </label>

                                                                <div class="input-group">
                                                                    <input type="text" class="form-control"
                                                                        name="{{ $language->code }}_title"
                                                                        placeholder="{{ __('Enter Title') }}">
                                                                    @if($canAiContent)
                                                                    <div class="input-group-append">
                                                                        <button class="btn btn-primary btn-sm"
                                                                            type="button" data-toggle="modal"
                                                                            data-target="#aiContentModal"
                                                                            data-lang-code="{{ $language->code }}"
                                                                            data-lang-name="{{ $language->name }}"
                                                                            data-field-type="title"><i
                                                                                class="fas fa-magic"></i> {{ __('Generate') }}</button>
                                                                    </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-lg-12">
                                                            <div
                                                                class="form-group {{ $language->direction == 1 ? 'rtl text-right' : '' }}">
                                                                <label>{{ __('Address') }} <span
                                                                        class="text-danger">{{ '*' }}</span></label>
                                                                <input type="text"
                                                                    name="{{ $language->code }}_address"
                                                                    class="form-control"
                                                                    placeholder="{{ __('Enter Address') }}">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div
                                                                class="form-group {{ $language->direction == 1 ? 'rtl text-right' : '' }}">

                                                                <div
                                                                    class="d-flex justify-content-between align-items-center mb-1">
                                                                    <label>{{ __('Description') }} <span
                                                                            class="text-danger">{{ '*' }}</span></label>
                                                                    @if($canAiContent)
                                                                    <button type="button" class="btn btn-primary btn-sm"
                                                                        data-toggle="modal" data-target="#aiContentModal"
                                                                        data-lang-code="{{ $language->code }}"
                                                                        data-lang-name="{{ $language->name }}"
                                                                        data-field-type="description"><i
                                                                            class="fas fa-magic"></i> {{ __('Generate') }}</button>
                                                                        @endif
                                                                </div>
                                                                <textarea id="{{ $language->code }}_description" class="form-control summernote"
                                                                    placeholder="{{ __('Enter Description') }}" name="{{ $language->code }}_description" data-height="300"></textarea>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div
                                                                class="form-group {{ $language->direction == 1 ? 'rtl text-right' : '' }}">

                                                                <div
                                                                    class="d-flex justify-content-between align-items-center mb-1">
                                                                    <label> {{ __('Meta Keywords') }} </label>
                                                                    @if($canAiContent)
                                                                    <button type="button" class="btn btn-primary btn-sm"
                                                                        data-toggle="modal" data-target="#aiContentModal"
                                                                        data-lang-code="{{ $language->code }}"
                                                                        data-lang-name="{{ $language->name }}"
                                                                        data-field-type="meta_keyword"><i
                                                                            class="fas fa-magic"></i> {{ __('Generate') }}</button>
                                                                            @endif
                                                                </div>
                                                                <input class="form-control"
                                                                    name="{{ $language->code }}_meta_keyword"
                                                                    placeholder="{{ __('Enter Meta Keywords') }}"
                                                                    data-role="tagsinput">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div
                                                                class="form-group {{ $language->direction == 1 ? 'rtl text-right' : '' }}">

                                                                <div
                                                                    class="d-flex justify-content-between align-items-center mb-1">
                                                                    <label> {{ __('Meta Description') }} </label>
                                                                    @if($canAiContent)
                                                                    <button type="button" class="btn btn-primary btn-sm"
                                                                        data-toggle="modal" data-target="#aiContentModal"
                                                                        data-lang-code="{{ $language->code }}"
                                                                        data-lang-name="{{ $language->name }}"
                                                                        data-field-type="meta_description"><i
                                                                            class="fas fa-magic"></i> {{ __('Generate') }}</button>
                                                                            @endif
                                                                </div>
                                                                <textarea class="form-control" name="{{ $language->code }}_meta_description" rows="5"
                                                                    placeholder=" {{ __('Enter Meta Description') }}"></textarea>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col">
                                                            @php $currLang = $language; @endphp

                                                            @foreach ($tenantLangs as $language)
                                                                @continue($language->id == $currLang->id)

                                                                <div class="form-check py-0">
                                                                    <label class="form-check-label">
                                                                        <input class="form-check-input" type="checkbox"
                                                                            onchange="cloneInput('collapse{{ $currLang->id }}', 'collapse{{ $language->id }}', event)">
                                                                        <span
                                                                            class="form-check-sign">{{ __('Clone for') }}
                                                                            <strong
                                                                                class="text-capitalize text-secondary">{{ $language->name }}</strong>
                                                                            {{ __('language') }}
                                                                        </span>
                                                                    </label>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="row">
                                    <div class="col-lg-12" id="variation_pricing">
                                        <h4 for="">
                                            {{ __('Additional Features') . ' (' . __('Optional') . ')' }}
                                        </h4>
                                        @if ($agent->vendor_id != 0)
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="alert alert-warning">
                                                        

                                                        {{ __('You can add additional features, maximum') . ' ' . $currentPackage->number_of_property_adittionl_specifications }}
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>{{ __('Label') }}</th>
                                                    <th>{{ __('Value') }}</th>
                                                    <th><a href="" class="btn btn-sm btn-success addRow"><i
                                                                class="fas fa-plus-circle"></i></a></th>
                                                </tr>
                                            <tbody id="tbody">
                                                <tr>
                                                    <td>
                                                        @foreach ($tenantLangs as $language)
                                                            <div
                                                                class="form-group {{ $language->direction == 1 ? 'rtl text-right' : '' }}">
                                                                <input type="text"
                                                                    name="{{ $language->code }}_label[]"
                                                                    class="form-control"
                                                                    placeholder="{{ __('Label for') . ' ' . $language->name . ' ' . __('language') }}">
                                                            </div>
                                                        @endforeach
                                                    </td>
                                                    <td>
                                                        @foreach ($tenantLangs as $language)
                                                            <div
                                                                class="form-group {{ $language->direction == 1 ? 'rtl text-right' : '' }}">
                                                                <input type="text"
                                                                    name="{{ $language->code }}_value[]"
                                                                    class="form-control"
                                                                    placeholder="{{ __('Value for') . ' ' . $language->name . ' ' . __('language') }}">
                                                            </div>
                                                        @endforeach
                                                    </td>
                                                    <td>
                                                        <a href="javascript:void(0)"
                                                            class="btn btn-danger  btn-sm deleteRow">
                                                            <i class="fas fa-minus"></i></a>
                                                    </td>
                                                </tr>
                                            </tbody>
                                            </thead>
                                        </table>
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <div class="row">
                        <div class="col-12 text-center">
                            <button type="submit" id="propertySubmit" class="btn btn-success">
                                {{ __('Save') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- AI Content Modal -->
    @includeIf('agent.property.ai-content-modal')

    <!-- AI Image Modal -->
    @includeIf('agent.property.ai-image-modal')


    <!-- AI Image Generation Loader -->
    <div id="ai-loader" class="ai-loader-wrapper" style="display: none;">
        <div class="ai-loader-container">
            <!-- Title & Subtitle -->
            <h3 class="ai-loader-title">{{ __('Generating Your Images') }}</h3>
            <p class="ai-loader-subtitle">{{ __('Please wait while AI creates your property images') . '...' }}</p>

            <!-- Progress Bar with Status -->
            <div class="ai-loader-progress-container">
                <div class="ai-loader-progress-bar">
                    <div class="ai-loader-progress-fill" style="width: 0%;"></div>
                </div>
                <p class="ai-loader-progress-text">
                    <span id="ai-loader-status">{{ __('Processing') }}</span>
                    <span id="ai-loader-time">~0s</span>
                </p>
            </div>

            <!-- Image Counter -->
            <div class="ai-loader-counter">
                <i class="fas fa-image"></i>
                <span id="ai-image-counter">0</span> / <span id="ai-total-images">1</span> {{ __('images') }}
            </div>

            <!-- Fun Tips (Rotating) -->
            <div class="ai-loader-tips">
                <p id="ai-loader-tip">{{ __('Tip: Use specific descriptions for better results') }}</p>
                <!-- Cancel Button -->
                <button id="ai-loader-cancel" class="ai-loader-cancel-btn">
                    <i class="fas fa-times"></i> {{ __('Cancel Generation') }}
                </button>
            </div>

        </div>
    </div>
@endsection

@php

    $labels = '';
    $values = '';
    foreach ($tenantLangs as $language) {
        $label_name = $language->code . '_label[]';
        $value_name = $language->code . '_value[]';

        $labels_placeholder = __('Label for') . ' ' . $language->name . ' ' . __('language');
        $values_placeholder = __('Value for') . ' ' . $language->name . ' ' . __('language');

        if ($language->direction == 1) {
            $direction = 'form-group rtl text-right';
        } else {
            $direction = 'form-group';
        }

        $labels .=
            "<div class='$direction'><input type='text' name='" .
            $label_name .
            "' class='form-control' placeholder='$labels_placeholder'></div>";
        $values .= "<div class='$direction'><input type='text' name='$value_name' class='form-control' placeholder='$values_placeholder'></div>";
    }
@endphp

@section('script')
    <script>
        'use strict';
        var labels = "{!! $labels !!}";
        var values = "{!! $values !!}";
        var generateContentWithAi = "{{ __('Generate Content with AI') }}";
        var generateImageWithAi = "{{ __('Generate Images with AI') }}";
        var generateText = "{{ __('Generate') }}";
        var selectedText = "{{ __('Selected') }}";
        var addToGalleryText = "{{ __('Add to Gallery') }}";
        var thumbnailImageSuccessText = "{{ __('Thumbnail image applied successfully') }}";
        var floorPlanImageSuccessText = "{{ __('Floor plan image applied successfully') }}";
        var videoPosterImageSuccessText = "{{ __('Video poster image applied successfully') }}";
        var imagesText = "{{ __('Image(s)') }}";
        var confirmSelectionText = "{{ __('Confirm Selection') }}";
        var imageRemoveText = "{{ __('Image removed from selection') }}";
        var imageAddedSingleText  = "{{ __('Image added to selection') }}";
        var selectText = "{{ __('Select') }}";
        var successText = "{{ __('Success') }}";
        var infoText = "{{ __('Info') }}";
        var successfullText = "{{ __('successfully') }}";
        var imageAddedText = "{{ __('image(s) added to') }}";
        var closeGalleryText = "{{ __('Close Gallery') }}";
        var useThisImageText = "{{ __('Use This Image') }}";
        var selectMultipleImageText = "{{ __('Generated Images - Select Multiple Images for Gallery') }}";
        var clickToUseText = "{{ __('Generated Image - Click to Use') }}";
        var initializingText = "{{ __('Initializing') }}";
        var finalizingText = "{{ __('Finalizing') }}";
        var floor_planning_gallery = "{{ __('Floor Planning Gallery') }}";
        var galleryTxt = "{{ __('Gallery') }}";
        var thumbnailTxt = "{{ __('Thumbnail') }}";
        var videoPosterTxt = "{{ __('Video Poster') }}";
        var floorPlanTxt = "{{ __('Floor Plan') }}";
        var imageTxt = "{{ __('Image') }}";
        var titleText = "{{ __('Title') }}";
        var errorText = "{{ __('Error') }}";
        var validationErrorText = "{{ __('Please enter a description for your image') }}";
        var descriptitonText = "{{ __('Description') }}";
        var metaKeywordText = "{{ __('Meta Keywords') }}";
        var metaDescriptionText = "{{ __('Meta Description') }}";
        var storeUrl =
            "{{ route('agent.property.imagesstore', getParam()) }}";
        var removeUrl = "{{ route('agent.property.imagermv', getParam()) }}";
        var stateUrl = "{{ route('agent.property_specification.get_state_cities', getParam()) }}";
        let cityUrl = "{{ route('agent.property_specification.get_cities', getParam()) }}";
        var galleryImages = {{  $currentPackage->number_of_property_gallery_images }};
        var selectAmenLocal = "{{ __('Select Amenities') }}";
        let generateImageUrl = "{{ route('agent.property.ai.generate.image', getParam()) }}";
    </script>
    <script type="text/javascript" src="{{ asset('assets/tenant/js/admin-dropzone.js') }}"></script>
    <script src="{{ asset('assets/tenant/js/property.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/tenant/js/ai-content-image-generator.js') }}"></script>
@endsection
