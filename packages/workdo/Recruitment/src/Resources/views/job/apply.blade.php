@extends('recruitment::layouts.master')
@section('page-title')
    {{ $job->title }}
@endsection

@section('content')
    <div class="job-wrapper">
        <div class="job-content">
            <nav class="navbar">
                <div class="container">
                    <div class="d-flex align-items-center justify-content-between my-sm-4 mt-2 w-100 gap-2">
                        <a  href="{{route('home')}}">
                            <img src="{{ !empty (get_file(company_setting('logo_light',$job->created_by, $job->workspace))) ? get_file('uploads/logo/logo_light.png') :'WorkDo'}}" alt="logo" style="width: 90px">
                        </a>
                        <div class="d-flex align-item-center justify-content-between">
                            <li class="dropdown dash-h-item drp-language">
                                <div class="dropdown d-flex align-item-center justify-content-between">
                                    <a class="nav-link  btn bg-white d-flex align-items-center justify-content-between px-sm-3 py-sm-2 p-2"
                                        role="button" data-toggle="dropdown" aria-haspopup="true"
                                        aria-expanded="false" data-offset="0,10">
                                        <span class="d-none d-sm-inline-block">{{ \Str::upper($currantLang) }}</span>
                                        <i class="ti ti-world nocolor m-0  d-sm-none"></i>
                                        <i class="ti ti-chevron-down drp-arrow nocolor ms-sm-2 ms-1"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-left" aria-labelledby="dropdownMenuButton" style="min-width: auto">
                                        @foreach ($languages as $key => $language)
                                        <a class="dropdown-item @if ($key == $currantLang) text-danger @endif" href="{{ route('job.apply', [$job->code, $key]) }}">{{$language}}</a>
                                        @endforeach
                                    </div>
                                    <a href="{{ route('find.job', $slug) }}"class="nav-link btn bg-white d-flex align-items-center px-sm-3 py-sm-2 p-2 " style="font-size: 13px">
                                        {{ __('Track Application') }}
                                    </a>
                                </div>
                            </li>
                        </div>
                    </div>
                </div>
            </nav>
            <section class="job-banner">
                <div class="job-banner-bg">
                    <img src="{{ asset('packages/workdo/Recruitment/src/Resources/assets/image/banner.png') }}"
                        alt="">
                </div>
                <div class="container">
                    <div class="job-banner-content text-center text-white">
                        <h1 class="text-white mb-3">
                            {{ __(' We help') }} <br> {{ __('businesses grow') }}
                        </h1>
                        <p>{{ __('Work there. Find the dream job you’ve always wanted..') }}</p>
                        </p>
                    </div>
                </div>
            </section>
            <section class="apply-job-section">
                <div class="container">
                    <div class="apply-job-wrapper card mb-0">
                        <div class="section-title text-center">
                            <h2 class="h1 mb-3"> {{ $job->title }}</h2>
                            <div class="d-flex flex-wrap justify-content-center gap-1 mb-3">
                                @foreach (explode(',', $job->skill) as $skill)
                                    <span class="badge p-2 px-3 bg-primary status-badge6">{{ $skill }}</span>
                                @endforeach
                            </div>
                            @if (!empty($job->location) ? $job->location : '')
                                <p> <i class="ti ti-map-pin ms-1"></i>
                                    {{ !empty($job->location) ? $job->location : '' }}</p>
                            @endif
                        </div>
                        <div class="apply-job-form">
                            <h2 class="mb-4">{{ __('Apply for this job') }}</h2>
                            {{ Form::open(['route' => ['job.apply.data', $job->code], 'method' => 'post', 'enctype' => 'multipart/form-data', 'class' => 'needs-validation', 'novalidate']) }}
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        {{ Form::label('name', __('Name'), ['class' => 'form-label']) }}<x-required></x-required>
                                        {{ Form::text('name', null, ['class' => 'form-control name', 'required' => 'required', 'placeholder' => __('Enter Name')]) }}
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        {{ Form::label('email', __('Email'), ['class' => 'form-label']) }}<x-required></x-required>
                                        {{ Form::text('email', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Email')]) }}
                                    </div>
                                </div>
                                <x-mobile divClass="col-sm-6" name="phone" label="{{ __('Phone') }}"
                                    placeholder="{{ __('Enter Phone Number') }}" id="phone" required>
                                </x-mobile>
                                <div class="col-sm-6">
                                    @if (!empty($job->applicant) && in_array('dob', explode(',', $job->applicant)))
                                        <div class="form-group">
                                            {!! Form::label('dob', __('Date of Birth'), ['class' => 'form-label']) !!}<x-required></x-required>
                                            {!! Form::date('dob', old('dob'), ['class' => 'form-control datepicker w-100', 'required' => 'required', 'max' => date('Y-m-d')]) !!}
                                        </div>
                                    @endif
                                </div>
                                @if (!empty($job->applicant) && in_array('gender', explode(',', $job->applicant)))
                                    <div class="form-group col-sm-6 ">
                                        {!! Form::label('gender', __('Gender'), ['class' => 'form-label']) !!}<x-required></x-required>
                                        <div class="card  mb-0">
                                            <div class="card-body p-3 py-2">
                                                <div class="d-flex radio-check">
                                                    <div class="custom-control custom-radio custom-control-inline pointer">
                                                        <input type="radio" id="g_male" value="Male" name="gender"
                                                            class="custom-control-input" required>
                                                        <label class="custom-control-label"
                                                            for="g_male">{{ __('Male') }}</label>
                                                    </div>
                                                    <div class="custom-control custom-radio custom-control-inline pointer">
                                                        <input type="radio" id="g_female" value="Female" name="gender"
                                                            class="custom-control-input" required>
                                                        <label class="custom-control-label"
                                                            for="g_female">{{ __('Female') }}</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if (!empty($job->applicant) && in_array('country', explode(',', $job->applicant)))
                                    <div class="form-group col-sm-6 ">
                                        {{ Form::label('country', __('Country'), ['class' => 'form-label']) }}<x-required></x-required>
                                        {{ Form::text('country', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Country')]) }}
                                    </div>
                                    <div class="form-group col-sm-6 country">
                                        {{ Form::label('state', __('State'), ['class' => 'form-label']) }}<x-required></x-required>
                                        {{ Form::text('state', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter State')]) }}
                                    </div>
                                    <div class="form-group col-sm-6 country">
                                        {{ Form::label('city', __('City'), ['class' => 'form-label']) }}<x-required></x-required>
                                        {{ Form::text('city', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter City')]) }}
                                    </div>
                                @endif

                                @if (!empty($job->visibility) && in_array('profile', explode(',', $job->visibility)))
                                    <div class="form-group col-sm-6 ">
                                        {{ Form::label('profile', __('Profile'), ['class' => 'form-label']) }}<x-required></x-required>
                                        <input type="file" class="form-control h-auto" name="profile" id="profile"
                                            data-filename="profile_create"
                                            onchange="document.getElementById('blah').src = window.URL.createObjectURL(this.files[0])"
                                            required>
                                        <img id="blah" src="" class="mt-3" width="25%"
                                            style="max-height: 100px; max-width: 100px" />
                                        <p class="profile_create"></p>
                                    </div>
                                @endif

                                @if (!empty($job->visibility) && in_array('resume', explode(',', $job->visibility)))
                                    <div class="form-group col-sm-6 ">
                                        {{ Form::label('resume', __('CV / Resume'), ['class' => 'form-label']) }}<x-required></x-required>
                                        <input type="file" class="form-control h-auto" name="resume" id="resume"
                                            data-filename="resume_create"
                                            onchange="document.getElementById('blah1').src = window.URL.createObjectURL(this.files[0])"
                                            required>
                                        <img id="blah1" class="mt-3" src="" width="25%"
                                            style="max-height: 100px; max-width: 100px" />
                                        <p class="resume_create"></p>
                                    </div>
                                @endif

                                @if (!empty($job->visibility) && in_array('letter', explode(',', $job->visibility)))
                                    <div class="form-group col-md-12 ">
                                        {{ Form::label('cover_letter', __('Cover Letter'), ['class' => 'form-label']) }}<x-required></x-required>
                                        {{ Form::textarea('cover_letter', null, ['class' => 'form-control', 'rows' => '3', 'required' => 'required', 'placeholder' => __('Enter Cover Letter')]) }}
                                    </div>
                                @endif

                                @foreach ($questions as $question)
                                    <div class="form-group col-md-12  question question_{{ $question->id }}">
                                        {{ Form::label($question->question, $question->question, ['class' => 'form-label']) }}@if($question->is_required == 'yes')<x-required></x-required>@endif
                                        <input type="text" class="form-control"
                                            name="question[{{ $question->question }}]"
                                            {{ $question->is_required == 'yes' ? 'required' : '' }}>
                                    </div>
                                @endforeach

                                @if (!empty($job->visibility) && in_array('terms', explode(',', $job->visibility)))
                                    <div class="form-group col-md-12 ">
                                        <div class="form-check custom-checkbox">
                                            <input type="checkbox" class="form-check-input" id="termsCheckbox"
                                                name="terms_condition_check" required>
                                            <label class="form-check-label" for="termsCheckbox">{{ __('I Accept the') }}
                                                <a href="{{ route('job.terms.and.conditions', [$job->code, $currantLang]) }}"
                                                    target="_blank">{{ __('terms and conditions') }}</a></label>
                                        </div>

                                    </div>
                                @endif

                                <div class="col-12">
                                    <div class="text-center">
                                        <button type="submit"
                                            class="btn btn-primary">{{ __('Submit your application') }}</button>
                                    </div>
                                </div>
                            </div>
                            {{ Form::close() }}
                            @if (Session::has('create_application'))
                                <div class="alert alert-success">
                                    <p>{!! session('create_application') !!}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="{{ asset('packages/workdo/Recruitment/src/Resources/assets/js/moment.min.js') }}"></script>
    <script src="{{ asset('packages/workdo/Recruitment/src/Resources/assets/js/site.core.js') }}"></script>
    <script src="{{ asset('packages/workdo/Recruitment/src/Resources/assets/js/autosize.min.js') }}"></script>
    <script src="{{ asset('packages/workdo/Recruitment/src/Resources/assets/js/site.js') }}"></script>
    <script src="{{ asset('packages/workdo/Recruitment/src/Resources/assets/js/demo.js') }} "></script>
    <script src="{{ asset('packages/workdo/Recruitment/src/Resources/assets/js/bootstrap-notify.min.js') }}"></script>
    <script src="{{ asset('packages/workdo/Recruitment/src/Resources/assets/js/daterangepicker.js') }}"></script>
@endpush
