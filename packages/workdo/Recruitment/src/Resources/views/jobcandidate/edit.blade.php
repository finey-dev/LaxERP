@extends('layouts.main')
@section('page-title')
    {{ __('Edit Job Candidate') }}
@endsection
@section('page-breadcrumb')
    {{ __('Job Candidate') }}
@endsection

@push('css')
    <link href="{{ asset('assets/js/plugins/summernote-0.8.18-dist/summernote-lite.min.css') }}" rel="stylesheet">
@endpush

@push('scripts')
    <script src="{{ asset('assets/js/plugins/summernote-0.8.18-dist/summernote-lite.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/dropzone-amd-module.min.js') }}"></script>

    <script>
        Dropzone.autoDiscover = false;
        myDropzone = new Dropzone("#dropzonewidget", {
            url: "{{ route('jobcandidate.file.upload', [$job_candidates->id]) }}",
            success: function(file, response) {
                if (response.is_success) {
                    // dropzoneBtn(file, response);
                    location.reload();
                    myDropzone.removeFile(file);
                    toastrs('{{ __('Success') }}', 'File Successfully Uploaded', 'success');
                } else {
                    location.reload();
                    myDropzone.removeFile(response.error);
                    toastrs('Error', response.error, 'error');
                }
            },
            error: function(file, response) {
                myDropzone.removeFile(file);
                location.reload();
                if (response.error) {
                    toastrs('Error', response.error, 'error');
                } else {
                    toastrs('Error', response, 'error');
                }
            }
        });
        myDropzone.on("sending", function(file, xhr, formData) {
            formData.append("_token", $('meta[name="csrf-token"]').attr('content'));
            formData.append("jobcandidate_id", {{ $job_candidates->id }});
        });
    </script>
@endpush

