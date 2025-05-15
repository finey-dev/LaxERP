@extends('support-ticket::layouts.master')
@section('page-title')
{{ __('Search Your Ticket') }}
@endsection
@section('content')

<div class="auth-wrapper create-ticket knowledge-desc justify-content-between flex-column auth-v1">
    <div class="bg-auth-side"></div>
    <div class="auth-content">

        <nav class="navbar navbar-expand-md bg-primary default ">
            <div class="container-fluid pe-sm-4 pe-3">
                <a class="navbar-brand" href="#">

                    <img src="{{ !empty(company_setting('logo_light', $workspace->created_by, $workspace->id)) ? get_file(company_setting('logo_light', $workspace->created_by, $workspace->id)) : get_file(!empty(admin_setting('logo_light', $workspace->created_by, $workspace->id)) ? admin_setting('logo_light') : 'uploads/logo/logo_light.png', $workspace->created_by, $workspace->id) }}{{ '?' . time() }}"
                        class="navbar-brand-img auth-navbar-brand">

                </a>
                <button class="navbar-toggler p-1 border-white" type="button" aria-expanded="false">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse">
                    <ul class="navbar-nav align-items-center ms-auto   gap-3 ">
                        <li class="nav-item">
                            <a class="nav-link text-white p-0"
                                href="#">{{ __('Create Ticket') }}</a>
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
            <div class="col-12">
                <div class="card rounded-4">
                    <div class="card-body p-sm-4 p-3 w-100">
                        <h4 class="mb-3">{{ $descriptions->title }}</h4>
                            @if($descriptions->count())
                                <p class="mb-0">{!! $descriptions->description !!}</p>
                            @else
                                <h6 class="card-title mb-0 text-center">{{ __('No Knowledges found.') }}</h6>
                            @endif
                    </div>
                </div>
            </div>
        </div>

    </div>
    <div class="auth-footer create-ticket-footer w-100 bg-primary mt-4">
        <div class="container-fluid">
            <p class="p-2 mb-0 text-white text-capitalize">{{ env('FOOTER_TEXT') }}</p>
        </div>
    </div>
</div>


@endsection
@push('script')
    <script>
    $(document).ready(function () {
        let wasSmallScreen = $(window).width() <= 767;
        $('.navbar-toggler').click(function () {
            if ($(window).width() <= 767) {
                $('.navbar-collapse').toggleClass('show');
                $('body').toggleClass('no_scroll');
            }
        });

        $(window).resize(function () {
            const isSmallScreen = $(window).width() <= 767;

            if (wasSmallScreen && !isSmallScreen) {
                $('.navbar-collapse').removeClass('show');
                $('body').removeClass('no_scroll');
                location.reload();
            }
                wasSmallScreen = isSmallScreen;
        });
    });



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
