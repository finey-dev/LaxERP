@extends('layouts.main')

@section('page-title')
    {{ __('Dashboard') }}
@endsection

@section('page-breadcrumb')
    {{ __('Procurement') }}
@endsection

@push('css')
    <link rel="stylesheet" href="{{ asset('packages/workdo/Procurement/src/Resources/assets/css/main.css') }}" />
@endpush
@push('scripts')
    <script src="{{ asset('packages/workdo/Procurement/src/Resources/assets/js/main.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/apexcharts.min.js') }}"></script>
    <script>
        (function() {
            var etitle;
            var etype;
            var etypeclass;
            var calendar = new FullCalendar.Calendar(document.getElementById('event_calendar'), {
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'timeGridDay,timeGridWeek,dayGridMonth'
                },
                buttonText: {
                    timeGridDay: "{{ __('Day') }}",
                    timeGridWeek: "{{ __('Week') }}",
                    dayGridMonth: "{{ __('Month') }}"
                },
                themeSystem: 'bootstrap',
                initialDate: '{{ $transdate }}',
                slotDuration: '00:10:00',
                navLinks: true,
                droppable: true,
                selectable: true,
                selectMirror: true,
                editable: true,
                dayMaxEvents: true,
                handleWindowResize: true,
                events: {!! json_encode($calenderTasks) !!},
            });
            calendar.render();
        })();
    </script>

    <script>
        var WorkedHoursChart = (function() {
            var $chart = $('#rfx_stage');

            function init($this) {
                var options = {
                    chart: {
                        height: 270,
                        type: 'bar',
                        zoom: {
                            enabled: false
                        },
                        toolbar: {
                            show: false
                        },
                        shadow: {
                            enabled: false,
                        },

                    },
                    plotOptions: {
                        bar: {
                            columnWidth: '30%',
                            borderRadius: 10,
                            dataLabels: {
                                position: 'top',
                            },
                        }
                    },
                    stroke: {
                        show: true,
                        width: 1,
                        colors: ['#fff']
                    },
                    series: [{
                        name: 'Platform',
                        data: {!! json_encode($dealStageData) !!},
                    }],
                    xaxis: {
                        labels: {
                            // format: 'MMM',
                            style: {
                                colors: '#293240',
                                fontSize: '12px',
                                fontFamily: "sans-serif",
                                cssClass: 'apexcharts-xaxis-label',
                            },
                        },
                        axisBorder: {
                            show: false
                        },
                        axisTicks: {
                            show: true,
                            borderType: 'solid',
                            color: '#f2f2f2',
                            height: 6,
                            offsetX: 0,
                            offsetY: 0
                        },
                        title: {
                            text: 'Platform'
                        },
                        categories: {!! json_encode($dealStageName) !!},
                    },
                    yaxis: {
                        labels: {
                            style: {
                                color: '#f2f2f2',
                                fontSize: '12px',
                                fontFamily: "Open Sans",
                            },
                        },
                        axisBorder: {
                            show: false
                        },
                        axisTicks: {
                            show: true,
                            borderType: 'solid',
                            color: '#f2f2f2',
                            height: 6,
                            offsetX: 0,
                            offsetY: 0
                        }
                    },
                    fill: {
                        type: 'solid',
                        opacity: 1

                    },
                    markers: {
                        size: 4,
                        opacity: 0.7,
                        strokeColor: "#000",
                        strokeWidth: 3,
                        hover: {
                            size: 7,
                        }
                    },
                    grid: {
                        borderColor: '#f2f2f2',
                        strokeDashArray: 5,
                    },
                    dataLabels: {
                        enabled: false
                    }
                }
                // Get data from data attributes
                var dataset = $this.data().dataset,
                    labels = $this.data().labels,
                    color = $this.data().color,
                    height = $this.data().height,
                    type = $this.data().type;

                // Init chart
                var chart = new ApexCharts($this[0], options);
                // Draw chart
                setTimeout(function() {
                    chart.render();
                }, 300);
            }

            // Events
            if ($chart.length) {
                $chart.each(function() {
                    init($(this));
                });
            }
        })();
    </script>

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
@endpush

@push('css')
    <link rel="stylesheet" href="{{ asset('packages/workdo/Procurement/src/Resources/assets/css/custom.css') }}">
