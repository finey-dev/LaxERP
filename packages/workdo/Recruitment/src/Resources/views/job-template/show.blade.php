@extends('layouts.main')
@section('page-title')
    {{ __('Job Template Details') }}
@endsection
@section('page-action')
    <div>
        @permission('job template edit')
            <a href="{{ route('job-template.index') }}" data-bs-toggle="tooltip" title=""
                class="btn-submit btn btn-sm btn-primary ">
                <i class=" ti ti-arrow-back-up"></i>
            </a>
        @endpermission
    </div>
@endsection

@section('page-breadcrumb')
    {{ __('Job Template Details') }}
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

@section('content')
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
                                                    class="text-muted">{{ !empty($job_template->title) ? $job_template->title : '-' }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info font-style">
                                                <strong>{{ __('Status') }} :</strong>
                                                <span
                                                    class="text-muted">{{ !empty($job_template->status) ? $job_template->status : '-' }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info font-style">
                                                <strong>{{ __('Positions') }} :</strong>
                                                <span
                                                    class="text-muted">{{ !empty($job_template->position) ? $job_template->position : '-' }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info font-style">
                                                <strong>{{ __('Location') }} :</strong>
                                                <span
                                                    class="text-muted">{{ !empty($job_template->location) ? $job_template->location : '-' }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info font-style">
                                                <strong>{{ __('Salary From') }} :</strong>
                                                <span
                                                    class="text-muted">{{ !empty($job_template->salary_from) ? currency_format_with_sym($job_template->salary_from) : '-' }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info font-style">
                                                <strong>{{ __('Salary To') }} :</strong>
                                                <span
                                                    class="text-muted">{{ !empty($job_template->salary_to) ? currency_format_with_sym($job_template->salary_to) : '-' }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info font-style">
                                                <strong>{{ __('Start Date') }} :</strong>
                                                <span
                                                    class="text-muted">{{ !empty($job_template->start_date) ? company_date_formate($job_template->start_date) : '-' }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info font-style">
                                                <strong>{{ __('End Date') }} :</strong>
                                                <span
                                                    class="text-muted">{{ !empty($job_template->end_date) ? company_date_formate($job_template->end_date) : '-' }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="info font-style">
                                                <strong>{{ __('Address') }} :</strong>
                                                <span
                                                    class="text-muted">{{ !empty($job_template->address) ? $job_template->address : '-' }}</span>
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
                                                    @if ($job_template->job_type == 'full_time')
                                                        {{ __('Full time') }}
                                                    @elseif($job_template->job_type == 'part_time')
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
                                                    class="text-muted">{{ !empty($job_template->category) ? $job_template->categories->title : '-' }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info font-style">
                                                <strong>{{ __('Recruitment Type') }} :</strong>
                                                <span
                                                    class="text-muted">{{ !empty($job_template->recruitment_type) ? $job_template->recruitment_type : '-' }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info font-style">
                                                @if ($job_template->recruitment_type == 'internal')
                                                    <strong>{{ __('Branch') }} :</strong>
                                                    <span
                                                        class="text-muted">{{ !empty($job_template->branch) ? $job_template->branches->name : '-' }}</span>
                                                @else
                                                    <strong>{{ __('Customer') }} :</strong>
                                                    <span
                                                        class="text-muted">{{ !empty($job_template->user_id) ? $job_template->UserName->name : '-' }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="info font-style">
                                            <strong>{{ __('Job Link') }} :</strong>
                                            <span
                                                class="text-muted">{{ !empty($job_template->job_link) ? $job_template->job_link : '-' }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="info font-style">
                                            <strong>{{ __('Skills') }} :</strong>
                                            @foreach ($job_template->skill as $skill)
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
                                        @if ($job_template->applicant)
                                            <div class="col-6">
                                                <h6>{{ __('Need to ask ?') }}</h6>
                                                <ul class="">
                                                    @foreach ($job_template->applicant as $applicant)
                                                        <li>{{ ucfirst($applicant) }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                        @if (!empty($job_template->visibility))
                                            <div class="col-6">
                                                <h6>{{ __('Need to show option ?') }}</h6>
                                                <ul class="">
                                                    @foreach ($job_template->visibility as $visibility)
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
                                        @if (count($job_template->questions()) > 0)
                                            <div class="col-12">
                                                <ul class="">
                                                    @foreach ($job_template->questions() as $question)
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
                                            {!! $job_template->description !!}
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
                                            {!! $job_template->requirement !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @if (!empty($job_template->terms_and_conditions))
                            <div class="col-sm-12">
                                <div class="card employee-detail-body fulls-card">
                                    <div class="card-header">
                                        <h5>{{ __('Terms And Conditions') }}</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-12">
                                                {!! $job_template->terms_and_conditions !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