@section('content')
    @php
        $tab = session('experience-tab');
    @endphp
    <div class="row">
        <div class="col-sm-12">
            <div class="col-md-12 d-flex align-items-center justify-content-between justify-content-md-end mb-4">
                <div class="col-md-6">
                    <ul class="nav nav-pills nav-fill cust-nav information-tab" id="pills-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ $tab == false ? 'active' : '' }} " id="details"
                                data-bs-toggle="pill" data-bs-target="#details-tab"
                                type="button">{{ __('Details') }}</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ $tab == true ? 'active' : '' }}" id="experience"
                                data-bs-toggle="pill" data-bs-target="#experience-tab"
                                type="button">{{ __('Experience') }}</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="referrals" data-bs-toggle="pill" data-bs-target="#referrals-tab"
                                type="button">{{ __('Referrals') }}</button>
                        </li>
                        @if (module_is_active('FileSharing'))
                            @permission('jobcandidate-attachment manage')
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="attachment-tab" data-bs-toggle="pill" data-bs-target="#attachment"
                                        type="button">{{ __('Attachment') }}</button>
                                </li>
                            @endpermission
                        @endif
                        @permission('jobcandidate-note manage')
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="notes-tab" data-bs-toggle="pill" data-bs-target="#notes"
                                    type="button">{{ __('Notes') }}</button>
                            </li>
                        @endpermission
                        @permission('jobcandidate-todo manage')
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="to-do-tab" data-bs-toggle="pill" data-bs-target="#to-do"
                                    type="button">{{ __('To Do') }}</button>
                            </li>
                        @endpermission
                        @if (module_is_active('ActivityLog'))
                            @permission('jobcandidate-activity manage')
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="activity-log-tab" data-bs-toggle="pill"
                                        data-bs-target="#activity-log" type="button">{{ __('Activity Log') }}</button>
                                </li>
                            @endpermission
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">

            <div class="tab-content" id="pills-tabContent">
                <div class="tab-pane fade  {{ $tab == false ? 'show active' : '' }}" id="details-tab" role="tabpanel"
                    aria-labelledby="pills-user-tab-1">
                    <div class="card">
                        <div class="card-body">
                            {{ Form::model($job_candidates, ['route' => ['job-candidates.update', $job_candidates->id], 'method' => 'PUT', 'enctype' => 'multipart/form-data', 'class' => 'needs-validation', 'novalidate']) }}
                            <div class="row">
                                <div class="form-group col-md-6">
                                    {{ Form::label('name', __('Name'), ['class' => 'form-label']) }}<x-required></x-required>
                                    <div class="form-icon-user">
                                        {{ Form::text('name', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Name')]) }}
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    {!! Form::label('candidate_category', __('Candidate Category'), ['class' => 'form-label']) !!}<x-required></x-required>
                                    {{ Form::select('candidate_category', $candidate_category, null, ['class' => 'form-control ', 'placeholder' => __('Select Candidate Category'), 'required' => 'required']) }}
                                    @if (empty($candidate_category->count()))
                                        <div class=" text-xs">
                                            {{ __('Please add job candidate category. ') }}<a
                                                href="{{ route('jobcandidate-category.index') }}"><b>{{ __('Add Job Candidate Category') }}</b></a>
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group col-md-6">
                                    {{ Form::label('email', __('Email'), ['class' => 'form-label']) }}<x-required></x-required>
                                    <div class="form-icon-user">
                                        {{ Form::email('email', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Email')]) }}
                                    </div>
                                </div>
                                <x-mobile divClass="col-md-6" name="phone" label="{{ __('Phone') }}"
                                    placeholder="{{ __('Enter phone number') }}" id="phone" required>
                                </x-mobile>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    {!! Form::label('dob', __('Date of Birth'), ['class' => 'form-label']) !!}<x-required></x-required>
                                    {{ Form::date('dob', null, ['class' => 'form-control ', 'required' => 'required', 'autocomplete' => 'off', 'max' => date('Y-m-d')]) }}
                                </div>
                                <div class="form-group col-md-6 gender">
                                    {!! Form::label('gender', __('Gender'), ['class' => 'form-label']) !!}
                                    <div class="d-flex radio-check">
                                        <div class="form-check form-check-inline form-group">
                                            <input type="radio" id="g_male" value="Male" name="gender"
                                                class="form-check-input"
                                                {{ isset($job_candidates->gender) && $job_candidates->gender == 'Male' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="g_male">{{ __('Male') }}</label>
                                        </div>
                                        <div class="form-check form-check-inline form-group">
                                            <input type="radio" id="g_female" value="Female" name="gender"
                                                class="form-check-input"
                                                {{ isset($job_candidates->gender) && $job_candidates->gender == 'Female' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="g_female">{{ __('Female') }}</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    {{ Form::label('address', __('Address'), ['class' => 'form-label']) }}<x-required></x-required>
                                    <div class="input-group">
                                        {{ Form::textarea('address', null, ['class' => 'form-control', 'required' => 'required', 'rows' => 2, 'placeholder' => __('Enter Address')]) }}
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    {{ Form::label('country', __('Country'), ['class' => 'form-label']) }}<x-required></x-required>
                                    <div class="form-icon-user">
                                        {{ Form::text('country', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Country')]) }}
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    {{ Form::label('state', __('State'), ['class' => 'form-label']) }}<x-required></x-required>
                                    <div class="form-icon-user">
                                        {{ Form::text('state', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter State')]) }}
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    {{ Form::label('city', __('City'), ['class' => 'form-label']) }}<x-required></x-required>
                                    <div class="form-icon-user">
                                        {{ Form::text('city', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter City')]) }}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    {{ Form::label('profile', __('Profile'), ['class' => 'form-label']) }}<x-required></x-required>
                                    <div class="choose-file form-group">
                                        <label for="profile" class="form-label d-block">
                                            <input type="file" class="form-control file" name="profile"
                                                id="profile" data-filename="profile"
                                                onchange="document.getElementById('blah').src = window.URL.createObjectURL(this.files[0])">
                                            <hr>
                                            <div class="mt-1">
                                                <img src="@if ($job_candidates->profile) {{ get_file($job_candidates->profile) }} @endif"
                                                    id="blah" width="15%" />
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group col-md-6">
                                    {{ Form::label('resume', __('CV / Resume'), ['class' => 'form-label']) }}<x-required></x-required>
                                    <div class="choose-file form-group">
                                        <label for="resume" class="form-label d-block">
                                            <input type="file" class="form-control file" name="resume"
                                                id="resume" data-filename="resume"
                                                onchange="document.getElementById('blah1').src = window.URL.createObjectURL(this.files[0])">
                                            <hr>
                                            <div class="mt-1">
                                                <img src="@if ($job_candidates->resume) {{ get_file($job_candidates->resume) }} @endif"
                                                    id="blah1" width="15%" />
                                            </div>
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group col-md-12">
                                    {{ Form::label('description', __('Description'), ['class' => 'form-label']) }}
                                    <textarea class="tox-target summernote" id="description" name="description" rows="8">{!! $job_candidates->description !!}</textarea>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col"></div>
                                <div class="col-6 text-end">
                                    <input type="submit" value="{{ __('Update') }}" class="btn  btn-primary">
                                </div>
                            </div>
                            {{ Form::close() }}
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade {{ $tab == true ? 'show active' : '' }}" id="experience-tab" role="tabpanel"
                    aria-labelledby="pills-user-tab-2">
                    <div class="row">
                        @permission('job project manage')
                            <div class="col-sm-12">
                                <div class="card set-card">
                                    <div class="card-header">
                                        <div class="row">
                                            <div class="col-6">
                                                <h5>{{ __('Project') }}</h5>
                                            </div>
                                            @permission('job project create')
                                                <div class="col-6 text-end create_btn">
                                                    <a data-url="{{ route('job-project.create', ['id' => $job_candidates->id]) }}"
                                                        data-ajax-popup="true" data-title="{{ __('Create Job Project') }}"
                                                        data-bs-toggle="tooltip" title="" data-size="lg"
                                                        class="btn btn-sm btn-primary"
                                                        data-bs-original-title="{{ __('Create') }}">
                                                        <i class="ti ti-plus"></i>
                                                    </a>
                                                </div>
                                            @endpermission
                                        </div>
                                    </div>
                                    <div class="card-body table-border-style">
                                        <div class="table-responsive">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th>{{ __('Title') }}</th>
                                                        <th>{{ __('Organization') }}</th>
                                                        <th>{{ __('Start Date') }}</th>
                                                        <th>{{ __('End Date') }}</th>
                                                        <th>{{ __('Reference') }}</th>
                                                        @if (Laratrust::hasPermission('job project show') ||
                                                                Laratrust::hasPermission('job project edit') ||
                                                                Laratrust::hasPermission('job project delete'))
                                                            <th>{{ __('Action') }}</th>
                                                        @endif
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse ($job_projects as $job_project)
                                                        <tr>
                                                            <td>{{ !empty($job_project->title) ? $job_project->title : '' }}
                                                            </td>
                                                            <td>{{ !empty($job_project->organization) ? $job_project->organization : '' }}
                                                            </td>

                                                            <td>{{ !empty($job_project->start_date) ? company_date_formate($job_project->start_date) : '' }}
                                                            </td>
                                                            <td>{{ !empty($job_project->end_date) ? company_date_formate($job_project->end_date) : '' }}
                                                            </td>
                                                            <td>{{ !empty($job_project->reference) ? ($job_project->reference == 'yes' ? 'Yes' : 'No') : '' }}
                                                            </td>
                                                            @if (Laratrust::hasPermission('job project show') ||
                                                                    Laratrust::hasPermission('job project edit') ||
                                                                    Laratrust::hasPermission('job project delete'))
                                                                <td class="Action">
                                                                    <span>
                                                                        @permission('job project show')
                                                                            <div class="action-btn  me-2 show_btn">
                                                                                <a class="mx-3 btn btn-sm  align-items-center bg-warning"
                                                                                    data-url="{{ route('job-project.show', $job_project->id) }}"
                                                                                    data-ajax-popup="true" data-size="lg"
                                                                                    data-bs-toggle="tooltip" title=""
                                                                                    data-title="{{ __('Job Project Details') }}"
                                                                                    data-bs-original-title="{{ __('View') }}">
                                                                                    <i class="ti ti-eye text-white"></i>
                                                                                </a>
                                                                            </div>
                                                                        @endpermission
                                                                        @permission('job project edit')
                                                                            <div class="action-btn me-2 edit_btn">
                                                                                <a class="mx-3 btn btn-sm  align-items-center bg-info"
                                                                                    data-url="{{ route('job-project.edit', $job_project->id) }}"
                                                                                    data-ajax-popup="true" data-size="lg"
                                                                                    data-bs-toggle="tooltip" title=""
                                                                                    data-title="{{ __('Edit Job Project') }}"
                                                                                    data-bs-original-title="{{ __('Edit') }}">
                                                                                    <i class="ti ti-pencil text-white"></i>
                                                                                </a>
                                                                            </div>
                                                                        @endpermission
                                                                        @permission('job project delete')
                                                                            <div class="action-btn delete_btn">
                                                                                {{ Form::open(['route' => ['job-project.destroy', $job_project->id], 'class' => 'm-0']) }}
                                                                                @method('DELETE')
                                                                                <a class="mx-3 btn btn-sm  align-items-center bs-pass-para show_confirm bg-danger"
                                                                                    data-bs-toggle="tooltip" title="{{ __('Delete') }}"
                                                                                    data-bs-original-title="Delete"
                                                                                    aria-label="Delete"
                                                                                    data-confirm="{{ __('Are You Sure?') }}"
                                                                                    data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                                                    data-confirm-yes="delete-form-{{ $job_project->id }}"><i
                                                                                        class="ti ti-trash text-white text-white"></i></a>
                                                                                {{ Form::close() }}
                                                                            </div>
                                                                        @endpermission
                                                                    </span>
                                                                </td>
                                                            @endif
                                                        </tr>
                                                    @empty
                                                        @include('layouts.nodatafound')
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endpermission
                        @permission('experience candidate job manage')
                            <div class="col-sm-12">
                                <div class="card set-card">
                                    <div class="card-header">
                                        <div class="row">
                                            <div class="col-6">
                                                <h5>{{ __('Jobs') }}</h5>
                                            </div>
                                            @permission('experience candidate job create')
                                                <div class="col-6 text-end create_btn">
                                                    <a data-url="{{ route('job-experience-candidate.create', ['id' => $job_candidates->id]) }}"
                                                        data-ajax-popup="true" data-size="lg"
                                                        data-title="{{ __('Create Experience Candidate Job') }}"
                                                        data-bs-toggle="tooltip" title="" class="btn btn-sm btn-primary"
                                                        data-bs-original-title="{{ __('Create') }}">
                                                        <i class="ti ti-plus"></i>
                                                    </a>
                                                </div>
                                            @endpermission
                                        </div>
                                    </div>
                                    <div class="card-body table-border-style">
                                        <div class="table-responsive">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th>{{ __('Title') }}</th>
                                                        <th>{{ __('Organization') }}</th>
                                                        <th>{{ __('Start Date') }}</th>
                                                        <th>{{ __('End Date') }}</th>
                                                        <th>{{ __('Reference') }}</th>
                                                        @if (Laratrust::hasPermission('experience candidate job show') ||
                                                                Laratrust::hasPermission('experience candidate job edit') ||
                                                                Laratrust::hasPermission('experience candidate job delete'))
                                                            <th>{{ __('Action') }}</th>
                                                        @endif
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse ($job_experience_candidates as $job_experience_candidate)
                                                        <tr>
                                                            <td>{{ !empty($job_experience_candidate->title) ? $job_experience_candidate->title : '' }}
                                                            </td>
                                                            <td>{{ !empty($job_experience_candidate->organization) ? $job_experience_candidate->organization : '' }}
                                                            </td>

                                                            <td>{{ !empty($job_experience_candidate->start_date) ? company_date_formate($job_experience_candidate->start_date) : '' }}
                                                            </td>
                                                            <td>{{ !empty($job_experience_candidate->end_date) ? company_date_formate($job_experience_candidate->end_date) : '' }}
                                                            </td>
                                                            <td>{{ !empty($job_experience_candidate->reference) ? ($job_experience_candidate->reference == 'yes' ? 'Yes' : 'No') : '' }}
                                                            </td>
                                                            @if (Laratrust::hasPermission('experience candidate job show') ||
                                                                    Laratrust::hasPermission('experience candidate job edit') ||
                                                                    Laratrust::hasPermission('experience candidate job delete'))
                                                                <td class="Action">
                                                                    <span>
                                                                        @permission('experience candidate job show')
                                                                            <div class="action-btn me-2 show_btn">
                                                                                <a class="mx-3 btn btn-sm  align-items-center bg-warning"
                                                                                    data-url="{{ route('job-experience-candidate.show', $job_experience_candidate->id) }}"
                                                                                    data-ajax-popup="true" data-size="lg"
                                                                                    data-bs-toggle="tooltip" title=""
                                                                                    data-title="{{ __('Experience Candidate Job Details') }}"
                                                                                    data-bs-original-title="{{ __('View') }}">
                                                                                    <i class="ti ti-eye text-white"></i>
                                                                                </a>
                                                                            </div>
                                                                        @endpermission
                                                                        @permission('experience candidate job edit')
                                                                            <div class="action-btn me-2 edit_btn">
                                                                                <a class="mx-3 btn btn-sm  align-items-center bg-info"
                                                                                    data-url="{{ route('job-experience-candidate.edit', $job_experience_candidate->id) }}"
                                                                                    data-ajax-popup="true" data-size="lg"
                                                                                    data-bs-toggle="tooltip" title=""
                                                                                    data-title="{{ __('Edit Experience Candidate Job') }}"
                                                                                    data-bs-original-title="{{ __('Edit') }}">
                                                                                    <i class="ti ti-pencil text-white"></i>
                                                                                </a>
                                                                            </div>
                                                                        @endpermission
                                                                        @permission('experience candidate job delete')
                                                                            <div class="action-btn delete_btn">
                                                                                {{ Form::open(['route' => ['job-experience-candidate.destroy', $job_experience_candidate->id], 'class' => 'm-0']) }}
                                                                                @method('DELETE')
                                                                                <a class="mx-3 btn btn-sm  align-items-center bs-pass-para show_confirm bg-danger"
                                                                                    data-bs-toggle="tooltip" title="{{ __('Delete') }}"
                                                                                    data-bs-original-title="Delete"
                                                                                    aria-label="Delete"
                                                                                    data-confirm="{{ __('Are You Sure?') }}"
                                                                                    data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                                                    data-confirm-yes="delete-form-{{ $job_experience_candidate->id }}"><i
                                                                                        class="ti ti-trash text-white text-white"></i></a>
                                                                                {{ Form::close() }}
                                                                            </div>
                                                                        @endpermission
                                                                    </span>
                                                                </td>
                                                            @endif
                                                        </tr>
                                                    @empty
                                                        @include('layouts.nodatafound')
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endpermission
                    </div>

                    <div class="row">
                        @permission('job qualification manage')
                            <div class="col-sm-12">
                                <div class="card set-card">
                                    <div class="card-header">
                                        <div class="row">
                                            <div class="col-6">
                                                <h5>{{ __('Qualifications') }}</h5>
                                            </div>
                                            @permission('job qualification create')
                                                <div class="col-6 text-end create_btn">
                                                    <a data-url="{{ route('job-qualification.create', ['id' => $job_candidates->id]) }}"
                                                        data-ajax-popup="true" data-size="lg"
                                                        data-title="{{ __('Create Job Qualification') }}"
                                                        data-bs-toggle="tooltip" title="" class="btn btn-sm btn-primary"
                                                        data-bs-original-title="{{ __('Create') }}">
                                                        <i class="ti ti-plus"></i>
                                                    </a>
                                                </div>
                                            @endpermission
                                        </div>
                                    </div>
                                    <div class="card-body table-border-style">
                                        <div class="table-responsive">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th>{{ __('Title') }}</th>
                                                        <th>{{ __('Organization') }}</th>
                                                        <th>{{ __('Start Date') }}</th>
                                                        <th>{{ __('End Date') }}</th>
                                                        <th>{{ __('Reference') }}</th>
                                                        @if (Laratrust::hasPermission('job qualification show') ||
                                                                Laratrust::hasPermission('job qualification edit') ||
                                                                Laratrust::hasPermission('job qualification delete'))
                                                            <th>{{ __('Action') }}</th>
                                                        @endif
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse ($job_qualifications as $job_qualification)
                                                        <tr>
                                                            <td>{{ !empty($job_qualification->title) ? $job_qualification->title : '' }}
                                                            </td>
                                                            <td>{{ !empty($job_qualification->organization) ? $job_qualification->organization : '' }}
                                                            </td>

                                                            <td>{{ !empty($job_qualification->start_date) ? company_date_formate($job_qualification->start_date) : '' }}
                                                            </td>
                                                            <td>{{ !empty($job_qualification->end_date) ? company_date_formate($job_qualification->end_date) : '' }}
                                                            </td>
                                                            <td>{{ !empty($job_qualification->reference) ? ($job_qualification->reference == 'yes' ? 'Yes' : 'No') : '' }}
                                                            </td>
                                                            @if (Laratrust::hasPermission('job qualification show') ||
                                                                    Laratrust::hasPermission('job qualification edit') ||
                                                                    Laratrust::hasPermission('job qualification delete'))
                                                                <td class="Action">
                                                                    <span>
                                                                        @permission('job qualification show')
                                                                            <div class="action-btn me-2 show_btn">
                                                                                <a class="mx-3 btn btn-sm  align-items-center bg-warning"
                                                                                    data-url="{{ route('job-qualification.show', $job_qualification->id) }}"
                                                                                    data-ajax-popup="true" data-size="lg"
                                                                                    data-bs-toggle="tooltip" title=""
                                                                                    data-title="{{ __('Job Qualification Details') }}"
                                                                                    data-bs-original-title="{{ __('View') }}">
                                                                                    <i class="ti ti-eye text-white"></i>
                                                                                </a>
                                                                            </div>
                                                                        @endpermission
                                                                        @permission('job qualification edit')
                                                                            <div class="action-btn me-2 edit_btn">
                                                                                <a class="mx-3 btn btn-sm  align-items-center bg-info"
                                                                                    data-url="{{ route('job-qualification.edit', $job_qualification->id) }}"
                                                                                    data-ajax-popup="true" data-size="lg"
                                                                                    data-bs-toggle="tooltip" title=""
                                                                                    data-title="{{ __('Edit Job Qualification') }}"
                                                                                    data-bs-original-title="{{ __('Edit') }}">
                                                                                    <i class="ti ti-pencil text-white"></i>
                                                                                </a>
                                                                            </div>
                                                                        @endpermission
                                                                        @permission('job qualification delete')
                                                                            <div class="action-btn delete_btn">
                                                                                {{ Form::open(['route' => ['job-qualification.destroy', $job_qualification->id], 'class' => 'm-0']) }}
                                                                                @method('DELETE')
                                                                                <a class="mx-3 btn btn-sm  align-items-center bs-pass-para show_confirm bg-danger"
                                                                                    data-bs-toggle="tooltip" title="{{ __('Delete') }}"
                                                                                    data-bs-original-title="Delete"
                                                                                    aria-label="Delete"
                                                                                    data-confirm="{{ __('Are You Sure?') }}"
                                                                                    data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                                                    data-confirm-yes="delete-form-{{ $job_qualification->id }}"><i
                                                                                        class="ti ti-trash text-white text-white"></i></a>
                                                                                {{ Form::close() }}
                                                                            </div>
                                                                        @endpermission
                                                                    </span>
                                                                </td>
                                                            @endif
                                                        </tr>
                                                    @empty
                                                        @include('layouts.nodatafound')
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endpermission
                        @permission('job skill manage')
                            <div class="col-sm-12">
                                <div class="card set-card">
                                    <div class="card-header">
                                        <div class="row">
                                            <div class="col-6">
                                                <h5>{{ __('Skills') }}</h5>
                                            </div>
                                            @permission('job skill create')
                                                <div class="col-6 text-end create_btn">
                                                    <a data-url="{{ route('job-skill.create', ['id' => $job_candidates->id]) }}"
                                                        data-ajax-popup="true" data-title="{{ __('Create Job Skill') }}"
                                                        data-bs-toggle="tooltip" title="" data-size="lg"
                                                        class="btn btn-sm btn-primary"
                                                        data-bs-original-title="{{ __('Create') }}">
                                                        <i class="ti ti-plus"></i>
                                                    </a>
                                                </div>
                                            @endpermission
                                        </div>
                                    </div>
                                    <div class="card-body table-border-style">
                                        <div class="table-responsive">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th>{{ __('Type') }}</th>
                                                        <th>{{ __('Title') }}</th>
                                                        <th>{{ __('Organization') }}</th>
                                                        <th>{{ __('Start Date') }}</th>
                                                        <th>{{ __('End Date') }}</th>
                                                        <th>{{ __('Reference') }}</th>
                                                        @if (Laratrust::hasPermission('job skill show') ||
                                                                Laratrust::hasPermission('job skill edit') ||
                                                                Laratrust::hasPermission('job skill delete'))
                                                            <th>{{ __('Action') }}</th>
                                                        @endif
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse ($job_skills as $job_skill)
                                                        <tr>
                                                            <td>{{ !empty($job_skill->type) ? $job_skill->type : '' }}
                                                            </td>
                                                            <td>{{ !empty($job_skill->title) ? $job_skill->title : '' }}
                                                            </td>
                                                            <td>{{ !empty($job_skill->organization) ? $job_skill->organization : '' }}
                                                            </td>

                                                            <td>{{ !empty($job_skill->start_date) ? company_date_formate($job_skill->start_date) : '' }}
                                                            </td>
                                                            <td>{{ !empty($job_skill->end_date) ? company_date_formate($job_skill->end_date) : '' }}
                                                            </td>
                                                            <td>{{ !empty($job_skill->reference) ? ($job_skill->reference == 'yes' ? 'Yes' : 'No') : '' }}
                                                            </td>
                                                            @if (Laratrust::hasPermission('job skill show') ||
                                                                    Laratrust::hasPermission('job skill edit') ||
                                                                    Laratrust::hasPermission('job skill delete'))
                                                                <td class="Action">
                                                                    <span>
                                                                        @permission('job skill show')
                                                                            <div class="action-btn me-2 show_btn">
                                                                                <a class="mx-3 btn btn-sm  align-items-center bg-warning"
                                                                                    data-url="{{ route('job-skill.show', $job_skill->id) }}"
                                                                                    data-ajax-popup="true" data-size="lg"
                                                                                    data-bs-toggle="tooltip" title=""
                                                                                    data-title="{{ __('Job Skill Details') }}"
                                                                                    data-bs-original-title="{{ __('View') }}">
                                                                                    <i class="ti ti-eye text-white"></i>
                                                                                </a>
                                                                            </div>
                                                                        @endpermission
                                                                        @permission('job skill edit')
                                                                            <div class="action-btn me-2 edit_btn">
                                                                                <a class="mx-3 btn btn-sm  align-items-center bg-info"
                                                                                    data-url="{{ route('job-skill.edit', $job_skill->id) }}"
                                                                                    data-ajax-popup="true" data-size="lg"
                                                                                    data-bs-toggle="tooltip" title=""
                                                                                    data-title="{{ __('Edit Job Skill') }}"
                                                                                    data-bs-original-title="{{ __('Edit') }}">
                                                                                    <i class="ti ti-pencil text-white"></i>
                                                                                </a>
                                                                            </div>
                                                                        @endpermission
                                                                        @permission('job skill delete')
                                                                            <div class="action-btn delete_btn">
                                                                                {{ Form::open(['route' => ['job-skill.destroy', $job_skill->id], 'class' => 'm-0']) }}
                                                                                @method('DELETE')
                                                                                <a class="mx-3 btn btn-sm  align-items-center bs-pass-para show_confirm bg-danger"
                                                                                    data-bs-toggle="tooltip" title="{{ __('Delete') }}"
                                                                                    data-bs-original-title="Delete"
                                                                                    aria-label="Delete"
                                                                                    data-confirm="{{ __('Are You Sure?') }}"
                                                                                    data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                                                    data-confirm-yes="delete-form-{{ $job_skill->id }}"><i
                                                                                        class="ti ti-trash text-white text-white"></i></a>
                                                                                {{ Form::close() }}
                                                                            </div>
                                                                        @endpermission
                                                                    </span>
                                                                </td>
                                                            @endif
                                                        </tr>
                                                    @empty
                                                        @include('layouts.nodatafound')
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endpermission
                    </div>

                    <div class="row">
                        @permission('job award manage')
                            <div class="col-sm-12">
                                <div class="card set-card">
                                    <div class="card-header">
                                        <div class="row">
                                            <div class="col-6">
                                                <h5>{{ __('Awards') }}</h5>
                                            </div>
                                            @permission('job award create')
                                                <div class="col-6 text-end create_btn">
                                                    <a data-url="{{ route('job-award.create', ['id' => $job_candidates->id]) }}"
                                                        data-ajax-popup="true" data-title="{{ __('Create job Award') }}"
                                                        data-bs-toggle="tooltip" title="" data-size="lg"
                                                        class="btn btn-sm btn-primary"
                                                        data-bs-original-title="{{ __('Create') }}">
                                                        <i class="ti ti-plus"></i>
                                                    </a>
                                                </div>
                                            @endpermission
                                        </div>
                                    </div>
                                    <div class="card-body table-border-style">
                                        <div class="table-responsive">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th>{{ __('Title') }}</th>
                                                        <th>{{ __('Organization') }}</th>
                                                        <th>{{ __('Start Date') }}</th>
                                                        <th>{{ __('End Date') }}</th>
                                                        <th>{{ __('Reference') }}</th>
                                                        @if (Laratrust::hasPermission('job award show') ||
                                                                Laratrust::hasPermission('job award edit') ||
                                                                Laratrust::hasPermission('job award delete'))
                                                            <th>{{ __('Action') }}</th>
                                                        @endif
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse ($job_awards as $job_award)
                                                        <tr>
                                                            <td>{{ !empty($job_award->title) ? $job_award->title : '' }}
                                                            </td>
                                                            <td>{{ !empty($job_award->organization) ? $job_award->organization : '' }}
                                                            </td>

                                                            <td>{{ !empty($job_award->start_date) ? company_date_formate($job_award->start_date) : '' }}
                                                            </td>
                                                            <td>{{ !empty($job_award->end_date) ? company_date_formate($job_award->end_date) : '' }}
                                                            </td>
                                                            <td>{{ !empty($job_award->reference) ? ($job_award->reference == 'yes' ? 'Yes' : 'No') : '' }}
                                                            </td>
                                                            @if (Laratrust::hasPermission('job award show') ||
                                                                    Laratrust::hasPermission('job award edit') ||
                                                                    Laratrust::hasPermission('job award delete'))
                                                                <td class="Action">
                                                                    <span>
                                                                        @permission('job award show')
                                                                            <div class="action-btn me-2 show_btn">
                                                                                <a class="mx-3 btn btn-sm  align-items-center bg-warning"
                                                                                    data-url="{{ route('job-award.show', $job_award->id) }}"
                                                                                    data-ajax-popup="true" data-size="lg"
                                                                                    data-bs-toggle="tooltip" title=""
                                                                                    data-title="{{ __('Job Award Details') }}"
                                                                                    data-bs-original-title="{{ __('View') }}">
                                                                                    <i class="ti ti-eye text-white"></i>
                                                                                </a>
                                                                            </div>
                                                                        @endpermission
                                                                        @permission('job award edit')
                                                                            <div class="action-btn me-2 edit_btn">
                                                                                <a class="mx-3 btn btn-sm  align-items-center bg-info"
                                                                                    data-url="{{ route('job-award.edit', $job_award->id) }}"
                                                                                    data-ajax-popup="true" data-size="lg"
                                                                                    data-bs-toggle="tooltip" title=""
                                                                                    data-title="{{ __('Edit Job Award') }}"
                                                                                    data-bs-original-title="{{ __('Edit') }}">
                                                                                    <i class="ti ti-pencil text-white"></i>
                                                                                </a>
                                                                            </div>
                                                                        @endpermission
                                                                        @permission('job award delete')
                                                                            <div class="action-btn delete_btn">
                                                                                {{ Form::open(['route' => ['job-award.destroy', $job_award->id], 'class' => 'm-0']) }}
                                                                                @method('DELETE')
                                                                                <a class="mx-3 btn btn-sm  align-items-center bs-pass-para show_confirm bg-danger"
                                                                                    data-bs-toggle="tooltip" title="{{ __('Delete') }}"
                                                                                    data-bs-original-title="Delete"
                                                                                    aria-label="Delete"
                                                                                    data-confirm="{{ __('Are You Sure?') }}"
                                                                                    data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                                                    data-confirm-yes="delete-form-{{ $job_award->id }}"><i
                                                                                        class="ti ti-trash text-white text-white"></i></a>
                                                                                {{ Form::close() }}
                                                                            </div>
                                                                        @endpermission
                                                                    </span>
                                                                </td>
                                                            @endif
                                                        </tr>
                                                    @empty
                                                        @include('layouts.nodatafound')
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endpermission
                        @permission('job experience manage')
                            <div class="col-sm-12">
                                <div class="card set-card">
                                    <div class="card-header">
                                        <div class="row">
                                            <div class="col-6">
                                                <h5>{{ __('Experience') }}</h5>
                                            </div>
                                            @permission('job experience create')
                                                <div class="col-6 text-end create_btn">
                                                    <a data-url="{{ route('job-experience.create', ['id' => $job_candidates->id]) }}"
                                                        data-ajax-popup="true" data-size="lg"
                                                        data-title="{{ __('Create Job Experience') }}" data-bs-toggle="tooltip"
                                                        title="" class="btn btn-sm btn-primary"
                                                        data-bs-original-title="{{ __('Create') }}">
                                                        <i class="ti ti-plus"></i>
                                                    </a>
                                                </div>
                                            @endpermission
                                        </div>
                                    </div>
                                    <div class="card-body table-border-style">
                                        <div class="table-responsive">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th>{{ __('Title') }}</th>
                                                        <th>{{ __('Organization') }}</th>
                                                        <th>{{ __('Start Date') }}</th>
                                                        <th>{{ __('End Date') }}</th>
                                                        <th>{{ __('Country') }}</th>
                                                        <th>{{ __('State') }}</th>
                                                        <th>{{ __('City') }}</th>
                                                        <th>{{ __('Experience Document') }}</th>
                                                        @if (Laratrust::hasPermission('job experience show') ||
                                                                Laratrust::hasPermission('job experience edit') ||
                                                                Laratrust::hasPermission('job experience delete'))
                                                            <th>{{ __('Action') }}</th>
                                                        @endif
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse ($job_experiences as $job_experience)
                                                        <tr>
                                                            <td>{{ !empty($job_experience->title) ? $job_experience->title : '' }}
                                                            </td>
                                                            <td>{{ !empty($job_experience->organization) ? $job_experience->organization : '' }}
                                                            </td>

                                                            <td>{{ !empty($job_experience->start_date) ? company_date_formate($job_experience->start_date) : '' }}
                                                            </td>
                                                            <td>{{ !empty($job_experience->end_date) ? company_date_formate($job_experience->end_date) : '' }}
                                                            </td>
                                                            <td>{{ !empty($job_experience->country) ? $job_experience->country : '' }}
                                                            </td>
                                                            <td>{{ !empty($job_experience->state) ? $job_experience->state : '' }}
                                                            </td>
                                                            <td>{{ !empty($job_experience->city) ? $job_experience->city : '' }}
                                                            </td>
                                                            <td>
                                                                @if (!empty($job_experience->experience_proof))
                                                                    <div class="action-btn bg-primary ms-2">
                                                                        <a class="mx-3 btn btn-sm align-items-center"
                                                                            href="{{ get_file($job_experience->experience_proof) }}"
                                                                            download>
                                                                            <i class="ti ti-download text-white"></i>
                                                                        </a>
                                                                    </div>
                                                                    <div class="action-btn bg-secondary ms-2">
                                                                        <a class="mx-3 btn btn-sm align-items-center"
                                                                            href="{{ get_file($job_experience->experience_proof) }}"
                                                                            target="_blank">
                                                                            <i class="ti ti-crosshair text-white"
                                                                                data-bs-toggle="tooltip"
                                                                                data-bs-original-title="{{ __('Preview') }}"></i>
                                                                        </a>
                                                                    </div>
                                                                @else
                                                                    <p>-</p>
                                                                @endif
                                                            </td>
                                                            @if (Laratrust::hasPermission('job experience show') ||
                                                                    Laratrust::hasPermission('job experience edit') ||
                                                                    Laratrust::hasPermission('job experience delete'))
                                                                <td class="Action">
                                                                    <span>
                                                                        @permission('job experience show')
                                                                            <div class="action-btn me-2 show_btn">
                                                                                <a class="mx-3 btn btn-sm  align-items-center bg-warning"
                                                                                    data-url="{{ route('job-experience.show', $job_experience->id) }}"
                                                                                    data-ajax-popup="true" data-size="lg"
                                                                                    data-bs-toggle="tooltip" title=""
                                                                                    data-title="{{ __('Job Experience Details') }}"
                                                                                    data-bs-original-title="{{ __('View') }}">
                                                                                    <i class="ti ti-eye text-white"></i>
                                                                                </a>
                                                                            </div>
                                                                        @endpermission
                                                                        @permission('job experience edit')
                                                                            <div class="action-btn me-2 edit_btn">
                                                                                <a class="mx-3 btn btn-sm  align-items-center bg-info"
                                                                                    data-url="{{ route('job-experience.edit', $job_experience->id) }}"
                                                                                    data-ajax-popup="true" data-size="lg"
                                                                                    data-bs-toggle="tooltip" title=""
                                                                                    data-title="{{ __('Edit Job Experience') }}"
                                                                                    data-bs-original-title="{{ __('Edit') }}">
                                                                                    <i class="ti ti-pencil text-white"></i>
                                                                                </a>
                                                                            </div>
                                                                        @endpermission
                                                                        @permission('job experience delete')
                                                                            <div class="action-btn delete_btn">
                                                                                {{ Form::open(['route' => ['job-experience.destroy', $job_experience->id], 'class' => 'm-0']) }}
                                                                                @method('DELETE')
                                                                                <a class="mx-3 btn btn-sm  align-items-center bs-pass-para show_confirm bg-danger"
                                                                                    data-bs-toggle="tooltip" title="{{ __('Delete') }}"
                                                                                    data-bs-original-title="Delete"
                                                                                    aria-label="Delete"
                                                                                    data-confirm="{{ __('Are You Sure?') }}"
                                                                                    data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                                                    data-confirm-yes="delete-form-{{ $job_experience->id }}"><i
                                                                                        class="ti ti-trash text-white text-white"></i></a>
                                                                                {{ Form::close() }}
                                                                            </div>
                                                                        @endpermission
                                                                    </span>
                                                                </td>
                                                            @endif
                                                        </tr>
                                                    @empty
                                                        @include('layouts.nodatafound')
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endpermission
                    </div>
                </div>
                <div class="tab-pane fade" id="referrals-tab" role="tabpanel" aria-labelledby="pills-user-tab-2">
                    <div class="row">
                        @permission('jobcandidate-referral manage')
                            <div class="col-sm-12">
                                <div class="card set-card">
                                    <div class="card-header">
                                        <div class="row">
                                            <div class="col-6">
                                                <h5>{{ __('Referrals') }}</h5>
                                            </div>
                                            @permission('jobcandidate-referral create')
                                                <div class="col-6 text-end create_btn">
                                                    <a data-url="{{ route('jobcandidate-referral.create', ['id' => $job_candidates->id]) }}"
                                                        data-ajax-popup="true" data-title="{{ __('Create Referrals') }}"
                                                        data-bs-toggle="tooltip" title="" data-size="lg"
                                                        class="btn btn-sm btn-primary"
                                                        data-bs-original-title="{{ __('Create') }}">
                                                        <i class="ti ti-plus"></i>
                                                    </a>
                                                </div>
                                            @endpermission
                                        </div>
                                    </div>
                                    <div class="card-body table-border-style">
                                        <div class="table-responsive">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th>{{ __('Title') }}</th>
                                                        <th>{{ __('Organization') }}</th>
                                                        <th>{{ __('Start Date') }}</th>
                                                        <th>{{ __('End Date') }}</th>
                                                        <th>{{ __('Country') }}</th>
                                                        <th>{{ __('State') }}</th>
                                                        <th>{{ __('City') }}</th>
                                                        @if (Laratrust::hasPermission('jobcandidate-referral show') ||
                                                                Laratrust::hasPermission('jobcandidate-referral edit') ||
                                                                Laratrust::hasPermission('jobcandidate-referral delete'))
                                                            <th>{{ __('Action') }}</th>
                                                        @endif
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse ($jobcandidate_referrals as $jobcandidate_referral)
                                                        <tr>
                                                            <td>{{ !empty($jobcandidate_referral->title) ? $jobcandidate_referral->title : '' }}
                                                            </td>
                                                            <td>{{ !empty($jobcandidate_referral->organization) ? $jobcandidate_referral->organization : '' }}
                                                            </td>

                                                            <td>{{ !empty($jobcandidate_referral->start_date) ? company_date_formate($jobcandidate_referral->start_date) : '' }}
                                                            </td>
                                                            <td>{{ !empty($jobcandidate_referral->end_date) ? company_date_formate($jobcandidate_referral->end_date) : '' }}
                                                            </td>
                                                            <td>{{ !empty($jobcandidate_referral->country) ? $jobcandidate_referral->country : '' }}
                                                            </td>
                                                            <td>{{ !empty($jobcandidate_referral->state) ? $jobcandidate_referral->state : '' }}
                                                            </td>
                                                            <td>{{ !empty($jobcandidate_referral->city) ? $jobcandidate_referral->city : '' }}
                                                            </td>
                                                            @if (Laratrust::hasPermission('jobcandidate-referral show') ||
                                                                    Laratrust::hasPermission('jobcandidate-referral edit') ||
                                                                    Laratrust::hasPermission('jobcandidate-referral delete'))
                                                                <td class="Action">
                                                                    <span>
                                                                        @permission('jobcandidate-referral show')
                                                                            <div class="action-btn me-2 show_btn">
                                                                                <a class="mx-3 btn btn-sm  align-items-center bg-warning"
                                                                                    data-url="{{ route('jobcandidate-referral.show', $jobcandidate_referral->id) }}"
                                                                                    data-ajax-popup="true" data-size="lg"
                                                                                    data-bs-toggle="tooltip" title=""
                                                                                    data-title="{{ __('Referrals Details') }}"
                                                                                    data-bs-original-title="{{ __('View') }}">
                                                                                    <i class="ti ti-eye text-white"></i>
                                                                                </a>
                                                                            </div>
                                                                        @endpermission
                                                                        @permission('jobcandidate-referral edit')
                                                                            <div class="action-btn me-2 edit_btn">
                                                                                <a class="mx-3 btn btn-sm  align-items-center bg-info"
                                                                                    data-url="{{ route('jobcandidate-referral.edit', $jobcandidate_referral->id) }}"
                                                                                    data-ajax-popup="true" data-size="lg"
                                                                                    data-bs-toggle="tooltip" title=""
                                                                                    data-title="{{ __('Edit Referrals') }}"
                                                                                    data-bs-original-title="{{ __('Edit') }}">
                                                                                    <i class="ti ti-pencil text-white"></i>
                                                                                </a>
                                                                            </div>
                                                                        @endpermission
                                                                        @permission('jobcandidate-referral delete')
                                                                            <div class="action-btn delete_btn">
                                                                                {{ Form::open(['route' => ['jobcandidate-referral.destroy', $jobcandidate_referral->id], 'class' => 'm-0']) }}
                                                                                @method('DELETE')
                                                                                <a class="mx-3 btn btn-sm  align-items-center bs-pass-para show_confirm bg-danger"
                                                                                    data-bs-toggle="tooltip" title="{{ __('Delete') }}"
                                                                                    data-bs-original-title="Delete"
                                                                                    aria-label="Delete"
                                                                                    data-confirm="{{ __('Are You Sure?') }}"
                                                                                    data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                                                    data-confirm-yes="delete-form-{{ $jobcandidate_referral->id }}"><i
                                                                                        class="ti ti-trash text-white text-white"></i></a>
                                                                                {{ Form::close() }}
                                                                            </div>
                                                                        @endpermission
                                                                    </span>
                                                                </td>
                                                            @endif
                                                        </tr>
                                                    @empty
                                                        @include('layouts.nodatafound')
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endpermission
                    </div>
                </div>
                <div class="tab-pane fade" id="attachment" role="tabpanel" aria-labelledby="pills-user-tab-5">
                    <div class="row">
                        @permission('jobcandidate-attachment manage')
                            <h5 class="d-inline-block my-3">{{ __('Attachments') }}</h5>
                            @permission('jobcandidate-attachment upload')
                                <div class="col-3">
                                    <div class="card border-primary border">
                                        <div class="card-body table-border-style">
                                            <div class="col-md-12 dropzone browse-file" id="dropzonewidget">
                                                <div class="dz-message my-5" data-dz-message>
                                                    <span>{{ __('Drop files here to upload') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endpermission
                            <div class="col-9">
                                <div class="card border-primary border">
                                    <div class="card-body table-border-style">
                                        <div class="table-responsive">
                                            <table class="table mb-0 pc-dt-simple" id="assets">
                                                <thead>
                                                    <tr>
                                                        <th class="text-dark">{{ __('#') }}</th>
                                                        <th class="text-dark">{{ __('File Name') }}</th>
                                                        <th class="text-dark">{{ __('File Size') }}</th>
                                                        <th class="text-dark">{{ __('Date Created') }}</th>
                                                        @if (Laratrust::hasPermission('jobcandidate-attachment delete'))
                                                            <th class="text-dark">{{ __('Action') }}</th>
                                                        @endif
                                                    </tr>
                                                </thead>
                                                @forelse($jobCandidate_attachments as $key =>$jobCandidate_attachment)
                                                    <td>{{ ++$key }}</td>
                                                    <td>{{ $jobCandidate_attachment->file_name }}</td>
                                                    <td>{{ $jobCandidate_attachment->file_size }}</td>
                                                    <td>{{ company_date_formate($jobCandidate_attachment->created_at) }}
                                                    </td>
                                                    <td>
                                                        <div class="action-btn bg-primary ms-2">
                                                            <a href="{{ url($jobCandidate_attachment->file_path) }}"
                                                                class="mx-3 btn btn-sm align-items-center"
                                                                title="{{ __('Download') }}"
                                                                data-bs-original-title="{{ __('Download') }}"
                                                                target="_blank" download>
                                                                <i class="ti ti-download text-white"></i>
                                                            </a>
                                                        </div>
                                                        @permission('jobcandidate-attachment delete')
                                                            <div class="action-btn">
                                                                {{ Form::open(['route' => ['job.attachment.destroy', $jobCandidate_attachment->id], 'class' => 'm-0']) }}
                                                                @method('DELETE')
                                                                <a href="#"
                                                                    class="mx-3 btn btn-sm  align-items-center bs-pass-para show_confirm bg-danger"
                                                                    data-bs-toggle="tooltip" title="{{ __('Delete') }}"
                                                                    aria-label="Delete"
                                                                    data-bs-original-title="{{ __('Delete') }}"
                                                                    data-confirm="{{ __('Are You Sure?') }}"
                                                                    data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                                    data-confirm-yes="delete-form-{{ $jobCandidate_attachment->id }}">
                                                                    <i class="ti ti-trash text-white text-white"></i>
                                                                </a>
                                                                {{ Form::close() }}
                                                            </div>
                                                        @endpermission
                                                    </td>
                                                    </tr>
                                                @empty
                                                    @include('layouts.nodatafound')
                                                @endforelse
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endpermission
                    </div>
                </div>
                <div class="tab-pane fade" id="notes" role="tabpanel" aria-labelledby="pills-user-tab-4">
                    <div class="row">
                        @permission('jobcandidate-note manage')
                            <div class="col-sm-12">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="row">
                                            <div class="col-6">
                                                <h5>{{ __('Notes') }}</h5>
                                            </div>
                                            @permission('jobcandidate-note create')
                                                <div class="col-6 text-end create_btn">
                                                    <a data-url="{{ route('jobcandidatenote.create', ['id' => $job_candidates->id]) }}"
                                                        data-ajax-popup="true" data-size="md"
                                                        data-title="{{ __('Create Notes') }}" data-bs-toggle="tooltip"
                                                        title="" class="btn btn-sm btn-primary"
                                                        data-bs-original-title="{{ __('Create') }}">
                                                        <i class="ti ti-plus"></i>
                                                    </a>
                                                </div>
                                            @endpermission
                                        </div>
                                    </div>
                                    <div class="card-body table-border-style">
                                        <div class="table-responsive">
                                            <table class="table mb-0 pc-dt-simple" id="products1">
                                                <thead>
                                                    <tr>
                                                        <th>{{ __('created_by') }}</th>
                                                        <th>{{ __('Description') }}</th>
                                                        @if (Laratrust::hasPermission('jobcandidate-note show') ||
                                                                Laratrust::hasPermission('jobcandidate-note edit') ||
                                                                Laratrust::hasPermission('jobcandidate-note delete'))
                                                            <th>{{ __('Action') }}</th>
                                                        @endif
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse ($jobCandidate_notes as $jobCandidate_note)
                                                        <tr>
                                                            <td>{{ !empty($jobCandidate_note->created_by) ? $jobCandidate_note->createdBy->name : '' }}
                                                            </td>
                                                            <td>
                                                                <p class="job-to-do">
                                                                    {{ !empty($jobCandidate_note->description) ? $jobCandidate_note->description : '' }}
                                                                </p>
                                                            </td>
                                                            @if (Laratrust::hasPermission('jobcandidate-note show') ||
                                                                    Laratrust::hasPermission('jobcandidate-note edit') ||
                                                                    Laratrust::hasPermission('jobcandidate-note delete'))
                                                                <td class="Action">
                                                                    <span>
                                                                        @permission('jobcandidate-note show')
                                                                            <div class="action-btn me-2">
                                                                                <a data-url="{{ route('jobcandidatenote.description', $jobCandidate_note->id) }}"
                                                                                    class="mx-3 btn btn-sm  align-items-center bg-warning"
                                                                                    data-ajax-popup="true"
                                                                                    data-bs-original-title="{{ __('View') }}"
                                                                                    data-bs-toggle="tooltip" data-size="md"
                                                                                    data-bs-placement="top"
                                                                                    data-title="{{ __('Desciption') }}">
                                                                                    <i class="ti ti-eye text-white"></i>
                                                                                </a>
                                                                            </div>
                                                                        @endpermission
                                                                        @permission('jobcandidate-note edit')
                                                                            <div class="action-btn me-2">
                                                                                <a data-url="{{ route('jobcandidatenote.edit', $jobCandidate_note->id) }}"
                                                                                    data-ajax-popup="true" data-size="md"
                                                                                    data-title="{{ __('Edit Notes') }}"
                                                                                    data-bs-toggle="tooltip" title=""
                                                                                    data-bs-original-title="{{ __('Edit') }}"
                                                                                    class="mx-3 btn btn-sm  align-items-center bg-info">
                                                                                    <i class="ti ti-pencil text-white"></i>
                                                                                </a>
                                                                            </div>
                                                                        @endpermission
                                                                        @permission('jobcandidate-note delete')
                                                                            <div class="action-btn delete_btn">
                                                                                {{ Form::open(['route' => ['jobcandidatenote.destroy', $jobCandidate_note->id], 'class' => 'm-0']) }}
                                                                                @method('DELETE')
                                                                                <a class="mx-3 btn btn-sm  align-items-center bs-pass-para show_confirm bg-danger"
                                                                                    data-bs-toggle="tooltip" title="{{ __('Delete') }}"
                                                                                    aria-label="Delete"
                                                                                    data-bs-original-title="{{ __('Delete') }}"
                                                                                    data-confirm="{{ __('Are You Sure?') }}"
                                                                                    data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                                                    data-confirm-yes="delete-form-{{ $jobCandidate_note->id }}"><i
                                                                                        class="ti ti-trash text-white text-white"></i></a>
                                                                                {{ Form::close() }}
                                                                            </div>
                                                                        @endpermission
                                                                    </span>
                                                                </td>
                                                            @endif
                                                        </tr>
                                                    @empty
                                                        @include('layouts.nodatafound')
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endpermission
                    </div>
                </div>
                <div class="tab-pane fade" id="to-do" role="tabpanel" aria-labelledby="pills-user-tab-4">
                    <div class="row">
                        @permission('jobcandidate-todo manage')
                            <div class="col-sm-12">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="row">
                                            <div class="col-6">
                                                <h5>{{ __('To Do') }}</h5>
                                            </div>
                                            @permission('jobcandidate-todo create')
                                                <div class="col-6 text-end create_btn">
                                                    <a data-url="{{ route('jobcandidatetodo.create', ['id' => $job_candidates->id]) }}"
                                                        data-ajax-popup="true" data-size="lg"
                                                        data-title="{{ __('Create Todo') }}"
                                                        data-bs-toggle="tooltip" title=""
                                                        class="btn btn-sm btn-primary"
                                                        data-bs-original-title="{{ __('Create') }}">
                                                        <i class="ti ti-plus"></i>
                                                    </a>
                                                </div>
                                            @endpermission
                                        </div>
                                    </div>
                                    <div class="card-body table-border-style">
                                        <div class="table-responsive">
                                            <table class="table mb-0 pc-dt-simple" id="products2">
                                                <thead>
                                                    <tr>
                                                        <th>{{ __('Title') }}</th>
                                                        <th>{{ __('Description') }}</th>
                                                        <th>{{ __('Due date') }}</th>
                                                        <th>{{ __('Assigned by') }}</th>
                                                        <th>{{ __('Assigned to') }}</th>
                                                        @if (Laratrust::hasPermission('jobcandidate-todo show') ||
                                                                Laratrust::hasPermission('jobcandidate-todo edit') ||
                                                                Laratrust::hasPermission('jobcandidate-todo delete'))
                                                            <th>{{ __('Action') }}</th>
                                                        @endif
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse ($jobcandidate_todos as $jobcandidate_todo)
                                                        <tr>
                                                            <td>{{ !empty($jobcandidate_todo->title) ? $jobcandidate_todo->title : '' }}
                                                            <td>
                                                                <p class="job-to-do">
                                                                    {{ !empty($jobcandidate_todo->description) ? $jobcandidate_todo->description : '' }}
                                                                </p>
                                                            </td>
                                                            <td>{{ !empty($jobcandidate_todo->due_date) ? company_date_formate($jobcandidate_todo->due_date) : '' }}
                                                            <td>{{ !empty($jobcandidate_todo->assign_by) ? $jobcandidate_todo->assignedByUser->name : '' }}
                                                            </td>
                                                            <td>
                                                                @if ($users = $jobcandidate_todo->users())
                                                                    @foreach ($users as $key => $user)
                                                                        @if ($key < 3)
                                                                            <img alt="image" data-bs-toggle="tooltip"
                                                                                data-bs-placement="top"
                                                                                title="{{ $user->name }}"
                                                                                @if ($user->avatar) src="{{ get_file($user->avatar) }}" @else src="{{ get_file('avatar.png') }}" @endif
                                                                                class="rounded-circle " width="20px"
                                                                                height="20px">
                                                                        @endif
                                                                    @endforeach
                                                                @endif
                                                            </td>
                                                            @if (Laratrust::hasPermission('jobcandidate-todo show') ||
                                                                    Laratrust::hasPermission('jobcandidate-todo edit') ||
                                                                    Laratrust::hasPermission('jobcandidate-todo delete'))
                                                                <td class="Action">
                                                                    <span>
                                                                        @permission('jobcandidate-todo show')
                                                                            <div class="action-btn me-2">
                                                                                <a class="mx-3 btn btn-sm align-items-center bg-warning"
                                                                                    data-url="{{ route('jobcandidatetodo.show', $jobcandidate_todo->id) }}"
                                                                                    data-ajax-popup="true" data-size="md"
                                                                                    data-title="{{ __('To Do Detail') }}"
                                                                                    data-bs-original-title="{{ __('View') }}"
                                                                                    data-bs-toggle="tooltip">
                                                                                    <i class="ti ti-eye text-white"></i>
                                                                                </a>
                                                                            </div>
                                                                        @endpermission
                                                                        @permission('jobcandidate-todo edit')
                                                                            <div class="action-btn me-2">
                                                                                <a data-url="{{ route('jobcandidatetodo.edit', $jobcandidate_todo->id) }}"
                                                                                    data-ajax-popup="true" data-size="md"
                                                                                    data-title="{{ __('Edit Notes') }}"
                                                                                    data-bs-toggle="tooltip" title=""
                                                                                    data-bs-original-title="{{ __('Edit') }}"
                                                                                    class="mx-3 btn btn-sm  align-items-center bg-info">
                                                                                    <i class="ti ti-pencil text-white"></i>
                                                                                </a>
                                                                            </div>
                                                                        @endpermission
                                                                        @permission('jobcandidate-todo delete')
                                                                            <div class="action-btn delete_btn">
                                                                                {{ Form::open(['route' => ['jobcandidatetodo.destroy', $jobcandidate_todo->id], 'class' => 'm-0']) }}
                                                                                @method('DELETE')
                                                                                <a class="mx-3 btn btn-sm  align-items-center bs-pass-para show_confirm bg-danger"
                                                                                    data-bs-toggle="tooltip" title="{{ __('Delete') }}"
                                                                                    aria-label="Delete"
                                                                                    data-bs-original-title="{{ __('Delete') }}"
                                                                                    data-confirm="{{ __('Are You Sure?') }}"
                                                                                    data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                                                    data-confirm-yes="delete-form-{{ $jobcandidate_todo->id }}"><i
                                                                                        class="ti ti-trash text-white text-white"></i></a>
                                                                                {{ Form::close() }}
                                                                            </div>
                                                                        @endpermission
                                                                    </span>
                                                                </td>
                                                            @endif
                                                        </tr>
                                                    @empty
                                                        @include('layouts.nodatafound')
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endpermission
                    </div>
                </div>
                <div class="tab-pane fade" id="activity-log" role="tabpanel" aria-labelledby="pills-user-tab-4">
                    <div class="row">
                        @permission('jobcandidate-activity manage')
                            <div class="col-sm-12">
                                <div class="col-sm-12">
                                    <div class=" multi-collapse mt-2" id="multiCollapseExample1">
                                        <div class="card">
                                            <div class="card-body">
                                                {{ Form::open(['route' => ['job-candidates.edit', \Crypt::encrypt($job_candidates->id)], 'method' => 'GET', 'id' => 'module_form']) }}
                                                <div class="row d-flex align-items-center justify-content-end">
                                                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 mr-2">
                                                        <div class="btn-box">
                                                            <label for="staff">{{ __('Staff') }}</label>
                                                            <select class="form-control staff " name="staff"
                                                                id="staff" tabindex="-1" aria-hidden="true">
                                                                <option value="">{{ __('Select staff') }}</option>
                                                                @foreach ($staffs as $staff)
                                                                    @if ($staff->id == $creatorId)
                                                                        <span class="badge bg-dark">
                                                                            {{ Auth::user()->roles->first()->name }}</span>
                                                                    @else
                                                                        <span class="badge bg-dark">
                                                                            {{ __('') }}</span>
                                                                    @endif
                                                                    <option value="{{ $staff->id }}"
                                                                        {{ isset(request()->staff) && request()->staff == $staff->id ? 'selected' : '' }}>
                                                                        {{ $staff->name }}@if ($staff->id == $creatorId)
                                                                            <span class="badge bg-dark">
                                                                                {{ '(' . $staff->type . ')' }}</span>
                                                                        @endif
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <input type="hidden" name="filter" value="Job Candidate">
                                                    <div class="col-auto float-end ms-2 mt-4">
                                                        <div class="d-flex">
                                                            <a class="btn btn-sm btn-primary me-2"
                                                                onclick="document.getElementById('module_form').submit(); return false;"
                                                                data-bs-toggle="tooltip" title="{{ __('Search') }}"
                                                                data-original-title="{{ __('apply') }}">
                                                                <span class="btn-inner--icon"><i
                                                                        class="ti ti-search"></i></span>
                                                            </a>
                                                            <a href="{{ route('job-candidates.edit', \Crypt::encrypt($job_candidates->id)) }}"
                                                                class="btn btn-sm btn-danger" data-bs-toggle="tooltip"
                                                                title="{{ __('Reset') }}"
                                                                data-original-title="{{ __('Reset') }}">
                                                                <span class="btn-inner--icon"><i
                                                                        class="ti ti-trash-off text-white-off"></i></span>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                                {{ Form::close() }}

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-header">
                                        <div class="row">
                                            <div class="col-6">
                                                <h5>{{ __('Job Activity Log') }}</h5>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body table-border-style">
                                        <div class="table-responsive">
                                            <table class="table mb-0 pc-dt-simple" id="products3">
                                                <thead>
                                                    <tr>
                                                        <th>{{ __('Description') }}</th>
                                                        <th>{{ __('Staff') }}</th>
                                                        @if (Laratrust::hasPermission('jobcandidate-activity delete'))
                                                            <th>{{ __('Action') }}</th>
                                                        @endif
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse ($activitys as $activity)
                                                        <tr>
                                                            <td>{{ $activity->description . (!empty($activity->name) ? $activity->name : '') . '.' }}
                                                            </td>
                                                            <td>{{ !empty($activity->name) ? $activity->name : '--' }}
                                                                @if (!empty($activity->user_id) && $activity->user_id == $creatorId)
                                                                    <span
                                                                        class="badge bg-primary p-2">{{ $activity->type }}</span>
                                                                @endif
                                                            </td>
                                                            @if (Laratrust::hasPermission('jobcandidate-activity delete'))
                                                                <td class="Action">
                                                                    <span>
                                                                        <div class="action-btn">
                                                                            {!! Form::open([
                                                                                'method' => 'DELETE',
                                                                                'route' => ['jobcandidateactivitylog.destroy', $activity->id],
                                                                                'id' => 'delete-form-' . $activity->id,
                                                                            ]) !!}
                                                                            <a class="mx-3 btn btn-sm  align-items-center bs-pass-para show_confirm bg-danger"
                                                                                data-bs-toggle="tooltip"
                                                                                title="{{ __('Delete') }}" data-confirm="{{ __('Are You Sure?') }}" data-text="{{__('This action can not be undone. Do you want to continue?')}}"><i
                                                                                    class="ti ti-trash text-white text-white"></i></a>
                                                                            {!! Form::close() !!}
                                                                        </div>
                                                                    </span>
                                                                </td>
                                                            @endif
                                                        </tr>
                                                    @empty
                                                        @include('layouts.nodatafound')
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endpermission
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
