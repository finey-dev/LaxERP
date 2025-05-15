@extends('layouts.main')
@section('page-title')
    {{ __('Create Job') }}
@endsection
@section('page-breadcrumb')
    {{ __('Create Job') }}
@endsection
@push('css')
    <link href="{{ asset('packages/workdo/Recruitment/src/Resources/assets/css/bootstrap-tagsinput.css') }}"
        rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('packages/workdo/Recruitment/src/Resources/assets/css/custom.css') }}">
    <link href="{{ asset('assets/js/plugins/summernote-0.8.18-dist/summernote-lite.min.css') }}" rel="stylesheet">
@endpush
@section('page-action')
    <div class="">
        <div class="mb-2 text-end">
        @if (module_is_active('AIAssistant'))
            @include('aiassistant::ai.generate_ai_btn', [
                'template_module' => 'job',
                'module' => 'Recruitment',
            ])
        @endif
        </div>
        <ul class="nav nav-pills nav-fill cust-nav information-tab" id="pills-tab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="details" data-bs-toggle="pill" data-bs-target="#details-tab"
                    type="button">{{ __('Details') }}</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="questions" data-bs-toggle="pill" data-bs-target="#questions-tab"
                    type="button">{{ __('Questions') }}</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="descriptions" data-bs-toggle="pill" data-bs-target="#descriptions-tab"
                    type="button">{{ __('Descriptions') }}</button>
            </li>
        </ul>
    </div>
@endsection
@push('scripts')
    <script src="{{ asset('assets/js/plugins/summernote-0.8.18-dist/summernote-lite.min.js') }}"></script>
    <script src="{{ asset('packages/workdo/Recruitment/src/Resources/assets/js/editorplaceholder.js') }}"></script>
    <script src="{{ asset('packages/workdo/Recruitment/src/Resources/assets/js/bootstrap-tagsinput.min.js') }}"></script>
    <script>
        var e = $('[data-toggle="tags"]');
        e.length && e.each(function() {
            $(this).tagsinput({
                tagClass: "badge badge-primary",
            })
        });
    </script>

    <script>
        $(document).ready(function() {
            var checkbox = $('#check-terms');
            var termsDiv = $('#termsandcondition');

            checkbox.change(function() {
                if (checkbox.is(':checked')) {
                    termsDiv.show();
                } else {
                    termsDiv.hide();
                }
            });

            if (!checkbox.is(':checked')) {
                termsDiv.hide();
            }
        });
    </script>

    <script>
        $(document).ready(function() {
            function toggleFormGroups() {
                var selectedType = $('#recruitment_type').val();
                if (selectedType === 'internal') {
                    $('#branch').show();
                    $('#users').hide();
                    $('#branch').prop('required', true);
                    $('#users').prop('required', false);
                } else if (selectedType === 'client') {
                    $('#branch').hide();
                    $('#users').show();
                    $('#users').prop('required', true);
                    $('#branch').prop('required', false);
                } else {
                    $('#branch').hide();
                    $('#users').hide();
                    $('#users').prop('required', false);
                    $('#branch').prop('required', false);
                }
            }

            toggleFormGroups();

            $('#recruitment_type').change(toggleFormGroups);
        });
    </script>

    <script>
        $(document).ready(function () {
            const existingLinkUrl = "{{ route('career') }}";

            function toggleJobLinkInput() {
                if ($('#existing_link').is(':checked')) {
                    $('input[name="job_link"]').val(existingLinkUrl).prop('disabled', true);
                    $('input[name="job_link"]').prop('required', false);
                } else {
                    $('input[name="job_link"]').val('').prop('disabled', false);
                    $('input[name="job_link"]').prop('required', true);
                }
            }

            $('input[name="link_type"]').on('change', toggleJobLinkInput);

            toggleJobLinkInput();
        });
    </script>

    <script>
        function changetab(tabname) {
            var someTabTriggerEl = document.querySelector('button[data-bs-target="' + tabname + '"]');
            var actTab = new bootstrap.Tab(someTabTriggerEl);
            actTab.show();
        }
    </script>
@endpush

@php
    $company_settings = getCompanyAllSetting();
