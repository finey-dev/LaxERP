@extends('support-ticket::layouts.master')
@php
    $company_settings = getCompanyAllSetting($workspace->created_by, $workspace->id);
@endphp
@section('content')
    <div class="auth-wrapper create-ticket justify-content-between flex-column auth-v1">
        <div class="bg-auth-side"></div>
        <div class="auth-content">
            <nav class="navbar navbar-expand-md bg-primary default">
                <div class="container-fluid pe-sm-4 pe-3">
                    <a class="navbar-brand" href="#">
                        @if ((isset($company_settings['cust_darklayout']) ? $company_settings['cust_darklayout'] : 'off') == 'on')
                            <img src="{{ check_file(company_setting('logo_light', $workspace->created_by, $workspace->id)) ? get_file(company_setting('logo_light', $workspace->created_by, $workspace->id)) : get_file(!empty(admin_setting('logo_light')) ? admin_setting('logo_light') : 'uploads/logo/logo_light.png') }}{{ '?' . time() }}"
                                class="navbar-brand-img auth-navbar-brand">
                        @else
                            <img src="{{ check_file(company_setting('logo_dark', $workspace->created_by, $workspace->id)) ? get_file(company_setting('logo_dark', $workspace->created_by, $workspace->id)) : get_file(!empty(admin_setting('logo_dark')) ? admin_setting('logo_dark') : 'uploads/logo/logo_dark.png') }}{{ '?' . time() }}"
                                class="navbar-brand-img auth-navbar-brand">
                        @endif
                    </a>
                    <button class="navbar-toggler p-1 border-white" type="button" aria-expanded="false">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse">
                        <ul class="navbar-nav align-items-center ms-auto   gap-3 ">
                            <li class="nav-item">
                                <a class="nav-link text-white p-0" href="#">{{ __('Create Ticket') }}</a>
                            </li>
                            @if (isset($faq) && $faq == 'on')
                                <li class="nav-item">
                                    <a class="nav-link text-white p-0"
                                        href="{{ route('faqs', $workspace->slug) }}">{{ __('FAQ') }}</a>
                                </li>
                            @endif
                            @if (isset($knowledge) && $knowledge == 'on')
                                <li class="nav-item">
                                    <a class="nav-link text-white p-0"
                                        href="{{ route('knowledge', $workspace->slug) }}">{{ __('Knowledge') }}</a>
                                </li>
                            @endif

                            <li class="nav-item">
                                <a class="nav-link text-white p-0"
                                    href="{{ route('get.ticket.search', $workspace->slug) }}">{{ __('Search Ticket') }}</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>

            <div class="row align-items-center justify-content-center text-start">
                <div class="col-xl-9 text-center">
                    @if (Session::has('create_ticket'))
                        <div class="alert alert-success">
                            <p>{!! session('create_ticket') !!}</p>
                        </div>
                    @endif
                    <div class="card rounded-4">
                        <div class="ticket-title bg-primary">
                            <h4 class="text-white  mb-0 text-capitalize">{{ __('Create Ticket') }}</h4>
                        </div>
                        <div class="card-body w-100 p-3">
                            <form method="post" action="{{ route('ticket.store', $workspace->slug) }}" class="create-form"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="text-start row">
                                    @if (!$fields->isEmpty())
                                        @include('support-ticket::formBuilder')
                                    @endif
                                    <div class="text-center">
                                        <input type="hidden" name="status" value="In Progress" />
                                        <button class="btn btn-primary btn-block ticket-btn">
                                            {{ __('Create Ticket') }}
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="auth-footer create-ticket-footer text-center w-100 bg-primary mt-4">
            <div class="container-fluid">
                <p class="p-2 mb-0 text-white">
                    {{ !empty(company_setting('footer_text', $workspace->created_by, $workspace->id)) ? company_setting('footer_text', $workspace->created_by, $workspace->id) : admin_setting('footer_text') }}
                </p>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ asset('assets/js/plugins/summernote-0.8.18-dist/summernote-lite.min.js') }}"></script>
    <script>
        if ($(".summernote").length) {
            setTimeout(function() {
                $('.summernote').summernote({
                    dialogsInBody: !0,
                    minHeight: 200,
                    toolbar: [
                        ['style', ['style']],
                        ["font", ["bold", "italic", "underline", "clear", "strikethrough"]],
                        ['fontname', ['fontname']],
                        ['color', ['color']],
                        ["para", ["ul", "ol", "paragraph"]],
                    ]
                });
            }, 100);
        }
        $(document).ready(function() {
            let wasSmallScreen = $(window).width() <= 767;
            $('.navbar-toggler').click(function() {
                if ($(window).width() <= 767) {
                    $('.navbar-collapse').toggleClass('show');
                    $('body').toggleClass('no_scroll');
                }
            });

            $(window).resize(function() {
                const isSmallScreen = $(window).width() <= 767;

                if (wasSmallScreen && !isSmallScreen) {
                    $('.navbar-collapse').removeClass('show');
                    $('body').removeClass('no_scroll');
                    location.reload();
                }
                wasSmallScreen = isSmallScreen;
            });
        });
    </script>
@endpush
