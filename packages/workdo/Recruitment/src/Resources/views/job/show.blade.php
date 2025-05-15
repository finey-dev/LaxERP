@extends('layouts.main')
@section('page-title')
    {{ __('Job Details') }}
@endsection
@section('page-breadcrumb')
    {{ __('Job Details') }}
@endsection
@push('css')
    <link href="{{ asset('packages/workdo/Recruitment/src/Resources/assets/css/custom.css') }}" rel="stylesheet" />
    <style>
        .job-card .card .info.font-style {
            margin-bottom: 10px;
        }

        .job-card .card .info.font-style strong {
            font-weight: 500;
            margin-right: 5px;
        }
    </style>
@endpush

@push('scripts')
    <script src="{{ asset('assets/js/plugins/dropzone-amd-module.min.js') }}"></script>
    <script>
        Dropzone.autoDiscover = false;
        myDropzone = new Dropzone("#dropzonewidget", {
            url: "{{ route('job.file.upload', [$job->id]) }}",
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
            formData.append("job_id", {{ $job->id }});
        });
    </script>
@endpush
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card ">
                <div class="card-body">
                    <div class="row timeline-wrapper">
                        <div class="col-md-6 col-lg-4 col-xl-6">
                            <div class="timeline-icons"><span class="timeline-dots"></span>
                                <i class="ti ti-plus text-primary"></i>
                            </div>
                            <h6 class="text-primary">{{ __('Create Job') }}</h6>
                            <p class="text-muted text-sm mb-3"><i
                                    class="ti ti-clock mr-2"></i>{{ __('Created on ') }}{{ company_date_formate($job->created_at) }}
                            </p>
                            @permission('job edit')
                                <a href="{{ route('job.edit', $job->id) }}" class="btn btn-sm btn-primary"
                                    data-bs-toggle="tooltip" title="{{ __('Edit') }}" data-title="{{ __('Edit') }}">
                                    <i class="ti ti-pencil mr-2"></i>{{ __('Edit Job') }}</a>
                            @endpermission
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-6">
                            <div class="timeline-icons"><span class="timeline-dots"></span>
                                <i class="ti ti-user-plus text-info"></i>
                            </div>
                            <h6 class="text-info me-2">{{ __('Post job') }}</h6>
                            <p class="text-muted text-sm mb-3">{{ __('Status') }} : @if ($job->is_post == 1)
                                    {{ __('Post') }}@else{{ __('Draft') }}
                                @endif
                            </p>
                            @permission('job post')
                                <a href="{{ route('job.post', $job->id) }}" class="btn btn-sm btn-info"
                                    data-bs-toggle="tooltip" title="{{ __('Post') }}" data-title="{{ __('Post') }}" data-original-title="{{ __('Edit Job') }}"><i
                                        class="ti ti-send mr-2"></i>{{ __('Post Job') }}</a>
                            @endpermission
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-between align-items-center mb-3">
        <div class="col-md-8">
            <ul class="nav nav-pills nav-fill cust-nav information-tab" id="pills-tab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="details-tab" data-bs-toggle="pill" data-bs-target="#details"
                        type="button">{{ __('Details') }}</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="questions-tab" data-bs-toggle="pill" data-bs-target="#questions"
                        type="button">{{ __('Questions') }}</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="descriptions-tab" data-bs-toggle="pill" data-bs-target="#descriptions"
                        type="button">{{ __('Descriptions') }}</button>
                </li>
                @permission('jobapplication manage')
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="applications-tab" data-bs-toggle="pill" data-bs-target="#applications"
                            type="button">{{ __('Applications') }}</button>
                    </li>
                @endpermission
                @if (module_is_active('FileSharing'))
                    @permission('job attachment manage')
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="attachment-tab" data-bs-toggle="pill" data-bs-target="#attachment"
                                type="button">{{ __('Attachment') }}</button>
                        </li>
                    @endpermission
                @endif
                @permission('job note manage')
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="notes-tab" data-bs-toggle="pill" data-bs-target="#notes"
                            type="button">{{ __('Notes') }}</button>
                    </li>
                @endpermission
                @permission('job todo manage')
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="to-do-tab" data-bs-toggle="pill" data-bs-target="#to-do"
                            type="button">{{ __('To Do') }}</button>
                    </li>
                @endpermission
                @if (module_is_active('ActivityLog'))
                    @permission('job activity manage')
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="activity-log-tab" data-bs-toggle="pill" data-bs-target="#activity-log"
                                type="button">{{ __('Activity Log') }}</button>
                        </li>
                    @endpermission
                @endif
            </ul>
        </div>
    </div>

    <div class="row">
        <div class="job-card">
            <div class="col-lg-12">
                <div class="tab-content" id="pills-tabContent">
                    <div class="tab-pane fade show active" id="details" role="tabpanel"
                        aria-labelledby="pills-user-tab-1">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="card employee-detail-body fulls-card">
                                    <div class="card-header ">
                                        <h5>{{ __('Job Details') }}</h5>
                                    </div>
                                    <div class="card-body ">
                                        <div class="row ">
                                            <div class="col-md-6">
                                                <div class="info font-style">
                                                    <strong>{{ __('Job title') }} :</strong>
                                                    <span
                                                        class="text-muted">{{ !empty($job->title) ? $job->title : '-' }}</span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="info font-style">
                                                    <strong>{{ __('Status') }} :</strong>
                                                    <span
                                                        class="text-muted">{{ !empty($job->status) ? $job->status : '-' }}</span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="info font-style">
                                                    <strong>{{ __('Positions') }} :</strong>
                                                    <span
                                                        class="text-muted">{{ !empty($job->position) ? $job->position : '-' }}</span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="info font-style">
                                                    <strong>{{ __('Location') }} :</strong>
                                                    <span
                                                        class="text-muted">{{ !empty($job->location) ? $job->location : '-' }}</span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="info font-style">
                                                    <strong>{{ __('Salary From') }} :</strong>
                                                    <span
                                                        class="text-muted">{{ !empty($job->salary_from) ? currency_format_with_sym($job->salary_from) : '-' }}</span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="info font-style">
                                                    <strong>{{ __('Salary To') }} :</strong>
                                                    <span
                                                        class="text-muted">{{ !empty($job->salary_to) ? currency_format_with_sym($job->salary_to) : '-' }}</span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="info font-style">
                                                    <strong>{{ __('Start Date') }} :</strong>
                                                    <span
                                                        class="text-muted">{{ !empty($job->start_date) ? company_date_formate($job->start_date) : '-' }}</span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="info font-style">
                                                    <strong>{{ __('End Date') }} :</strong>
                                                    <span
                                                        class="text-muted">{{ !empty($job->end_date) ? company_date_formate($job->end_date) : '-' }}</span>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="info font-style">
                                                    <strong>{{ __('Address') }} :</strong>
                                                    <span
                                                        class="text-muted">{{ !empty($job->address) ? $job->address : '-' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="card employee-detail-body fulls-card">
                                    <div class="card-header ">
                                        <h5>{{ __('Category Details') }}</h5>
                                    </div>
                                    <div class="card-body ">
                                        <div class="row ">
                                            <div class="col-md-6">
                                                <div class="info font-style">
                                                    <strong>{{ __('Job Type') }} :</strong>
                                                    <span class="text-muted">
                                                        @if ($job->job_type == 'full_time')
                                                            {{ __('Full time') }}
                                                        @elseif($job->job_type == 'part_time')
                                                            {{ __('Part time') }}
                                                        @else
                                                            -
                                                        @endif
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="info font-style">
                                                    <strong>{{ __('Job Category') }} :</strong>
                                                    <span
                                                        class="text-muted">{{ !empty($job->category) ? $job->categories->title : '-' }}</span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="info font-style">
                                                    <strong>{{ __('Recruitment Type') }} :</strong>
                                                    <span
                                                        class="text-muted">{{ !empty($job->recruitment_type) ? $job->recruitment_type : '-' }}</span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="info font-style">
                                                    @if ($job->recruitment_type == 'internal')
                                                        <strong>{{ __('Branch') }} :</strong>
                                                        <span
                                                            class="text-muted">{{ !empty($job->branch) ? $job->branches->name : '-' }}</span>
                                                    @else
                                                        <strong>{{ __('Customer') }} :</strong>
                                                        <span
                                                            class="text-muted">{{ !empty($job->user_id) ? $job->UserName->name : '-' }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="info font-style">
                                                <strong>{{ __('Job Link') }} :</strong>
                                                <span
                                                    class="text-muted">{{ !empty($job->job_link) ? $job->job_link : '-' }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="info font-style">
                                                <strong>{{ __('Skills') }} :</strong>
                                                @foreach ($job->skill as $skill)
                                                    <span
                                                        class="badge p-2 px-3 bg-primary status-badge6">{{ $skill }}</span>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="questions" role="tabpanel" aria-labelledby="pills-user-tab-2">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="card employee-detail-body fulls-card">
                                    <div class="card-header">
                                        <h5>{{ __('Job Questions') }}</h5>
                                    </div>
                                    <div class="card-body ">
                                        <div class="row">
                                            @if ($job->applicant)
                                                <div class="col-6">
                                                    <h6>{{ __('Need to ask ?') }}</h6>
                                                    <ul class="">
                                                        @foreach ($job->applicant as $applicant)
                                                            <li>{{ ucfirst($applicant) }}</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endif
                                            @if (!empty($job->visibility))
                                                <div class="col-6">
                                                    <h6>{{ __('Need to show option ?') }}</h6>
                                                    <ul class="">
                                                        @foreach ($job->visibility as $visibility)
                                                            <li>{{ ucfirst($visibility) }}</li>
                                                        @endforeach
                                                    </ul>

                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="card employee-detail-body fulls-card">
                                    <div class="card-header">
                                        <h5>{{ __('Custom Questions') }}</h5>
                                    </div>
                                    <div class="card-body ">
                                        <div class="row">
                                            @if (count($job->questions()) > 0)
                                                <div class="col-12">
                                                    <ul class="">
                                                        @foreach ($job->questions() as $question)
                                                            <li>{{ $question->question }}</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="descriptions" role="tabpanel" aria-labelledby="pills-user-tab-3">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="card employee-detail-body fulls-card">
                                    <div class="card-header">
                                        <h5>{{ __('Job Description') }}</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-12">
                                                {!! $job->description !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="card employee-detail-body fulls-card">
                                    <div class="card-header">
                                        <h5>{{ __('Job Requirement') }}</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-12">
                                                {!! $job->requirement !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if (!empty($job->terms_and_conditions))
                                <div class="col-sm-12">
                                    <div class="card employee-detail-body fulls-card">
                                        <div class="card-header">
                                            <h5>{{ __('Terms And Conditions') }}</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-12">
                                                    {!! $job->terms_and_conditions !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="tab-pane fade" id="applications" role="tabpanel" aria-labelledby="pills-user-tab-4">
                        <div class="row">
                            @permission('jobapplication manage')
                                <div class="col-sm-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <div class="row">
                                                <div class="col-6">
                                                    <h5>{{ __('Manage job Applications') }}</h5>
                                                </div>
                                                @permission('jobapplication create')
                                                    <div class="col-6 text-end create_btn">
                                                        <a data-url="{{ route('job-application.create', ['job_id' => $job->id]) }}"
                                                            data-ajax-popup="true" data-size="lg"
                                                            data-title="{{ __('Create New Job Application') }}"
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
                                                <table class="table mb-0 pc-dt-simple" id="products">
                                                    <thead>
                                                        <tr>
                                                            <th>{{ __('Name') }}</th>
                                                            <th>{{ __('Stage') }}</th>
                                                            <th>{{ __('CV/Resume') }}</th>
                                                            <th>{{ __('Rating') }}</th>
                                                            <th>{{ __('Created At') }}</th>
                                                            @if (Laratrust::hasPermission('jobapplication show') || Laratrust::hasPermission('jobapplication delete'))
                                                                <th>{{ __('Action') }}</th>
                                                            @endif
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse ($applications as $application)
                                                            <tr>
                                                                <td>
                                                                    <a class=" text-primary"
                                                                        href="{{ route('job-application.show', \Crypt::encrypt($application->id)) }}">{{ !empty($application->name) ? $application->name : '' }}</a>
                                                                </td>
                                                                <td>{{ !empty($application->stages) ? $application->stages->title : '' }}
                                                                </td>
                                                                <td>
                                                                    @if (!empty($application->resume))
                                                                        <span class="text-sm action-btn bg-primary ms-2">
                                                                            <a class=" btn btn-sm align-items-center"
                                                                                href="{{ get_file($application->resume) }}"
                                                                                data-bs-toggle="tooltip" download=""><i
                                                                                    class="ti ti-download text-white"></i></a>
                                                                        </span>

                                                                        <div class="action-btn bg-secondary ms-2 ">
                                                                            <a class=" mx-3 btn btn-sm align-items-center"
                                                                                href="{{ get_file($application->resume) }}"
                                                                                target="_blank">
                                                                                <i class="ti ti-crosshair text-white"
                                                                                    data-bs-toggle="tooltip"></i>
                                                                            </a>
                                                                        </div>
                                                                    @else
                                                                        <div class="mx-4">-</div>
                                                                    @endif
                                                                </td>
                                                                <td><span class="static-rating static-rating-sm d-block">
                                                                        @for ($i = 1; $i <= 5; $i++)
                                                                            @if ($i <= $application->rating)
                                                                                <i class="star fas fa-star voted"></i>
                                                                            @else
                                                                                <i class="star fas fa-star"></i>
                                                                            @endif
                                                                        @endfor
                                                                    </span></td>
                                                                <td>{{ company_date_formate($application->created_at) }}</td>
                                                                @if (Laratrust::hasPermission('jobapplication show') || Laratrust::hasPermission('jobapplication delete'))
                                                                    <td class="Action">
                                                                        <span>
                                                                            @permission('jobapplication show')
                                                                                <div class="action-btn me-2">
                                                                                    <a href="{{ route('job-application.show', \Crypt::encrypt($application->id)) }}"
                                                                                        class="mx-3 btn btn-sm  align-items-center bg-warning"
                                                                                        data-bs-toggle="tooltip"
                                                                                        data-bs-placement="top"
                                                                                        data-bs-original-title="{{ __('View') }}">
                                                                                        <i class="ti ti-eye text-white"></i>
                                                                                    </a>
                                                                                </div>
                                                                            @endpermission
                                                                            @permission('jobapplication delete')
                                                                                <div class="action-btn">
                                                                                    {{ Form::open(['route' => ['job-application.destroy', $application->id], 'class' => 'm-0']) }}
                                                                                    @method('DELETE')
                                                                                    <a class="mx-3 btn btn-sm  align-items-center bs-pass-para show_confirm bg-danger"
                                                                                        data-bs-toggle="tooltip" title="{{ __('Delete') }}"
                                                                                        aria-label="Delete"
                                                                                        data-bs-original-title="{{ __('Delete') }}"
                                                                                        data-confirm="{{ __('Are You Sure?') }}"
                                                                                        data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                                                        data-confirm-yes="delete-form-{{ $application->id }}"><i
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
                            @permission('job attachment manage')
                                <h5 class="d-inline-block my-3">{{ __('Attachments') }}</h5>
                                @permission('job attachment upload')
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
                                                            @if (Laratrust::hasPermission('job attachment delete'))
                                                                <th class="text-dark">{{ __('Action') }}</th>
                                                            @endif
                                                        </tr>
                                                    </thead>
                                                    @forelse($job_attachments as $key =>$job_attachment)
                                                        <td>{{ ++$key }}</td>
                                                        <td>{{ $job_attachment->file_name }}</td>
                                                        <td>{{ $job_attachment->file_size }}</td>
                                                        <td>{{ company_date_formate($job_attachment->created_at) }}</td>
                                                        <td>
                                                            <div class="action-btn ms-2">
                                                                <a href="{{ url($job_attachment->file_path) }}"
                                                                    class="mx-3 btn btn-sm align-items-center bg-primary"
                                                                    title="{{ __('Download') }}" data-bs-toggle="tooltip"
                                                                    data-bs-original-title="{{ __('Download') }}"
                                                                    target="_blank" download>
                                                                    <i class="ti ti-download text-white"></i>
                                                                </a>
                                                            </div>
                                                            @permission('job attachment delete')
                                                                <div class="action-btn">
                                                                    {{ Form::open(['route' => ['job.attachment.destroy', $job_attachment->id], 'class' => 'm-0']) }}
                                                                    @method('DELETE')
                                                                    <a href="#"
                                                                        class="mx-3 btn btn-sm  align-items-center bs-pass-para show_confirm bg-danger"
                                                                        data-bs-toggle="tooltip" title="{{ __('Delete') }}"
                                                                        aria-label="Delete"
                                                                        data-bs-original-title="{{ __('Delete') }}"
                                                                        data-confirm="{{ __('Are You Sure?') }}"
                                                                        data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                                        data-confirm-yes="delete-form-{{ $job_attachment->id }}">
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
                            @permission('job note manage')
                                <div class="col-sm-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <div class="row">
                                                <div class="col-6">
                                                    <h5>{{ __('Notes') }}</h5>
                                                </div>
                                                @permission('job note create')
                                                    <div class="col-6 text-end create_btn">
                                                        <a data-url="{{ route('jobnote.create', ['id' => $job->id]) }}"
                                                            data-ajax-popup="true" data-size="md"
                                                            data-title="{{ __('Create Note') }}" data-bs-toggle="tooltip"
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
                                                            @if (Laratrust::hasPermission('job note show') ||
                                                                    Laratrust::hasPermission('job note edit') ||
                                                                    Laratrust::hasPermission('job note delete'))
                                                                <th>{{ __('Action') }}</th>
                                                            @endif
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse ($notes as $note)
                                                            <tr>
                                                                <td>{{ !empty($note->created_by) ? $note->createdBy->name : '' }}
                                                                </td>
                                                                <td>
                                                                    <p class="job-to-do">
                                                                        {{ !empty($note->description) ? $note->description : '' }}
                                                                    </p>
                                                                </td>
                                                                @if (Laratrust::hasPermission('job note show') ||
                                                                        Laratrust::hasPermission('job note edit') ||
                                                                        Laratrust::hasPermission('job note delete'))
                                                                    <td class="Action">
                                                                        <span>
                                                                            @permission('job note show')
                                                                                <div class="action-btn me-2">
                                                                                    <a data-url="{{ route('jobnote.description', $note->id) }}"
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
                                                                            @permission('job note edit')
                                                                                <div class="action-btn me-2">
                                                                                    <a data-url="{{ route('jobnote.edit', $note->id) }}"
                                                                                        data-ajax-popup="true" data-size="md"
                                                                                        data-title="{{ __('Edit Note') }}"
                                                                                        data-bs-toggle="tooltip" title=""
                                                                                        data-bs-original-title="{{ __('Edit') }}"
                                                                                        class="mx-3 btn btn-sm  align-items-center bg-info">
                                                                                        <i class="ti ti-pencil text-white"></i>
                                                                                    </a>
                                                                                </div>
                                                                            @endpermission
                                                                            @permission('job note delete')
                                                                                <div class="action-btn">
                                                                                    {{ Form::open(['route' => ['jobnote.destroy', $note->id], 'class' => 'm-0']) }}
                                                                                    @method('DELETE')
                                                                                    <a class="mx-3 btn btn-sm  align-items-center bs-pass-para show_confirm bg-danger"
                                                                                        data-bs-toggle="tooltip" title="{{ __('Delete') }}"
                                                                                        aria-label="Delete"
                                                                                        data-bs-original-title="{{ __('Delete') }}"
                                                                                        data-confirm="{{ __('Are You Sure?') }}"
                                                                                        data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                                                        data-confirm-yes="delete-form-{{ $note->id }}"><i
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
                            @permission('job todo manage')
                                <div class="col-sm-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <div class="row">
                                                <div class="col-6">
                                                    <h5>{{ __('To Do') }}</h5>
                                                </div>
                                                @permission('job todo create')
                                                    <div class="col-6 text-end create_btn">
                                                        <a data-url="{{ route('job-todo.create', ['id' => $job->id]) }}"
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
                                                            @if (Laratrust::hasPermission('job todo show') ||
                                                                    Laratrust::hasPermission('job todo edit') ||
                                                                    Laratrust::hasPermission('job todo delete'))
                                                                <th>{{ __('Action') }}</th>
                                                            @endif
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse ($todos as $todo)
                                                            <tr>
                                                                <td>{{ !empty($todo->title) ? $todo->title : '' }}
                                                                <td>
                                                                    <p class="job-to-do">
                                                                        {{ !empty($todo->description) ? $todo->description : '' }}
                                                                    </p>
                                                                </td>
                                                                <td>{{ !empty($todo->due_date) ? company_date_formate($todo->due_date) : '' }}
                                                                <td>{{ !empty($todo->assign_by) ? $todo->assignedByUser->name : '' }}
                                                                </td>
                                                                <td>
                                                                    @if ($users = $todo->users())
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
                                                                @if (Laratrust::hasPermission('job todo show') ||
                                                                        Laratrust::hasPermission('job todo edit') ||
                                                                        Laratrust::hasPermission('job todo delete'))
                                                                    <td class="Action">
                                                                        <span>
                                                                            @permission('job todo show')
                                                                                <div class="action-btn me-2">
                                                                                    <a class="mx-3 btn btn-sm align-items-center bg-warning"
                                                                                        data-url="{{ route('job-todo.show', $todo->id) }}"
                                                                                        data-ajax-popup="true" data-size="md"
                                                                                        data-title="{{ __('To Do Detail') }}"
                                                                                        data-bs-original-title="{{ __('View') }}"
                                                                                        data-bs-toggle="tooltip">
                                                                                        <i class="ti ti-eye text-white"></i>
                                                                                    </a>
                                                                                </div>
                                                                            @endpermission
                                                                            @permission('job todo edit')
                                                                                <div class="action-btn me-2">
                                                                                    <a data-url="{{ route('job-todo.edit', $todo->id) }}"
                                                                                        data-ajax-popup="true" data-size="md"
                                                                                        data-title="{{ __('Edit Notes') }}"
                                                                                        data-bs-toggle="tooltip" title=""
                                                                                        data-bs-original-title="{{ __('Edit') }}"
                                                                                        class="mx-3 btn btn-sm  align-items-center bg-info">
                                                                                        <i class="ti ti-pencil text-white"></i>
                                                                                    </a>
                                                                                </div>
                                                                            @endpermission
                                                                            @permission('job todo delete')
                                                                                <div class="action-btn">
                                                                                    {{ Form::open(['route' => ['job-todo.destroy', $todo->id], 'class' => 'm-0']) }}
                                                                                    @method('DELETE')
                                                                                    <a class="mx-3 btn btn-sm  align-items-center bs-pass-para show_confirm bg-danger"
                                                                                        data-bs-toggle="tooltip" title="{{ __('Delete') }}"
                                                                                        aria-label="Delete"
                                                                                        data-bs-original-title="{{ __('Delete') }}"
                                                                                        data-confirm="{{ __('Are You Sure?') }}"
                                                                                        data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                                                        data-confirm-yes="delete-form-{{ $todo->id }}"><i
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
                            @permission('job activity manage')
                                <div class="col-sm-12">
                                    <div class="col-sm-12">
                                        <div class=" multi-collapse mt-2" id="multiCollapseExample1">
                                            <div class="card">
                                                <div class="card-body">
                                                    {{ Form::open(['route' => ['job.show', 'job' => $job], 'method' => 'GET', 'id' => 'module_form']) }}
                                                    <div class="row d-flex align-items-center justify-content-end">
                                                        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 mr-2">
                                                            <div class="btn-box">
                                                                <label for="staff">{{ __('Staff') }}</label>
                                                                <select class="form-control staff " name="staff" id="staff" tabindex="-1"
                                                                    aria-hidden="true">
                                                                    <option value="">{{ __('Select staff') }}</option>
                                                                    @foreach ($staffs as $staff)
                                                                        @if ($staff->id == $creatorId)
                                                                            <span class="badge bg-dark"> {{ Auth::user()->roles->first()->name }}</span>
                                                                        @else
                                                                            <span class="badge bg-dark"> {{ __('') }}</span>
                                                                        @endif
                                                                        <option value="{{ $staff->id }}"
                                                                            {{ isset(request()->staff) && request()->staff == $staff->id ? 'selected' : '' }}>
                                                                            {{ $staff->name }}@if ($staff->id == $creatorId)
                                                                                <span class="badge bg-dark">
                                                                                    {{'('. $staff->type.')' }}</span>
                                                                            @endif
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <input type="hidden" name="filter" value="Jobs">
                                                        <div class="col-auto float-end ms-2 mt-4">
                                                            <div class="d-flex">
                                                                <a class="btn btn-sm btn-primary me-2"
                                                                    onclick="document.getElementById('module_form').submit(); return false;"
                                                                    data-bs-toggle="tooltip" title="{{ __('Search') }}"
                                                                    data-original-title="{{ __('apply') }}">
                                                                    <span class="btn-inner--icon"><i class="ti ti-search"></i></span>
                                                                </a>
                                                                <a href="{{ route('job.show', ['job' => $job]) }}" class="btn btn-sm btn-danger"
                                                                    data-bs-toggle="tooltip" title="{{ __('Reset') }}"
                                                                    data-original-title="{{ __('Reset') }}">
                                                                    <span class="btn-inner--icon"><i class="ti ti-trash-off text-white-off"></i></span>
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
                                                            @if (Laratrust::hasPermission('job activity delete'))
                                                                <th>{{ __('Action') }}</th>
                                                            @endif
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse ($activitys as $activity)
                                                            <tr>
                                                                <td>{{ $activity->description . (!empty($activity->name) ? $activity->name : '') . '.' }}</td>
                                                                <td>{{ (!empty($activity->name) ? $activity->name : '--') }}
                                                                    @if (!empty($activity->user_id)  && $activity->user_id == $creatorId)
                                                                        <span class="badge bg-primary p-2">{{ $activity->type }}</span>
                                                                    @endif
                                                                </td>
                                                                @if (Laratrust::hasPermission('job activity delete'))
                                                                    <td class="Action">
                                                                        <span>
                                                                            <div class="action-btn">
                                                                                {!! Form::open([
                                                                                    'method' => 'DELETE',
                                                                                    'route' => ['jobactivitylog.destroy', $activity->id],
                                                                                    'id' => 'delete-form-' . $activity->id,
                                                                                ]) !!}
                                                                                <a class="mx-3 btn btn-sm  align-items-center bs-pass-para show_confirm bg-danger"
                                                                                    data-bs-toggle="tooltip" title="{{ __('Delete') }}" data-confirm="{{ __('Are You Sure?') }}" data-text="{{__('This action can not be undone. Do you want to continue?')}}"><i
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
    </div>
@endsection
