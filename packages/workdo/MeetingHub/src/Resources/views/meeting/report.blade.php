@extends('layouts.main')
@section('page-title')
    {{ __('Manage Meeting Reports') }}
@endsection

@section('page-breadcrumb')
    {{ __('Meeting Reports') }}
@endsection
@section('page-action')
    <div>

    </div>
@endsection
@section('content')
    <div class="row">
        <div class="mt-2" id="multiCollapseExample1">
            <div class="card">
                <div class="card-body">
                    {{ Form::open(['route' => ['meetinghub.meeting.report'], 'method' => 'GET', 'id' => 'customer_submit']) }}
                    <div class="row d-flex align-items-center justify-content-end">
                        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 mr-2">
                            <div class="btn-box">
                                {{ Form::label('start_date', __('Start date'), ['class' => 'form-label']) }}
                                {{ Form::text('start_date', isset($_GET['start_date']) ? $_GET['start_date'] : now()->startOfMonth()->format('Y-m-d'), ['class' => 'form-control flatpickr-input', 'placeholder' => 'Select Date']) }}
                            </div>
                        </div>

                        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 mr-2">
                            <div class="btn-box">
                                {{ Form::label('end_date', __('End date'), ['class' => 'form-label']) }}
                                {{ Form::text('end_date', isset($_GET['end_date']) ? $_GET['end_date'] : now()->endOfMonth()->format('Y-m-d'), ['class' => 'form-control flatpickr-input', 'placeholder' => 'Select Date']) }}
                            </div>
                        </div>

                        <div class="col-auto float-end ms-2 mt-4">

                            <a href="#" class="btn btn-sm btn-primary me-1"
                                onclick="document.getElementById('customer_submit').submit(); return false;"
                                data-bs-toggle="tooltip" title="{{ __('Apply') }}"
                                data-original-title="{{ __('apply') }}">
                                <span class="btn-inner--icon"><i class="ti ti-search"></i></span>
                            </a>
                            <a href="{{ route('meetinghub.meeting.report') }}" class="btn btn-sm btn-danger" data-toggle="tooltip"
                                data-original-title="{{ __('Reset') }}">
                                <span class="btn-inner--icon"><i class="ti ti-trash-off text-white-off"></i></span>
                            </a>
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
        <div class="col-ms-12">
            <div class="card">
                <div class="card-body">
                <div id="monthly-chart"></div>
                </div>
            </div>
        </div>
        <div class="col-ms-12">
        </div>
    </div>
@endsection
@push('scripts')
    <script src="{{ asset('assets/js/plugins/apexcharts.min.js') }}"></script>
    <script>
        (function() {
            var chartBarOptions = {
                series: [{
                    name: "{{ __('Meeting') }}",
                    data: {!! json_encode($dataArr['meetings']) !!}
                }],
                chart: {
                    height: 350,
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
                    categories: {!! json_encode($dataArr['month']) !!},
                    title: {
                        text: '{{ __('Date') }}'
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
                        text: '{{ __('Meetings') }}'
                    },
                }
            };

            var arChart = new ApexCharts(document.querySelector("#monthly-chart"), chartBarOptions);
            arChart.render();
        })();
    </script>
@endpush
