@extends('admin.layout')

@if(!empty($blog->language) && $blog->language->rtl == 1)
@section('styles')
<style>
    form input,
    form textarea,
    form select {
        direction: rtl;
    }
    form .note-editor.note-frame .note-editing-area .note-editable {
        direction: rtl;
        text-align: right;
    }
</style>
@push('scripts')

<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">

<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>

<script>

$(document).ready(function() {

    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

    $('.summernote').summernote({ height: 300 });

    

    $('#submitBtn').click(function(e) {

        e.preventDefault();

        const form = $('#ajaxForm')[0];

        const formData = new FormData(form);
        
        // DEBUG: Mostrar todos os dados sendo enviados
        console.log("=== DADOS DO FORMULÁRIO ===");
        for (let pair of formData.entries()) {
            console.log(pair[0] + " = " + pair[1]);
        }
        console.log("=========================");

        

        // Debug: ver o que está sendo enviado
        console.log("FormData contents:");
        for (let pair of formData.entries()) {
            console.log(pair[0] + ": " + pair[1]);
        }
        
        $.ajax({

            url: $(form).attr('action'),

            method: 'POST',

            data: formData,

            processData: false,

            contentType: false,

            beforeSend: function() { $('#submitBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Atualizando...'); },

            success: function(res) { console.log("Resposta:", res); if(res == 'success') location.href = '{{ route("admin.blog", ["language" => request("language")]) }}'; else alert(res); },

            error: function(xhr) { console.log("Erro:", xhr); alert('Erro: ' + xhr.status); $('#submitBtn').prop('disabled', false).html('Atualizar'); }

        });

    });

});

</script>

@endpush
@endsection
@endif

@section('content')
  <div class="page-header">
    <h4 class="page-title">{{__('Edit Blog')}}</h4>
    <ul class="breadcrumbs">
      <li class="nav-home">
        <a href="{{route('admin.dashboard')}}">
          <i class="flaticon-home"></i>
        </a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">{{__('Blog Page')}}</a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">{{__('Edit Blog')}}</a>
      </li>
    </ul>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <div class="card-title d-inline-block">{{__('Edit Blog')}}</div>
          <a class="btn btn-info btn-sm float-right d-inline-block" href="{{route('admin.blog.index') . '?language=' . request()->input('language')}}">
            <span class="btn-label">
              <i class="fas fa-backward"></i>
            </span>
            Back
          </a>
        </div>
        <div class="card-body pt-5 pb-5">
          <div class="row">
            <div class="col-lg-6 offset-lg-3">
              <form id="ajaxForm" class="" action="{{route('admin.blog.update')}}" method="post" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="blog_id" value="{{$blog->id}}">
                <div class="row">
                  <div class="col-lg-6">
                    <div class="form-group">
                      <div class="col-12 mb-2">
                        <label for="image"><strong>{{__('Image')}}</strong></label>
                      </div>
                      <div class="col-md-12 showImage mb-3">
                        <img src="{{$blog->main_image ? asset('assets/front/img/blogs/'.$blog->main_image) : asset('assets/admin/img/noimage.jpg')}}" alt="..." class="img-thumbnail">
                      </div>
                      <input type="file" name="image" id="image" class="form-control">
                      <p id="errimage" class="mb-0 text-danger em"></p>
                      <p class="text-warning mb-0">{{__('Upload 900 * 570 image for best quality')}}</p>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <label for="">{{__('Title')}} <span class="text-danger">{{ '*' }}</span></label>
                  <input type="text" class="form-control" name="title" value="{{$blog->title}}" placeholder="{{__('Enter title')}}">
                  <p id="errtitle" class="mb-0 text-danger em"></p>
                </div>
                <div class="form-group">
                  <label for="">{{__('Category')}} <span class="text-danger">{{ '*' }}</span></label>
                  <select class="form-control" name="category">
                    <option value="" selected disabled>{{__('Select a category')}}</option>
                    @foreach ($bcats as $key => $bcat)
                      <option value="{{$bcat->id}}" {{$bcat->id == $blog->bcategory->id ? 'selected' : ''}}>{{$bcat->name}}</option>
                    @endforeach
                  </select>
                  <p id="errcategory" class="mb-0 text-danger em"></p>
                </div>
                <div class="form-group">
                  <label for="">{{__('Content')}} <span class="text-danger">{{ '*' }}</span></label>
                  <textarea class="form-control summernote" name="content" data-height="300" placeholder="{{__('Enter content')}}">{{ replaceBaseUrl($blog->content) }}</textarea>
                  <p id="errcontent" class="mb-0 text-danger em"></p>
                </div>

                <div class="form-group">
                  <label for="">{{__('Serial Number')}} <span class="text-danger">{{ '*' }}</span></label>
                  <input type="number" class="form-control " name="serial_number" value="{{$blog->serial_number}}" placeholder="{{__('Enter Serial Number')}}">
                  <p id="errserial_number" class="mb-0 text-danger em"></p>
                  <p class="text-warning"><small>{{__('The higher the serial number is, the later will be shown')}}</small></p>
                </div>
                <div class="form-group">
                  <label for="">{{__('Meta Keywords')}}</label>
                  <input type="text" class="form-control" name="meta_keywords" value="{{$blog->meta_keywords}}" data-role="tagsinput">
                  <p id="errmeta_keywords" class="mb-0 text-danger em"></p>
                </div>
                <div class="form-group">
                  <label for="">{{__('Meta Description')}}</label>
                  <textarea type="text" class="form-control" name="meta_description" rows="5">{{$blog->meta_description}}</textarea>
                  <p id="errmeta_description" class="mb-0 text-danger em"></p>
                </div>
              </form>
            </div>
          </div>
        </div>
        <div class="card-footer">
          <div class="form">
            <div class="form-group from-show-notify row">
              <div class="col-12 text-center">
                <button type="submit" id="submitBtn" class="btn btn-success">{{__('Update')}}</button>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>

