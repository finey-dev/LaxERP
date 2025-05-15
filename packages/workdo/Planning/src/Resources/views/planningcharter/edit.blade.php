@extends('layouts.main')

@section('page-title')
    {{ __('Edit Charter') }}
@endsection

@section('page-breadcrumb')
    {{ __('Charter') }},
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">
            {{ Form::model($Charters, ['route' => ['planningcharters.update', $Charters->id], 'method' => 'PUT', 'enctype' => 'multipart/form-data', 'class' => 'needs-validation', 'novalidate']) }}

            <div class="row">

                <div class="col-md-6">
                    <div class="card em-card">
                        <div class="card-header">
                            <h6 class="mb-0">{{ __('Edit Planning Charter') }}</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {{ Form::label('charter_name', __('Charter Name'), ['class' => 'form-label']) }}<x-required></x-required>
                                        {{ Form::text('charter_name', null, ['class' => 'form-control', 'placeholder' => __('Enter Charter Name'), 'required' => 'required']) }}
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        {{ Form::label('challenge', __('Challenge'), ['class' => 'form-label']) }}<x-required></x-required>
                                        {{ Form::select('challenge', $challengesArray, null, ['class' => 'form-control', 'required' => 'required']) }}
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
                                                                {{ Form::label('thumbnail_image', __('Thumbnail Image'), ['class' => 'form-label']) }}
                                                                <input type="file" name="thumbnail_image"
                                                                    class="mb-3 form-control"
                                                                    onchange="document.getElementById('blah').src = window.URL.createObjectURL(this.files[0])">
                                                                <img id="blah" class="mt-2"
                                                                    src="{{ get_file($Charters->thumbnail_image) }}"
                                                                    style="width:45%;" />
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
                                                                <video id="videoPreview" class="mt-2" width="200" height="100" controls>
                                                                    <source src="{{ check_file($Charters->video_file) ? get_file($Charters->video_file) : get_file('assets/images/gallery.png') }}" type="video/mp4">
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
                                        {{ Form::label('status', __('Status'), ['class' => 'col-form-label']) }}<x-required></x-required>
                                        {{ Form::select('status', $Planningstatus, null, ['class' => 'form-control', 'required' => 'required']) }}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {{ Form::label('stage', __('Stage'), ['class' => 'col-form-label']) }}<x-required></x-required>
                                        {{ Form::select('stage', $Planningstage, null, ['class' => 'form-control', 'required' => 'required']) }}
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <div class="btn-box">
                                            <label class="d-block form-label">{{ __('Visibility') }}<x-required></x-required></label>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-check form-check-inline">
                                                        <input type="radio" class="form-check-input visibility_type"
                                                            id="customRadio7" name="visibility_type" value="users"
                                                            @if ($Charters->visibility_type == 'users') checked @endif
                                                            >
                                                        <label class="custom-control-label form-label"
                                                            for="customRadio7">{{ __('Users') }}</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-check form-check-inline">
                                                        <input type="radio" class="form-check-input visibility_type"
                                                            id="customRadio8" name="visibility_type" value="role"
                                                            @if ($Charters->visibility_type == 'role') checked @endif
                                                           >
                                                        <label class="custom-control-label form-label"
                                                            for="customRadio8">{{ __('Role') }}</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <div id="users-list" class="users-list">
                                                    {{ Form::select('users_list[]', $users, $Charters->user_id, ['class' => 'form-control choices ' . ($Charters->visibility_type == 'users' ? ' d-none' : 'd-block'), 'id' => 'choices-multiple1', 'multiple' => '']) }}
                                                    <p class="text-danger d-none" id="user_validation">
                                                        {{ __('Users filed is required.') }}</p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    {{ Form::select('role_list', $role, $Charters->role_id, ['class' => 'form-control role_list ' . ($Charters->visibility_type == 'role' ? ' d-none' : 'd-block'), 'placeholder' => 'Select Role']) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-12">
                                            {{ Form::label('description', __('Description'), ['class' => 'form-label']) }}<x-required></x-required>
                                            <textarea name="description"
                                                class="form-control summernote {{ !empty($errors->first('description')) ? 'is-invalid' : '' }}"
                                                id="description_ck">{!! $Charters->dsescription !!}</textarea>
                                            @if ($errors->has('description'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('description') }}
                                                </div>
                                            @endif
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
                            <h6 class="mb-0">{{ __('Edit Planning Charter') }}</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="form-group col-md-12">
                                    <label class="require form-label">{{ __('Attachments') }}
                                        <small>({{ __('You can select multiple files') }})</small> </label>
                                        <div class="choose-file form-group">
                                            <label for="file" class="form-label d-block">
                                                <input type="file" name="attachments[]" id="file" class="form-control mb-2 {{ $errors->has('attachments') ? ' is-invalid' : '' }}" multiple data-filename="multiple_file_selection" onchange="previewFiles(this)">
                                                <div id="attachmentsPreview" class="mt-2">
                                                    @if(!empty($Charters->charter_attachments))
                                                    @foreach(json_decode($Charters->charter_attachments, true) as $attachment)
                                                        <a href="{{ get_file($attachment['path']) }}" target="_blank">
                                                            <img src="{{ get_file($attachment['path']) }}" style="width:20%; margin-right:10px;" />
                                                        </a>
                                                    @endforeach
                                                    @endif
                                                </div>
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('attachments.*') }}
                                                </div>
                                            </label>
                                        </div>

                                    {{-- @if(!empty($Charters->charter_attachments))
                                    @foreach(json_decode($Charters->charter_attachments) as $attachment)
                                        <div class="existing-attachment">
                                            <a href="{{ get_file($attachment->path) }}" target="_blank">{{ $attachment->name }}</a>
                                        </div>
                                    @endforeach
                                @endif --}}


                                    <p class="mx-4 multiple_file_selection"></p>
                                </div>
                                <div class="form-group col-md-12">
                                    {{ Form::label('organisational_effects', __('Organisational Effects'), ['class' => 'form-label']) }}
                                    <textarea name="organisational_effects"
                                        class="form-control summernote {{ !empty($errors->first('organisational_effects')) ? 'is-invalid' : '' }}"
                                        id="exampleFormControlTextarea2">{!! $Charters->organisational_effects !!}</textarea>
                                    @if ($errors->has('organisational_effects'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('organisational_effects') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group col-md-12">
                                    {{ Form::label('goal_description', __('Goal Description '), ['class' => 'form-label']) }}
                                    <textarea name="goal_description"
                                        class="form-control summernote {{ !empty($errors->first('goal_description')) ? 'is-invalid' : '' }}"
                                        id="exampleFormControlTextarea3">{!! $Charters->goal_description !!}</textarea>
                                    @if ($errors->has('goal_description'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('goal_description') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group col-md-12">
                                    {{ Form::label('notes', __('Notes'), ['class' => 'form-label']) }}
                                    <textarea name="notes" class="form-control summernote {{ !empty($errors->first('notes')) ? 'is-invalid' : '' }}"
                                        id="exampleFormControlTextare4">{!! $Charters->notes !!}</textarea>
                                    @if ($errors->has('notes'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('notes') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <input type="button" value="{{ __('Cancel') }}"
            onclick="location.href = '{{ route('planningcharters.index') }}';" class="btn btn-light me-2">
        {{ Form::submit(__('Update'), ['class' => 'btn  btn-primary']) }}
    </div>
    {{ Form::close() }}
@endsection

@push('css')
    <link rel="stylesheet" href="{{ asset('packages/workdo/Planning/src/Resources/assets/css/custom.css') }}">
    <link rel="stylesheet" href="{{ asset('packages/workdo/Planning/src/Resources/assets/summernote/summernote-bs4.css') }}">
    <link href="{{ asset('assets/js/plugins/summernote-0.8.18-dist/summernote-lite.min.css') }}" rel="stylesheet">
@endpush
@push('scripts')
    <script src="{{ asset('js/custom.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/summernote-0.8.18-dist/summernote-lite.min.js') }}"></script>
    <script src="{{ asset('packages/workdo/Planning/src/Resources/assets/js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('packages/workdo/Planning/src/Resources/assets/summernote/summernote-bs4.js') }}"></script>
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
            $('.role_list').removeClass('d-none').addClass('d-block');
            $('.users-list').removeClass('d-block').addClass('d-none');
        } else {
            $('.users-list').removeClass('d-none').addClass('d-block');
            $('.role_list').removeClass('d-block').addClass('d-none');
        }
    });

    $(document).ready(function() {
        // Trigger the click event to set the initial state based on the checked radio button
        $('.visibility_type:checked').trigger('click');
    });
</script>
    <script>
        function previewVideo(input) {
            const file = input.files[0];
            const preview = document.getElementById('videoPreview');
            const source = preview.getElementsByTagName('source')[0];

            if (file) {
                const url = URL.createObjectURL(file);
                source.src = url;
                preview.load();  // Reload the video element to reflect the new source
            }
        }
    </script>
   <script>
    function previewFiles(input) {
        var preview = document.getElementById('attachmentsPreview');
        preview.innerHTML = ''; // Clear the current previews

        if (input.files) {
            Array.from(input.files).forEach(file => {
                var reader = new FileReader();
                reader.onload = function (e) {
                    var img = document.createElement('img');
                    img.src = e.target.result;
                    img.style.width = '20%';
                    img.style.marginRight = '10px';
                    preview.appendChild(img);
                }
                reader.readAsDataURL(file);
            });
        }
    }
</script>
@endpush
