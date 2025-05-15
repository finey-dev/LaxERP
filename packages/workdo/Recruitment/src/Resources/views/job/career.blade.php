@extends('recruitment::layouts.master')
@section('page-title')
{{__('Career')}}
@endsection
@push('css')
<link rel="stylesheet" href="{{ asset('assets/fonts/tabler-icons.min.css') }}">
@endpush

@section('content')
<div class="job-wrapper">
    <div class="job-content">
        <nav class="navbar">
            <div class="container">
                <div class="d-flex align-items-center justify-content-between my-sm-4 mt-2 w-100 gap-2">
                    <a  href="{{route('home')}}">
                        <img src="{{ !empty (get_file(company_setting('logo_light',$company_id,$workspace_id))) ? get_file('uploads/logo/logo_light.png') :'WorkDo'}}" alt="logo" style="width: 90px">
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
                                        <a class="dropdown-item @if ($key == $currantLang) text-danger @endif"
                                            href="{{ route('careers', [$slug, $key]) }}">{{ $language }}</a>
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
                <img src="{{ asset('packages/workdo/Recruitment/src/Resources/assets/image/banner.png') }}" alt="">
            </div>
            <div class="container">
                <div class="job-banner-content text-center text-white">
                    <h1 class="text-white mb-3">
                        {{ __(' We help') }} <br> {{ __('businesses grow') }}
                    </h1>
                    <p>{{ __('Work there. Find the dream job you’ve always wanted..') }}</p>
                </div>
            </div>
        </section>
        <section class="placedjob-section">
            <div class="container">
                <div class="section-title bg-light">
                    @php
                    $totaljob = \Workdo\Recruitment\Entities\Job::where('created_by', '=', $company_id)->where('workspace', '=', $workspace_id)->where('status', '=', 'Active')->where('is_post', '=', 1)->count();
                    @endphp

                    <h2 class="h1 mb-3"> <span class="text-primary">+{{ $totaljob }}
                        </span>{{ __('Job openings') }}</h2>
                    <p>{{ __('Always looking for better ways to do things, innovate') }} <br>
                        {{ __('and help people achieve their goals') }}.
                    </p>
                </div>
                <div class="row g-4">
                    @foreach ($jobs as $job)
                    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 job-card">
                        <div class="job-card-body job-badge-body">
                            <div class="d-flex mb-3 align-items-center justify-content-between ">
                                {{-- next version upload job logo and show here --}}
                                {{-- <img src="{{ asset('packages/workdo/Recruitment/src/Resources/assets/image/figma.png') }}" alt=""> --}}
                                @if (!empty($job->location) ? $job->location : '')
                                <span>{{ !empty($job->location) ? $job->location : '' }} <i
                                        class="ti ti-map-pin ms-1"></i></span>
                                @endif
                            </div>
                            <h5 class="mb-3">
                                <a href="{{ route('job.requirement', [$job->code, !empty($job) ? (!empty($currantLang) ? $currantLang : 'en') : 'en']) }}" target="_blank"
                                    class="text-dark">{{ $job->title }}
                                </a>
                            </h5>
                            <div
                                class="d-flex mb-3 align-items-start flex-column flex-xl-row flex-md-row flex-lg-column">
                                <span class="d-inline-block me-2"> <i class="ti ti-circle-plus "></i>
                                    {{ $job->position }} {{ __('position available') }}</span>
                            </div>

                            <div class="d-flex flex-wrap gap-1 align-items-center">
                                @foreach (explode(',', $job->skill) as $skill)
                                <span class="badge fix_badges bg-primary p-2 px-3">{{ $skill }}</span>
                                @endforeach

                            </div>

                            <a href="{{ route('job.requirement', [$job->code, !empty($job) ? (!empty($currantLang) ? $currantLang : 'en') : 'en']) }}" target="_blank"
                                class="btn btn-primary w-100 mt-4">
                                {{ __('Read more') }}
                            </a>

                        </div>
                    </div>
                    @endforeach

                </div>
            </div>
        </section>
    </div>
</div>
@endsection
@push('scripts')
<script src="{{ asset('packages/workdo/Recruitment/src/Resources/assets/js/site.core.js') }}"></script>
<script src="{{ asset('packages/workdo/Recruitment/src/Resources/assets/js/autosize.min.js') }}"></script>
<script src="{{ asset('packages/workdo/Recruitment/src/Resources/assets/js/site.js') }}"></script>
<script src="{{ asset('packages/workdo/Recruitment/src/Resources/assets/js/demo.js') }} "></script>
<link rel="stylesheet" href="{{ asset('assets/fonts/tabler-icons.min.css') }}">
@endpush
