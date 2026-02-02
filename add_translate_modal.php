<?php
$file = 'resources/views/admin/blog/blog/index.blade.php';
$content = file_get_contents($file);

// Substituir o comentário pelo modal completo
$modal = <<<'MODAL'
<!-- Modal de Tradução -->
<div class="modal fade" id="translateModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Translate Blog') }}</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="translateForm" action="{{ route('admin.blog.translate') }}" method="POST">
                    @csrf
                    <input type="hidden" id="source_blog_id" name="source_blog_id">
                    
                    <div class="alert alert-info">
                        <strong id="sourceBlogTitle"></strong>
                    </div>
                    
                    <div class="form-group">
                        <label>{{ __('Target Language') }} <span class="text-danger">*</span></label>
                        <select name="target_language" class="form-control" required>
                            <option value="">{{ __('Select Language') }}</option>
                            @foreach($langs as $lang)
                                @if($lang->id != $lang_id)
                                <option value="{{ $lang->id }}">{{ $lang->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="alert alert-warning">
                        <i class="fas fa-info-circle"></i> {{ __('This will create a copy of the blog in the selected language. You will need to manually translate the content after creation.') }}
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
                <button type="button" class="btn btn-primary" onclick="submitTranslation()">{{ __('Create Translation') }}</button>
            </div>
        </div>
    </div>
</div>

<script>
function openTranslateModal(blogId, blogTitle) {
    $('#source_blog_id').val(blogId);
    $('#sourceBlogTitle').text('Original: ' + blogTitle);
    $('#translateModal').modal('show');
}

function submitTranslation() {
    var form = $('#translateForm');
    var formData = form.serialize();
    
    $.ajax({
        url: form.attr('action'),
        type: 'POST',
        data: formData,
        success: function(response) {
            if(response.success) {
                alert(response.message || 'Translation created successfully! Please edit to add translated content.');
                location.reload();
            } else {
                alert(response.message || 'Error creating translation');
            }
        },
        error: function(xhr) {
            var error = xhr.responseJSON?.message || 'Error creating translation';
            alert(error);
        }
    });
}
</script>
MODAL;

$content = str_replace('<!-- Modal de Tradução -->', $modal, $content);
file_put_contents($file, $content);

echo "✅ Modal de tradução adicionado!\n";
