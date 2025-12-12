    <div class="modal fade" id="aiImageModal" tabindex="-1" role="dialog" aria-labelledby="aiImageModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="aiImageModalLabel">{{ __('Generate Images with AI') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="image_prompt">{{ __('Describe Your Image Idea') . '*' }}</label>
                        <textarea class="form-control textarea-medium" id="image_prompt" rows="3"
                            placeholder="{{ __('A modern living room with a view of the city at sunset') }}"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="art_style">{{ __('Art Style') }}</label>
                                <select class="form-control" id="art_style">
                                    <option value="photorealistic" selected>{{ __('Photorealistic') }}</option>
                                    <option value="interior-design">{{ __('Interior Design') }}</option>
                                    <option value="architecture">{{ __('Architecture') }}</option>
                                    <option value="3d-render">{{ __('3D Render') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="lighting">{{ __('Lighting') }}</label>
                                <select class="form-control" id="lighting">
                                    <option value="natural" selected>{{ __('Natural Light') }}</option>
                                    <option value="cinematic">{{ __('Cinematic') }}</option>
                                    <option value="studio">{{ __('Studio') }}</option>
                                    <option value="golden-hour">{{ __('Golden Hour') }}</option>
                                    <option value="blue-hour">{{ __('Blue Hour') }}</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="camera_angle">{{ __('Camera Angle') }}</label>
                                <select class="form-control" id="camera_angle">
                                    <option value="eye-level" selected>{{ __('Eye-level') }}</option>
                                    <option value="low-angle">{{ __('Low Angle') }}</option>
                                    <option value="high-angle">{{ __('High Angle') }}</option>
                                    <option value="aerial-view">{{ __('Aerial View') }}</option>
                                    <option value="wide-shot">{{ __('Wide Shot') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="image_size">{{ __('Image Size') }}</label>
                                <select class="form-control" id="image_size">
                                    <option value="1024x1024" selected>{{ __('Square (1024x1024)') }}</option>
                                    <option value="1792x1024">{{ __('Landscape (1792x1024)') }}</option>
                                    <option value="1024x1792">{{ __('Portrait (1024x1792)') }}</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group" id="num_images_field_container">
                        <label for="num_images">{{ __('Number of Images') }}</label>
                        <select class="form-control" id="num_images">
                            <option value="1">1</option>
                            <option value="2" selected>2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                        </select>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
                    <button type="button" class="btn btn-primary">{{ __('Generate Images') }}</button>
                </div>
            </div>
        </div>
    </div>
