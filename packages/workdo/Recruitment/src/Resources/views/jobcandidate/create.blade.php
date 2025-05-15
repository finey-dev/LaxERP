@extends('layouts.main')
@section('page-title')
    {{ __('Create Job Candidate') }}
@endsection
@section('page-breadcrumb')
    {{ __('Job Candidate') }}
@endsection

@push('css')
    <link href="{{ asset('assets/js/plugins/summernote-0.8.18-dist/summernote-lite.min.css') }}" rel="stylesheet">
@endpush

@push('scripts')
    <script src="{{ asset('assets/js/plugins/summernote-0.8.18-dist/summernote-lite.min.js') }}"></script>
    <script>
    function previewImage(input) {
        const imgPreview = document.getElementById('blah');
        if (input.files && input.files[0]) {
            imgPreview.src = URL.createObjectURL(input.files[0]);
            imgPreview.style.display = 'block';
        } else {
            imgPreview.style.display = 'none';
        }
    }

    function previewImage1(input) {
        const imgPreview = document.getElementById('blah1');
        if (input.files && input.files[0]) {
            imgPreview.src = URL.createObjectURL(input.files[0]);
            imgPreview.style.display = 'block';
        } else {
            imgPreview.style.display = 'none';
        }
    }
</script>

@endpush

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="col-md-12 d-flex align-items-center justify-content-between justify-content-md-end mb-4">
                <div class="col-md-6">
                    <ul class="nav nav-pills nav-fill cust-nav information-tab" id="pills-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="details" data-bs-toggle="pill"
                                data-bs-target="#details-tab" type="button">{{ __('Details') }}</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="experience" data-bs-toggle="pill" data-bs-target="#experience-tab"
                                type="button">{{ __('Experience') }}</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="referrals" data-bs-toggle="pill" data-bs-target="#referrals-tab"
                                type="button">{{ __('Referrals') }}</button>
                        </li>
                        @permission('jobcandidate-attachment manage')
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="attachment-tab" data-bs-toggle="pill" data-bs-target="#attachment"
                                    type="button">{{ __('Attachment') }}</button>
                            </li>
                        @endpermission
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
                        @permission('jobcandidate-activity manage')
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="activity-log-tab" data-bs-toggle="pill"
                                    data-bs-target="#activity-log" type="button">{{ __('Activity Log') }}</button>
                            </li>
                        @endpermission
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="tab-content" id="pills-tabContent">
                <div class="tab-pane fade show active" id="details-tab" role="tabpanel" aria-labelledby="pills-user-tab-1">
                    <div class="card">
                        <div class="card-body">
                            {{ Form::open(['route' => 'job-candidates.store', 'enctype' => 'multipart/form-data', 'class' => 'needs-validation', 'novalidate']) }}
                            <div class="row">
                                <div class="form-group col-md-6">
                                    {{ Form::label('name', __('Name'), ['class' => 'form-label']) }}<x-required></x-required>
                                    <div class="form-icon-user">
                                        {{ Form::text('name', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Name')]) }}
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    {{ Form::label('candidate_category', __('Candidate Category'), ['class' => 'form-label']) }}<x-required></x-required>
                                    {{ Form::select('candidate_category', $candidate_category, null, ['class' => 'form-control', 'id' => 'candidate_category', 'required' => 'required', 'placeholder' => __('Select candidate Category')]) }}
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
                                    {!! Form::label('gender', __('Gender'), ['class' => 'form-label']) !!}<x-required></x-required>
                                    <div class="d-flex radio-check">
                                        <div class="form-check form-check-inline form-group">
                                            <input type="radio" id="g_male" value="Male" name="gender"
                                                class="form-check-input" required>
                                            <label class="form-check-label" for="g_male">{{ __('Male') }}</label>
                                        </div>
                                        <div class="form-check form-check-inline form-group">
                                            <input type="radio" id="g_female" value="Female" name="gender"
                                                class="form-check-input" required>
                                            <label class="form-check-label" for="g_female">{{ __('Female') }}</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    {{ Form::label('address', __('Address'), ['class' => 'form-label']) }}<x-required></x-required>
                                    <div class="input-group">
                                        {{ Form::textarea('address', null, ['class' => 'form-control', 'rows' => 2, 'required' => 'required', 'placeholder' => __('Enter Address')]) }}
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
                                                onchange="previewImage(this)"
                                                required>
                                            <hr>
                                            <img id="blah" width="15%" src="" style="display: none;" />
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    {{ Form::label('resume', __('CV / Resume'), ['class' => 'form-label']) }}<x-required></x-required>
                                    <div class="choose-file form-group">
                                        <label for="resume" class="form-label d-block">
                                            <input type="file" class="form-control file" name="resume"
                                                id="resume" data-filename="resume"
                                                onchange="previewImage1(this)"
                                                required>
                                            <hr>
                                            <img id="blah1" width="15%" src="" style="display: none;" />
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group col-md-12">
                                    {{ Form::label('description', __('Description'), ['class' => 'form-label']) }}
                                    <textarea class="tox-target summernote" id="description" name="description" rows="8"></textarea>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col"></div>
                                <div class="col-6 text-end">
                                    <input type="submit" value="{{ __('Create') }}" class="btn  btn-primary">
                                </div>
                            </div>
                            {{ Form::close() }}
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="experience-tab" role="tabpanel" aria-labelledby="pills-user-tab-2">
                    <div class="text-danger">
                        <p class="items-center text-danger text-center">
                            {{ __('Note : Please first create job candidate details') }}</p>
                    </div>
                    <div class="row">
                        @permission('job project manage')
                            <div class="col-sm-12">
                                <div class="card set-card">
                                    <div class="card-header">
                                        <div class="row">
                                            <div class="col-6">
                                                <h5>{{ __('Project') }}</h5>
                                            </div>
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
                                                    @include('layouts.nodatafound')
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
                                                <h5>{{ __('Experience Candidate Jobs') }}</h5>
                                            </div>
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
                                                    @include('layouts.nodatafound')
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
                                                    @include('layouts.nodatafound')
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
                                                    @include('layouts.nodatafound')
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
                                                    @include('layouts.nodatafound')
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
                                                    @include('layouts.nodatafound')
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
                        <div class="text-danger">
                            <p class="items-center text-danger text-center">
                                {{ __('Note : Please first create job candidate details') }}</p>
                        </div>
                        @permission('jobcandidate-referral manage')
                            <div class="col-sm-12">
                                <div class="card set-card">
                                    <div class="card-header">
                                        <div class="row">
                                            <div class="col-6">
                                                <h5>{{ __('Referrals') }}</h5>
                                            </div>
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
                                                    @include('layouts.nodatafound')
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
                        <div class="text-danger">
                            <p class="items-center text-danger text-center">
                                {{ __('Note : Please first create job candidate details') }}</p>
                        </div>
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
                                                @include('layouts.nodatafound')
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
                        <div class="text-danger">
                            <p class="items-center text-danger text-center">
                                {{ __('Note : Please first create job candidate details') }}</p>
                        </div>
                        @permission('jobcandidate-note manage')
                            <div class="col-sm-12">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="row">
                                            <div class="col-6">
                                                <h5>{{ __('Notes') }}</h5>
                                            </div>
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
                                                    @include('layouts.nodatafound')
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
                        <div class="text-danger">
                            <p class="items-center text-danger text-center">
                                {{ __('Note : Please first create job candidate details') }}</p>
                        </div>
                        @permission('jobcandidate-todo manage')
                            <div class="col-sm-12">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="row">
                                            <div class="col-6">
                                                <h5>{{ __('To Do') }}</h5>
                                            </div>
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
                                                    @include('layouts.nodatafound')
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
                        <div class="text-danger">
                            <p class="items-center text-danger text-center">
                                {{ __('Note : Please first create job candidate details') }}</p>
                        </div>
                        @permission('jobcandidate-activity manage')
                            <div class="col-sm-12">
                                <div class="col-sm-12">
                                    <div class=" multi-collapse mt-2" id="multiCollapseExample1">
                                        <div class="card">
                                            <div class="card-body">
                                                {{ Form::open(['route' => ['job-candidates.create'], 'method' => 'GET', 'id' => 'module_form']) }}
                                                <div class="row d-flex align-items-center justify-content-end">
                                                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 mr-2">
                                                        <div class="btn-box">
                                                            <label for="staff">{{ __('Staff') }}</label>
                                                            <select class="form-control staff " name="staff" id="staff"
                                                                tabindex="-1" aria-hidden="true">
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
                                                            <a href="{{ route('job-candidates.create') }}"
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
                                                    @include('layouts.nodatafound')
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

