@extends('layouts.main')
@section('page-title')
    {{ __('Dashboard') }}
@endsection
@section('page-breadcrumb')
    {{ __('Support-ticket') }}
@endsection

@section('content')
    <div class="row row-gap mb-4 ">
        <div class="col-xl-6 col-12">
            <div class="dashboard-card">
                <img src="{{ asset('assets/images/layer.png')}}" class="dashboard-card-layer" alt="layer">
                <div class="card-inner">
                    <div class="card-content">
                        <h2>{{Auth::user()->ActiveWorkspaceName()}}</h2>
                        <p>{{ __('Handle customer inquiries efficiently with support tickets for tracking, prioritizing, and fast resolution.') }}</p>
                        <div class="btn-wrp d-flex gap-3">
                            <a href="javascript:" class="btn btn-primary d-flex align-items-center gap-1 cp_link" tabindex="0" data-link="{{ route('support-ticket', $workspace->slug) }}"
                                        data-bs-whatever="{{ __('Copy Link') }}" data-bs-toggle="tooltip"
                                        data-bs-original-title="{{ __('Copy Link') }}"
                                        title="{{ __('Click to copy link') }}">
                                <i class="ti ti-link text-white"></i>
                            <span>{{__('Create Ticket') }}</span></a>
                            <!-- <a href="javascript:" class="btn btn-primary" tabindex="0">
                                <i class="ti ti-share text-white"></i>
                            </a> -->
                        </div>
                    </div>
                    <div class="card-icon  d-flex align-items-center justify-content-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="63" height="70" viewBox="0 0 63 70" fill="none">
                            <path opacity="0.6" d="M43.3204 60.5504L35.571 68.311C33.3217 70.5635 29.6749 70.5635 27.4294 68.311L19.68 60.5504C17.6533 58.5209 14.4442 58.2942 12.1527 60.0162L6.93626 63.933C4.40676 65.8318 0.794922 64.0252 0.794922 60.858V9.95489C0.794922 6.78762 4.40676 4.9811 6.93626 6.87992L12.1527 10.7967C14.4442 12.5187 17.6533 12.292 19.68 10.2625L27.4294 2.50184C29.6787 0.249388 33.3256 0.249388 35.571 2.50184L43.3204 10.2625C45.3471 12.292 48.5562 12.5187 50.8478 10.7967L56.0642 6.87992C58.5937 4.9811 62.2055 6.78762 62.2055 9.95489V60.858C62.2055 64.0252 58.5937 65.8318 56.0642 63.933L50.8478 60.0162C48.5562 58.2942 45.3471 58.5209 43.3204 60.5504Z" fill="#18BF6B"/>
                            <path d="M46.8516 30.6055H27.6596C26.0705 30.6055 24.7808 29.314 24.7808 27.7227C24.7808 26.1314 26.0705 24.8398 27.6596 24.8398H46.8516C48.4407 24.8398 49.7304 26.1314 49.7304 27.7227C49.7304 29.314 48.4407 30.6055 46.8516 30.6055ZM49.7304 43.0977C49.7304 41.5064 48.4407 40.2149 46.8516 40.2149H27.6596C26.0705 40.2149 24.7808 41.5064 24.7808 43.0977C24.7808 44.6891 26.0705 45.9806 27.6596 45.9806H46.8516C48.4407 45.9806 49.7304 44.6891 49.7304 43.0977ZM16.1444 24.8398C14.5553 24.8398 13.2656 26.1314 13.2656 27.7227C13.2656 29.314 14.5553 30.6055 16.1444 30.6055C17.7335 30.6055 19.0232 29.314 19.0232 27.7227C19.0232 26.1314 17.7335 24.8398 16.1444 24.8398ZM16.1444 40.2149C14.5553 40.2149 13.2656 41.5064 13.2656 43.0977C13.2656 44.6891 14.5553 45.9806 16.1444 45.9806C17.7335 45.9806 19.0232 44.6891 19.0232 43.0977C19.0232 41.5064 17.7335 40.2149 16.1444 40.2149Z" fill="#18BF6B"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-12">
            <div class="row dashboard-wrp">
                <div class="col-sm-4 col-12">
                    <div class="dashboard-project-card">
                        <div class="card-inner  d-flex justify-content-between">
                            <div class="card-content">
                                <div class="theme-avtar bg-white">
                                    <i class="fas fa-list-alt text-danger"></i>
                                </div>
                                <a href="{{ route('ticket-category.index') }}"><h3 class="mt-3 mb-0 text-danger">{{ __('Categories') }}</h3></a>
                            </div>
                            <h3 class="mb-0">{{ $categories }}
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4 col-12">
                    <div class="dashboard-project-card">
                        <div class="card-inner  d-flex justify-content-between">
                            <div class="card-content">
                                <div class="theme-avtar bg-white">
                                    <i class="fas fa-ticket-alt"></i>
                                </div>
                                <a href="{{ route('support-tickets.index') }}"><h3 class="mt-3 mb-0">{{ __('Open Tickets') }}</h3></a>
                            </div>
                            <h3 class="mb-0">{{ $open_ticket }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4 col-12">
                    <div class="dashboard-project-card">
                        <div class="card-inner  d-flex justify-content-between">
                            <div class="card-content">
                                <div class="theme-avtar bg-white">
                                    <i class="fas fa-ticket-alt"></i>
                                </div>
                                <a href="{{ route('support-tickets.index') }}"><h3 class="mt-3 mb-0">{{ __('Closed Tickets') }}</h3></a>
                            </div>
                            <h3 class="mb-0">{{ $close_ticket }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xxl-7 mb-4">
            <div class="card h-100 mb-0">
                <div class="card-header">
                    <h5>{{ __('This Year Tickets') }}</h5>
                </div>
                <div class="card-body">
                    <div id="chartBar"></div>
                </div>
            </div>
        </div>

        <div class="col-xxl-5 mb-4">
            <div class="card h-100 mb-0">
                <div class="card-header">
                    <h5>{{ __('Tickets By Category') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-12">
                            <div id="categoryPie"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/plugins/apexcharts.min.js') }}"></script>

    <script>
        $('.cp_link').on('click', function() {
            var value = $(this).attr('data-link');
            var $temp = $("<input>");
            $("body").append($temp);
            $temp.val(value).select();
            document.execCommand("copy");
            $temp.remove();
            toastrs('Success', '{{ __('Link Copy on Clipboard') }}', 'success')
        });
    </script>

    <script>
        (function() {
            var categoryPieOptions = {
                chart: {
                    height: 250,
                    type: 'donut',
                },
                dataLabels: {
                    enabled: false,
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '70%',
                        }
                    }
                },
                series: {!! json_encode($chartData['value']) !!},
                colors: {!! json_encode($chartData['color']) !!},
                labels: {!! json_encode($chartData['name']) !!},
                legend: {
                    show: true
                }
            };
            var categoryPieChart = new ApexCharts(document.querySelector("#categoryPie"), categoryPieOptions);
            categoryPieChart.render();
        })();

        (function() {
            var chartBarOptions = {
                series: [{
                    name: '{{ __('Tickets') }}',
                    data: {!! json_encode(array_values($monthData)) !!}
                }, ],

                chart: {
                    height: 240,
                    type: 'area',

                    dropShadow: {
                        enabled: true,
                        color: '#000',
                        top: 18,
                        left: 7,
                        blur: 10,
                        opacity: 0.2
                    },
                    toolbar: {
                        show: false
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    width: 2,
                    curve: 'smooth'
                },
                title: {
                    text: '',
                    align: 'left'
                },
                xaxis: {
                    categories: {!! json_encode(array_keys($monthData)) !!},
                    title: {
                        text: '{{ __('Months') }}'
                    }
                },
                colors: ['#ffa21d', '#FF3A6E'],

                grid: {
                    strokeDashArray: 4,
                },
                legend: {
                    show: false,
                },

                yaxis: {
                    title: {
                        text: '{{ __('Tickets') }}'
                    },
                    tickAmount: 3,
                    min: 1,
                    max: 30,
                }
            };
            var arChart = new ApexCharts(document.querySelector("#chartBar"), chartBarOptions);
            arChart.render();
        })();

        (function() {
            var chartBarOptions = {
                series: [{
                    name: 'Order',
                    data: {!! json_encode($chartDatas['data']) !!},
                }, ],
                chart: {
                    height: 250,
                    type: 'area',
                    dropShadow: {
                        enabled: true,
                        color: '#000',
                        top: 18,
                        left: 7,
                        blur: 10,
                        opacity: 0.2
                    },
                    toolbar: {
                        show: false
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    width: 2,
                    curve: 'smooth'
                },
                title: {
                    text: '',
                    align: 'left'
                },
                xaxis: {
                    categories: ["15-Jun", "16-Jun", "17-Jun", "18-Jun", "19-Jun", "20-Jun", "21-Jun"],
                    title: {
                        text: ''
                    }
                },
                colors: ['#1260CC'],

                grid: {
                    strokeDashArray: 4,
                },
                legend: {
                    show: false,
                },
                yaxis: {
                    title: {
                        text: ''
                    },

                }
            };
            var arChart = new ApexCharts(document.querySelector("#chart-sales"), chartBarOptions);
            arChart.render();
        })();
    </script>
@endpush
