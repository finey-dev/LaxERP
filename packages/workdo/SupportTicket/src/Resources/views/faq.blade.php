@extends('support-ticket::layouts.master')
@section('page-title')
    {{ __('FAQ') }}
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
                <div class="col-xl-9 text-center">
                    <div class="card rounded-4">
                        <div class="ticket-title bg-primary">
                            <h4 class="mb-0 text-white text-capitalize">{{ __('FAQ') }}</h4>
                        </div>
                        <div class="card-body w-100 p-4 faq">
                            <div class="faq-acnav gap-3 d-flex flex-column">
                                <div class="set has-children border p-3 rounded-1">
                                    <a href="javascript:;"
                                        class="acnav-label d-flex gap-2 align-items-center justify-content-between">
                                        <h6 class="mb-0 h5  text-black"><span>01</span> Why do we use it?</h6>
                                        <div class="faq-icon shadow"></div>
                                    </a>
                                    <div class="acnav-list mt-3 ms-3" style="display: none;">
                                        <p class="mb-0">Shopify is an e-commerce platform for building and managing online
                                            stores effortlessly. It offers customizable themes, integrated marketing tools,
                                            and seamless order management for businesses of all sizes.
                                        </p>
                                    </div>
                                </div>
                                <div class="set has-children border p-3 rounded-1">
                                    <a href="javascript:;"
                                        class="acnav-label d-flex gap-2 align-items-center justify-content-between">
                                        <h6 class="mb-0 h5  text-black"><span>02</span> Where does it come from?</h6>
                                        <div class="faq-icon shadow"></div>
                                    </a>
                                    <div class="acnav-list mt-3 ms-3" style="display: none;">
                                        <p class="mb-0">Shopify is an e-commerce platform for building and managing online
                                            stores effortlessly. It offers customizable themes, integrated marketing tools,
                                            and seamless order management for businesses of all sizes.
                                        </p>
                                    </div>
                                </div>
                                <div class="set has-children border p-3 rounded-1">
                                    <a href="javascript:;"
                                        class="acnav-label d-flex gap-2 align-items-center justify-content-between">
                                        <h6 class="mb-0 h5  text-black"><span>03</span> Where does it come from?</h6>
                                        <div class="faq-icon shadow"></div>
                                    </a>
                                    <div class="acnav-list mt-3 ms-3" style="display: none;">
                                        <p class="mb-0">Shopify is an e-commerce platform for building and managing online
                                            stores effortlessly. It offers customizable themes, integrated marketing tools,
                                            and seamless order management for businesses of all sizes.
                                        </p>
                                    </div>
                                </div>
                                <div class="set has-children border p-3 rounded-1">
                                    <a href="javascript:;"
                                        class="acnav-label d-flex gap-2 align-items-center justify-content-between">
                                        <h6 class="mb-0 h5  text-black"><span>04</span> Where does it come from?</h6>
                                        <div class="faq-icon shadow"></div>
                                    </a>
                                    <div class="acnav-list mt-3 ms-3" style="display: none;">
                                        <p class="mb-0">Shopify is an e-commerce platform for building and managing online
                                            stores effortlessly. It offers customizable themes, integrated marketing tools,
                                            and seamless order management for businesses of all sizes.
                                        </p>
                                    </div>
                                </div>
                                <div class="set has-children border p-3 rounded-1">
                                    <a href="javascript:;"
                                        class="acnav-label d-flex gap-2 align-items-center justify-content-between">
                                        <h6 class="mb-0 h5  text-black"><span>05</span> Where does it come from?</h6>
                                        <div class="faq-icon shadow"></div>
                                    </a>
                                    <div class="acnav-list mt-3 ms-3" style="display: none;">
                                        <p class="mb-0">Shopify is an e-commerce platform for building and managing online
                                            stores effortlessly. It offers customizable themes, integrated marketing tools,
                                            and seamless order management for businesses of all sizes.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            {{-- @if ($faqs->count())
                        <div class="accordion accordion-flush faq-tab" id="faq-accordion">
                            @foreach ($faqs as $index => $faq)
                                <div class="accordion-item faq-card border">
                                    <h2 class="accordion-header" id="heading-{{ $index }}">
                                        <button class="accordion-button collapsed p-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $index }}" aria-expanded="{{ $index == 0 ? 'true' : 'false' }}" aria-controls="collapse-{{ $index }}">
                                            <span class="d-flex align-items-center justify-content-between w-100">
                                                {{$index+1}} {{ $faq->title }}
                                                <div class="faq-icon"></div>
                                            </span>
                                        </button>
                                    </h2>
                                    <div id="collapse-{{ $index }}"
                                        class="accordion-collapse p-3 pt-0 collapse @if ($index == 0) show @endif" aria-labelledby="heading-{{ $index }}" data-bs-parent="#faq-accordion">
                                        <div class="accordion-body p-0">
                                            <p class="mb-0">{!! $faq->description !!}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @else
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title mb-0 text-center">{{ __('No Faqs found.') }}</h6>
                                </div>
                            </div>
                        @endif --}}
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

        document.addEventListener('DOMContentLoaded', () => {
        const labels = document.querySelectorAll('.acnav-label');

        labels.forEach(label => {
            label.addEventListener('click', () => {
                const parent = label.closest('.set.has-children'); // Find the parent
                const isOpen = parent.classList.contains('is_open'); // Check if open

                // Close all other sets
                document.querySelectorAll('.set.has-children.is_open').forEach(openParent => {
                    openParent.classList.remove('is_open');
                    openParent.querySelector('.acnav-list').style.display = 'none';
                });

                // Toggle current one
                if (!isOpen) {
                    parent.classList.add('is_open');
                    parent.querySelector('.acnav-list').style.display = 'block';
                }
            });
        });
    });

</script>
@endpush
