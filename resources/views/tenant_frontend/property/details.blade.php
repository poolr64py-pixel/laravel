 @extends('tenant_frontend.layout')

 @section('pageHeading')
     {{ $propertyContent->title }}
 @endsection

 @section('metaKeywords')
     @if ($propertyContent)
         {{ $propertyContent->meta_keyword }}
     @endif
 @endsection

 @section('metaDescription')
     @if ($propertyContent)
         {{ $propertyContent->meta_description }}
     @endif
 @endsection

 @section('og:tag')
    @include('components.property-schema', ['property' => $propertyContent, 'content' => $propertyContent, 'language' => $language, 'currencyInfo' => $currencyInfo])
    <!-- SEO SCHEMA TESTE -->
@endsection

 @section('content')
     <div class="product-single pt-100 pb-70 border-top header-next">
         <div class="container">
             <div class="row gx-xl-5">
                 <div class="col-lg-9 col-xl-9">
                     <div class="product-single-gallery mb-40">
                         <div class="slider-navigation">
                             <button type="button" title="Slide prev" class="slider-btn slider-btn-prev">
                                 <i class="fal fa-angle-left"></i>
                             </button>
                             <button type="button" title="Slide next" class="slider-btn slider-btn-next">
                                 <i class="fal fa-angle-right"></i>
                             </button>
                         </div>
                         <div class="swiper product-single-slider">
                             <div class="swiper-wrapper">
                                 @foreach ($sliders as $slider)
                                     <div class="swiper-slide">
                                         <figure class="radius-lg lazy-container ratio ratio-16-11">
                                             <a href="{{ asset('assets/img/property/slider-images/' . $slider->image) }}"
                                                 class="lightbox-single">
                                                 <img class="lazyload" src="assets/images/placeholder.png"
                                                     data-src="{{ asset('assets/img/property/slider-images/' . $slider->image) }}">
                                             </a>
                                         </figure>
                                     </div>
                                 @endforeach

                             </div>
                         </div>

                         <div class="swiper slider-thumbnails">
                             <div class="swiper-wrapper">
                                 @foreach ($sliders as $slider)
                                     <div class="swiper-slide">
                                         <div class="thumbnail-img lazy-container radius-md ratio ratio-16-11">
                                             <img class="lazyload" src="assets/images/placeholder.png"
                                                 data-src="{{ asset('assets/img/property/slider-images/' . $slider->image) }}">
                                         </div>
                                     </div>
                                 @endforeach
                             </div>
                         </div>
                     </div>
                     <div class="product-single-details">
                         <div class="row">
                             <div class="col-md-8">
                                 <div class="d-flex align-items-center justify-content-between mb-10">
                                     <span class="product-category text-sm"> <a
                                             href="{{ route('frontend.properties', [ 'category' => $propertyContent->categoryContent?->slug]) }}">
                                             {{ $propertyContent->categoryContent?->name }}</a></span>
                                 </div>
                                 <h3 class="product-title">
                                     <a href="#">{{ $propertyContent->title }}</a>
                                 </h3>
                                 <div class="product-location icon-start">
                                     <i class="fal fa-map-marker-alt"></i>
                                     <span>
                                         {{ $propertyContent->address }}
                                     </span>
                                     <span>
                                         {{ $propertyContent->property->city?->getContent($propertyContent->language_id)?->name }}
                                         {{ $propertyContent->property->isStateActive ? ', ' . $propertyContent->property->state?->getContent($propertyContent->language_id)?->name : '' }}
                                         {{ $propertyContent->property->isCountryActive ? ', ' . $propertyContent->property->country?->getContent($propertyContent->language_id)?->name : '' }}
                                     </span>
                                 </div>
                                 <ul class="product-info p-0 list-unstyled d-flex align-items-center mt-10 mb-30">
                                     <li class="icon-start" data-tooltip="tooltip" data-bs-placement="top"
                                         title="{{ __('Area') }}">
                                         <i class="fal fa-vector-square"></i>
                                         <span>{{ $propertyContent->area }} {{ $keywords['Sqft'] ?? __('Sqft') }}</span>
                                     </li>
                                     @if ($propertyContent->type == 'residential')
                                         <li class="icon-start" data-tooltip="tooltip" data-bs-placement="top"
                                             title="{{ $keywords['Beds'] ?? __('Beds') }}">
                                             <i class="fal fa-bed"></i>
                                             <span>{{ $propertyContent->beds }}
                                                 {{ $keywords['Beds'] ?? __('Beds') }}</span>
                                         </li>
                                         <li class="icon-start" data-tooltip="tooltip" data-bs-placement="top"
                                             title="{{ $keywords['Baths'] ?? __('Baths') }}">
                                             <i class="fal fa-bath"></i>
                                             <span>{{ $propertyContent->bath }}
                                                 {{ $keywords['Baths'] ?? __('Baths') }}</span>
                                         </li>
                                     @endif
                                 </ul>
                             </div>
                             @php
                                $symbol = $basicInfo->base_currency_symbol ?? '';
                                $position = $basicInfo->base_currency_symbol_position; 
                                $price = $propertyContent->price ? $propertyContent->price : ($keywords['Negotiable'] ?? __('Negotiable'));
                            
                                if (is_numeric($propertyContent->price)) {
                                    $formattedPrice = $position === 'left' ? $symbol . $price : $price . $symbol;
                                } else {
                                    $formattedPrice = $price;
                                }
                             @endphp
                             <div class="col-md-4">
                                 <div class="product-price mb-10">
                                     <span class="new-price">{{ $keywords['Price'] ?? __('Price') }}: {{ $formattedPrice }}</span>
                                 </div>
                                 @if (!empty($agent))
                                     <a
                                         href="{{ route('frontend.agent.details', [ 'agentusername' => $agent->username]) }}">
                                     @else
                                         <a
                                             href="{{ route('frontend.agent.details', [ 'agentusername' => $user->username, 'admin' => 'true']) }}">
                                 @endif
                                 <div class="user mb-20">
                                     <div class="user-img">

                                         <div class="lazy-container ratio ratio-1-1 rounded-pill">
                                             <img class="lazyload" src="{{ asset('assets/img/blank-user.jpg') }}"
                                                 data-src="@if (!empty($propertyContent->$agent)) {{ $agent->image }}
                                            @elseif(!empty($user))
                                                {{ !empty($user->photo) ? asset($user->photo) : asset('assets/img/blank-user.jpg') }} @endif">


                                         </div>
                                     </div>
                                     <div class="user-info">
                                         <h5 class="m-0">

                                             @if (!empty($agent))
                                                 {{ $agent->agentInfo?->full_name }}
                                             @elseif(!empty($user))
                                                 {{ $user->first_name . ' ' . $user->last_name }}
                                             @endif
                                         </h5>

                                     </div>
                                 </div>
                                 </a>

                                 <ul class="share-link list-unstyled mb-30">
                                     <li>
                                         <a class="btn blue" href="#" data-bs-toggle="modal"
                                             data-bs-target="#socialMediaModal">
                                             <i class="far fa-share-alt"></i>
                                         </a>
                                         <span>{{ $keywords['Share'] ?? __('Share') }}</span>

                                     </li>


                                     <li>
                                         @if (Auth::guard('customer')->check())
                                             @php
                                                 $user_id = Auth::guard('customer')->user()->id;
                                                 $checkWishList = checkWishList($propertyContent->propertyId, $user_id);
                                             @endphp
                                         @else
                                             @php
                                                 $checkWishList = false;
                                             @endphp
                                         @endif
                                         <a href="{{ $checkWishList == false ? route('frontend.user.property.addto.wishlist', [ 'property' => $propertyContent->propertyId]) : route('frontend.user.property.remove.wishlist', [ 'property' => $propertyContent->propertyId]) }}"
                                             class="btn red " data-tooltip="tooltip" data-bs-placement="top"
                                             title="{{ $checkWishList == false ? __('Add to Wishlist') : __('Saved') }}">

                                             @if ($checkWishList == false)
                                                 <i class="fal fa-heart"></i>
                                             @else
                                                 <i class="fas fa-heart"></i>
                                             @endif
                                         </a>
                                         <span>{{ $checkWishList == false ? __('Save') : __('Saved') }}</span>

                                     </li>

                                 </ul>
                             </div>
                         </div>
                         <div class="mb-20"></div>
                         <div class="product-desc mb-40">
                             <h3 class="mb-20">{{ $keywords['Property Description'] ?? __('Property Description') }}</h3>
                             <p class=" summernote-content">{!! $propertyContent->description !!}</p>
                         </div>
                         @if (!empty(showAd(3)))
                             <div class="text-center mb-3 mt-3">
                                 {!! showAd(3) !!}
                             </div>
                         @endif

                         @if (count($propertyContent->propertySpacifications) > 0)
                             <div class="row" class="mb-20">
                                 <div class="col-12">
                                     <h3 class="mb-20"> {{ $keywords['Features'] ?? __('Features') }}</h3>
                                 </div>

                                 @foreach ($propertyContent->propertySpacifications as $specification)
                                     @php

                                         $ps_content = $specification->getContent($language->id);
                                     @endphp

                                     <div class="col-lg-3 col-sm-6 col-md-4 mb-20">
                                         <strong class="mb-1 text-dark d-block">{{ $ps_content?->label }}</strong>
                                         <span>{{ $ps_content?->value }}</span>
                                     </div>
                                 @endforeach
                             </div>
                             <div class="pb-20"></div>
                         @endif

                         <div class="product-featured mb-40">
                             <h3 class="mb-20">{{ $keywords['Amenities'] ?? __('Amenities') }}</h3>
                             <ul class="featured-list list-unstyled p-0 m-0">
                                 @foreach ($amenities as $amenity)
                                     <li class="d-inline-block icon-start">
                                         <i class="{{ $amenity->amenity->icon }}"></i>
                                         <span>{{ $amenity->amenityContent?->name }}</span>
                                     </li>
                                 @endforeach

                             </ul>
                         </div>
                         @if (!empty($propertyContent->video_url))
                             <div class="product-video mb-40">
                                 <h3 class="mb-20"> {{ $keywords['Video'] ?? __('Video') }}</h3>
                                 <div class="lazy-container radius-lg ratio ratio-16-11">
                                     <img class="lazyload" src="{{ asset('assets/front/images/placeholder.png') }}"
                                         data-src="{{ $propertyContent->video_image ? asset('assets/img/property/video/' . $propertyContent->video_image) : asset('assets/front/images/placeholder.png') }}">
                                     <a href="{{ $propertyContent->video_url }}"
                                         class="video-btn youtube-popup p-absolute">
                                         <i class="fas fa-play"></i>
                                     </a>
                                 </div>
                             </div>
                         @endif
                         @if (!empty($propertyContent->floor_planning_image))
                             <div class="product-planning mb-40">
                                 <h3 class="mb-20">{{ $keywords['Floor Planning'] ?? __('Floor Planning') }}</h3>
                                 <div class="lazy-container radius-lg ratio ratio-16-11 border">
                                     <img class="lazyload" src="assets/images/placeholder.png"
                                         data-src="{{ asset('assets/img/property/plannings/' . $propertyContent->floor_planning_image) }}">
                                 </div>
                             </div>
                         @endif
                         @if (!empty($propertyContent->latitude) && !empty($propertyContent->longitude))
                             <div class="product-location mb-40">
                                 <h3 class="mb-20">{{ $keywords['Location'] ?? __('Location') }}</h3>
                                 <div class="lazy-container radius-lg ratio ratio-21-9 border">
                                     <iframe class="lazyload"
                                         src="https://maps.google.com/maps?q={{ $propertyContent->latitude }},{{ $propertyContent->longitude }}&hl={{ $currentLanguageInfo->code }}&z=14&amp;output=embed"></iframe>
                                 </div>
                             </div>
                         @endif
                         @if (!empty(showAd(3)))
                             <div class="text-center mb-3 mt-3">
                                 {!! showAd(3) !!}
                             </div>
                         @endif
                     </div>
                 </div>

                 <div class="col-lg-3 col-xl-3">
                     <aside class="sidebar-widget-area mb-10" data-aos="fade-up">
                         @if ((!empty($agent) && $agent->show_contact_form) || (empty($agent) && !empty($user) && $user->show_contact_form))
                             <div class="widget widget-form radius-md mb-30">
                                 <div class="user mb-20">
                                     <div class="user-img">
                                         <div class="lazy-container ratio ratio-1-1 rounded-pill">
                                             @if (!empty($agent))
                                                 <a
                                                     href="{{ route('frontend.agent.details', [ 'agentusername' => $agent->username]) }}">

                                                     <img class="lazyload" src="{{ asset($agent->image) }}">
                                                 </a>
                                             @else
                                                 <a href="{{ route('frontend.tenant.details') }}">

                                                     <img class="lazyload" src="{{ asset($user->photo) }}">
                                                 </a>
                                             @endif

                                         </div>
                                     </div>
                                     <div class="user-info">
                                         <h4 class="mb-0">
                                             <a @if (!empty($agent)) href="{{ route('frontend.agent.details', [ 'agentusername' => $agent->username]) }}"> {{ $agent->agentInfo?->full_name }}
                                           
                                            @else
                                              href="{{ route('frontend.tenant.details') }}">   {{ $user->first_name . ' ' . $user->last_name }} @endif
                                                 </a>
                                         </h4>
                                         
                                         <a class="d-block"
                                             href="tel:@if (!empty($agent) && $agent->show_phone_number) {{ $agent->phone }}
                                         @elseif (empty($agent) && $user->show_phone_number && !empty($user->phone))
                                            {{ $user->phone }} @endif
                                        ">
                                             @if (!empty($agent) && $agent->show_phone_number)
                                                 {{ $agent->phone }}
                                             @elseif (empty($agent) && $user->show_phone_number && !empty($user->phone))
                                                 {{ $user->phone }}
                                             @endif
                                         </a>
                                         

                                         <a
                                             href="mailto:@if (!empty($agent) && $agent->show_email_addresss) {{ $agent->email }}
                                         @elseif (empty($agent) && $user->show_email_addresss && !empty($user->email))
                                            {{ $user->email }} @endif">
                                             @if (!empty($agent) && $agent->show_email_addresss)
                                                 {{ $agent->email }}
                                             @elseif (empty($agent) && $user->show_email_addresss && !empty($user->email))
                                                 {{ $user->email }}
                                             @endif
                                         </a>
                                     </div>
                                 </div>

                                 <form action="{{ route('frontend.property_contact') }}" method="POST">
                                     @csrf
                                     @if (!empty($agent))
                                         <input type="hidden" name="user_id" value="{{ $agent->user_id }}">
                                         <input type="hidden" name="agent_id"
                                             value="{{ !empty($agent) ? $agent->id : '' }}">
                                     @else
                                         <input type="hidden" name="user_id" value="{{ $user->id }}">
                                     @endif
                                     <input type="hidden" name="property_id"
                                         value="{{ $propertyContent->propertyId }}">

                                     <x-tenant.frontend.agentContact :basicInfo="$basicInfo" />
                                 </form>
                             </div>
                         @endif
                        
                         @if ($relatedProperty->count() > 0)
                             <div class="widget widget-recent radius-md mb-30 ">
                                 <h3 class="title">
                                     <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                         data-bs-target="#products" aria-expanded="true" aria-controls="products">
                                         {{ $keywords['Related Property'] ?? __('Related Property') }}
                                     </button>
                                 </h3>
                                 <div id="products" class="collapse show">
                                     <div class="accordion-body p-0">
                                         @foreach ($relatedProperty as $property)
                                             <div class="product-default product-inline mt-20">
                                                 <figure class="product-img">
                                                     <a href="{{ route('frontend.property.details', $property->slug) }}"
                                                         class="lazy-container ratio ratio-1-1 radius-md">
                                                         <img class="lazyload" src="assets/images/placeholder.png"
                                                             data-src="{{ asset('assets/img/property/featureds/' . $property->featured_image) }}">
                                                     </a>
                                                 </figure>
                                                 <div class="product-details">
                                                     <h6 class="product-title"><a
                                                             href="{{ route('frontend.property.details', ['slug' => $property->slug]) }}">{{ $property->title }}</a>
                                                     </h6>
                                                     <span class="product-location icon-start"> <i
                                                             class="fal fa-map-marker-alt"></i>
                                                         {{ $property->city->getContent($property->language_id)?->name }}
                                                         {{ $property->isStateActive ? ', ' . $property->state?->getContent($property->language_id)?->name : '' }}
                                                         {{ $property->isCountryActive ? ', ' . $property->country?->getContent($property->language_id)?->name : '' }}</span>
                                                     <div class="product-price">

                                                         <span
                                                             class="new-price">{{ ($keywords['Price'] ?? __('Price')) . ':' }}
                                                             {{ $property->price ? $property->price : $keywords['Negotiable'] ?? __('Negotiable') }}</span>
                                                     </div>
                                                     <ul class="product-info p-0 list-unstyled d-flex align-items-center">
                                                         <li class="icon-start" data-tooltip="tooltip"
                                                             data-bs-placement="top"
                                                             title="{{ $keywords['Area'] ?? __('Area') }}">
                                                             <i class="fal fa-vector-square"></i>
                                                             <span>{{ $property->area }}</span>
                                                         </li>
                                                         @if ($property->type == 'residential')
                                                             <li class="icon-start" data-tooltip="tooltip"
                                                                 data-bs-placement="top"
                                                                 title="{{ $keywords['Bed'] ?? __('Bed') }}">
                                                                 <i class="fal fa-bed"></i>
                                                                 <span>{{ $property->beds }} </span>
                                                             </li>
                                                             <li class="icon-start" data-tooltip="tooltip"
                                                                 data-bs-placement="top"
                                                                 title="{{ $keywords['Bath'] ?? __('Bath') }}">
                                                                 <i class="fal fa-bath"></i>
                                                                 <span>{{ $property->bath }} </span>
                                                             </li>
                                                         @endif

                                                     </ul>
                                                 </div>
                                             </div>
                                         @endforeach
                                     </div>
                                 </div>
                             </div>
                         @endif
                         @if (!empty(showAd(2)))
                             <div class="text-center mb-3 mt-3">
                                 {!! showAd(2) !!}
                             </div>
                         @endif
                         @if (!empty(showAd(1)))
                             <div class="text-center mb-3 mt-3">
                                 {!! showAd(1) !!}
                             </div>
                         @endif
                     </aside>
                 </div>

             </div>
         </div>
     </div>

    
     <x-tenant.frontend.social-share />
 @endsection
