@extends('admin.layout')

@section('content')
<div class="page-header">
    <h4 class="page-title">{{ __('Manage Properties') }}</h4>
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
            <a href="#">{{ __('Properties') }}</a>
        </li>
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-lg-4">
                        <div class="card-title d-inline-block">{{ __('Properties') }}</div>
                    </div>
                    <div class="col-lg-4">
                        @if (!empty($langs))
                            <select name="language" class="form-control" onchange="window.location='{{ url()->current() . '?language=' }}'+this.value">
                                <option selected disabled>{{ __('Select a Language') }}</option>
                                @foreach ($langs as $lang)
                                    <option value="{{ $lang->code }}" {{ $lang->code == request('language', $currentLang->code ?? 'en') ? 'selected' : '' }}>
                                        {{ $lang->name }}
                                    </option>
                                @endforeach
                            </select>
                        @endif
                    </div>
                    <div class="col-lg-4">
        <!-- DEPOIS -->
<a href="{{ route('admin.property.create') }}"
   class="btn btn-success btn-sm float-right">
    <i class="fas fa-plus"></i> {{ __('Add New Property') }}
</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-12">
                        @if (count($properties) == 0)
                            <h3 class="text-center">{{ __('NO PROPERTY FOUND') }}</h3>
                        @else
                            <div class="table-responsive">
                                <table class="table table-striped mt-3">
                                    <thead>
                                        <tr>
                                            <th scope="col">
                                                <input type="checkbox" class="bulk-check" data-val="all">
                                            </th>
                                            <th scope="col">{{ __('Image') }}</th>
                                            <th scope="col">{{ __('Title') }}</th>
                                            <th scope="col">{{ __('Price') }}</th>
                                            <th scope="col">{{ __('Type') }}</th>
                                            <th scope="col">{{ __('Purpose') }}</th>
                                            <th scope="col">{{ __('Em Destaque') }}</th>
                                            <th scope="col">{{ __('Status') }}</th>
                                            <th scope="col">{{ __('Approved') }}</th>
                                            <th scope="col">{{ __('Actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($properties as $property)
                                            @php
                                                $content = $property->contents->first();
                                            @endphp
                                            <tr>
                                                <td>
                                                    <input type="checkbox" class="bulk-check" data-val="{{ $property->id }}">
                                                </td>
                                                <td>
                                                    @if($property->featured_image)
                                                        <img src="{{ asset('assets/img/property/featureds/' . $property->featured_image) }}" 
                                                             alt="property" width="80">
                                                    @endif
                                                </td>
                                                <td>{{ $content->title ?? 'N/A' }}</td>
                                                <td>
    @php
    $currency = $property->currency ?? 'USD';
    echo match($currency) {
        'BRL' => 'R$ ' . number_format($property->price, 2, ',', '.'),
        'PYG' => 'Gs. ' . number_format($property->price, 0, '.', '.'),
        default => 'US$ ' . number_format($property->price, 0, ',', '.')
    };
    @endphp
</td>
                                                <td>{{ ucfirst($property->type) }}</td>
                                                <td>{{ ucfirst($property->purpose) }}</td>
                                                <td>
                                                    <form class="d-inline-block" action="{{ route('admin.property.update_featured') }}" method="post">
                                                        @csrf
                                                        <input type="hidden" name="property_id" value="{{ $property->id }}">
                                                        <select name="featured" class="form-control form-control-sm" onchange="this.form.submit()">
                                                            <option value="1" {{ $property->featured == 1 ? 'selected' : '' }}>{{ __('Yes') }}</option>
                                                            <option value="0" {{ $property->featured == 0 ? 'selected' : '' }}>{{ __('No') }}</option>
                                                        </select>
                                                    </form>
                                                </td>
                                                <td>
                                                    <form class="d-inline-block" action="{{ route('admin.property.update_status') }}" method="post">
                                                        @csrf
                                                        <input type="hidden" name="property_id" value="{{ $property->id }}">
                                                        <select name="status" class="form-control form-control-sm" onchange="this.form.submit()">
                                                            <option value="1" {{ $property->status == 1 ? 'selected' : '' }}>{{ __('Active') }}</option>
                                                            <option value="0" {{ $property->status == 0 ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                                                        </select>
                                                    </form>
                                                   <td>
    <form class="d-inline-block" action="{{ route('admin.property.update_approve') }}" method="post">
        @csrf
        <input type="hidden" name="property_id" value="{{ $property->id }}">
        <select name="approve_status" class="form-control form-control-sm" onchange="this.form.submit()">
            <option value="1" {{ $property->approve_status == 1 ? 'selected' : '' }}>{{ __('Approved') }}</option>
            <option value="0" {{ $property->approve_status == 0 ? 'selected' : '' }}>{{ __('Pending') }}</option>
        </select>
    </form>
</td>
                                                </td>
                                                <td>
                                                 <!-- DEPOIS -->
<a class="btn btn-secondary btn-sm"
   href="{{ route('admin.property.edit', ['id' => $property->id]) . '?language=' . request('language', 'pt') }}">
    <i class="fas fa-edit"></i>
</a>                                                    
                                                    <form class="deleteform d-inline-block" action="{{ route('admin.property.delete') }}" method="post">
                                                        @csrf
                                                        <input type="hidden" name="property_id" value="{{ $property->id }}">
                                                        <button type="submit" class="btn btn-danger btn-sm deletebtn">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="row">
                    <div class="col-12">
                        {{ $properties->appends(['language' => request('language')])->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
