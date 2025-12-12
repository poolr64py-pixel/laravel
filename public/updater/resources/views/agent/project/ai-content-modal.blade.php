    <div class="modal fade" id="aiContentModal" tabindex="-1" role="dialog" aria-labelledby="aiContentModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="aiContentModalLabel">{{ __('Generate Content with AI') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="aiContentForm" action="{{ route('agent.project.ai.generate', getParam()) }}" method="POST">
                    <div class="modal-body">
                        @csrf
                        <input type="hidden" id="ai-lang-code" name="lang_code">
                        <input type="hidden" id="ai-field-type" name="field_type">

                        <div id="all-languages-container" class="form-group">
                            <label>{{ __('Generate in Language') }}</label>
                            <div class="row">
                                <div class="col-12">
                                    @foreach ($languages as $language)
                                        <div class="form-check form-check-inline">
                                            <label class="form-check-label" for="ai_lang_{{ $language->id }}">
                                                <input class="form-check-input" type="checkbox" name="ai_language[]"
                                                    id="ai_lang_{{ $language->id }}" value="{{ $language->code }}"
                                                    {{ $language->is_default == 1 ? 'checked' : '' }}>
                                                <span class="form-check-sign"></span>
                                                {{ $language->name }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div id="single-language-container" class="form-group d-none">
                            <label>{{ __('Language') }}: <strong id="single-language-display"></strong></label>
                        </div>

                        <div class="form-group">
                            <label
                                for="ai_content_prompt">{{ __('What kind of content do you want to generate?') }}</label>
                            <textarea class="form-control textarea-medium" id="ai_content_prompt" name="ai_content_prompt"
                                placeholder="{{ __('A luxury 3-bedroom apartment in the city center with a great view') }}"></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('Category') }}</label>
                                    <select name="ai_category_id" class="form-control">
                                        <option value="" selected disabled>{{ __('Select Category') }}</option>
                                        @foreach ($projectCategories as $category)
                                            <option value="{{ $category->id }}">
                                                {{ @$category->getContent($defaultLang->id)->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('Country') }}</label>
                                    <select name="ai_country_id" class="form-control">
                                        <option value="" selected disabled>{{ __('Select Country') }}</option>
                                        @foreach ($projectCountries as $country)
                                            <option value="{{ $country->id }}">
                                                {{ @$country->getContent($defaultLang->id)->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            data-dismiss="modal">{{ __('Close') }}</button>
                        <button type="submit" class="btn btn-primary" id="submitAiForm">{{ __('Generate') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
