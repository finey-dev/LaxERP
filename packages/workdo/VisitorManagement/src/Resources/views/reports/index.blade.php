@extends('layouts.main')

@section('page-title')
    {{ __('Manage Visitors Report') }}
@endsection
@section('page-breadcrumb')
    {{ __('Visitors Report') }}
@endsection
@section('page-action')
    <div class="d-flex">
        @stack('addButtonHook')
    </div>
@endsection
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="row">
                        <div class="mt-2" id="multiCollapseExample1">
                            <div class="card">
                                <div class="card-body">
                                    <form method="GET" action="http://localhost/product/dash-v2/invoice"
                                        accept-charset="UTF-8" id="customer_submit">
                                        <div class="row d-flex align-items-center justify-content-end">
                                            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 mr-2">
                                                <div class="btn-box">
                                                    {{ Form::label('start_date', __('Start Date'), ['class' => 'form-label']) }}
                                                    {{ Form::date('start_date', null, ['class' => 'form-control month-btn']) }}
                                                </div>
                                            </div>
                                            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 mr-2">
                                                <div class="btn-box">
                                                    {{ Form::label('end_date', __('End Date'), ['class' => 'form-label']) }}
                                                    {{ Form::date('end_date', null, ['class' => 'form-control month-btn']) }}
                                                </div>
                                            </div>
                                            <div class="col-auto float-end d-flex mt-4">
                                                <a href="#" class="btn btn-sm me-2 btn-primary" data-toggle="tooltip"
                                                    title="{{ __('Apply') }}" id="apply">
                                                    <span class="btn-inner--icon"><i class="ti ti-search"></i></span>
                                                </a>
                                                <a href="{{ route('visitor.reports.index') }}" class="btn btn-sm btn-danger"
                                                    data-toggle="tooltip" title="{{ __('Reset') }}">
                                                    <span class="btn-inner--icon"><i
                                                            class="ti ti-trash-off text-white-off"></i></span>
                                                </a>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="row">
                                <div class="col-xxl-8">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5>{{ __('Visitors') }}</h5>
                                        </div>
                                        <div class="card-body">
                                            <div id="visitorChart"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xxl-4">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5>{{ __('Purpose') }}</h5>
                                        </div>
                                        <div class="card-body" style="min-height: 365px;">
                                            <div class="tab-content" id="analyticsTabContent">
                                                <div class="tab-pane fade show active" id="home3" role="tabpanel"
                                                    aria-labelledby="home-tab3">
                                                    <div id="Safari"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
        let chartInstance = null;

        function renderLineChart(data, label) {

            var options = {
                chart: {
                    height: 300,
                    type: 'bar',
                    toolbar: {
                        show: false,
                    },
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    width: 2,
                    curve: 'smooth'
                },
                series: [{
                    name: "{{ __('Visitors') }}",
                    data: data
                }],
                xaxis: {
                    categories: label,
                },
                colors: ['#3ec9d6', '#FF3A6E'],
                fill: {
                    type: 'solid',
                },
                grid: {
                    strokeDashArray: 4,
                },
                legend: {
                    show: true,
                    position: 'top',
                    horizontalAlign: 'right',
                },
                markers: {
                    size: 4,
                    colors: ['#3ec9d6', '#FF3A6E', ],
                    opacity: 0.9,
                    strokeWidth: 2,
                    hover: {
                        size: 7,
                    }
                }
            };
            if (chartInstance) {
                chartInstance.updateOptions({
                    xaxis: {
                        categories: label
                    }
                });
                chartInstance.updateSeries([{
                    data: data
                }]);
            } else {
                chartInstance = new ApexCharts(document.querySelector("#visitorChart"), options);
                chartInstance.render();
            }
        }
        let checkPieInstance = null;

        function renderPieChart(data, label) {

            var options = {
                series: data,
                chart: {
                    width: 450,
                    type: 'pie',
                },
                colors: ["#6FD943", "#316849", "#1A3C4E", "#EBF7E7", " #EBEDEF"],
                labels: label,
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 300
                        },
                        legend: {
                            position: 'bottom',
                        }
                    }
                }]
            };

            if (checkPieInstance !== null && typeof checkPieInstance !== 'undefined') {
                checkPieInstance.updateOptions({
                    series: data,
                    labels: label
                });
            } else {
                checkPieInstance = new ApexCharts(document.querySelector("#Safari"), options);
                checkPieInstance.render();
            }
        }

        (function() {
            renderLineChart({!! json_encode($dataForChart['visitorCounts']) !!}, {!! json_encode($dataForChart['dates']) !!});
            renderPieChart({!! json_encode(array_values($visitReasons)) !!}, {!! json_encode(array_keys($visitReasons)) !!});
        })();
    </script>
    <script>
        $(document).on('click', '#apply', function() {
            var start_date = $('input[name=start_date]').val();
            var end_date = $('input[name=end_date]').val();
            var url = '{{ route('visitor.get.by.date') }}';
            if (start_date && end_date) {
                if (end_date > start_date) {

                    $.ajax({
                        type: 'POST',
                        url: url,
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'start_date': start_date,
                            'end_date': end_date
                        },
                        beforeSend: function() {
                            $(".loader-wrapper").removeClass('d-none');
                        },
                        success: function(response) {
                            $('.loader-wrapper').addClass('d-none');
                            var lineChartData = response.dataForChart;
                            var pieChartData = response.visitReasons;

                            renderLineChart(lineChartData.visitorCounts, lineChartData.dates);
                            renderPieChart(Object.values(pieChartData), Object.keys(pieChartData));
                        }
                    });
                } else {
                    toastrs('Error', __('End Date Must Be Greater than Start Date'), 'error');
                }
            } else {
                toastrs('Error', __('Please Select Start date and End date'), 'error');
            }
        });
    </script>
@endpush
