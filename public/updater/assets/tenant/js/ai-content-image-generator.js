"use strict"
$(document).ready(function () {

  // ----AI Content Modal Logic -----
  $('#aiContentModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var fieldType = button.data('field-type');

    // Sync common form values to the modal
    $('select[name="ai_purpose"]').val($('select[name="purpose"]').val());
    $('select[name="ai_category_id"]').val($('select[name="category_id"]').val());
    $('select[name="ai_country_id"]').val($('select[name="country_id"]').val());
    $('input[name="ai_area"]').val($('input[name="area"]').val());
    var amenities = $('select[name="amenities[]"]').val();
    $('select[name="ai_amenities[]"]').val(amenities).trigger('change');

    if (fieldType) {
      var langCode = button.data('lang-code');
      var langName = button.data('lang-name');
      var fieldTitle = fieldType.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());

      if (fieldTitle == 'Title') {
        fieldTitle = titleText;
      } else if (fieldTitle == 'Description') {
        fieldTitle = descriptitonText;
      } else if (fieldTitle == 'Meta Keyword') {
        fieldTitle = metaKeywordText;
      } else if (fieldTitle == 'Meta Description') {
        fieldTitle = metaDescriptionText;
      }

      $('#aiContentModalLabel').text(generateText + ' ' + fieldTitle);

      $('#single-language-display').text(langName);
      $('#single-language-container').removeClass('d-none');
      $('#all-languages-container').addClass('d-none');

      $('#ai-lang-code').val(langCode);
      $('#ai-field-type').val(fieldType);

    } else {
      $('#aiContentModalLabel').text(generateContentWithAi);

      $('#all-languages-container').removeClass('d-none');
      $('#single-language-container').addClass('d-none');

      $('#ai-lang-code').val('');
      $('#ai-field-type').val('');
    }
  });

  $(document).on('submit', '#aiContentForm', function (e) {
    e.preventDefault();

    let form = $(this);
    let url = form.attr('action');
    let submitBtn = form.find('#submitAiForm');

    let formData = new FormData();
    formData.append('_token', form.find('input[name="_token"]').val());

    formData.append('ai_property_type', form.find('select[name="ai_property_type"]').val());
    formData.append('ai_purpose', form.find('select[name="ai_purpose"]').val());
    formData.append('ai_content_prompt', form.find('input[name="ai_content_prompt"]').val());
    formData.append('ai_area', form.find('input[name="ai_area"]').val());

    let categoryName = form.find('select[name="ai_category_id"] option:selected').text().trim();
    let countryName = form.find('select[name="ai_country_id"] option:selected').text().trim();
    formData.append('ai_category_name', categoryName);
    formData.append('ai_country_name', countryName);

    let amenityNames = form.find('select[name="ai_amenities[]"] option:selected').map(
      function () {
        return $(this).text().trim();
      }).get();

    for (let i = 0; i < amenityNames.length; i++) {
      formData.append('ai_amenities_names[]', amenityNames[i]);
    }

    let fieldType = $('#ai-field-type').val();
    formData.append('field_type', fieldType);

    if (fieldType) {
      formData.append('lang_code', $('#ai-lang-code').val());
    } else {
      form.find('input[name="ai_language[]"]:checked').each(function () {
        formData.append('ai_language[]', $(this).val());
      });
    }

    $.ajax({
      url: url,
      type: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      beforeSend: function () {

        $('.request-loader').addClass('show');
      },
      success: function (response) {
        $('.request-loader').removeClass('show');

        if (response.status === 'success') {

          let generatedContent = response.generated_content;
          let fieldType = response.field_type;
          // ==========================================================
          //  START: AI Overload/Error Message Handling
          // ==========================================================
          let errorMessage = null;
          if (generatedContent && typeof generatedContent === 'object') {
            for (const langCode in generatedContent) {
              if (Object.hasOwnProperty.call(generatedContent, langCode) && generatedContent[langCode]?.error) {
                errorMessage = generatedContent[langCode].error.message || 'An unknown AI error occurred.';
                break; // Stop checking after finding the first error
              }
            }
          }

          if (errorMessage) {
            // If an error message was found, display it and stop
            var content = {};
            content.message = errorMessage;
            content.title = 'AI Error';
            content.icon = 'fa fa-exclamation-triangle';
            $.notify(content, {
              type: 'danger',
              placement: {
                from: 'top',
                align: 'right'
              },
              showProgressbar: true,
              time: 1000,
              delay: 5000,
            });
            $('#aiContentModal').modal('hide'); 
            return; 
          }
          // ==========================================================
          //  END: AI Overload/Error Message Handling
          // ==========================================================


          // If no error, proceed with success notification and field updates
          var successContent = {};
          successContent.message = response.message;
          successContent.title = successText;
          successContent.icon = 'fa fa-check';
          $.notify(successContent, {
            type: 'success',
            placement: {
              from: 'top',
              align: 'right'
            },
            showProgressbar: true,
            time: 1000,
            delay: 4000,
          });

          // Loop through each language in the response 
          for (const langCode in generatedContent) {
            if (Object.hasOwnProperty.call(generatedContent, langCode)) {
              const content = generatedContent[langCode];

              // If it's a single-field generation, only update that one
              if (fieldType) {
                if (content.hasOwnProperty(fieldType)) {
                  let targetField = $(
                    `[name="${langCode}_${fieldType}"]`);
                  let editorId = `${langCode}_description`;
                  if (targetField.hasClass('summernote') && tinymce
                    .get(editorId)) {
                    tinymce.get(editorId).setContent(content[
                      fieldType]);
                  } else if (targetField.data('role') ===
                    'tagsinput') {
                    targetField.tagsinput('removeAll');
                    targetField.tagsinput('add', content[
                      fieldType]);
                  } else {
                    targetField.val(content[fieldType]);
                  }
                }
              } else {
                // It's a full generation, update all fields
                $(`[name="${langCode}_title"]`).val(content.title);
                $(`textarea[name="${langCode}_meta_description"]`).val(
                  content.meta_description);

                // Handle tinymce editor
                let editorId = `${langCode}_description`;
                if (tinymce.get(editorId)) {
                  tinymce.get(editorId).setContent(content
                    .description);
                }

                // Handle tagsinput for meta keywords
                let keywordsInput = $(
                  `input[name="${langCode}_meta_keyword"]`);
                keywordsInput.tagsinput('removeAll');
                keywordsInput.tagsinput('add', content.meta_keyword);
              }
            }
          }
          $('#aiContentModal').modal('hide');
        } else {
          $('.request-loader').removeClass('show');
           $('#aiContentModal').modal('hide');
  
          var content = {};
          content.message = response.message ||
            'A network error occurred. Please try again.';
          content.title = errorText;
          content.icon = 'fa fa-times';
          $.notify(content, {
            type: 'danger',
            placement: {
              from: 'top',
              align: position
            },
            showProgressbar: true,
            time: 1000,
            delay: 4000,
          });

        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
        $('.request-loader').removeClass('show');
        $('#aiContentModal').modal('hide');

        // Default message
        let message = 'A network error occurred. Please try again.';

        // If server returns JSON with message, use that
        if (jqXHR.responseJSON && jqXHR.responseJSON.message) {
          message = jqXHR.responseJSON.message;
        } else if (jqXHR.responseText) {
          // Fallback: if plain text
          message = jqXHR.responseText;
        } else if (textStatus) {
          message = textStatus;
        }

        var content = {};
        content.message = message;
        content.title = errorText;
        content.icon = 'fa fa-times';

        $.notify(content, {
          type: 'danger',
          placement: {
            from: 'top',
            align: 'right' 
          },
          showProgressbar: true,
          time: 1000,
          delay: 4000,
        });
      },
      complete: function () {
        $('.request-loader').removeClass('show');
        $('#ai-loader').hide();
      }
    });
  });

});
// ----- END: AI Content Modal Logic -----

