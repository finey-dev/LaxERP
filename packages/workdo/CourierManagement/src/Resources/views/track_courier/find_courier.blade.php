
@php
    $company_settings = getCompanyAllSetting($workspace->created_by, $workspace->id);
    $color =  !empty($company_settings['color'])?$company_settings['color']:'theme-1';
@endphp
@extends('courier-management::layouts.master')
@section('page-title')
{{ __('Track Your Courier') }}
@endsection
@section('content')
<div class="custom-login">
    <div class="login-bg-img">
        <img src="{{ asset('images/'.$color.'.svg') }}" class="login-bg-1">
        <img src="{{ asset('images/common.svg') }}" class="login-bg-2">
    </div>
    <div class="bg-login bg-primary"></div>
    <div class="custom-login-inner">
        <header class="dash-header">

            <nav class="navbar navbar-expand-md navbar-light default">
                <div class="container-fluid">
                    <a class="navbar-brand" href="#">
                        <img src="{{ get_file(sidebar_logo()) }}{{'?'.time()}}" alt="" class="logo logo-lg" style="max-width: 110px;"/>
                    </a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                        data-bs-target="#navbarTogglerDemo01" aria-controls="navbarTogglerDemo01" aria-expanded="false"
                        aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarTogglerDemo01" style="flex-grow: 0;">
                        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                            <li class="nav-item">
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        </header>
        <main class="custom-wrapper">
            <div class="custom-row">

                    <div class="card">

                        <div class="card-body">
                            <div>
                                <h2 class="mb-3 f-w-600">{{ __('Track Your Courier') }}</h2>
                            </div>
                            <form method="POST" action="{{route('track.courier',$workspace->slug)}}">
                                @csrf
                                @if (session()->has('success-alert'))
                                    <div class="alert alert-success">
                                        {{ session()->get('success-alert') }}
                                    </div>
                                @endif
                                @if (session()->has('error-alert'))
                                    <div class="alert alert-danger">
                                        {{ session()->get('error-alert') }}
                                    </div>
                                @endif

                                <div class="custom-login-form">
                                    <div class="">
                                        <div class="form-group mb-3">
                                            <label for="ticket_id" class="form-label">{{ __('Tracking Id') }}</label>
                                            <input type="number"
                                                class="form-control {{ $errors->has('tracking_number') ? 'is-invalid' : '' }}"
                                                min="0" id="tracking_number" name="tracking_number"
                                                placeholder="{{ __('Enter Tracking Id') }}" required=""
                                                value="{{ old('tracking_number') }}" autofocus>
                                            <div class="invalid-feedback d-block">
                                                {{ $errors->first('tracking_number') }}
                                            </div>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label for="email" class="form-label">{{ __('Email') }}</label>
                                            <input type="email"
                                                class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
                                                id="email" name="email" placeholder="{{ __('Enter Email') }}"
                                                reuired="" value="{{ old('email') }}">
                                            <div class="invalid-feedback d-block">
                                                {{ $errors->first('email') }}
                                            </div>
                                        </div>
                                        <div class="d-grid">
                                            <button
                                                class="btn btn-primary btn-submit btn-block mt-2">{{ __('Search') }}</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

            </div>
        </main>
        <footer>
            <div class="auth-footer">
                <div class="container">
                    <div class="row">
                        <div class="col-12">
                            <span>
                                @if (!empty($admin_settings['footer_text'])) {{$admin_settings['footer_text']}} @else{{__('Copyright')}} &copy; {{ config('app.name', 'WorkDo') }}@endif{{date('Y')}}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</div>

@endsection
@push('scripts')
    <script>
        // for Choose file
        $(document).on('change', 'input[type=file]', function () {
            var names = '';
            var files = $('input[type=file]')[0].files;

            for (var i = 0; i < files.length; i++) {
                names += files[i].name + '<br>';
            }
            $('.' + $(this).attr('data-filename')).html(names);
        });
    </script>
@endpush
