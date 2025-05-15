@extends('layouts.main')
@section('page-title')
    {{ __('Report') }}
@endsection
@section('title')
    {{ __('Sales Order Analytic') }}
@endsection
@section('page-breadcrumb')
    {{ __('Sales Order Analytic') }}
@endsection
@section('action-btn')
@endsection
@push('css')
    <link rel="stylesheet" href="{{ asset('packages/workdo/Sales/src/Resources/assets/css/custom.css') }}">
@endpush
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="collapse show float-end" id="collapseExample" style="">

                        {{ Form::open(['route' => ['report.salesorderanalytic'], 'method' => 'get']) }}
                        <div class="row filter-css">
                            <div class="col-auto">
                                {{ Form::month('start_month', isset($_GET['start_month']) ? $_GET['start_month'] : date('Y-01'), ['class' => 'form-control']) }}
                            </div>
                            <div class="col-auto">
                                {{ Form::month('end_month', isset($_GET['end_month']) ? $_GET['end_month'] : date('Y-12'), ['class' => 'form-control']) }}
                            </div>
                            <div class="col-auto" style="margin-left: -29px;">
                                {{ Form::select('status', ['' => 'Select Status'] + $status, isset($_GET['status']) ? $_GET['status'] : '', [
                                    'class' => 'form-control ',
                                    'style' => 'margin-left: 29px;',
                                ]) }}
                            </div>
                            <div class="col-auto float-end ms-3 mt-1">
                                <button type="submit" class="btn btn-sm btn-primary me-1" data-bs-toggle="tooltip"
                                    data-title="{{ __('Apply') }}" title="{{ __('Apply') }}"><i
                                        class="ti ti-search"></i></button>

                                <a href="{{ route('report.salesorderanalytic') }}" data-bs-toggle="tooltip"
                                    data-title="{{ __('Reset') }}" title="{{ __('Reset') }}"
                                    class="btn btn-sm btn-danger me-1"><i class="ti ti-trash-off"></i>
                                </a>
                                <a href="#" onclick="saveAsPDF();" class="btn btn-sm btn-primary"
                                    data-bs-toggle="tooltip" data-title="{{ __('Download') }}"
                                    title="{{ __('Download') }}" id="download-buttons">
                                    <i class="ti ti-download"></i>
                                </a>
                            </div>
                            {{ Form::close() }}

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="printableArea">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <dl class="row mb-0">
                                @if (isset($report['startDateRange']) || isset($report['endDateRange']))
                                    <input type="hidden"
                                        value="{{ __('Salesorder Report of') . ' ' . $report['startDateRange'] . ' to ' . $report['endDateRange'] }}"
                                        id="filesname">
                                @else
                                    <input type="hidden" value="{{ __('Salesorder Report') }}" id="filesname">
                                @endif

                                <div class="col">
                                    {{ __('Report') }} : <h6 class="mb-0 mt-1">{{ __('Salesorder Summary') }}</h6>
                                </div>
                                <div class="col">
                                    {{ __('Duration') }} : <h6 class="mb-0 mt-1">
                                        {{ $report['startDateRange'] . ' to ' . $report['endDateRange'] }}</h6>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div id="report-chart"></div>
                    </div>
                </div>
            </div>
        </div>
        {{-- <div class="row"> --}}
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-body table-border-style">
                        <div>
                        </div>
                        <div class="table-responsive mt-3">
                            <table class="table mb-0 pc-dt-simple" id="assets">
                                <thead>
                                    <tr>
                                        <th scope="col" class="sort" data-sort="name">{{ __('Name') }}</th>
                                        <th scope="col" class="sort" data-sort="budget">{{ __('Quote Name') }}</th>
                                        <th scope="col" class="sort" data-sort="name">{{ __('Account') }}</th>
                                        <th scope="col" class="sort" data-sort="budget">{{ __('Status') }}</th>
                                        <th scope="col" class="sort" data-sort="name">{{ __('Date Sales Order') }}
                                        </th>
                                    </tr>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($salesorder as $result)
                                        <tr>
                                            <td>
                                                {{ $result['name'] }}
                                            </td>
                                            <td>
                                                {{ !empty($result['quotes']['name']) ? $result['quotes']['name'] : '-' }}
                                            </td>
                                            <td class="">
                                                {{ !empty($result['accounts']['name']) ? $result['accounts']['name'] : '-' }}
                                            </td>
                                            <td>
                                                @if ($result->status == 0)
                                                    <span
                                                        class="badge bg-secondary p-2 px-3 status-btn">{{ __(Workdo\Sales\Entities\SalesOrder::$status[$result->status]) }}</span>
                                                @elseif($result->status == 1)
                                                    <span
                                                        class="badge bg-info p-2 px-3 status-btn">{{ __(Workdo\Sales\Entities\SalesOrder::$status[$result->status]) }}</span>
                                                @endif
                                            </td>
                                            <td class="">
                                                {{ company_date_formate($result['date_quoted']) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        {{-- </div> --}}
    @endsection

    @push('scripts')
        <script type="text/javascript" src="{{ asset('js/html2pdf.bundle.min.js') }}"></script>
        <script src="{{ asset('assets/js/plugins/apexcharts.min.js') }}"></script>

        <script>
            var filename = $('#filesname').val();

            function saveAsPDF() {
                var element = document.getElementById('printableArea');
                var opt = {
                    margin: 0.3,
                    filename: filename,
                    image: {
                        type: 'jpeg',
                        quality: 1
                    },
                    html2canvas: {
                        scale: 4,
                        dpi: 72,
                        letterRendering: true
                    },
                    jsPDF: {
                        unit: 'in',
                        format: 'A2'
                    }
                };
                html2pdf().set(opt).from(element).save();
            }
        </script>

        <script>
            var WorkedHoursChart = (function() {
                var $chart = $('#report-chart');

                (function() {
                    var options = {
                        chart: {
                            height: 180,
                            type: 'area',
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
                            name: 'Salesorder',
                            data: {!! json_encode($salesorderTotal) !!},
                        }],
                        xaxis: {
                            categories: {!! json_encode($monthList) !!},
                            title: {
                                text: '{{ __('Salesorder') }}'
                            },
                        },
                        colors: ['#3ec9d6', '#FF3A6E'],

                        grid: {
                            strokeDashArray: 4,
                        },
                        legend: {
                            show: true,
                            position: 'top',
                            horizontalAlign: 'right',
                        },

                    };
                    var chart = new ApexCharts(document.querySelector("#report-chart"), options);
                    chart.render();
                })();




                // Events
                if ($chart.length) {
                    $chart.each(function() {
                        init($(this));
                    });
                }
            })();
        </script>
    @endpush
