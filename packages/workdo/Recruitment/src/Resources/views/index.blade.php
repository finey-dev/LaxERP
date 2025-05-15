@extends('layouts.main')

@section('page-title')
    {{ __('Dashboard') }}
@endsection

@section('page-breadcrumb')
    {{ __('Recruitment') }}
@endsection

@push('css')
    <link rel="stylesheet" href="{{ asset('packages/workdo/Recruitment/src/Resources/assets/css/main.css') }}" />
@endpush
@push('scripts')
    <script src="{{ asset('packages/workdo/Recruitment/src/Resources/assets/js/main.min.js') }}"></script>
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
            var $chart = $('#job_stage');

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
    <link rel="stylesheet" href="{{ asset('packages/workdo/Recruitment/src/Resources/assets/css/custom.css') }}">
@endpush
@section('content')
    <div class="row row-gap mb-4">
        <div class="col-xxl-5 col-12">
            <div class="row row-gap">
                <div class="col-md-12 col-12">
                    <div class="dashboard-card">
                        <img src="{{ asset('assets/images/layer.png')}}" class="dashboard-card-layer" alt="layer">
                        <div class="card-inner">
                            <div class="card-content">
                                <h2>{{ $workspace->name }}</h2>
                                <p>{{__('Streamline recruitment by efficiently organizing and tracking candidate details and interactions.')}}</p>
                                <div class="btn-wrp d-flex gap-3">
                                    <a href="#" class="btn btn-primary d-flex align-items-center gap-1 cp_link" tabindex="0" data-link="{{ route('careers', $workspace->slug) }}" data-bs-whatever="{{ __('Career Link') }}" data-bs-toggle="tooltip" data-bs-original-title="{{ __('Career Page') }}" title="{{ __('Click to copy link') }}">
                                        <i class="ti ti-link text-white"></i>
                                    <span>{{ __('Career Page') }}</span></a>
                                    {{-- <a href="javascript:" class="btn btn-primary" tabindex="0">
                                        <i class="ti ti-share text-white"></i>
                                    </a> --}}
                                </div>
                            </div>
                            <div class="card-icon  d-flex align-items-center justify-content-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="70" height="72" viewBox="0 0 70 72" fill="none">
                                    <path d="M10.9524 71.2901H3.65082C1.63557 71.2901 0 69.6522 0 67.6341V53.0104C0 50.9923 1.63557 49.3545 3.65082 49.3545H10.9524C12.9677 49.3545 14.6033 50.9923 14.6033 53.0104V67.6341C14.6033 69.6522 12.9677 71.2901 10.9524 71.2901Z" fill="#18BF6B"/>
                                    <path opacity="0.6" d="M69.3639 54.3265C69.3639 55.7889 68.7067 57.2148 67.5385 58.1288L55.1256 68.0728C52.5335 70.1567 49.3208 71.2901 45.9986 71.2901H25.5541C21.5382 71.2901 18.2524 67.9997 18.2524 63.9782V56.6663C18.2524 52.6448 21.5382 49.3545 25.5541 49.3545H44.2462C46.5097 49.3545 48.3717 51.1824 48.3717 53.4491C48.3717 55.7523 46.5097 57.5803 44.2462 57.5803H32.8557C31.3589 57.5803 30.1176 58.8233 30.1176 60.3223C30.1176 61.8212 31.3589 63.0642 32.8557 63.0642H44.2462C48.9923 63.0642 52.9351 59.5909 53.7017 55.0941L61.9891 50.1221C62.7923 49.6468 63.6322 49.4277 64.4719 49.4277C67.0275 49.4277 69.3639 51.4749 69.3639 54.3265Z" fill="#18BF6B"/>
                                    <path opacity="0.6" d="M40.1589 21.9346H32.8572C24.7743 21.9346 21.9048 27.8609 21.9048 32.9354C21.9048 37.5601 24.5661 40.2142 29.21 40.2142H43.8061C48.4463 40.2142 51.1113 37.5601 51.1113 32.9354C51.1113 27.8609 48.2418 21.9346 40.1589 21.9346Z" fill="#18BF6B"/>
                                    <path d="M36.5259 16.4517C41.0625 16.4517 44.7402 12.7688 44.7402 8.22584C44.7402 3.68283 41.0625 0 36.5259 0C31.9892 0 28.3115 3.68283 28.3115 8.22584C28.3115 12.7688 31.9892 16.4517 36.5259 16.4517Z" fill="#18BF6B"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-7 col-12">
            <div class="row d-flex dashboard-wrp">
                @if (isset($arrCount['job_published']))
                    <div class="col-md-4 col-sm-6 col-12 d-flex flex-wrap">
                        <div class="dashboard-project-card">
                            <div class="card-inner  d-flex justify-content-between">
                                <div class="card-content">
                                    <div class="theme-avtar bg-white">
                                        <i class="fas fa-tasks text-danger"></i>
                                    </div>
                                    <h3 class="mt-3 mb-0 text-danger">{{ __('Total Job Published') }}</h3>
                                </div>
                                <h3 class="mb-0">{{ $arrCount['job_published'] ?? 0 }}</h3>
                            </div>
                        </div>
                    </div>
                @endif
                @if (isset($arrCount['job_expired']))
                    <div class="col-md-4 col-sm-6 col-12 d-flex flex-wrap">
                        <div class="dashboard-project-card">
                            <div class="card-inner  d-flex justify-content-between">
                                <div class="card-content">
                                    <div class="theme-avtar bg-white">
                                        <i class="ti ti-file-invoice"></i>
                                    </div>
                                <h3 class="mt-3 mb-0">{{ __('Total Job Expired') }}</h3>
                                </div>
                                <h3 class="mb-0">{{ $arrCount['job_expired'] ?? 0 }}</h3>
                            </div>
                        </div>
                    </div>
                @endif
                @if (isset($arrCount['job_candidate']))
                    <div class="col-md-4 col-sm-6 col-12 d-flex flex-wrap">
                        <div class="dashboard-project-card">
                            <div class="card-inner  d-flex justify-content-between">
                                <div class="card-content">
                                    <div class="theme-avtar bg-white">
                                        <i class="fas fa-bug"></i>
                                    </div>
                                    <a href="{{ route('job-candidates.index') }}"><h3 class="mt-3 mb-0">{{ __('Total Job Candidates') }}</h3></a>
                                </div>
                                <h3 class="mb-0">{{ $arrCount['job_candidate'] }}</h3>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="row">
        @php
            $class = '';
            if (count($arrCount) < 3) {
                $class = 'col-lg-4 col-md-4';
            } else {
                $class = 'col-lg-3 col-md-3';
            }
        @endphp
        <div class="col-xxl-7 d-flex flex-column">

            <div class="card h-100">
                <div class="card-header">
                    <h5>{{ __('Interview Schedule') }}</h5>
                </div>
                <div class="card-body">
                    <div class="w-100" id='event_calendar'></div>
                </div>
            </div>
        </div>

        <div class="col-xxl-5 d-flex flex-column">
            @if (!empty($dealStageData))
                <div class="card h-100">
                    <div class="card-header ">
                        <h5>{{ __('Job Application by stage') }}</h5>
                    </div>
                    <div class="card-body p-2">
                        <div id="job_stage" data-color="primary" data-height="230"></div>
                    </div>
                </div>
            @endif
            <div class="card h-100">
                <div class="card-header">
                    <h5>{{ __('Recently Created Jobs') }}</h5>
                </div>
                <div class="card-body table-border-style">
                    <div class="table-responsive custom-scrollbar create-job-table account-info-table">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('Job Name') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Created At') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($jobs as $job)
                                    <tr>
                                        <td>{{ $job->title }}</td>
                                        <td>
                                            @if ($job->status === 'active')
                                                {{ __('Active') }}
                                            @else
                                                {{ __('Not Active') }}
                                            @endif
                                        </td>
                                        <td>{{ $job->created_at }}</td>
                                    </tr>
                                @empty
                                    @include('layouts.nodatafound')
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
