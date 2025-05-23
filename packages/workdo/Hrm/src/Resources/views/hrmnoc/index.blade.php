@extends('layouts.main')
@section('page-title')
    {{ __('No Objection Certificate Settings') }}
@endsection
@section('page-breadcrumb')
{{ __('No Objection Certificate Settings') }}
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
        @include('hrm::layouts.hrm_setup')
    </div>
    <div class="col-sm-9">
        <div class="">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h5>{{ __('No Objection Certificate Settings') }}</h5>
                    <div class="d-flex justify-content-end drp-languages">
                        @if (module_is_active('AIAssistant'))
                            @include('aiassistant::ai.generate_ai_btn', [
                                'template_module' => 'noc settings',
                                'module' => 'Hrm',
                            ])
                        @endif
                        <ul class="list-unstyled mb-0 m-2">
                            <li class="dropdown dash-h-item drp-language" style="margin-left: 10px;">
                                <a class="dash-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown"
                                    role="button" aria-haspopup="false" aria-expanded="false" id="dropdownLanguage1">
                                    <span class="drp-text hide-mob text-primary">

                                        {{ Str::upper($noclang) }}
                                    </span>
                                    <i class="ti ti-chevron-down drp-arrow nocolor"></i>
                                </a>
                                <div class="dropdown-menu dash-h-dropdown dropdown-menu-end"
                                    aria-labelledby="dropdownLanguage1">
                                    @foreach (languages() as $key => $noclangs)
                                        <a href="{{ route('hrmnoc.index', ['noclangs' => $key]) }}"
                                            class="dropdown-item {{ $key == $noclang ? 'text-primary' : '' }}">{{ Str::ucfirst($noclangs) }}</a>
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
                                        <p class="col-4">{{ __('Date') }} : <span
                                                class="pull-end text-primary">{date}</span></p>
                                        <p class="col-4">{{ __('App Name') }} : <span
                                                class="pull-right text-primary">{app_name}</span></p>
                                        <p class="col-4">{{ __('Company Name') }} : <span
                                            class="pull-right text-primary">{company_name}</span></p>
                                        <p class="col-4">{{ __('Employee Name') }} : <span
                                                class="pull-right text-primary">{employee_name}</span></p>
                                        <p class="col-4">{{ __('Designation') }} : <span
                                                class="pull-right text-primary">{designation}</span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body table-border-style ">
                    {{ Form::open(['route' => ['noc.update', $noclang], 'method' => 'post']) }}
                    <div class="form-group col-12">
                        {{ Form::label('noc_content', __(' Format'), ['class' => 'form-label text-dark']) }}
                        <textarea name="noc_content"
                            class="form-control summernote  {{ !empty($errors->first('noc_content')) ? 'is-invalid' : '' }}" required
                            id="noc_content">{!! isset($currnocLang->content) ? $currnocLang->content : '' !!}</textarea>
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
