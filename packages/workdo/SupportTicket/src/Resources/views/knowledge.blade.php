@extends('support-ticket::layouts.master')
@section('page-title')
{{ __('Search Your Ticket') }}
@endsection
@section('content')
<div class="auth-wrapper create-ticket justify-content-between flex-column auth-v1">
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
            <div class="col-12 text-center">
                <div class="card rounded-4">
                    <div class="ticket-title  bg-primary">
                        <h4 class="mb-0 text-white text-capitalize">{{ __('Knowledge') }}</h4>
                    </div>
                    <div class="card-body p-4 w-100">
                        <div class="text-start">
                            @if ($knowledges->count())
                                <div class="row row-gap-2">
                                    @foreach ($knowledges as $index => $knowledge)
                                        <div class="col-md-6 col-12">
                                            <div class="card h-100">
                                                <div class="card-header knowledge-title p-3" id="heading-{{ $index }}"role="button"
                                                    aria-expanded="{{ $index == 0 ? 'true' : 'false' }}">
                                                        <h5 class="mb-0">{{ \Workdo\SupportTicket\Entities\KnowledgeBase::knowlege_details($knowledge->category)}}  ( {{ \Workdo\SupportTicket\Entities\KnowledgeBase::category_count($knowledge->category)}} ) </h5>

                                                </div>
                                                <ul class="knowledge_ul d-flex flex-column gap-3 mb-0 w-100 p-3">
                                                    @foreach ($knowledges_detail as $details)
                                                        @if ($knowledge->category == $details->category)
                                                            <li style="list-style: none;" class="child gap-2  p-2 rounded-1 d-flex align-items-center justify-content-between">
                                                                <a href="{{ route('knowledgedesc',[$workspace->slug,'id'=>$details->id])}}" class="text-primary d-block">
                                                                    <i class="far fa-file-alt text-primary me-2"></i>  {{ !empty($details->title) ? $details->title : '-' }}
                                                                </a>
                                                                <i class="ti ti-chevron-right text-primary fs-4"></i>
                                                            </li>
                                                        @endif
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0 text-center">{{ __('No Knowledges found.') }}</h6>
                                    </div>
                                </div>
                            @endif
                        </div>
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
