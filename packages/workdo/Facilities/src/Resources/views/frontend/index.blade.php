@php
    $admin_settings = getAdminAllSetting($workspace->created_by, $workspace->id);
    $company_settings = getCompanyAllSetting($workspace->created_by, $workspace->id);
    $favicon = isset($company_settings['favicon']) ? $company_settings['favicon'] : (isset($admin_settings['favicon']) ? $admin_settings['favicon'] : 'uploads/logo/favicon.png');
    if ($company_settings['cust_darklayout'] == 'on') {
        $logo = isset($company_settings['logo_light']) ? $company_settings['logo_light'] : (isset($admin_settings['logo_light']) ? $admin_settings['logo_light'] : 'uploads/logo/logo_light.png');
    } else {
        $logo = isset($company_settings['logo_dark']) ? $company_settings['logo_dark'] : (isset($admin_settings['logo_dark']) ? $admin_settings['logo_dark'] : 'uploads/logo/logo_dark.png');
    }
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    dir="{{ $company_settings['site_rtl'] == 'on' ? 'rtl' : '' }}">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="author" content="Workdo.io">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0" />
    <title>{{ __('Facilities Booking') }}</title>
    <meta name="title"
        content="{{ !empty($admin_settings['meta_title']) ? $admin_settings['meta_title'] : 'WOrkdo Dash' }}">
    <meta name="description" content="{{ !empty($admin_settings['meta_description']) ? $admin_settings['meta_description'] : 'Facilities Booking.' }}">
    <meta name="keywords" content="{{ !empty($admin_settings['meta_keywords']) ? $admin_settings['meta_keywords'] : 'WorkDo Dash,SaaS solution,Multi-workspace' }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ env('APP_URL') }}">
    <meta property="og:title"
        content="{{ !empty($admin_settings['meta_title']) ? $admin_settings['meta_title'] : 'WOrkdo Dash' }}">
    <meta property="og:description"
        content="{{ !empty($admin_settings['meta_description']) ? $admin_settings['meta_description'] : 'Discover the efficiency of Dash, a user-friendly web application by WorkDo.' }} ">
    <meta property="og:image"
        content="{{ get_file(!empty($admin_settings['meta_image']) ? (check_file($admin_settings['meta_image']) ? $admin_settings['meta_image'] : 'uploads/meta/meta_image.png') : 'uploads/meta/meta_image.png') }}{{ '?' . time() }}">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ env('APP_URL') }}">
    <meta property="twitter:title"
        content="{{ !empty($admin_settings['meta_title']) ? $admin_settings['meta_title'] : 'WOrkdo Dash' }}">
    <meta property="twitter:description"
        content="{{ !empty($admin_settings['meta_description']) ? $admin_settings['meta_description'] : 'Discover the efficiency of Dash, a user-friendly web application by WorkDo.' }} ">
    <meta property="twitter:image"
        content="{{ get_file(!empty($admin_settings['meta_image']) ? (check_file($admin_settings['meta_image']) ? $admin_settings['meta_image'] : 'uploads/meta/meta_image.png') : 'uploads/meta/meta_image.png') }}{{ '?' . time() }}">

    <!-- Favicon icon -->
    <link rel="icon"
        href="{{ check_file($favicon) ? get_file($favicon) : get_file('uploads/logo/favicon.png') }}{{ '?' . time() }}"
        type="image/x-icon" />
    <!-- font css -->

    <link rel="stylesheet" href="{{ asset('assets/fonts/tabler-icons.min.css')}}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/fontawesome.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/material.css') }}">

    <!-- vendor css -->
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/style.css') }}">

    <link rel="stylesheet" href="{{ asset('assets/css/plugins/bootstrap-switch-button.min.css')}}">
    <link rel="stylesheet" href="{{ asset('assets/css/customizer.css') }}">
    <link rel="stylesheet" href="{{ asset('css/custome.css') }}">
    <link rel="stylesheet" href="{{ asset('packages/workdo/Facilities/src/Resources/assets/css/custom.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="main-style-link">

    <script src="{{ asset('js/jquery.min.js') }}"></script>
    {{-- <link rel="stylesheet" href="{{ asset('js/jquery.min.js') }}"> --}}
    <link rel='stylesheet' href='https://unpkg.com/nprogress@0.2.0/nprogress.css' />
    <script src='https://unpkg.com/nprogress@0.2.0/nprogress.js'></script>
    <link rel="stylesheet" href="{{ asset('css/responsive.css')}}">


    @if ((isset($company_settings['site_rtl']) ? $company_settings['site_rtl'] : 'off') == 'on')
        <link rel="stylesheet" href="{{ asset('assets/css/style-rtl.css') }}">
    @endif

    @if ((isset($company_settings['cust_darklayout']) ? $company_settings['cust_darklayout'] : 'off') == 'on')
        <link rel="stylesheet" href="{{ asset('assets/css/style-dark.css') }}" id="main-style-link">
    @endif

    @if (
        (isset($company_settings['site_rtl']) ? $company_settings['site_rtl'] : 'off') != 'on' &&
            (isset($company_settings['cust_darklayout']) ? $company_settings['cust_darklayout'] : 'off') != 'on')
        <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="main-style-link">
    @else
        <link rel="stylesheet" href="" id="main-style-link">
    @endif

