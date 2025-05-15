@php
    $company_settings = getCompanyAllSetting($company_id, $workspace_id);
    $logo_dark = isset($company_settings['logo_dark']) ? $company_settings['logo_dark'] : (isset($admin_settings['logo_dark']) ? $admin_settings['logo_dark'] : 'uploads/logo/logo_dark.png');
    $admin_settings = getAdminAllSetting();
    $favicon = isset($company_settings['favicon']) ? $company_settings['favicon'] : (isset($admin_settings['favicon']) ? $admin_settings['favicon'] : 'uploads/logo/favicon.png');
    $color = !empty($company_settings['color']) ? $company_settings['color'] : 'theme-1';
    if (isset($company_settings['color_flag']) && $company_settings['color_flag'] == 'true') {
        $themeColor = 'custom-color';
    } else {
        $themeColor = $color;
    }
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <title>
        {{ company_setting('header_text', $company_id, $workspace_id) ? company_setting('header_text', $company_id, $workspace_id) : config('app.name', 'LeadGo') }}
        &dash; {{ __('Form') }}</title>
    <!-- Meta -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="Dashboard Template Description" />
    <meta name="keywords" content="Dashboard Template" />
    <meta name="author" content="WorkDo" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Favicon icon -->
    <link rel="icon" href="{{ !empty($favicon) ? get_file($favicon) : get_file('uploads/logo/favicon.png') }}"
        type="image/x-icon" />

    <!-- font css -->
    <link rel="stylesheet" href="{{ asset('assets/fonts/tabler-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/fontawesome.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/material.css') }}">

    <!-- vendor css -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="main-style-link">
    <link rel="stylesheet" href="{{ asset('assets/css/customizer.css') }}">
    <link rel="stylesheet" href="{{ asset('css/custom-color.css') }}">
    <style>
        :root {
            --color-customColor: <?= $color ?>;
        }
    </style>
</head>

<body class="{{ isset($themeColor) ? $themeColor : 'theme-1' }}">
    <!-- [ auth-signup ] start -->
    <div class="auth-wrapper auth-v1">
        <div class="auth-content justify-content-center">

            <div class="row align-items-center justify-content-center text-start">
                <div class="col-xl-6">
                    <div class="mx-3 mx-md-5 text-center">
                        <h2 class="mb-3 text-white f-w-600"><img
                                src="{{ check_file($logo_dark) ? get_file($logo_dark) : get_file('uploads/logo/logo_dark.png') }}{{ '?' . time() }}"
                                alt="{{ config('app.name', 'Workdo Crm') }}" class="navbar-brand-img"
                                style="max-width: 200px;"></h2>
                    </div>
                    <div class="card">
                        <div class="card-body p-4 w-100">
                            @if ($form->is_active == 1)
                                <div class="page-title text-center">
                                    <h5 class="mb-3">{{ $form->name }}</h5>
                                </div>
                                <form method="POST" action="{{ route('form.view.store') }}">
                                    @csrf
                                    @if ($objFields && $objFields->count() > 0)
                                        @foreach ($objFields as $objField)
                                            @if ($objField->type == 'text')
                                                <div class="form-group">
                                                    {{ Form::label('field-' . $objField->id, __($objField->name), ['class' => 'form-label']) }}
                                                    {{ Form::text('field[' . $objField->id . ']', null, ['class' => 'form-control', 'required' => 'required', 'id' => 'field-' . $objField->id]) }}
                                                </div>
                                            @elseif($objField->type == 'email')
                                                <div class="form-group">
                                                    {{ Form::label('field-' . $objField->id, __($objField->name), ['class' => 'form-label']) }}
                                                    {{ Form::email('field[' . $objField->id . ']', null, ['class' => 'form-control', 'required' => 'required', 'id' => 'field-' . $objField->id]) }}
                                                </div>
                                            @elseif($objField->type == 'number')
                                                <div class="form-group">
                                                    {{ Form::label('field-' . $objField->id, __($objField->name), ['class' => 'form-label']) }}
                                                    {{ Form::number('field[' . $objField->id . ']', null, ['class' => 'form-control', 'required' => 'required', 'id' => 'field-' . $objField->id]) }}
                                                </div>
                                            @elseif($objField->type == 'date')
                                                <div class="form-group">
                                                    {{ Form::label('field-' . $objField->id, __($objField->name), ['class' => 'form-label']) }}
                                                    {{ Form::date('field[' . $objField->id . ']', null, ['class' => 'form-control', 'required' => 'required', 'id' => 'field-' . $objField->id]) }}
                                                </div>
                                            @elseif($objField->type == 'textarea')
                                                <div class="form-group">
                                                    {{ Form::label('field-' . $objField->id, __($objField->name), ['class' => 'form-label']) }}
                                                    {{ Form::textarea('field[' . $objField->id . ']', null, ['class' => 'form-control', 'required' => 'required', 'id' => 'field-' . $objField->id , 'rows' => 3]) }}
                                                </div>
                                            @endif
                                        @endforeach
                                        <input type="hidden" value="{{ $code }}" name="code">
                                        <div class="text-center">
                                            <button class="btn btn-primary btn-block">{{ __('Submit') }}</button>
                                        </div>
                                    @endif
                                </form>
                            @else
                                <div class="page-title">
                                    <h5>{{ __('Form is not active.') }}</h5>
                                </div>
                            @endif

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- [ auth-signup ] end -->

    <div class="loader-wrapper d-none">
        <span class="site-loader"> </span>
    </div>
    <div class="position-fixed top-0 end-0 p-3" style="z-index: 99999">
        <div id="liveToast" class="toast text-white  fade" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body"> </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                    aria-label="Close"></button>
            </div>
        </div>
    </div>

    <!-- Required Js -->
    <script src="{{ asset('assets/js/vendor-all.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/feather.min.js') }}"></script>
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script>
        feather.replace();
    </script>
    @if ($admin_settings['enable_cookie'] == 'on')
        @include('layouts.cookie_consent')
    @endif
    <script src="{{ asset('js/custom.js') }}"></script>
    @if ($message = Session::get('success'))
        <script>
            toastrs('Success', '{!! $message !!}', 'success');
        </script>
    @endif
    @if ($message = Session::get('error'))
        <script>
            toastrs('Error', '{!! $message !!}', 'error');
        </script>
    @endif
</body>

</html>
