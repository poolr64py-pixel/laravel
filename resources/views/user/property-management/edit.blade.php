@extends('user.layout')


@section('content')
    <div class="page-header">
        <h4 class="page-title">{{ __('Edit Property') }}</h4>
        <ul class="breadcrumbs">
            <li class="nav-home">
                <a href="{{ route('user-dashboard') }}">
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
                <a href="#">{{ __('Edit Property') }}</a>
            </li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title d-inline-block">{{ __('Edit Property') }}</div>
                   
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-10 offset-lg-1">
                            <div class="alert alert-danger pb-1 dis-none" id="propertyErrors">
                                <button type="button" class="close" data-dismiss="alert">×</button>
                                <ul></ul>
                            </div>

                            <div class="col-lg-12">
                                <label for="" class="mb-2"><strong>{{ __('Gallery Images') }}</strong> <span
                                        class="text-danger">{{ '*' }}</span> </label>
                                <div id="reload-slider-div">
                                    <div class="row">

                                        <div class="col-12">
                                            <table class="table table-striped" id="imgtable">

                                                @foreach ($galleryImages as $item)
                                                    <tr class="trdb table-row" id="trdb{{ $item->id }}">
                                                        <td>
                                                            <div class="">
                                                                <img class="thumb-preview wf-150"
                                                                    src="{{ asset('assets/img/property/slider-images/' . $item->image) }}"
                                                                    alt="Ad Image">
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <i class="fa fa-times rmvbtndb"
                                                                data-indb="{{ $item->id }}"></i>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <form action="{{ route('user.property.imagesstore') }}" id="my-dropzone"
                                    enctype="multipart/formdata" class="dropzone create">
                                    @csrf
                                    <div class="fallback">
                                        <input name="file" type="file" multiple />
                                    </div>
                                    <input type="hidden" value="{{ $property->id }}" name="property_id">
                                </form>
                                <p class="em text-danger mb-0" id="errslider_images"></p>

                            </div>

                            <form id="propertyForm"
                                action="{{ route('user.property_management.update_property', $property->id) }}"
                                method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="property_id" value="{{ $property->id }}">
                                <input type="hidden" name="type" value="{{ $property->type }}">
                                <input type="hidden" name="vendor_id" value="{{ $property->vendor_id }}">
                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="">{{ __('Thumbnail Image') }} <span
                                                    class="text-danger">{{ '*' }}</span> </label>
                                            <br>
                                            <div class="thumb-preview">
                                                <img src="{{ $property->featured_image ? asset('assets/img/property/featureds/' . $property->featured_image) : asset('assets/img/noimage.jpg') }}"
                                                    alt="..." class="uploaded-img">
                                            </div>
                                            <div class="mt-3">
                                                <div role="button" class="btn btn-primary btn-sm upload-btn">
                                                    {{ __('Choose Image') }}
                                                    <input type="file" class="img-input" name="featured_image">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="">{{ __('Floor Planning Image') }}</label>
                                            <br>
                                            <div class="thumb-preview remove">

                                                <img src="{{ !empty($property->floor_planning_image) ? asset('assets/img/property/plannings/' . $property->floor_planning_image) : asset('assets/img/noimage.jpg') }}"
                                                    alt="..." class="uploaded-img2">
                                                @if (!empty($property->floor_planning_image))
                                                    <i class="fas fa-times text-danger rmvflrImg"
                                                        data-indb="{{ $property->id }}"></i>
                                                @endif
                                            </div>

                                            <div class="mt-3">
                                                <div role="button" class="btn btn-primary btn-sm upload-btn">
                                                    {{ __('Choose Image') }}
                                                    <input type="file" class="img-input2" name="floor_planning_image">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="">{{ __('Video Image') }}</label>
                                            <br>
                                            <div class="thumb-preview remove">

                                                <img src="{{ !empty($property->video_image) ? asset('assets/img/property/video/' . $property->video_image) : asset('assets/img/noimage.jpg') }}"
                                                    alt="..." class="uploaded-img3">
                                                @if (!empty($property->video_image))
                                                    <i class="fas fa-times text-danger rmvvdoImg"
                                                        data-indb="{{ $property->id }}"></i>
                                                @endif
                                            </div>

                                            <div class="mt-3">
                                                <div role="button" class="btn btn-primary btn-sm upload-btn">
                                                    {{ __('Choose Image') }}
                                                    <input type="file" class="img-input3" name="video_image">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label>{{ __('Video Url') }} </label>
                                            <input type="text" class="form-control" name="video_url"
                                                placeholder="{{ __('Enter video url') }}"
                                                value="{{ $property->video_url }}">
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label>{{ __('Purpose') }} <span
                                                    class="text-danger">{{ '*' }}</span> </label>

                                            <select name="purpose" class="form-control">
                                                <option disabled>
                                                    {{ __('Select a Purpose') }} </option>
                                                <option value="rent" @if ($property->purpose == 'rent') 'selected' @endif>
                                                    {{ __('Rent') }}
                                                </option>
                                                <option value="sale" @if ($property->purpose == 'sale') 'selected' @endif>
                                                    {{ __('Sale') }}
                                                </option>
                                            </select>
                                        </div>

                                    </div>

                                    <div class="col-lg-3">
                                        <div class="form-group ">
                                            <label>{{ __('Category') }} <span
                                                    class="text-danger">{{ '*' }}</span> </label>
                                            <select name="category_id" class="form-control category">
                                                <option disabled selected>
                                                    {{ __('Select a Category') }}
                                                </option>

                                                @foreach ($propertyCategories as $category)
                                                    <option value="{{ $category->id }}"
                                                        {{ $property->category_id == $category->id ? 'selected' : '' }}>
                                                        {{ $category->getContent($language->id)?->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    @if ($propertySettings->property_country_status == 1)
                                        <div class="col-lg-3">
                                            <div class="form-group ">

                                                <label>{{ __('Country') }} <span
                                                        class="text-danger">{{ '*' }}</span> </label>
                                                <select name="country_id" class="form-control country">
                                                    <option disabled selected>
                                                        {{ __('Select Country') }}
                                                    </option>

                                                    @foreach ($propertyCountries as $country)
                                                        <option value="{{ $country->id }}"
                                                            {{ $property->country_id == $country->id ? 'selected' : '' }}>
                                                            {{ $country?->getContent($language->id)?->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    @endif
                                    @if ($propertySettings->property_country_status == 1 && $propertySettings->property_state_status == 1)
                                        <div class="col-lg-3 state"
                                            @if (is_null($property->state_id)) style="display:none !important;" @else style="display:block !important;" @endif>
                                            <div class="form-group">

                                                <label>{{ __('State') }} <span
                                                        class="text-danger">{{ '*' }}</span> </label>
                                                <select onchange="getCities(event)" name="state_id"
                                                    class="form-control  state_id states">
                                                    <option disabled>{{ __('Select State') }}
                                                    </option>
                                                    @if ($property->state_id)
                                                        @foreach ($propertyStates as $state)
                                                            <option value="{{ $state->id }}"
                                                                {{ $property->state_id == $state->id ? 'selected' : '' }}>
                                                                {{ $state?->getContent($language->id)?->name }}</option>
                                                        @endforeach
                                                    @endif


                                                </select>
                                            </div>
                                        </div>
                                    @elseif ($propertySettings->property_country_status == 0 && $propertySettings->property_state_status == 1)
                                        <div class="col-lg-3 state ">
                                            <div class="form-group">

                                                <label>{{ __('State') }} <span
                                                        class="text-danger">{{ '*' }}</span> </label>
                                                <select onchange="getCities(event)" name="state_id"
                                                    class="form-control state_id states js-example-basic-single3">
                                                    <option disabled selected>{{ __('Select State') }}
                                                    </option>

                                                    @foreach ($states as $state)
                                                        <option value="{{ $state->id }}"
                                                            {{ $property->state_id == $state->id ? 'selected' : '' }}>
                                                            {{ $state?->getContent($language->id)?->name }}</option>
                                                    @endforeach

                                                </select>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="col-lg-3 city"
                                        @if (empty($property->city_id)) style="display:none;"@else style="display:block;" @endif>
                                        <div class="form-group ">

                                            <label>{{ __('City') }} <span
                                                    class="text-danger">{{ '*' }}</span> </label>
                                            <select name="city_id" class="form-control city_id">
                                                <option value="" disabled>
                                                    {{ __('Select City') }}
                                                </option>
                                                @if ($property->city_id)
                                                    @foreach ($propertyCities as $city)
                                                        <option value="{{ $property->city_id }}"
                                                            {{ $property->city_id == $city->id ? 'selected' : '' }}>
                                                            {{ $city?->getContent($language->id)?->name }}</option>
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
                                                    <option value="{{ $amenity->id }}"
                                                        @foreach ($propertyAmenities as $propertyAmenity) 
                                                            {{ $propertyAmenity->amenity_id == $amenity->id ? 'selected' : '' }} @endforeach>
                                                        {{ $amenity->getContent($language->id)?->name }}</option>
                                                @endforeach
                                            </select>

                                        </div>
                                    </div>

                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label>{{ __('Price') . ' (' . $userBs->base_currency_text . ')' }}
                                            </label>
                                            <input type="number" class="form-control" name="price"
                                                placeholder="{{ __('Enter Current Price') }}"
                                                value="{{ $property->price }}">
                                            <p class="text-warning">
                                                {{ __('If you leave it blank, price will be negotiable') }}
                                            </p>
                                        </div>
                                    </div>


                                    @if ($property->type == 'residential')
                                        <div class="col-lg-3">
                                            <div class="form-group">
                                                <label>{{ __('Beds') }} <span
                                                        class="text-danger">{{ '*' }}</span> </label>
                                                <input type="text" class="form-control" name="beds"
                                                    value="{{ $property->beds }}"
                                                    placeholder="{{ __('Enter number of bed') }}">
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <div class="form-group">
                                                <label>{{ __('Baths') }} <span
                                                        class="text-danger">{{ '*' }}</span> </label>
                                                <input type="text" class="form-control" name="bath"
                                                    value="{{ $property->bath }}"
                                                    placeholder="{{ __('Enter number of bath') }}">
                                            </div>
                                        </div>
                                    @endif

                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label>{{ __('Area (sqft)') }} <span
                                                    class="text-danger">{{ '*' }}</span> </label>
                                            <input type="text" class="form-control" name="area"
                                                value="{{ $property->area }}"
                                                placeholder="{{ __('Enter area (sqft)') }} ">
                                        </div>
                                    </div>

                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label>{{ __('Status') }} <span
                                                    class="text-danger">{{ '*' }}</span> </label>
                                            <select name="status" id="" class="form-control">
                                                <option {{ $property->status == 1 ? 'selected' : '' }} value="1">
                                                    {{ __('Active') }}</option>
                                                <option {{ $property->status == 0 ? 'selected' : '' }} value="0">
                                                    {{ __('Deactive') }}</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label>{{ __('Latitude') }} <span
                                                    class="text-danger">{{ '*' }}</span> </label>
                                            <input type="text" class="form-control" value="{{ $property->latitude }}"
                                                name="latitude" placeholder="{{ __('Enter Latitude') }}">
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
                                            <input type="text" class="form-control"
                                                value="{{ $property->longitude }}" name="longitude"
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

                                    <div class="col-lg-3">
                                        <div class="form-group agent  ">
                                            <label for="">{{ __('Agent') }}</label>
                                            <select name="agent_id" class="form-control agent_id">
                                                <option value="" selected>
                                                    {{ __('Select Agent') }}
                                                </option>
                                                @foreach ($agents as $agent)
                                                    <option value="{{ $agent->id }}"
                                                        {{ $agent->id == $property->agent_id ? 'selected' : '' }}>
                                                        {{ $agent->username }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <p class="text-warning">
                                                {{ __('If you do not select any agent, then this property will be listed under you') }}
                                            </p>
                                        </div>
                                    </div>


                                </div>

                                <div id="accordion" class="mt-3">
                                    @foreach ($languages as $language)
                                        @php
                                            $peopertyContent = $propertyContents
                                                ->where('language_id', $language->id)
                                                ->first();

                                        @endphp
                                        <div class="version">
                                            <div class="version-header" id="heading{{ $language->id }}">
                                                <h5 class="mb-0">
                                                    <button type="button" class="btn btn-link" data-toggle="collapse"
                                                        data-target="#collapse{{ $language->id }}"
                                                        aria-expanded="{{ $language->is_default == 1 ? 'true' : 'false' }}"
                                                        aria-controls="collapse{{ $language->id }}">
                                                        {{ $language->name . ' ' . __('Language') }}
                                                        {{ $language->is_default == 1 ? '(Default)' : '' }}
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
                                                                <input type="text" class="form-control"
                                                                    name="{{ $language->code }}_title"
                                                                    placeholder="{{ __('Enter Title') }}"
                                                                    value="{{ $peopertyContent ? $peopertyContent->title : '' }}">
                                                            </div>
                                                        </div>



                                                        <div class="col-lg-12">
                                                            <div
                                                                class="form-group {{ $language->direction == 1 ? 'rtl text-right' : '' }}">
                                                                <label>{{ __('Address') }} <span
                                                                        class="text-danger">{{ '*' }}</span>
                                                                </label>
                                                                <input type="text"
                                                                    name="{{ $language->code }}_address"
                                                                    placeholder="{{ __('Enter Address') }}"
                                                                    value="{{ @$peopertyContent->address }}"
                                                                    class="form-control">
                                                            </div>
                                                        </div>


                                                    </div>
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div
                                                                class="form-group {{ $language->direction == 1 ? 'rtl text-right' : '' }}">
                                                                <label>{{ __('Description') }} <span
                                                                        class="text-danger">{{ '*' }}</span>
                                                                </label>
                                                                <textarea class="form-control summernote " id="{{ $language->code }}_description"
                                                                    placeholder="{{ __('Enter Description') }}" name="{{ $language->code }}_description" data-height="300">{{ @$peopertyContent->description }}</textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div
                                                                class="form-group {{ $language->direction == 1 ? 'rtl text-right' : '' }}">
                                                                <label>{{ __('Meta Keywords') }} </label>
                                                                <input class="form-control"
                                                                    name="{{ $language->code }}_meta_keyword"
                                                                    placeholder="{{ __('Enter Meta Keywords') }}"
                                                                    data-role="tagsinput"
                                                                    value="{{ $peopertyContent ? $peopertyContent->meta_keyword : '' }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div
                                                                class="form-group {{ $language->direction == 1 ? 'rtl text-right' : '' }}">
                                                                <label>{{ __('Meta Description') }} </label>
                                                                <textarea class="form-control" name="{{ $language->code }}_meta_description" rows="5"
                                                                    placeholder=" {{ __('Enter Meta Description') }}">{{ $peopertyContent ? $peopertyContent->meta_description : '' }}</textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col">
                                                            @php $currLang = $language; @endphp

                                                            @foreach ($languages as $language)
                                                                @continue($language->id == $currLang->id)

                                                                <div class="form-check py-0">
                                                                    <label class="form-check-label">
                                                                        <input class="form-check-input" type="checkbox"
                                                                            onchange="cloneInput('collapse{{ $currLang->id }}', 'collapse{{ $language->id }}', event)">
                                                                        <span
                                                                            class="form-check-sign">{{ __('Clone for') }}
                                                                            <strong
                                                                                class="text-capitalize text-secondary">{{ $language->name }}</strong>
                                                                            {{ __('language') }}</span>
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
                                        <table class="table table-bordered ">
                                            <thead>
                                                <tr>
                                                    <th>{{ __('Label') }}</th>
                                                    <th>{{ __('Value') }}</th>
                                                    <th><a href="javascrit:void(0)"
                                                            class="btn  btn-sm btn-success addRow"><i
                                                                class="fas fa-plus-circle"></i></a></th>
                                                </tr>
                                            <tbody id="tbody">

                                                @if (count($specifications) > 0)
                                                    @foreach ($specifications as $specification)
                                                        <tr>
                                                            <td>
                                                                @foreach ($languages as $language)
                                                                    @php
                                                                        $sp_content = $specification->getContent(
                                                                            $language->id,
                                                                        );
                                                                    @endphp
                                                                    <div
                                                                        class="form-group  {{ $language->direction == 1 ? 'rtl text-right' : '' }}">
                                                                        <input type="text"
                                                                            name="{{ $language->code }}_label[]"
                                                                            value="{{ $sp_content?->label }}"
                                                                            class="form-control"
                                                                            plplaceholder="{{ __('Label for') . ' ' . $language->name . ' ' . __('language') }}">
                                                                    </div>
                                                                @endforeach
                                                            </td>
                                                            <td>
                                                                @foreach ($languages as $language)
                                                                    @php
                                                                        $sp_content = $specification->getContent(
                                                                            $language->id,
                                                                        );
                                                                    @endphp
                                                                    <div
                                                                        class="form-group {{ $language->direction == 1 ? 'rtl text-right' : '' }}">
                                                                        <input type="text"
                                                                            name="{{ $language->code }}_value[]"
                                                                            value="{{ $sp_content?->value }}"
                                                                            class="form-control"
                                                                            placeholder="{{ __('Value for') . ' ' . $language->name . ' ' . __('language') }}">
                                                                    </div>
                                                                @endforeach
                                                            </td>
                                                            <td>
                                                                <a href="javascript:void(0)"
                                                                    data-specification="{{ $specification->id }}"
                                                                    class="btn  btn-sm btn-danger deleteSpecification">
                                                                    <i class="fas fa-minus"></i></a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td>
                                                            @foreach ($languages as $language)
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
                                                            @foreach ($languages as $language)
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
                                                @endif
                                            </tbody>
                                            </thead>
                                        </table>
                                    </div>
                                </div>

                                <div id="sliders"></div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <div class="row">
                        <div class="col-12 text-center">
                            <button type="submit" id="propertySubmit" class="btn btn-primary">
                                {{ __('Update') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@php
    
    $labels = '';
    $values = '';
    foreach ($languages as $language) {
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

@section('scripts')
    <script>
        var labels = "{!! $labels !!}";
        var values = "{!! $values !!}";
        var stateUrl = "{{ route('user.property_management.get_state_cities') }}";
        var cityUrl = "{{ route('user.property_management.get_cities') }}";
        var storeUrl = "{{ route('user.property.imagesupdate') }}";
        var removeUrl = "{{ route('user.property.imagermv') }}";
        var rmvdbUrl = "{{ route('user.property.imgdbrmv') }}";
        var specificationRmvUrl = "{{ route('user.property_management.specification_delete') }}";
        let galleryImages = "{{ $userCurrentPackage->number_of_property_gallery_images }}";
        let flrimgrmv = "{{ route('user.property_management.floorImgrmv') }}";
        let vdoimgrmv = "{{ route('user.property_management.videoImgrmv') }}";
        var selectAmenLocal = "{{ $keywords['Select Amenities'] ?? __('Select Amenities') }}";
    </script>
    <script type="text/javascript" src="{{ asset('assets/tenant/js/admin-partial.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/tenant/js/admin-dropzone.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/tenant/js/property.js') }}"></script>
@endsection