</head>
<body class="{{ !empty($company_settings['color']) ? $company_settings['color'] : 'theme-1' }}">
    <header class="site-header header-style-one" id="head-sticky">
        <div class="main-navigationbar">
            <div class="container">
                <div class="navigationbar-row d-flex align-items-center justify-content-between">
                    <div class="logo-col">
                        <h1 class="mb-0">
                            <a href="#" tabindex="0" class="d-block">
                                <img src="{{ check_file($logo) ? get_file($logo) : get_file('uploads/logo/logo_dark.png') }}{{ '?' . time() }}"
                                    alt="" class="logo logo-lg" style="width: 125px;" />
                            </a>
                        </h1>
                    </div>
                    <a class="dash-head-link dropdown-toggle btn btn-primary text-white" data-bs-toggle="dropdown" href="#">
                        <span class="drp-text hide-mob text-white">{{Str::upper($lang)}}</span>
                    </a>
                    <div class="dropdown-menu dash-h-dropdown dropdown-menu-end">
                        @foreach(languages() as $key => $language)
                        <a href="{{ route('facilities.booking',['slug' => $slug,'lang' => $key]) }}" class="dropdown-item @if($lang == $key) text-primary  @endif">
                            <span>{{Str::ucfirst($language)}}</span>
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </header>
    <main class="wrapper">
        <section class="main-banner-sec" style="background-image:url({{ asset('packages/workdo/Facilities/src/Resources/assets/images/facilities.png') }});">
            <div class="container">
                <div class="section-title">
                    <h2 class="">{{ __('Facilities') }}</h2>
                    <p class="m-0">{{ __('Get Appointment for Facilities Service') }}</p>
                </div>
            </div>
        </section>
        <section class="select-box">
            <div class="container">
                <div class="select-box-inner bg-primary rounded-3 text-white">
                    <div class="booking-form row align-items-end justify-content-center">
                        <div class="form-group col-lg-3 col-md-3 col-sm-6 col-12">
                            {{ Form::label('service', __('Service'), ['class' => 'form-label']) }}
                            {{ Form::select('service', $service_name, null, ['class' => 'form-control ', 'id' => 'service', 'required' => 'required', 'placeholder' => 'Select Service']) }}
                        </div>
                        <div class="form-group col-lg-2 col-md-3 col-sm-6 col-12">
                            {{ Form::label('date', __('Date'), ['class' => 'form-label']) }}
                            {{ Form::date('date', null, ['class' => 'form-control ', 'placeholder' => 'Select Date', 'id' => 'date', 'required' => 'required', 'min' => date('Y-m-d')]) }}
                        </div>
                        <div class="form-group col-lg-3 col-md-3 col-sm-6 col-12">
                            <label class= "form-label">{{ __('Gender') }}</label>
                            <select class="form-select" id="gender" required>
                                <option selected>{{ __('Select Gender') }}</option>
                                <option>{{ __('Male') }}</option>
                                <option>{{ __('Female') }}</option>
                            </select>
                        </div>
                        <div class="form-group col-lg-2 col-md-3 col-sm-6 col-12">
                            {{ Form::label('person', __('Person'), ['class' => 'form-label']) }}
                            {{ Form::number('person', null, ['class' => 'form-control', 'placeholder' => 'Enter Total Person', 'id' => 'person', 'required' => 'required']) }}
                        </div>
                        <div class="form-group col-lg-2 col-md-3 col-sm-6 col-12">
                            <button class="btn btn-warning w-100" name="search" id="searchBtn" type="submit">{{ __('Search') }}</button>
                        </div>
                    </div>
                </div>
            </div>
            </div>
        </section>
        <section class="text-center mt-5">
            <b>
                <h2 class="error_msg alert">
                    @if(session('msg'))
                        {{ session('msg') }}
                    @endif
                </h2>
            </b>
        </section>
        <section class="VendorDetails" id="append_div">
        </section>
    </main>
    <script>
        $(document).ready(function() {
            $(".single").click(function() {
                $(".single").removeClass("selected");
                $(this).addClass("selected");
            });
        });
    </script>
    <script src="{{ asset('assets/js/plugins/popper.min.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="{{ asset('assets/js/plugins/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/jquery.form.js') }}"></script>
    <script>
        $(document).on("click", ".payment_method", function() {
            var payment_action = $(this).attr("data-payment-action");
            if (payment_action != '' && payment_action != undefined) {
                $("#payment_form").attr("action", payment_action);

            } else {
                $("#payment_form").attr("action", '');
            }
        });
    </script>
    @stack('customer_live_chat_script')
</body>
</html>

<script>
    $(document).ready(function() {
        $('#searchBtn').on('click', function() {
            var service = $('#service').val();
            var date = $('#date').val();
            var person = $('#person').val();
            var gender = $('#gender').val();

            if (service !== '' && date !== '' && person !== '' && gender !== '') {
                $.ajax({
                    type: 'POST',
                    url: "{{ route('facilities.search', [$workspace->id, $slug]) }}",
                    data: {
                        "service": service,
                        "date": date,
                        "person": person,
                        "gender": gender,
                        "_token": "{{ csrf_token() }}",
                    },
                    success: function(response) {
                        $('#append_div').empty();
                        $('.error_msg').empty();
                        if (response.is_success == true) {
                            $('#append_div').html(response.html);
                        } else {
                            $('.error_msg').text(response.message);
                        }
                    },
                    error: function(error) {
                        console.log(error);
                    }
                });
            } else {
                alert('Please fill in all fields before searching.');
            }
        });
    });
</script>
