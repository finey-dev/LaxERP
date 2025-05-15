@extends('recruitment::layouts.master')
@section('page-title')
    {{$job->title}}
@endsection
@section('content')
<div class="job-wrapper">
    <div class="job-content">
        <nav class="navbar">
            <div class="container">
                <div class="d-flex align-items-center justify-content-between my-sm-4 mt-2 w-100 gap-2">
                    <a  href="{{route('home')}}">
                        <img src="{{ !empty (get_file(company_setting('logo_light',$job->created_by,$job->workspace))) ? get_file('uploads/logo/logo_light.png') :'WorkDo'}}" alt="logo" style="width: 90px">
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
                                    <a class="dropdown-item @if ($key == $currantLang) text-danger @endif" href="{{route('job.requirement',[$job->code,$key])}}">{{$language}}</a>
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
                        {{__(' We help')}} <br> {{__('businesses grow')}}
                    </h1>
                    <p>{{ __('Work there. Find the dream job you’ve always wanted..') }}</p>
                    </p>
                </div>
            </div>
        </section>
        <section class="apply-job-section">
            <div class="container">
                <div class="apply-job-wrapper bg-light">
                    <div class="section-title text-center">
                        <p><b>{{$job->title}}</b></p>
                        <div class="d-flex flex-wrap justify-content-center gap-1 mb-4">
                            @foreach (explode(',', $job->skill) as $skill)
                                <span class="badge p-2 px-3 bg-primary status-badge6">{{ $skill }}</span>
                            @endforeach
                        </div>

                        @if(!empty($job->location) ? $job->location : '' )
                            <p> <i class="ti ti-map-pin ms-1"></i> {{!empty($job->location) ? $job->location : ''}}</p>
                        @endif
                        @if ($job->link_type == 'Custom Link' && $job->job_link != null)
                            <a href="{{ $job->job_link }}" class="btn btn-primary" target="_blank">{{ __('Apply now') }} <i
                                    class="ti ti-send ms-2"></i> </a>
                        @else
                            <a href="{{ route('job.apply', [$job->code, $currantLang]) }}"
                                class="btn btn-primary">{{ __('Apply now') }} <i class="ti ti-send ms-2"></i> </a>
                        @endif
                        </div>
                    <h3>{{__('Requirements')}}</h3>
                    <p>{!! $job->requirement !!}</p>

                    <hr>
                    <h3>{{__('Description')}}</h3><br>
                    {!! $job->description !!}
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
@endpush