// Wait for external Dropzone to initialize
$(document).ready(function () {
  'use strict';
  setTimeout(initAiImageGeneration, 1000);
});

function initAiImageGeneration() {
  // Global Variables
  let currentImageType = null;
  let generatedImageUrls = [];
  let generatedCount = 0;

  // Get Existing Dropzone Instance by ID
  function getDropzoneInstance(dropzoneId = 'my-dropzone') {
    const dropzoneElement = document.getElementById(dropzoneId);

    if (dropzoneElement && dropzoneElement.dropzone) {
      return dropzoneElement.dropzone;
    }

    if (typeof Dropzone !== 'undefined' && Dropzone.instances.length > 0) {
      for (let i = 0; i < Dropzone.instances.length; i++) {
        if (Dropzone.instances[i].element.id === dropzoneId) {
          return Dropzone.instances[i];
        }
      }
    }

    return null;
  }

  // Add AI Image to Specific Dropzone (Fixed)
  window.addAiImageToDropzone = function (imageUrl, imagePath, dropzoneId = 'my-dropzone') {
    const myDropzone = getDropzoneInstance(dropzoneId);

    if (!myDropzone) {
      showNotification(
        'Gallery upload system not ready. Please refresh the page.',
        'Error',
        'danger',
        'fa fa-exclamation-triangle'
      );
      return;
    }

    const fileName = imagePath.split('/').pop();
    const mockFile = {
      name: fileName,
      size: 12345,
      type: 'image/png',
      accepted: true,
      status: Dropzone.ADDED,
      upload: {
        progress: 100,
        total: 12345,
        bytesSent: 12345
      },
      dataURL: imageUrl
    };

    // Add file to dropzone
    myDropzone.emit("addedfile", mockFile);
    myDropzone.emit("thumbnail", mockFile, imageUrl);
    myDropzone.emit("complete", mockFile);
    mockFile.status = Dropzone.SUCCESS;

    // Use existing containers with correct names
    let inputName, inputClass, containerSelector;

    if (dropzoneId === 'my-dropzone2') {
      // Floor Planning Gallery
      inputName = 'ai_floorplanning_gallery_images[]';
      inputClass = 'ai-floorplan-gallery-input';
      containerSelector = '#floorPlan';
    } else {
      // Regular Gallery (my-dropzone)
      inputName = 'ai_gallery_images[]';
      inputClass = 'ai-gallery-input-stored';
      containerSelector = '#sliders';
    }

    // Add hidden input for form submission
    $(containerSelector).append(
      `<input type="hidden" name="${inputName}" class="${inputClass}" data-path="${imagePath}" data-dropzone="${dropzoneId}" value="${imagePath}">`
    );

    // Create remove button
    const removeButton = Dropzone.createElement(
      "<button class='rmv-btn'><i class='fa fa-times'></i></button>"
    );

    removeButton.addEventListener("click", function (e) {
      e.preventDefault();
      e.stopPropagation();

      // Remove from dropzone UI
      myDropzone.removeFile(mockFile);

      // Remove only the matching hidden input
      $(`input.${inputClass}[data-path="${imagePath}"][data-dropzone="${dropzoneId}"]`).remove();
    });

    mockFile.previewElement.appendChild(removeButton);
  };

  // ---- Show AI Modal ---
  $('#aiImageModal').on('show.bs.modal', function (event) {
    generatedCount = 0;
    const button = $(event.relatedTarget);
    const imageType = button.data('image-type');

    currentImageType = imageType;
    
    const fieldName = button.data('field-name');
    const modal = $(this);

    

    if (fieldName) {

      let displayName = fieldName;

      if (fieldName == 'floor_planning_gallery') {
        displayName = floor_planning_gallery;
      } else if (fieldName == 'Gallery') {
        displayName = galleryTxt;
      } else if (fieldName == 'Thumbnail') {
        displayName = thumbnailTxt;
      } else if (fieldName == 'Video Poster') {
        displayName = videoPosterTxt;
      } else if (fieldName == 'Floor Plan') {
        displayName = floorPlanTxt;
      }
      modal.find('.modal-title').text(generateText + ' ' + displayName + ' ' + imageTxt);
    } else {
      modal.find('.modal-title').text(generateImageWithAi);
    }

    if (imageType === 'gallery' || imageType === 'floor_planning_gallery') {
      $('#num_images_field_container').show();
    } else {
      $('#num_images_field_container').hide();
      $('#num_images').val('1');
    }

    $('#image_prompt').val('');
  });

  // ======= Generate Images (AJAX) ======
  let ajaxRequest = null;
  let timerInterval = null;
  let progressInterval = null;
  let progress = 0;
  let elapsedTime = 0;
  let isCancelled = false;

  $(document).on('click', '#aiImageModal .modal-footer .btn-primary', function (e) {
    e.preventDefault();

    const prompt = $('#image_prompt').val().trim();

    if (!prompt) {
      showNotification(
        validationErrorText,
        'Validation Error',
        'warning',
        'fa fa-exclamation-triangle'
      );
      return;
    }

    const numImages = parseInt($('#num_images').val());

    const formData = {
      _token: $('meta[name="csrf-token"]').attr('content'),
      image_prompt: prompt,
      art_style: $('#art_style').val(),
      lighting: $('#lighting').val(),
      camera_angle: $('#camera_angle').val(),
      image_size: $('#image_size').val(),
      num_images: numImages,
      image_type: currentImageType
    };

    ajaxRequest = $.ajax({
      url: generateImageUrl,
      type: 'POST',
      data: formData,
      timeout: 180000,

      beforeSend: function () {
        $('#aiImageModal').modal('hide');
        $('#ai-loader').show();

        $('#ai-total-images').text(numImages);
        $('#ai-image-counter').text('0');
        $('#ai-loader-status').text(initializingText);
        $('#ai-loader-time').text('~0s');
        $('.ai-loader-progress-fill').css('width', '0%');

        elapsedTime = 0;
        progress = 0;
        isCancelled = false;

        timerInterval = setInterval(() => {
          elapsedTime++;
          $('#ai-loader-time').text(`~${elapsedTime}s`);
        }, 1000);

        progressInterval = setInterval(() => {
          if (progress < 90) {
            progress += Math.random() * 8;
            $('.ai-loader-progress-fill').css('width', `${Math.min(progress, 90)}%`);
          }
        }, 800);
      },

      success: function (response) {
        if (isCancelled) {
          isCancelled = false;
          return;
        }

        if (response.status === 'success') {
          generatedCount = response.images.length;
          $('#ai-image-counter').text(generatedCount);
          $('#ai-loader-status').text(finalizingText);
          $('.ai-loader-progress-fill').css('width', '100%');

          setTimeout(() => {
            hideLoader();
            showNotification(response.message, successText, 'success', 'fa fa-check');
            generatedImageUrls = response.images;
            displayGeneratedImages(response.images, response.image_type);
          }, 600);
        } else {
          throw new Error(response.message || 'Failed to generate images.');
        }
      },

      error: function (jqXHR, textStatus) {
        if (textStatus === 'abort') {
          showNotification('Image generation cancelled.', 'Cancelled', 'info', 'fa fa-info-circle');
        } else {
          const msg = jqXHR.responseJSON?.message || 'Generation failed. Please try again.';
          showNotification(msg, 'Error', 'danger', 'fa fa-times');
        }
        hideLoader();
      },

      complete: function () {
        ajaxRequest = null;
      }
    });

    $('#ai-loader-cancel').off('click').on('click', function () {
      isCancelled = true;
      if (ajaxRequest) {
        ajaxRequest.abort();
      }
      hideLoader();
      showNotification('Generation cancelled by user.', 'Cancelled', 'info', 'fa fa-ban');
    });
  });

  function hideLoader() {
    if (timerInterval) {
      clearInterval(timerInterval);
      timerInterval = null;
    }
    if (progressInterval) {
      clearInterval(progressInterval);
      progressInterval = null;
    }
    setTimeout(() => {
      $('#ai-loader').fadeOut(400);
    }, 300);
  }

  // =========== Display Generated Images ==========
  function displayGeneratedImages(images, imageType) {
    currentImageType = imageType;
    const headerTitle = (imageType === 'gallery' || imageType === 'floor_planning_gallery') ?
      selectMultipleImageText :
      clickToUseText;

    let html = `
    <div id="generated-images-container" class="card mt-3" style="border: 2px solid #28a745;" data-image-type="${imageType}">
      <div class="card-header bg-success text-white">
        <h5 class="mb-0">
          <i class="fas fa-images"></i> ${headerTitle}
          <button type="button" class="close text-white" onclick="$('#generated-images-container').remove()">
            <span>&times;</span>
          </button>
        </h5>
      </div>
      <div class="card-body">
        <div class="row">`;

    images.forEach((image, index) => {
      const colSize = images.length === 1 ? '12' : images.length === 2 ? '6' : '4';
      html += `
          <div class="col-md-${colSize} mb-3">
            <div class="card generated-image-card"
              data-image-url="${image.url}"
              data-image-path="${image.path}"
              style="cursor: pointer; border: 2px solid #ddd; transition: all 0.3s;">
              <img src="${image.url}" class="card-img-top" alt="Generated Image ${index + 1}" style="height: 250px; object-fit: cover;">
                <div class="card-body text-center p-2">
                  <button class="btn btn-sm btn-success use-image-btn w-100">
                    <i class="fas fa-check-circle"></i>
                    ${(imageType === 'gallery' || imageType === 'floor_planning_gallery') ? selectText : useThisImageText}
                  </button>
                </div>
            </div>
          </div>`;
    });

    html += `</div>`;

    if (imageType === 'gallery' || imageType === 'floor_planning_gallery') {
      html += `
                <div class="text-center mt-3">
                    <button class="btn btn-danger" id="finishGallerySelection">
                        <i class="fal fa-times"></i> ${closeGalleryText}
                    </button>
                </div>`;
    }

    html += `</div></div>`;

    if (imageType === 'gallery') {
      $('#my-dropzone').after(html);
    } else if (imageType === 'floor_planning_gallery') {
      $('#my-dropzone2').after(html);
    } else if (imageType === 'thumbnail') {
      $('.uploaded-img').closest('.col-lg-4').find('.form-group').after(html);
    } else if (imageType === 'floor_plan') {
      $('.uploaded-img2').closest('.col-lg-4').find('.form-group').after(html);
    } else if (imageType === 'video_poster') {
      $('.uploaded-img3').closest('.col-lg-4').find('.form-group').after(html);
    }

    $('html, body').animate({
      scrollTop: $('#generated-images-container').offset().top - 100
    }, 500);

    applyHoverEffects();
  }

  function applyHoverEffects() {
    $('.generated-image-card').hover(
      function () {
        $(this).css({
          'border-color': '#28a745',
          'transform': 'translateY(-5px)',
          'box-shadow': '0 4px 15px rgba(40, 167, 69, 0.3)'
        });
      },
      function () {
        $(this).css({
          'border-color': '#ddd',
          'transform': 'translateY(0)',
          'box-shadow': 'none'
        });
      }
    );
  }

  // Gallery "Done" Button (Fixed - No Duplicate)
  $(document).on('click', '#finishGallerySelection', function (e) {
    e.preventDefault();

    // Get only selection inputs (temporary)
    const selectedInputs = $('.ai-gallery-input');
    const selectedCount = selectedInputs.length;

    if (selectedCount === 0) {
      $('#generated-images-container').fadeOut(300, function () {
        $(this).remove();
      });
      return;
    }

    // Determine target dropzone
    const imageType = $('#generated-images-container').data('image-type');
    const dropzoneId = imageType === 'floor_planning_gallery' ? 'my-dropzone2' : 'my-dropzone';

    // Add to correct dropzone only
    selectedInputs.each(function () {
      const imagePath = $(this).val();
      const imageUrl = $(this).data('url');

      if (imageUrl) {
        window.addAiImageToDropzone(imageUrl, imagePath, dropzoneId);
      }

      // Remove temporary selection input
      $(this).remove();
    });

    const galleryName = imageType === 'floor_planning_gallery' ? 'floor planning gallery' : 'gallery';
    showNotification(
      `${selectedCount} ${imageAddedText} ${galleryName} ${successfullText}!`,
      successText,
      'success',
      'fa fa-check'
    );

    $('#generated-images-container').fadeOut(300, function () {
      $(this).remove();
    });
  });

  // =========== Use Image Button Handler ====================

  $(document).on('click', '.use-image-btn', function (e) {
    e.preventDefault();
    e.stopPropagation();

    const card = $(this).closest('.generated-image-card');
    const imageUrl = card.data('image-url');
    const imagePath = card.data('image-path');

    // NEW: get the correct type from the container that is currently visible
    const container = card.closest('#generated-images-container');
    const imageType = container.data('image-type');  

    if (imageType === 'gallery' || imageType === 'floor_planning_gallery') {
      handleGallerySelection(card, imageUrl, imagePath);
    } else if (imageType === 'thumbnail') {
      handleThumbnailSelection(imageUrl, imagePath);
    } else if (imageType === 'floor_plan') {
      handleFloorPlanSelection(imageUrl, imagePath);
    } else if (imageType === 'video_poster') {
      handleVideoPosterSelection(imageUrl, imagePath);
    }
  });

  // Gallery Selection Handler (Fixed - Only Temporary Storage)
  function handleGallerySelection(card, imageUrl, imagePath) {
    const existingInput = $(`input.ai-gallery-input[value="${imagePath}"]`);

    if (existingInput.length > 0) {
      // Remove from temporary selection
      existingInput.remove();
      card.find('.use-image-btn')
        .html('<i class="fas fa-plus-circle"></i> ' + addToGalleryText)
        .removeClass('btn-info')
        .addClass('btn-success');

      showNotification(imageRemoveText, infoText, 'info', 'fa fa-info-circle', 2000);
    } else {

      // Add to temporary selection only 
      $('<input>').attr({
        type: 'hidden',
        name: 'temp_selection[]', 
        value: imagePath,
        class: 'ai-gallery-input', 
        'data-url': imageUrl
      }).appendTo('#propertyForm');

      card.find('.use-image-btn')
        .html('<i class="fas fa-check"></i> '+ selectedText)
        .removeClass('btn-success')
        .addClass('btn-info');

      showNotification(imageAddedSingleText, successText, 'success', 'fa fa-check', 2000);
    }

    updateDoneButtonText();
  }


  function updateDoneButtonText() {
    const selectedCount = $('.ai-gallery-input').length; 
    const $button = $('#finishGallerySelection');

    if (selectedCount > 0) {
      
      $button.removeClass('btn-danger').addClass('btn-primary');
      $button.html(`<i class="fas fa-check"></i> ${confirmSelectionText} <strong>${selectedCount}</strong> ${imagesText}`);
    } else {
      
      $button.removeClass('btn-primary').addClass('btn-danger');
      $button.html(`<i class="fas fa-times"></i> ${closeGalleryText}`);
    }
  }

  function handleThumbnailSelection(imageUrl, imagePath) {
    $('.uploaded-img').attr('src', imageUrl);
    $('input[name="ai_thumbnail_path"]').remove();
    $('<input>').attr({
      type: 'hidden',
      name: 'ai_thumbnail_path',
      value: imagePath
    }).appendTo('#propertyForm');

    showNotification(thumbnailImageSuccessText, successText, 'success', 'fa fa-check');
 
    // Remove container with data-image-type="thumbnail"
    $('#generated-images-container[data-image-type="thumbnail"]').fadeOut(300, function () {
      $(this).remove();
    });
  }

  function handleFloorPlanSelection(imageUrl, imagePath) {
    $('.uploaded-img2').attr('src', imageUrl);
    $('input[name="ai_floor_plan_path"]').remove();
    $('<input>').attr({
      type: 'hidden',
      name: 'ai_floor_plan_path',
      value: imagePath
    }).appendTo('#propertyForm');

    showNotification(floorPlanImageSuccessText, successText, 'success', 'fa fa-check');

    // Remove container with data-image-type="floor_plan"
    $('#generated-images-container[data-image-type="floor_plan"]').fadeOut(300, function () {
      $(this).remove();
    });
  }

  function handleVideoPosterSelection(imageUrl, imagePath) {
    $('.uploaded-img3').attr('src', imageUrl);
    $('input[name="ai_video_poster_path"]').remove();
    $('<input>').attr({
      type: 'hidden',
      name: 'ai_video_poster_path',
      value: imagePath
    }).appendTo('#propertyForm');

    showNotification(videoPosterImageSuccessText, successText, 'success', 'fa fa-check');

    //  Remove container with data-image-type="video_poster"
    $('#generated-images-container[data-image-type="video_poster"]').fadeOut(300, function () {
      $(this).remove();
    });
  }

  function showNotification(message, title, type, icon, delay = 4000) {
    const content = { message, title, icon };
    $.notify(content, {
      type,
      placement: { from: 'top', align: 'right' },
      showProgressbar: true,
      time: 1000,
      delay
    });
  }
}


