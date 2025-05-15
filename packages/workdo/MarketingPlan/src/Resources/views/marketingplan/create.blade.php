@extends('layouts.main')

@section('page-title')
    {{ __('Create Marketing Plan') }}
@endsection

@section('page-breadcrumb')
    {{ __('Marketing Plan') }}
@endsection
@section('content')
    <div class="row">
        <div class="col-sm-12">
            {{ Form::open(['route' => ['marketing-plan.store'], 'method' => 'post', 'enctype' => 'multipart/form-data','class'=>'needs-validation','novalidate']) }}
            <div class="row">
                <div class="col-md-6">
                    <div class="card em-card">
                        <div class="card-header">
                            <h6 class="mb-0">{{ __('Add Marketing Plan') }}</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {{ Form::label('name', __('Name'), ['class' => 'form-label']) }}<x-required></x-required>
                                        {{ Form::text('name', null, ['class' => 'form-control', 'placeholder' => __('Enter Name'), 'required' => 'required']) }}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {{ Form::label('challenge', __('Challenge'), ['class' => 'form-label']) }}<x-required></x-required>
                                        {{ Form::select('challenge', $Challenge, null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Select Challenge')]) }}
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-6 field" data-name="attachments">
                                                <div class="attachment-upload">
                                                    <div class="attachment-button">
                                                        <div class="pull-left">
                                                            <div class="form-group">
                                                                {{ Form::label('thumbnail_image', __('Thumbnail Image'), ['class' => 'form-label']) }}<x-required></x-required>
                                                                <input type="file" name="thumbnail_image"
                                                                    class="mb-3 form-control"
                                                                    onchange="document.getElementById('blah').src = window.URL.createObjectURL(this.files[0])" required>
                                                                <img id="blah" width="20%" height="20%" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="attachments"></div>
                                                </div>
                                            </div>
                                            <div class="col-6 video" data-name="attachments">
                                                <div class="attachment-upload">
                                                    <div class="attachment-button">
                                                        <div class="pull-left">
                                                            <div class="form-group">
                                                                {{ Form::label('video', __('Upload Video'), ['class' => 'form-label']) }}
                                                                <input type="file" name="video" class="mb-3 form-control" onchange="previewVideo(this)">
                                                                <video id="videoPreview" width="200" height="100" controls>
                                                                    <source id="videoSource" src="" type="video/mp4">
                                                                    Your browser does not support the video tag.
                                                                </video>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="attachments"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {{ Form::label('status', __('Status'), ['class' => 'form-label']) }}<x-required></x-required>
                                        {{ Form::select('status', $Planningstatus, null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Select Status')]) }}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {{ Form::label('stage', __('Stage'), ['class' => 'form-label']) }}<x-required></x-required>
                                        {{ Form::select('stage', $Planningstage, null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Select Stage')]) }}
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <div class="btn-box">
                                            <label class="d-block form-label">{{ __('Visibility') }}</label>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-check form-check-inline">
                                                        <input type="radio" class="form-check-input visibility_type"
                                                            id="customRadio7" name="visibility_type" value="users"
                                                            checked="checked">
                                                        <label class="custom-control-label form-label"
                                                            for="customRadio7">{{ __('Users') }}</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-check form-check-inline">
                                                        <input type="radio" class="form-check-input visibility_type"
                                                            id="customRadio8" name="visibility_type" value="role">
                                                        <label class="custom-control-label form-label"
                                                            for="customRadio8">{{ __('Role') }}</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <div id="users-list" class="users-list">
                                                    <select class=" multi-select choices users_list" id="users_list"
                                                        name="users_list[]" multiple="multiple"
                                                        data-placeholder="{{ __('Select Users ...') }}">
                                                        @foreach ($users as $user)
                                                            <option value="{{ $user->email }}">{{ $user->name }} -
                                                                {{ $user->email }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <p class="text-danger d-none" id="user_validation">
                                                    {{ __('Users filed is required.') }}</p>
                                            </div>
                                            <div class="form-group col-md-6">
                                                {{ Form::select('role_list', $role, null, ['class' => 'form-control role_list d-none', 'placeholder' => 'Select Role']) }}
                                            </div>
                                        </div>

                                        <div class="form-group col-md-12">
                                            {{ Form::label('description', __('Description'), ['class' => 'form-label']) }}<x-required></x-required>
                                            <textarea class="form-control summernote" name="description" id="exampleFormControlTextarea1" rows="3"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card em-card">
                        <div class="card-header">
                            <h6 class="mb-0">{{ __('Add Marketing Plan') }}</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="form-group col-md-12">
                                    <label class="require form-label">{{ __('Attachments') }}
                                        <small>({{ __('You can select multiple files') }})</small> </label>
                                        <div class="choose-file form-group">
                                            <label for="file" class="form-label d-block">
                                                <input type="file" name="attachments[]" id="file"
                                                    class="form-control mb-2 {{ $errors->has('attachments') ? ' is-invalid' : '' }}"
                                                    multiple="" data-filename="multiple_file_selection"
                                                    onchange="previewAttachments(this)">
                                                <div id="attachmentsPreview" class="mt-2"></div>
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('attachments.*') }}
                                                </div>
                                            </label>
                                        </div>
                                    <p class="mx-4 multiple_file_selection"></p>
                                </div>
                                <div class="form-group col-md-12">
                                    {{ Form::label('business_summary', __('Business Summary'), ['class' => 'form-label']) }}
                                    <p>{{__('Company name and mission statement')}}</p>
                                    <textarea class="form-control summernote {{ !empty($errors->first('business_summary')) ? 'is-invalid' : '' }}"
                                        name="business_summary" id="exampleFormControlTextarea2" rows="3"></textarea>

                                </div>
                                <div class="form-group col-md-12">
                                    {{ Form::label('company_description', __('Company Description'), ['class' => 'form-label']) }}
                                    <p>{{__('What does your company do? What challenges your company solve?')}}</p>
                                    <textarea class="form-control summernote {{ !empty($errors->first('company_description')) ? 'is-invalid' : '' }}"
                                        name="company_description" id="exampleFormControlTextarea3" rows="3"></textarea>
                                </div>
                                <div class="form-group col-md-12">
                                    {{ Form::label('team', __('Team'), ['class' => 'form-label']) }}
                                    <p>{{__('Who is involved in this journey? List who is enacting different stages of the plan.')}}</p>
                                    <textarea class="form-control summernote {{ !empty($errors->first('team')) ? 'is-invalid' : '' }}"
                                        name="team" id="exampleFormControlTextarea4" rows="3"></textarea>
                                </div>
                                <div class="form-group col-md-12">
                                    {{ Form::label('business_initiative', __('Business initiative'), ['class' => 'form-label']) }}
                                    <p>{{__('Summary of your marketing goals and initiatives to achieve them. Who are your competitors? Include marketing strategies.')}}</p>
                                    <textarea class="form-control summernote {{ !empty($errors->first('business_initiative')) ? 'is-invalid' : '' }}"
                                        name="business_initiative" id="exampleFormControlTextarea5" rows="3"></textarea>
                                </div>
                                <div class="form-group col-md-12">
                                    {{ Form::label('target_market', __('Target Market'), ['class' => 'form-label']) }}
                                    <p>{{__('Who are you targeting? Who makes up your target market? Who are your target buyer, personas, and ideal customers?')}}</p>
                                    <textarea class="form-control summernote {{ !empty($errors->first('target_market')) ? 'is-invalid' : '' }}"
                                        name="target_market" id="exampleFormControlTextarea6" rows="3"></textarea>
                                </div>
                                <div class="form-group col-md-12">
                                    {{ Form::label('marketing_channels', __('Marketing Channels'), ['class' => 'form-label']) }}
                                    <p>{{__('Which Channels and platforms you use to reach your audience and achieve your goals?')}}</p>
                                    <textarea class="form-control summernote {{ !empty($errors->first('marketing_channels')) ? 'is-invalid' : '' }}"
                                        name="marketing_channels" id="exampleFormControlTextarea7" rows="3"></textarea>
                                </div>
                                <div class="form-group col-md-12">
                                    {{ Form::label('budget', __('Budget'), ['class' => 'form-label']) }}
                                    <p>{{__('An overview of the amount you will spend to reach your marketing goals.')}}</p>
                                    <textarea class="form-control summernote {{ !empty($errors->first('budget')) ? 'is-invalid' : '' }}"
                                        name="budget" id="exampleFormControlTextarea8" rows="3"></textarea>
                                </div>
                                <div class="form-group col-md-12">
                                    {{ Form::label('notes', __('Notes '), ['class' => 'form-label']) }}
                                    <textarea class="form-control summernote {{ !empty($errors->first('notes')) ? 'is-invalid' : '' }}" name="notes"
                                        id="exampleFormControlTextare11" rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <input type="button" value="{{ __('Cancel') }}" onclick="location.href = '{{ route('marketing-plan.index') }}';"
                class="btn btn-light me-2">
                {{ Form::submit(__('Create'), ['class' => 'btn  btn-primary']) }}
            </div>
            {{ Form::close() }}
        </div>
    </div>
@endsection


@push('css')
    <link rel="stylesheet" href="{{ asset('packages/workdo/MarketingPlan/src/Resources/assets/css/custom.css') }}">
    <link rel="stylesheet" href="{{ asset('packages/workdo/MarketingPlan/src/Resources/assets/css/custom.css') }}">
    <link rel="stylesheet" href="{{ asset('packages/workdo/MarketingPlan/src/Resources/assets/summernote/summernote-bs4.css') }}">
@endpush
@push('scripts')
    <script src="{{ asset('js/custom.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/summernote-0.8.18-dist/summernote-lite.min.js') }}"></script>
    <script src="{{ asset('packages/workdo/MarketingPlan/src/Resources/assets/js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('packages/workdo/MarketingPlan/src/Resources/assets/summernote/summernote-bs4.js') }}"></script>
    <script>
        $(document).ready(function() {
            if ($("input[value='image']").is(":checked")) {
                ;
                $('.video').addClass('d-none')
                $('.video').removeClass('d-block');
            }
        });
        //hide & show video
        $(document).on('click', '.type', function() {
            var type = $(this).val();
            if (type == 'video') {
                $('.video').removeClass('d-none')
                $('.video').addClass('d-block');
            } else {
                $('.video').addClass('d-none')
                $('.video').removeClass('d-block');
            }
        });
    </script>
    <script>
        $(document).on('click', '.visibility_type', function() {
            var val = $('.visibility_type:checked').val();

            if (val == 'role') {
                $('.role_list').removeClass('d-none');
                $('.role_list').addClass('d-block');

                $('.users-list').removeClass('d-block');
                $('.users-list').addClass('d-none');
            } else {
                $('.users-list').removeClass('d-none');
                $('.users-list').addClass('d-block');

                $('.role_list').removeClass('d-block');
                $('.role_list').addClass('d-none');
            }
        });
    </script>
    <script>
        function previewVideo(input) {
            var file = input.files[0];
            var videoPreview = document.getElementById('videoPreview');
            var videoSource = document.getElementById('videoSource');

            if (file) {
                var url = URL.createObjectURL(file);
                videoSource.src = url;
                videoPreview.load();  // Reload the video element to reflect the new source
            }
        }
    </script>
    <script>
        function previewAttachments(input) {
            const preview = document.getElementById('attachmentsPreview');
            preview.innerHTML = ''; // Clear the current previews

            if (input.files) {
                Array.from(input.files).forEach(file => {
                    const fileReader = new FileReader();
                    fileReader.onload = function (e) {
                        const fileType = file.type;
                        let element;

                        if (fileType.startsWith('image/')) {
                            element = document.createElement('img');
                            element.src = e.target.result;
                            element.style.width = '20%';
                            element.style.marginRight = '10px';
                        } else {
                            element = document.createElement('div');
                            element.textContent = file.name;
                            element.style.marginRight = '10px';
                        }

                        preview.appendChild(element);
                    };

                    fileReader.readAsDataURL(file);
                });
            }
        }
    </script>
@endpush
