@extends('admin.layout')

@section('content')
<div class="page-header">
    <h4 class="page-title">{{ __('Criar Blog') }}</h4>
    <ul class="breadcrumbs">
        <li class="nav-home"><a href="{{ route('admin.dashboard') }}"><i class="flaticon-home"></i></a></li>
        <li class="separator"><i class="flaticon-right-arrow"></i></li>
        <li class="nav-item"><a href="{{ route('admin.blog.index', ['language' => request('language')]) }}">{{ __('Blogs') }}</a></li>
        <li class="separator"><i class="flaticon-right-arrow"></i></li>
        <li class="nav-item">{{ __('Criar Blog') }}</li>
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="card-title">{{ __('Criar Blog') }}</div>
                <a class="btn btn-info btn-sm float-right" href="{{ route('admin.blog.index', ['language' => request('language')]) }}">
                    <i class="fas fa-backward"></i> {{ __('Voltar') }}
                </a>
            </div>
            <form id="createBlogForm" action="{{ route('admin.blog.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="language" value="{{ request('language') }}">
                
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>{{ __('Título') }} **</label>
                                <input type="text" class="form-control" name="title" maxlength="255" required>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>{{ __('Categoria') }} **</label>
                                <select class="form-control" name="category" required>
                                    <option value="">{{ __('Selecionar') }}</option>
                                    @foreach ($bcats as $bcat)
                                        <option value="{{ $bcat->id }}">{{ $bcat->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>{{ __('Conteúdo') }} **</label>
                                <textarea id="contentEditor" class="form-control" name="content"></textarea>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>{{ __('Número de Série') }} **</label>
                                <input type="number" class="form-control" name="serial_number" value="1" required>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>{{ __('Imagem') }}</label>
                                <input type="file" class="form-control" name="image" accept="image/jpeg,image/png,image/jpg">
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>{{ __('Meta Keywords') }}</label>
                                <input type="text" class="form-control" name="meta_keywords" data-role="tagsinput">
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>{{ __('Meta Descrição') }}</label>
                                <textarea class="form-control" name="meta_description" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer text-center">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> {{ __('Salvar') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
<script>
$(document).ready(function() {
    $('#contentEditor').summernote({ 
        height: 300,
        callbacks: {
            onChange: function(contents, $editable) {
                $('#contentEditor').val(contents);
            }
        }
    });

    $('#createBlogForm').on('submit', function(e) {
        var content = $('#contentEditor').summernote('code');
        $('#contentEditor').val(content);
    });
});
</script>
@endpush
