@extends('layouts.main')

@section('page-title')
    {{ __('Edit Marketing Plan') }}
@endsection

@section('page-breadcrumb')
    {{ __('Marketing Plan') }}
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">
            {{ Form::model($MarketingPlan, ['route' => ['marketing-plan.update', $MarketingPlan->id], 'method' => 'PUT', 'enctype' => 'multipart/form-data','class'=>'needs-validation','novalidate']) }}
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
                                                                {{ Form::label('thumbnail_image', __('Thumbnail Image'), ['class' => 'form-label']) }}<x-required></x-required>
                                                                <input type="file" name="thumbnail_image"
                                                                    class="mb-3 form-control"
                                                                    onchange="document.getElementById('blah').src = window.URL.createObjectURL(this.files[0])">
                                                                <img id="blah" class="mt-2"
                                                                    src="{{ get_file($MarketingPlan->thumbnail_image) }}"
                                                                    style="width:35%;" />
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
                                                                    <source src="{{ check_file($MarketingPlan->video_file) ? get_file($MarketingPlan->video_file) : 'packages/workdo/MarketingPlan/src/Resources/assets/img/default.jpg' }}" type="video/mp4">
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
                                        {{ Form::select('status', $Planningstatus, null, ['class' => 'form-control', 'required' => 'required']) }}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {{ Form::label('stage', __('Stage'), ['class' => 'form-label']) }}<x-required></x-required>
                                        {{ Form::select('stage', $Planningstage, null, ['class' => 'form-control', 'required' => 'required']) }}
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
                                                            @if ($MarketingPlan->visibility_type == 'users') checked @endif
                                                            >
                                                        <label class="custom-control-label form-label"
                                                            for="customRadio7">{{ __('Users') }}</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-check form-check-inline">
                                                        <input type="radio" class="form-check-input visibility_type"
                                                            id="customRadio8" name="visibility_type" value="role"
                                                            @if ($MarketingPlan->visibility_type == 'role') checked @endif
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
                                                    {{ Form::select('users_list[]', $users, $MarketingPlan->user_id, ['class' => 'form-control choices ' . ($MarketingPlan->visibility_type == 'users' ? ' d-none' : 'd-block'), 'id' => 'choices-multiple1', 'multiple' => '']) }}
                                                    <p class="text-danger d-none" id="user_validation">
                                                        {{ __('Users filed is required.') }}</p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    {{ Form::select('role_list', $role, $MarketingPlan->role_id, ['class' => 'form-control role_list ' . ($MarketingPlan->visibility_type == 'role' ? ' d-none' : 'd-block'), 'placeholder' => 'Select Role']) }}
                                                </div>
                                            </div>
                                        </div>

                                        @permission('marketingplan item manage')
                                            <div class="col-md-12">
                                                <div class="card set-card">
                                                    <div class="card-header">
                                                        <div class="row">
                                                            <div class="col-6">
                                                                <h5>{{ __('Items') }}</h5>
                                                            </div>
                                                            @permission('marketingplan item create')
                                                                <div class="col text-end">
                                                                    <a data-url="{{ route('marketing-plan-item.create',$MarketingPlan->id) }}" data-ajax-popup="true"
                                                                        data-title="{{ __('Create Items') }}" data-bs-toggle="tooltip" title=""
                                                                        class="btn btn-sm btn-primary" data-bs-original-title="{{ __('Create') }}">
                                                                        <i class="ti ti-plus"></i>
                                                                    </a>
                                                                </div>
                                                            @endpermission
                                                        </div>
                                                    </div>
                                                    <div class=" card-body table-border-style">
                                                        <div class="table-responsive">
                                                            <table class="table">
                                                                <thead>
                                                                    <tr>
                                                                        <th>#</th>
                                                                        <th>{{ __('Item type') }}</th>
                                                                        <th>{{ __('Item') }}</th>
                                                                        @if (Laratrust::hasPermission('marketingplan item delete'))
                                                                            <th>{{ __('Action') }}</th>
                                                                        @endif
                                                                    </tr>
                                                                </thead>
                                                                <tbody>

                                                                    @foreach ($marketingplans as $index => $marketingplan)
                                                                        <tr>
                                                                            <th scope="row">{{++$index}}</th>
                                                                            <td>{{ $marketingplan->item_type }}</td>
                                                                            <td>{{ $marketingplan->Items->name }}</td>

                                                                            @if (Laratrust::hasPermission('marketingplan item delete'))
                                                                                <td class="Action">
                                                                                    <span>
                                                                                        @permission('marketingplan item delete')
                                                                                            <div class="action-btn bg-danger ms-2">
                                                                                                {{ Form::open(['route' => ['marketing-plan-item.destroy', $marketingplan->id], 'class' => 'm-0']) }}
                                                                                                @method('DELETE')
                                                                                                <a class="mx-3 btn btn-sm  align-items-center bs-pass-para show_confirm"
                                                                                                    data-bs-toggle="tooltip" title=""
                                                                                                    data-bs-original-title="Delete" aria-label="Delete"
                                                                                                    data-confirm="{{ __('Are You Sure?') }}"
                                                                                                    data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                                                                    data-confirm-yes="delete-form-{{ $marketingplan->id }}"><i
                                                                                                        class="ti ti-trash text-white text-white"></i></a>
                                                                                                {{ Form::close() }}
                                                                                            </div>
                                                                                        @endpermission
                                                                                    </span>
                                                                                </td>
                                                                            @endif
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endpermission

                                        <div class="form-group col-md-12">
                                            {{ Form::label('description', __('Description'), ['class' => 'form-label']) }}<x-required></x-required>
                                            <textarea name="description"
                                                class="form-control summernote {{ !empty($errors->first('description')) ? 'is-invalid' : '' }}"
                                                id="description_ck">{!! $MarketingPlan->description !!}</textarea>
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
                            <h6 class="mb-0">{{ __('Add Marketing Plan') }}</h6>
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
                                                    @if(!empty($MarketingPlan->marketing_attachments))
                                                        @foreach(json_decode($MarketingPlan->marketing_attachments, true) as $attachment)
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

                                    <p class="mx-4 multiple_file_selection"></p>
                                </div>
                                <div class="form-group col-md-12">
                                    {{ Form::label('business_summary', __('Business Summary'), ['class' => 'form-label']) }}
                                    <p>{{'Company name and mission statement'}}</p>
                                    <textarea name="business_summary"
                                        class="form-control summernote {{ !empty($errors->first('business_summary')) ? 'is-invalid' : '' }}"
                                        id="exampleFormControlTextarea2">{!! $MarketingPlan->business_summary !!}</textarea>
                                    @if ($errors->has('business_summary'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('business_summary') }}
                                        </div>
                                    @endif
                                </div>


                                <div class="form-group col-md-12">
                                    {{ Form::label('company_description', __('Company Description'), ['class' => 'form-label']) }}
                                    <p>{{'What does your company do? What challenges your company solve?'}}</p>
                                    <textarea class="form-control summernote {{ !empty($errors->first('company_description')) ? 'is-invalid' : '' }}" name="company_description" id="exampleFormControlTextarea3" rows="3">{!! $MarketingPlan->company_description !!}</textarea>
                                    @if ($errors->has('company_description'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('company_description') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group col-md-12">
                                    {{ Form::label('team', __('Team'), ['class' => 'form-label']) }}
                                    <p>{{'Who is involved in this journey? List who is enacting different stages of the plan.'}}</p>
                                    <textarea class="form-control summernote {{ !empty($errors->first('team')) ? 'is-invalid' : '' }}"
                                        name="team" id="exampleFormControlTextarea4" rows="3">{!! $MarketingPlan->team !!}</textarea>
                                    @if ($errors->has('team'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('team') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group col-md-12">
                                    {{ Form::label('business_initiative', __('Business initiative'), ['class' => 'form-label']) }}
                                    <p>{{'Summary of your marketing goals and initiatives to achieve them. Who are your competitors? Include marketing strategies.'}}</p>
                                    <textarea class="form-control summernote {{ !empty($errors->first('business_initiative')) ? 'is-invalid' : '' }}"
                                        name="business_initiative" id="exampleFormControlTextarea5" rows="3">{!! $MarketingPlan->business_initiative !!}</textarea>
                                    @if ($errors->has('business_initiative'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('business_initiative') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group col-md-12">
                                    {{ Form::label('target_market', __('Target Market'), ['class' => 'form-label']) }}
                                    <p>{{'Who are you targeting? Who makes up your target market? Who are your target buyer, personas, and ideal customers?'}}</p>
                                    <textarea class="form-control summernote {{ !empty($errors->first('target_market')) ? 'is-invalid' : '' }}"
                                        name="target_market" id="exampleFormControlTextarea6" rows="3">{!! $MarketingPlan->target_market !!}</textarea>
                                    @if ($errors->has('target_market'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('target_market') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group col-md-12">
                                    {{ Form::label('marketing_channels', __('Marketing Channels'), ['class' => 'form-label']) }}
                                    <p>{{'Which Channels and platforms you use to reach your audience and achieve your goals?'}}</p>
                                    <textarea class="form-control summernote {{ !empty($errors->first('marketing_channels')) ? 'is-invalid' : '' }}"
                                        name="marketing_channels" id="exampleFormControlTextarea7" rows="3">{!! $MarketingPlan->marketing_channels !!}</textarea>
                                    @if ($errors->has('marketing_channels'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('marketing_channels') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group col-md-12">
                                    {{ Form::label('budget', __('Budget'), ['class' => 'form-label']) }}
                                    <p>{{'An overview of the amount you will spend to reach your marketing goals.'}}</p>
                                    <textarea class="form-control summernote {{ !empty($errors->first('budget')) ? 'is-invalid' : '' }}"
                                        name="budget" id="exampleFormControlTextarea8" rows="3">{!! $MarketingPlan->budget !!}</textarea>
                                    @if ($errors->has('budget'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('budget') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group col-md-12">
                                    {{ Form::label('notes', __('Notes '), ['class' => 'form-label']) }}
                                    <textarea class="form-control summernote {{ !empty($errors->first('notes')) ? 'is-invalid' : '' }}" name="notes"
                                        id="exampleFormControlTextare11" rows="3">{!! $MarketingPlan->notes !!}</textarea>
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
            <div class="modal-footer">
                <input type="button" value="{{ __('Cancel') }}"
                    onclick="location.href = '{{ route('marketing-plan.index') }}';" class="btn btn-light me-2">
                {{ Form::submit(__('Update'), ['class' => 'btn  btn-primary']) }}
            </div>
            {{ Form::close() }}
        </div>
    </div>
@endsection

@push('css')
    <link rel="stylesheet" href="{{ asset('packages/workdo/MarketingPlan/src/Resources/assets/css/custom.css') }}">
    <link rel="stylesheet" href="{{ asset('packages/workdo/MarketingPlan/src/Resources/assets/summernote/summernote-bs4.css') }}">
    <link href="{{ asset('assets/js/plugins/summernote-0.8.18-dist/summernote-lite.min.css') }}" rel="stylesheet">
@endpush
@push('scripts')
    <script src="{{ asset('/js/custom.js') }}"></script>
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
