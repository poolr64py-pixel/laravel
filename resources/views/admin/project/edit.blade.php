@extends('admin.layout')

@section('content')
<div class="page-header">
    <h4 class="page-title">{{ __('Edit Project') }}</h4>
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
            <a href="{{ route('admin.project.index') }}">{{ __('Projects') }}</a>
        </li>
        <li class="separator">
            <i class="flaticon-right-arrow"></i>
        </li>
        <li class="nav-item">
            <a href="#">{{ __('Edit Project') }}</a>
        </li>
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="card-title">{{ __('Edit Project') }} #{{ $project->id }}</div>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.project.update', ['id' => $project->id]) }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <h3>{{ __('Basic Information') }}</h3>
                    <hr>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('Min Price') }} (USD)</label>
                                <input type="number" step="0.01" name="min_price" class="form-control" value="{{ old('min_price', $project->min_price) }}">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('Max Price') }} (USD)</label>
                                <input type="text" name="max_price" class="form-control" value="{{ old('max_price', $project->max_price) }}" pattern="[0-9.,]+" placeholder="Ex: 132997 ou 132997.00">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('Latitude') }}</label>
                                <input type="text" name="latitude" class="form-control" value="{{ old('latitude', $project->latitude) }}">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('Longitude') }}</label>
                                <input type="text" name="longitude" class="form-control" value="{{ old('longitude', $project->longitude) }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __("Tour Virtual URL") }} <span class="text-muted">({{ __("opcional") }})</span></label>
                                <input type="url" class="form-control" name="virtual_tour_url"
                                       placeholder="https://exemplo.com/tour"
                                       value="{{ old('virtual_tour_url', $project->virtual_tour_url) }}">
                                <small class="form-text text-muted">{{ __("Cole a URL completa do tour virtual (ex: Matterport, Kuula, etc)") }}</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('Featured Image') }}</label>
                                @if($project->featured_image)
                                    <div class="mb-2">
                                        <img src="{{ asset('assets/img/projects/' . $project->featured_image) }}" width="200" class="img-thumbnail">
                                    </div>
                                @endif
                                <input type="file" name="featured_image" class="form-control" accept="image/*">
                                <small class="text-muted">{{ __('Leave empty to keep current image') }}</small>
                            </div>
                        </div>
                                         
                               {{-- GALERIA --}}
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>{{ __('Gallery Images') }} ({{ __('Add More') }})</label>
                                <input type="file" name="gallery_images[]" class="form-control" accept="image/*" multiple>
                                <small class="text-muted">Max: 5MB cada</small>
                            </div>
                        </div>
                    </div>
                    
                    {{-- GALERIA EXISTENTE --}}
                    @if(isset($project->sliderImages) && count($project->sliderImages) > 0)
                    <div class="row">
                        <div class="col-md-12">
                            <label>{{ __('Current Gallery') }}</label>
                            <div class="row">
                                @foreach($project->sliderImages as $img)
                                <div class="col-md-2 mb-3">
                                    <img src="{{ asset('assets/img/projects/gallery/' . $img->image) }}" class="img-thumbnail" style="width: 100%;">
                                    <button type="button" class="btn btn-danger btn-sm btn-block mt-1" onclick="deleteGalleryImage({{ $img->id }})">
                                        <i class="fas fa-trash"></i> {{ __('Delete') }}
                                    </button>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('Project Status') }}</label>
                                <select name="complete_status" class="form-control">
                                    <option value="0" {{ $project->complete_status == 0 ? 'selected' : '' }}>{{ __('Planning') }}</option>
                                    <option value="1" {{ $project->complete_status == 1 ? 'selected' : '' }}>{{ __('Under Construction') }}</option>
                                    <option value="2" {{ $project->complete_status == 2 ? 'selected' : '' }}>{{ __('Completed') }}</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="featured" name="featured" value="1" {{ $project->featured ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="featured">{{ __('Featured Project') }}</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <h3 class="mt-4">{{ __('Content (Multi-language)') }}</h3>
                    <hr>

                    <ul class="nav nav-tabs" role="tablist">
                        @foreach($langs as $index => $lang)
                            <li class="nav-item">
                                <a class="nav-link {{ $index == 0 ? 'active' : '' }}" data-toggle="tab" href="#lang{{ $lang->id }}" role="tab">
                                    {{ $lang->name }}
                                </a>
                            </li>
                        @endforeach
                    </ul>

                    <div class="tab-content mt-3">
                        @foreach($langs as $index => $lang)
                            @php
                                $content = $project->contents->where('language_id', $lang->id)->first();
                            @endphp
                            <div class="tab-pane fade {{ $index == 0 ? 'show active' : '' }}" id="lang{{ $lang->id }}" role="tabpanel">
                                <div class="form-group">
                                    <label>{{ __('Title') }} ({{ $lang->code }})</label>
                                    <input type="text" name="title_{{ $lang->code }}" class="form-control" 
                                           value="{{ old('title_' . $lang->code, $content->title ?? '') }}">
                                </div>

                                <div class="form-group">
                                    <label>{{ __('Address') }} ({{ $lang->code }})</label>
                                    <input type="text" name="address_{{ $lang->code }}" class="form-control" 
                                           value="{{ old('address_' . $lang->code, $content->address ?? '') }}">
                                </div>

                                <div class="form-group">
                                    <label>{{ __('Description') }} ({{ $lang->code }})</label>
                                     <textarea name="description_{{ $lang->code }}" class="form-control summernote" rows="5">{{ old('description_' . $lang->code, $content->description ?? '') }}</textarea>
                                </div>

                                <div class="form-group">
                                    <label>{{ __('Meta Keywords') }} ({{ $lang->code }})</label>
                                    <input type="text" name="meta_keyword_{{ $lang->code }}" class="form-control" 
                                           value="{{ old('meta_keyword_' . $lang->code, $content->meta_keyword ?? '') }}">
                                </div>

                                <div class="form-group">
                                    <label>{{ __('Meta Description') }} ({{ $lang->code }})</label>
                                    <textarea name="meta_description_{{ $lang->code }}" class="form-control" rows="3">{{ old('meta_description_' . $lang->code, $content->meta_description ?? '') }}</textarea>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="form-group mt-4">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> {{ __('Update Project') }}
                        </button>
                        <a href="{{ route('admin.project.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> {{ __('Cancel') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