@endphp
@section('content')
    <div class="row">
        <div class="col-xl-12">
            {{ Form::open(['url' => 'job', 'method' => 'post', 'class' => 'needs-validation', 'novalidate']) }}
            <div class="card">
                <div class="card-body">
                    <div class="tab-content" id="pills-tabContent">
                        <div class="tab-pane fade active show" id="details-tab" role="tabpanel"
                            aria-labelledby="pills-user-tab-1">
                            <div class="row">
                                <div class="col-lg-6">
                                    <h5 class="mb-0">{{ __('Job Details') }}</h5>
                                    <hr>
                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            {!! Form::label('title', __('Job Title'), ['class' => 'form-label']) !!}<x-required></x-required>
                                            {!! Form::text('title', old('title'), [
                                                'class' => 'form-control',
                                                'required' => 'required',
                                                'placeholder' => __('Enter job title'),
                                            ]) !!}
                                        </div>

                                        <div class="form-group col-md-6">
                                            {!! Form::label('status', __('Status'), ['class' => 'form-label']) !!}<x-required></x-required>
                                            {{ Form::select('status', $status, null, ['class' => 'form-control ', 'placeholder' => __('Select Status'), 'required' => 'required']) }}
                                        </div>

                                        <div class="form-group col-md-6">
                                            {!! Form::label('location', __('Location'), ['class' => 'form-label']) !!}<x-required></x-required>
                                            {!! Form::text('location', old('location'), [
                                                'class' => 'form-control',
                                                'required' => 'required',
                                                'placeholder' => __('Enter job Location'),
                                            ]) !!}
                                        </div>

                                        <div class="form-group col-md-6">
                                            {!! Form::label('position', __('No. of Positions'), ['class' => 'form-label']) !!}<x-required></x-required>
                                            {!! Form::number('position', old('positions'), [
                                                'class' => 'form-control',
                                                'required' => 'required',
                                                'step' => '1',
                                                'placeholder' => __('Enter Position'),
                                            ]) !!}
                                        </div>

                                        <div class="form-group col-md-6">
                                            {!! Form::label('salary_from', __('Salary From'), ['class' => 'form-label']) !!}<x-required></x-required>
                                            {!! Form::number('salary_from', old('salary_from'), [
                                                'class' => 'form-control',
                                                'required' => 'required',
                                                'step' => '1',
                                                'placeholder' => __('Enter Amount'),
                                            ]) !!}
                                        </div>

                                        <div class="form-group col-md-6">
                                            {!! Form::label('salary_to', __('Salary To'), ['class' => 'form-label']) !!}<x-required></x-required>
                                            {!! Form::number('salary_to', old('salary_to'), [
                                                'class' => 'form-control',
                                                'required' => 'required',
                                                'step' => '1',
                                                'placeholder' => __('Enter Amount'),
                                            ]) !!}
                                        </div>

                                        <div class="form-group col-md-6">
                                            {!! Form::label('start_date', __('Start Date'), ['class' => 'form-label']) !!}<x-required></x-required>
                                            {!! Form::date('start_date', old('start_date'), [
                                                'class' => 'form-control ',
                                                'autocomplete' => 'off',
                                                'required' => 'required',
                                                'min' => date('Y-m-d'),
                                            ]) !!}
                                        </div>

                                        <div class="form-group col-md-6">
                                            {!! Form::label('end_date', __('End Date'), ['class' => 'form-label']) !!}<x-required></x-required>
                                            {!! Form::date('end_date', old('end_date'), [
                                                'class' => 'form-control ',
                                                'autocomplete' => 'off',
                                                'required' => 'required',
                                                'min' => date('Y-m-d'),
                                            ]) !!}
                                        </div>

                                        <div class="form-group col-md-12">
                                            {{ Form::label('address', __('Address'), ['class' => 'form-label']) }}
                                            {!! Form::textarea('address', null, [
                                                'class' => 'form-control',
                                                'rows' => 3,
                                                'placeholder' => __('Enter Address'),
                                            ]) !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <h5 class="mb-0">{{ __('Category Details') }}</h5>
                                    <hr>
                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            {{ Form::label('job_type', __('Job Type'), ['class' => 'form-label']) }}<x-required></x-required>
                                            {{ Form::select('job_type', $job_type, null, ['class' => 'form-control select', 'required' => 'required']) }}
                                        </div>

                                        <div class="form-group col-md-6">
                                            {!! Form::label('category', __('Job Category'), ['class' => 'form-label']) !!}<x-required></x-required>
                                            {{ Form::select('category', $categories, null, ['class' => 'form-control ', 'placeholder' => __('Select Job Category'), 'required' => 'required']) }}
                                            @if (empty($categories->count()))
                                                <div class=" text-xs">
                                                    {{ __('Please add job category. ') }}<a
                                                        href="{{ route('job-category.index') }}"><b>{{ __('Add Job Category') }}</b></a>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="form-group col-md-6">
                                            {{ Form::label('recruitment_type', __('Recruitment Type'), ['class' => 'form-label']) }}<x-required></x-required>
                                            {{ Form::select('recruitment_type', $recruitment_type, null, ['class' => 'form-control select', 'id' => 'recruitment_type', 'required' => 'required']) }}
                                        </div>

                                        @if (module_is_active('Hrm'))
                                            <div class="form-group col-md-6" id="branch" style="display: none;">
                                                {!! Form::label(
                                                    'branch',
                                                    !empty($company_settings['hrm_branch_name']) ? $company_settings['hrm_branch_name'] : __('Branch'),
                                                    ['class' => 'form-label'],
                                                ) !!}<x-required></x-required>
                                                {{ Form::select('branch', $branches, null, ['class' => 'form-control ', 'placeholder' => __('Select Branch')]) }}
                                            </div>
                                        @endif

                                        <div class="col-6 form-group" id="users" style="display: none;">
                                            {{ Form::label('user_id', __('Client'), ['class' => 'form-label']) }}<x-required></x-required>
                                            {{ Form::select('user_id', $users, null, ['class' => 'form-control select2']) }}
                                            @if (empty($users->count()))
                                                <div class="text-muted text-xs">
                                                    {{ __('Please create new client') }} <a
                                                        href="{{ route('users.index') }}">{{ __('here') }}</a>.
                                                </div>
                                            @endif
                                        </div>

                                        <div class="form-group col-6">
                                            {!! Form::label('link_type', __('Job Application'), ['class' => 'form-label']) !!}<x-required></x-required>
                                            <div class="d-flex radio-check">
                                                <div class="form-check form-check-inline form-group">
                                                    <input type="radio" id="existing_link" value="Existing Link" name="link_type" class="form-check-input" checked>
                                                    <label class="form-check-label" for="existing_link">{{ __('Existing link') }}</label>
                                                </div>
                                                <div class="form-check form-check-inline form-group">
                                                    <input type="radio" id="custom_link" value="Custom Link" name="link_type" class="form-check-input">
                                                    <label class="form-check-label" for="custom_link">{{ __('Custom Link') }}</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group col-md-12">
                                            {{ Form::label('job_link', __('Job Link'), ['class' => 'form-label']) }}<x-required></x-required>
                                            {{ Form::text('job_link', null, ['class' => 'form-control', 'placeholder' => __('Enter job link')]) }}
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label class="form-label"
                                                for="skill">{{ __('Skill Box') }}</label><x-required></x-required>
                                            <input type="text" class="form-control" value=""
                                                data-toggle="tags" name="skill" placeholder="Skill" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col"></div>
                                <div class="col-6 text-end">
                                    <button class="btn btn-primary d-inline-flex align-items-center"
                                        onClick="changetab('#questions-tab')" type="button">{{ __('Next') }}<i
                                            class="ti ti-chevron-right ms-2"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="questions-tab" role="tabpanel" aria-labelledby="pills-user-tab-2">
                            <div class="row">
                                <div class="col-lg-6">
                                    <h5 class="mb-0">{{ __('Job Checkboxes') }}</h5>
                                    <hr>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <h6>{{ __('Need to Ask ?') }}</h6>
                                                <div class="my-4">
                                                    <div class="form-check custom-checkbox">
                                                        <input type="checkbox" class="form-check-input"
                                                            name="applicant[]" value="gender" id="check-gender">
                                                        <label class="form-check-label"
                                                            for="check-gender">{{ __('Gender') }}
                                                        </label>
                                                    </div>
                                                    <div class="form-check custom-checkbox">
                                                        <input type="checkbox" class="form-check-input"
                                                            name="applicant[]" value="dob" id="check-dob">
                                                        <label class="form-check-label"
                                                            for="check-dob">{{ __('Date Of Birth') }}</label>
                                                    </div>
                                                    <div class="form-check custom-checkbox">
                                                        <input type="checkbox" class="form-check-input"
                                                            name="applicant[]" value="country" id="check-country">
                                                        <label class="form-check-label"
                                                            for="check-country">{{ __('Country') }}</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <h6>{{ __('Need to show Option ?') }}</h6>
                                                <div class="my-4">
                                                    <div class="form-check custom-checkbox">
                                                        <input type="checkbox" class="form-check-input"
                                                            name="visibility[]" value="profile" id="check-profile">
                                                        <label class="form-check-label"
                                                            for="check-profile">{{ __('Profile Image') }}
                                                        </label>
                                                    </div>
                                                    <div class="form-check custom-checkbox">
                                                        <input type="checkbox" class="form-check-input"
                                                            name="visibility[]" value="resume" id="check-resume">
                                                        <label class="form-check-label"
                                                            for="check-resume">{{ __('Resume') }}</label>
                                                    </div>
                                                    <div class="form-check custom-checkbox">
                                                        <input type="checkbox" class="form-check-input"
                                                            name="visibility[]" value="letter" id="check-letter">
                                                        <label class="form-check-label"
                                                            for="check-letter">{{ __('Cover Letter') }}</label>
                                                    </div>
                                                    <div class="form-check custom-checkbox">
                                                        <input type="checkbox" class="form-check-input"
                                                            name="visibility[]" value="terms" id="check-terms">
                                                        <label class="form-check-label"
                                                            for="check-terms">{{ __('Terms And Conditions') }}</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h5 class="mb-0">{{ __('Questions Checkboxes') }}</h5>
                                    <hr>
                                    <div class="form-group col-md-12">
                                        <h6>{{ __('Custom Questions') }}</h6>
                                        <div class="my-4">
                                            @foreach ($customQuestion as $question)
                                                <div class="form-check custom-checkbox">
                                                    <input type="checkbox"
                                                        class="form-check-input  @if ($question->is_required == 'yes') required-checkbox @endif"
                                                        name="custom_question[]" value="{{ $question->id }}"
                                                        @if ($question->is_required == 'yes') required @endif
                                                        id="custom_question_{{ $question->id }}">
                                                    <label class="form-check-label"
                                                        for="custom_question_{{ $question->id }}">{{ $question->question }}
                                                        @if ($question->is_required == 'yes')
                                                            <x-required></x-required>
                                                        @endif
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class=" text-xs mt-1">
                                            {{ __('Create custom question here. ') }}
                                            <a href="{{ route('custom-question.index') }}"><b>{{ __('Create custom question') }}</b></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <button class="btn btn-outline-secondary d-inline-flex align-items-center"
                                        onClick="changetab('#details-tab')" type="button"><i
                                            class="ti ti-chevron-left me-2"></i>{{ __('Previous') }}</button>
                                </div>
                                <div class="col-6 text-end" id="nextButtonContainer">
                                    <button class="btn btn-primary d-inline-flex align-items-center"
                                        onClick="changetab('#descriptions-tab')" type="button">{{ __('Next') }}
                                        <i class="ti ti-chevron-right ms-2"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="descriptions-tab" role="tabpanel"
                            aria-labelledby="pills-user-tab-2">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group col-md-12">
                                        {!! Form::label('description', __('Job Description'), ['class' => 'form-label']) !!}<x-required></x-required>
                                        <textarea name="description"
                                            class="form-control dec_data summernote {{ !empty($errors->first('description')) ? 'is-invalid' : '' }}" required
                                            id="description"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group col-md-12">
                                        {!! Form::label('requirement', __('Job Requirement'), ['class' => 'form-label']) !!}<x-required></x-required>
                                        <textarea name="requirement"
                                            class="form-control req_data summernote  {{ !empty($errors->first('requirement')) ? 'is-invalid' : '' }}" required
                                            id="requirement"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-12" id="termsandcondition">
                                    <div class="form-group terms_val col-md-12">
                                        {!! Form::label('terms_and_conditions', __('Terms And Conditions'), ['class' => 'form-label']) !!}<x-required></x-required>
                                        <textarea name="terms_and_conditions"
                                            class="form-control summernote  {{ !empty($errors->first('terms_and_conditions')) ? 'is-invalid' : '' }}"
                                            id="terms_and_conditions"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 row">
                                <div class="col-6">
                                    <button class="btn btn-outline-secondary d-inline-flex align-items-center"
                                        onClick="changetab('#questions-tab')" type="button"><i
                                            class="ti ti-chevron-left me-2"></i>{{ __('Previous') }}</button>
                                </div>
                                <div class="col-6 text-end" id="savebutton">
                                    <a class="btn btn-secondary btn-submit"
                                        href="{{ route('job.index') }}">{{ __('Cancel') }}</a>
                                    <button class="btn btn-primary btn-submit ms-2" type="submit"
                                        id="submit">{{ __('Create') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
@endsection
