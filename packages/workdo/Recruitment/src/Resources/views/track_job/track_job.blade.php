@extends('recruitment::layouts.master')
@section('page-title')
    {{ __('Job Application Details') }}
@endsection
<style>
    .card-body .form-label {
        font-weight: bold !important;
    }
</style>

@section('content')
    <div class="container   ">
        <div class="d-flex flex-wrap align-items-center justify-content-between row-gap  my-3">
            <div>
                <h3 class="mb-0">{{ __('Job Application Details') }}</h3>
            </div>
            <div>
                <a href="{{ route('career') }}"class="nav-link text-white btn btn-primary px-3 py-2">
                    {{ __('Open Career Page') }}
                </a>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>{{ __('Basic Details') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row align-items-center row-gap">
                            <div class="col-lg-6 col-md-12 col-sm-6">
                                <div class="d-flex align-items-center" data-toggle="tooltip"
                                    data-placement="right" data-title="2 hrs ago" data-original-title=""
                                    title="">
                                    <div>
                                        <a href="{{ check_file($job_appplication->profile) ? get_file($job_appplication->profile) : get_file('uploads/users-avatar/avatar.png') }}"
                                            target="_blank" class=" avatar-sm">
                                            <img src="{{ check_file($job_appplication->profile) ? get_file($job_appplication->profile) : get_file('uploads/users-avatar/avatar.png') }}"
                                                class="rounded border-2 border border-primary" width="50px"
                                                height="50px">
                                        </a>
                                    </div>
                                    <div class="flex-fill ms-3">
                                        <div class="h5 mb-1">
                                            {{ !empty($job_appplication->name) ? $job_appplication->name : '-' }}
                                        </div>
                                        <p class="text-sm  mb-0">
                                            {{ !empty($job_appplication->email) ? $job_appplication->email : '-' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-12 col-sm-6">
                                <label class="form-label mb-0 me-2" for="phone">{{ __('Status : ') }}</label>
                                <span class="badge bg-primary p-2 px-3">
                                    {{ !empty($stage->title) ? $stage->title : '-' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h5>{{ __('Additional Details') }}</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-none p-0 mb-0">
                            <li class="d-flex align-items-centter gap-1 mb-2">
                                <strong>{{ __('Interview date: ') }}</strong>
                                <p class="mb-0 flex-1" > {{ !empty($interview->date) ? company_date_formate($interview->date, $company_id, $workspace_id) : '-' }}</p>
                            </li>
                            <li class="d-flex align-items-centter gap-1">
                                <strong>{{ __('Interview time: ') }}</strong>
                                <p class="mb-0 flex-1" >  {{ !empty($interview->time) ? company_Time_formate($interview->time, $company_id, $workspace_id) : '-' }}</p>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>{{ __('Basic Information') }}</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-none user-list p-0 mb-0">
                            <li class="d-flex flex-wrap align-items-center gap-2">
                                <strong>{{ __('Phone : ') }}</strong>
                                <p class="mb-0 flex-1" > {{ !empty($job_appplication->phone) ? $job_appplication->phone : '-' }}</p>
                            </li>
                            <li class="d-flex flex-wrap align-items-center gap-2">
                                <strong>{{ __('DOB : ') }}</strong>
                                <p class="mb-0 flex-1" >{{ !empty($job_appplication->dob) ? company_date_formate($job_appplication->dob, $company_id, $workspace_id) : '-' }}</p>
                            </li>
                            <li class="d-flex flex-wrap align-items-center gap-2">
                                <strong>{{ __('Gender : ') }}</strong>
                                <p class="mb-0 flex-1" > {{ !empty($job_appplication->gender) ? $job_appplication->gender : '-' }}</p>
                            </li>
                            <li class="d-flex flex-wrap align-items-center gap-2">
                                <strong>{{ __('Country : ') }}</strong>
                                <p class="mb-0 flex-1" > {{ !empty($job_appplication->country) ? $job_appplication->country : '-' }}</p>
                            </li>
                            <li class="d-flex flex-wrap align-items-center gap-2">
                                <strong>{{ __('State : ') }}</strong>
                                <p class="mb-0 flex-1" > {{ !empty($job_appplication->state) ? $job_appplication->state : '-' }}</p>
                            </li>
                            <li class="d-flex flex-wrap align-items-center gap-2">
                                <strong>{{ __('City : ') }}</strong>
                                <p class="mb-0 flex-1" >{{ !empty($job_appplication->city) ? $job_appplication->city : '-' }}</p>
                            </li>
                            <li class="d-flex flex-wrap align-items-center gap-2">
                                <strong>{{ __('Applied For : ') }}</strong>
                                <p class="mb-0 flex-1" >{{ !empty($job_appplication->jobs) && !empty($job_appplication->jobs->title) ? $job_appplication->jobs->title : '-' }}</p>
                            </li>
                            <li class="d-flex flex-wrap align-items-center gap-2">
                                <strong>{{ __('Applied at : ') }}</strong>
                                <p class="mb-0 flex-1" >{{ !empty($job_appplication->created_at) ? company_date_formate($job_appplication->created_at, $company_id, $workspace_id) : '-' }}</p>
                            </li>
                            <li class="d-flex flex-wrap align-items-center gap-2">
                                <strong>{{ __('CV / Resume : ') }}</strong>
                                <div class="mb-0 flex-1" >
                                    @if (!empty($job_appplication->resume))
                                        <span class="text-md action-btn bg-primary me-1">
                                            <a class=" btn btn-sm align-items-center"
                                                href="{{ get_file($job_appplication->resume) }}"
                                                data-bs-toggle="tooltip"
                                                data-bs-original-title="{{ __('download') }}"
                                                download=""><i
                                                    class="ti ti-download text-white"></i></a>
                                        </span>

                                        <div class="action-btn bg-secondary">
                                            <a class=" mx-3 btn btn-sm align-items-center"
                                                href="{{ get_file($job_appplication->resume) }}"
                                                target="_blank">
                                                <i class="ti ti-crosshair text-white"
                                                    data-bs-toggle="tooltip"
                                                    data-bs-original-title="{{ __('Preview') }}"></i>
                                            </a>
                                        </div>
                                    @else
                                        <div>-</div>
                                    @endif
                                </div>
                            </li>
                            <li class="d-flex flex-wrap align-items-center gap-2">
                                <strong>{{ __('Cover Letter : ') }}</strong>
                                <div class="mb-0 flex-1" >
                                    @if (!empty($job_appplication->cover_letter))
                                        <span class="text-md">{{ $job_appplication->cover_letter }}</span>
                                    @else
                                        <div>-</div>
                                    @endif
                                </div>
                            </li>
                            <li class="d-flex flex-wrap align-items-center gap-2">
                                <strong>{{ __('Rating : ') }}</strong>
                                @for ($i = 1; $i <= 5; $i++)
                                @if ($job_appplication->rating < $i)
                                    @if (is_float($job_appplication->rating) && round($job_appplication->rating) == $i)
                                        <i class="text-warning fas fa-star-half-alt"></i>
                                    @else
                                        <i class="fas fa-star"></i>
                                    @endif
                                @else
                                    <i class="text-warning fas fa-star"></i>
                                @endif
                            @endfor
                            </li>

                        </ul>
                        <div class="row">
                            <div class="col-12">

                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="form-label" for="rating"
                                            class="form-label"></label><br>
                                    </div>
                                    <div class="col-md-6">

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