@push('scripts')

<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">

<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>

<script>

$(document).ready(function() {

    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

    $('.summernote').summernote({ height: 300 });

    

    $('#submitBtn').click(function(e) {

        e.preventDefault();

        const form = $('#ajaxForm')[0];

        const formData = new FormData(form);
        
        // DEBUG: Mostrar todos os dados sendo enviados
        console.log("=== DADOS DO FORMULÁRIO ===");
        for (let pair of formData.entries()) {
            console.log(pair[0] + " = " + pair[1]);
        }
        console.log("=========================");

        

        // Debug: ver o que está sendo enviado
        console.log("FormData contents:");
        for (let pair of formData.entries()) {
            console.log(pair[0] + ": " + pair[1]);
        }
        
        $.ajax({

            url: $(form).attr('action'),

            method: 'POST',

            data: formData,

            processData: false,

            contentType: false,

            beforeSend: function() { $('#submitBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Atualizando...'); },

            success: function(res) { 
                console.log("Resposta:", res); 
                console.log("Tipo:", typeof res);
                
                if(typeof res === 'object') {
                    console.log("Erros de validação:", res);
                    let errorMsg = 'Erros de validação:\n';
                    for(let field in res) {
                        errorMsg += field + ': ' + res[field] + '\n';
                    }
                    alert(errorMsg);
                    $('#submitBtn').prop('disabled', false).html('Atualizar');
                } else if(res == 'success') {
                    location.href = '{{ route("admin.blog.index", ["language" => request("language")]) }}';
                } else {
                    alert('Resposta inesperada: ' + res);
                    $('#submitBtn').prop('disabled', false).html('Atualizar');
                }
            },

            error: function(xhr) { console.log("Erro:", xhr); alert('Erro: ' + xhr.status); $('#submitBtn').prop('disabled', false).html('Atualizar'); }

        });

    });

});

</script>

@endpush
@endsection