@endpush
@section('content')
    <div class="row row-gap mb-4 ">
        @php
            $class = '';
            if (count($arrCount) < 3) {
                $class = 'col-lg-4 col-md-4';
            } else {
                $class = 'col-lg-3 col-md-3';
            }
        @endphp
        <div class="col-xl-6 col-12">
            <div class="dashboard-card">
                <img src="{{ asset('assets/images/layer.png') }}" class="dashboard-card-layer" alt="layer">
                <div class="card-inner">
                    <div class="card-content">
                        <h2>{{ Auth::user()->ActiveWorkspaceName() }}</h2>
                        <p>{{ __('Streamline purchasing, enhance efficiency, and control spending with our Procurement solution.') }}
                        </p>

                        <div class="btn-wrp d-flex gap-3">
                            <a href="javascript:" class="btn btn-primary d-flex align-items-center gap-1 cp_link"
                                tabindex="0" data-link="{{ route('rfx-list', $workspace->slug) }}"
                                data-bs-whatever="{{ __('RFX Apply') }}" data-bs-toggle="tooltip"
                                data-bs-original-title="{{ __('RFX Apply') }}">
                                <i class="ti ti-link text-white"></i>
                                <span> {{ __('RFX Apply') }}</span></a>

                            {{-- <a href="javascript:" class="btn btn-primary" tabindex="0">
                                <i class="ti ti-share text-white"></i>
                            </a> --}}
                        </div>
                    </div>
                    <div class="card-icon  d-flex align-items-center justify-content-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="66" height="66" viewBox="0 0 66 66" fill="none">
                            <g clip-path="url(#clip0_149_4900)">
                            <path opacity="0.6" d="M22.7594 20.8588L25.4664 25.5475C26.0003 26.4723 27.1829 26.7893 28.1077 26.2552L29.9048 25.2177C30.8029 25.9192 31.7966 26.5014 32.8686 26.9357V29.0039C32.8686 30.0718 33.7343 30.9375 34.8022 30.9375H40.2162C41.2841 30.9375 42.1498 30.0718 42.1498 29.0039V26.9357C43.2219 26.5014 44.2156 25.9192 45.1136 25.2177L46.9107 26.2552C47.8356 26.7892 49.0181 26.4723 49.552 25.5475L52.259 20.8588C52.793 19.9341 52.4761 18.7515 51.5512 18.2175L49.7523 17.1789C49.8301 16.6185 49.8842 16.0505 49.8842 15.4688C49.8842 14.887 49.8301 14.319 49.7525 13.7587L51.5514 12.7201C52.4763 12.1862 52.7931 11.0036 52.2592 10.0788L49.5521 5.39009C49.0182 4.46531 47.8356 4.14833 46.9109 4.68239L45.1138 5.71996C44.2157 5.01845 43.2219 4.43618 42.15 4.00189V1.93359C42.15 0.865734 41.2842 0 40.2164 0H34.8023C33.7344 0 32.8687 0.865734 32.8687 1.93359V4.00177C31.7966 4.43605 30.803 5.01832 29.9049 5.71983L28.1078 4.68226C27.183 4.14833 26.0005 4.46518 25.4665 5.38996L22.7595 10.0787C22.2256 11.0034 22.5424 12.186 23.4673 12.72L25.2662 13.7586C25.1884 14.319 25.1342 14.887 25.1342 15.4688C25.1342 16.0505 25.1884 16.6185 25.266 17.1788L23.4671 18.2174C22.5423 18.7513 22.2254 19.9339 22.7594 20.8588ZM37.5092 8.89453C41.1342 8.89453 44.0834 11.8438 44.0834 15.4688C44.0834 19.0937 41.1342 22.043 37.5092 22.043C33.8842 22.043 30.935 19.0937 30.935 15.4688C30.935 11.8438 33.8842 8.89453 37.5092 8.89453Z" fill="#18BF6B"/>
                            <path opacity="0.6" d="M37.5117 18.1758C39.0068 18.1758 40.2188 16.9638 40.2188 15.4688C40.2188 13.9737 39.0068 12.7617 37.5117 12.7617C36.0167 12.7617 34.8047 13.9737 34.8047 15.4688C34.8047 16.9638 36.0167 18.1758 37.5117 18.1758Z" fill="#18BF6B"/>
                            <path d="M12.3173 8.16776C12.1021 7.30705 11.3287 6.70312 10.4414 6.70312H5.28516C4.2173 6.70312 3.35156 7.56886 3.35156 8.63672C3.35156 9.70458 4.2173 10.5703 5.28516 10.5703H8.93166L10.8653 18.3047H14.8515L12.3173 8.16776Z" fill="#18BF6B"/>
                            <path d="M56.7929 47.7776L62.5937 24.5744C62.8988 23.3541 61.9757 22.1719 60.7178 22.1719H55.9728L52.9074 27.4811C51.8747 29.2702 49.949 30.3819 47.8824 30.3819C47.8821 30.3819 47.8821 30.3819 47.8819 30.3819C47.2155 30.3818 46.5548 30.266 45.9302 30.0426C45.4394 32.7471 43.0666 34.8047 40.2226 34.8047H34.8086C31.9645 34.8047 29.592 32.7471 29.101 30.0426C28.4764 30.266 27.8155 30.3819 27.1491 30.3819C25.0822 30.3819 23.1566 29.2704 22.1237 27.481L19.0584 22.1719H11.8359L20.4933 56.801C20.7084 57.6617 21.4819 58.2656 22.3691 58.2656H26.043C25.4361 59.0736 25.0762 60.0777 25.0762 61.166C25.0762 63.8358 27.2404 66 29.9101 66C32.5799 66 34.7441 63.8358 34.7441 61.166C34.7441 60.0777 34.3843 59.0736 33.7773 58.2656H46.668C46.0611 59.0736 45.7012 60.0777 45.7012 61.166C45.7012 63.8358 47.8654 66 50.5351 66C53.2049 66 55.3691 63.8358 55.3691 61.166C55.3691 60.0777 55.0093 59.0736 54.4023 58.2656H58.1406C59.2085 58.2656 60.0742 57.3999 60.0742 56.332C60.0742 55.2642 59.2085 54.3984 58.1406 54.3984H23.8789L22.5898 49.2422H54.9171C55.8043 49.2422 56.5776 48.6384 56.7929 47.7776Z" fill="#18BF6B"/>
                            </g>
                            <defs>
                            <clipPath id="clip0_149_4900">
                            <rect width="66" height="66" fill="white"/>
                            </clipPath>
                            </defs>
                            </svg>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-12">
            <div class="row dashboard-wrp">
                @if (isset($arrCount['rfx_published']))
                <div class="col-md-4 col-sm-6 col-12 d-flex flex-wrap">
                    <div class="dashboard-project-card">
                        <div class="card-inner  d-flex justify-content-between">
                            <div class="card-content">
                                <div class="theme-avtar bg-white">
                                    <i class="ti ti-user text-danger"></i>
                                </div>
                                <a href="{{ route('rfx.index') }}">
                                    <h3 class="mt-3 mb-0 text-danger">{{ __('RFx`s Published') }}</h3>
                                </a>
                            </div>
                            <h3 class="mb-0">{{ $arrCount['rfx_published'] }}</h3>
                        </div>
                    </div>
                </div>
                @endif


                @if (isset($arrCount['rfx_expired']))
                <div class="col-md-4 col-sm-6 col-12 d-flex flex-wrap">
                    <div class="dashboard-project-card">
                        <div class="card-inner  d-flex justify-content-between">
                            <div class="card-content">
                                <div class="theme-avtar bg-white">
                                    <i class="ti ti-user"></i>
                                </div>
                                <a href="{{ route('rfx.index') }}">
                                    <h3 class="mt-3 mb-0">{{ __('RFx`s Expired') }}</h3>
                                </a>

                            </div>
                            <h3 class="mb-0">{{ $arrCount['rfx_expired'] }}</h3>
                        </div>
                    </div>
                </div>
                @endif



                @if (isset($arrCount['rfx_applicant']))
                <div class=" col-md-4 col-sm-6 col-12 d-flex flex-wrap">
                    <div class="dashboard-project-card">
                        <div class="card-inner  d-flex justify-content-between">
                            <div class="card-content">
                                <div class="theme-avtar bg-white">
                                    <i class="ti ti-users"></i>
                                </div>
                                <a href="{{ route('rfx-applicant.index') }}">
                                    <h3 class="mt-3 mb-0">{{ __('RFxs Applicant') }}</h3>
                                </a>
                            </div>
                            <h3 class="mb-0">{{ $arrCount['rfx_applicant'] }}</h3>
                        </div>
                    </div>
                </div>
                @endif




            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xxl-6 mb-4">
            <div class="card h-100 mb-0">
                <div class="card-header">
                    <h5>{{ __('Interview Schedule') }}</h5>
                </div>
                <div class="card-body">
                    <div class="w-100" id='event_calendar'></div>
                </div>
            </div>
        </div>
        <div class="col-xxl-6 ">
            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Recently Created RFxs') }}</h5>
                </div>
                <div class="card-body table-border-style">
                    <div class="table-responsive custom-scrollbar account-info-table">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('rfx Name') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Created At') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($rfxs as $rfx)
                                    <tr>
                                        <td>{{ $rfx->title }}</td>
                                        <td>
                                            @if ($rfx->status === 'active')
                                                {{ __('Active') }}
                                            @else
                                                {{ __('Not Active') }}
                                            @endif
                                        </td>
                                        <td>{{ $rfx->created_at }}</td>
                                    </tr>
                                @empty
                                    @include('layouts.nodatafound')
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            @if (!empty($dealStageData))
            <div class="card">
                <div class="card-header ">
                    <h5>{{ __('RFxs Application by stage') }}</h5>
                </div>
                <div class="card-body p-2">
                    <div id="rfx_stage" data-color="primary" data-height="230"></div>
                </div>
            </div>
        @endif
        </div>
    </div>
@endsection
