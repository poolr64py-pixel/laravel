@extends('admin.layout')

@section('content')
<div class="page-header">
    <h4 class="page-title">{{ __('Manage Projects') }}</h4>
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
            <a href="#">{{ __('Projects') }}</a>
        </li>
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-lg-4">
                        <div class="card-title d-inline-block">{{ __('Projects') }}</div>
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
                        <a href="{{ route('admin.project.create') }}" 
                           target="_blank" 
                           class="btn btn-success btn-sm float-right">
                            <i class="fas fa-plus"></i> {{ __('Add New Project') }}
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-12">
                        @if (count($projects) == 0)
                            <h3 class="text-center">{{ __('NO PROJECT FOUND') }}</h3>
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
                                            <th scope="col">{{ __('Status') }}</th>
                                            <th scope="col">{{ __('Actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($projects as $project)
                                            @php
                                                $content = $project->contents->first();
                                            @endphp
                                            <tr>
                                                <td>
                                                    <input type="checkbox" class="bulk-check" data-val="{{ $project->id }}">
                                                </td>
                                                <td>
                                                    @if($project->featured_img)
                                                        <img src="{{ asset('assets/img/projects/' . $project->featured_img) }}" 
                                                             alt="project" width="80">
                                                    @endif
                                                </td>
                                                <td>{{ $content->title ?? 'N/A' }}</td>
                                                <td>
                                                    <form class="d-inline-block" action="{{ route('admin.project.update_status') }}" method="post">
                                                        @csrf
                                                        <input type="hidden" name="project_id" value="{{ $project->id }}">
                                                        <select name="status" class="form-control form-control-sm" onchange="this.form.submit()">
                                                            <option value="1" {{ $project->status == 1 ? 'selected' : '' }}>{{ __('Active') }}</option>
                                                            <option value="0" {{ $project->status == 0 ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                                                        </select>
                                                    </form>
                                                </td>
                                                <td>
                                                    <a class="btn btn-secondary btn-sm" 
                                                       href="{{ route('admin.project.edit', ['id' => $project->id]) }}" 
                                                       target="_blank">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    
                                                    <form class="deleteform d-inline-block" action="{{ route('admin.project.delete') }}" method="post">
                                                        @csrf
                                                        <input type="hidden" name="project_id" value="{{ $project->id }}">
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
                        {{ $projects->appends(['language' => request('language')])->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
