@extends('layouts.main')
@section('page-title')
    {{ __('Manage Offer Letter Settings') }}
@endsection
@section('page-breadcrumb')
{{ __('Offer Letter Settings') }}
@endsection
@section('page-action')
@push('css')
    <link href="{{  asset('assets/js/plugins/summernote-0.8.18-dist/summernote-lite.min.css')  }}" rel="stylesheet">
@endpush

@push('scripts')
    <script src="{{ asset('assets/js/plugins/summernote-0.8.18-dist/summernote-lite.min.js') }}"></script>
@endsection
@section('content')
<div class="row">
    <div class="col-sm-3">
        @include('recruitment::layouts.recruitment_setup')
    </div>
    <div class="col-sm-9">
        <div class="">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h5>{{ __('Offer Letter Settings') }}</h5>
                    <div class="d-flex justify-content-end drp-languages">
                        @if (module_is_active('AIAssistant'))
                            @include('aiassistant::ai.generate_ai_btn', [
                                'template_module' => 'offer letter settings',
                                'module' => 'Recruitment',
                            ])
                        @endif
                        <ul class="list-unstyled mb-0 m-2">
                            <li class="dropdown dash-h-item drp-language">
                                <a class="dash-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown" role="button"
                                    aria-haspopup="false" aria-expanded="false" id="dropdownLanguage">
                                    <span class="drp-text hide-mob text-primary">
                                        {{ Str::upper($offerlang) }}
                                    </span>
                                    <i class="ti ti-chevron-down drp-arrow nocolor"></i>
                                </a>
                                <div class="dropdown-menu dash-h-dropdown dropdown-menu-end" aria-labelledby="dropdownLanguage">
                                    @foreach (languages() as $key => $offerlangs)
                                        <a href="{{ route('offerletter.index', ['offerlangs' => $key]) }}"
                                            class="dropdown-item ms-1 {{ $key == $offerlang ? 'text-primary' : '' }}">{{ Str::ucfirst($offerlangs) }}</a>
                                    @endforeach
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="card-body ">
                    <h5 class="font-weight-bold pb-3">{{ __('Placeholders') }}</h5>

                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <div class="card">
                            <div class="card-header card-body">
                                <div class="row text-xs">
                                    <div class="row">
                                        <p class="col-4">{{ __('Applicant Name') }} : <span
                                                class="pull-end text-primary">{applicant_name}</span></p>
                                        <p class="col-4">{{ __('App Name') }} : <span
                                                class="pull-right text-primary">{app_name}</span></p>
                                        <p class="col-4">{{ __('Company Name') }} : <span
                                                class="pull-right text-primary">{company_name}</span></p>
                                        <p class="col-4">{{ __('Job title') }} : <span
                                                class="pull-right text-primary">{job_title}</span></p>
                                        <p class="col-4">{{ __('Job type') }} : <span
                                                class="pull-right text-primary">{job_type}</span></p>
                                        <p class="col-4">{{ __('Proposed Start Date') }} : <span
                                                class="pull-right text-primary">{start_date}</span></p>
                                        <p class="col-4">{{ __('Working Location') }} : <span
                                                class="pull-right text-primary">{workplace_location}</span></p>
                                        <p class="col-4">{{ __('Days Of Week') }} : <span
                                                class="pull-right text-primary">{days_of_week}</span></p>
                                        <p class="col-4">{{ __('Salary') }} : <span
                                                class="pull-right text-primary">{salary}</span></p>
                                        <p class="col-4">{{ __('Salary Type') }} : <span
                                                class="pull-right text-primary">{salary_type}</span></p>
                                        <p class="col-4">{{ __('Salary Duration') }} : <span
                                                class="pull-end text-primary">{salary_duration}</span></p>
                                        <p class="col-4">{{ __('Offer Expiration Date') }} : <span
                                                class="pull-right text-primary">{offer_expiration_date}</span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body table-border-style ">

                    {{ Form::open(['route' => ['offerletter.update', $offerlang], 'method' => 'post']) }}
                    <div class="form-group col-12">
                        {{ Form::label('offer_content', __(' Format'), ['class' => 'form-label text-dark']) }}
                        <textarea name="offer_content"
                            class="form-control summernote  {{ !empty($errors->first('offer_content')) ? 'is-invalid' : '' }}" required
                            id="offer_content">{!! isset($currOfferletterLang->content) ? $currOfferletterLang->content : '' !!}</textarea>
                    </div>
                    <div class="text-end">

                        {{ Form::submit(__('Save Changes'), ['class' => 'btn  btn-primary']) }}
                    </div>

                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
