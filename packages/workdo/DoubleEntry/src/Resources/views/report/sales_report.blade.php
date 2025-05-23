@extends('layouts.main')
@section('page-title')
    {{ __('Manage Sales Report') }}
@endsection
@section('page-breadcrumb')
    {{__('Sales Report')}}
@endsection

@push('scripts')
    <script src="{{ asset('packages/workdo/DoubleEntry/src/Resources/assets/js/html2pdf.bundle.min.js') }}"></script>
    <script>
        var filename = $('#filename').val();
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
        $(document).ready(function() {
            $("#filter").click(function() {
                $("#show_filter").toggle();
            });
        });
    </script>
    <script>
        $(document).ready(function () {
            callback();
            function callback() {
                var start_date = $(".startDate").val();
                var end_date = $(".endDate").val();

                $('.start_date').val(start_date);
                $('.end_date').val(end_date);
            }
        });

    </script>
    <script>
        $(document).ready(function() {
            var id1 = $('.nav-item .active').attr('href');
            $('.report').val(id1);

            $("ul.nav-pills > li > a").click(function() {
                var report = $(this).attr('href');
                $('.report').val(report);
            });
        });

    </script>
@endpush
@push('css')
    <link rel="stylesheet" href="{{ asset('packages/workdo/DoubleEntry/src/Resources/assets/css/app.css') }}" id="main-style-link">
@endpush

@section('page-action')
    <div class="d-flex">
        {{ Form::open(['route' => ['sales.report.print'],'class' => 'custom-action-btn me-2']) }}
        <input type="hidden" name="start_date" class="start_date">
        <input type="hidden" name="end_date" class="end_date">
        <input type="hidden" name="report" class="report">
        <button type="submit" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="{{ __('Print') }}"
            data-original-title="{{ __('Print') }}"><i class="ti ti-printer"></i></button>
        {{ Form::close() }}

            <button id="filter" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="{{ __('Filter') }}"><i class="ti ti-filter"></i></button>
    </div>
@endsection

@section('content')
    <div class="mt-4">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="mt-2" id="multiCollapseExample1">
                    <div class="card" id="show_filter" style="display:none;">
                        <div class="card-body">
                            {{ Form::open(['route' => ['report.sales'], 'method' => 'GET', 'id' => 'report_bill_summary']) }}
                            <div class="row align-items-center justify-content-end">
                                <div class="col-xl-10">
                                    <div class="row">
                                        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                            <div class="btn-box">
                                            </div>
                                        </div>
                                        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                            <div class="btn-box">
                                            </div>
                                        </div>
                                        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                            <div class="btn-box">
                                                {{ Form::label('start_date', __('Start Date'), ['class' => 'form-label']) }}
                                                {{ Form::date('start_date', $filter['startDateRange'], ['class' => 'startDate form-control']) }}
                                            </div>
                                        </div>

                                        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                            <div class="btn-box">
                                                {{ Form::label('end_date', __('End Date'), ['class' => 'form-label']) }}
                                                {{ Form::date('end_date', $filter['endDateRange'], ['class' => 'endDate form-control']) }}
                                            </div>
                                        </div>
                                        <input type="hidden" name="view" value="horizontal">
                                    </div>
                                </div>
                                <div class="col-auto mt-4">
                                    <div class="row">
                                        <div class="col-auto">
                                            <a href="#" class="btn btn-sm btn-primary me-1"
                                                onclick="document.getElementById('report_bill_summary').submit(); return false;"
                                                data-bs-toggle="tooltip" title="{{ __('Apply') }}"
                                                data-original-title="{{ __('apply') }}">
                                                <span class="btn-inner--icon"><i class="ti ti-search"></i></span>
                                            </a>

                                            <a href="{{ route('report.sales') }}" class="btn btn-sm btn-danger "
                                                data-bs-toggle="tooltip" title="{{ __('Reset') }}"
                                                data-original-title="{{ __('Reset') }}">
                                                <span class="btn-inner--icon"><i
                                                        class="ti ti-trash-off text-white-off "></i></span>
                                            </a>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>

        @php
            $authUser = creatorId();
            $user = App\Models\User::find($authUser);
        @endphp

        @php
            $authUser = creatorId();
            $user = App\Models\User::find($authUser);
        @endphp

    </div>

    <div class="row">
        <div class="col-12" id="invoice-container">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between w-100">
                        <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="profile-tab3" data-bs-toggle="pill" href="#item" role="tab" aria-controls="pills-item" aria-selected="true">{{__('Sales by Item')}}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="contact-tab4" data-bs-toggle="pill" href="#customer" role="tab" aria-controls="pills-customer" aria-selected="false">{{__('Sales by Customer')}}</a>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="tab-content" id="myTabContent2">
                                <div class="tab-pane fade fade show active" id="item" role="tabpanel" aria-labelledby="profile-tab3">
                                    <table class="table table-flush" id="report-dataTable">
                                        <thead>
                                        <tr>
                                            <th width="33%"> {{__('Invoice Item')}}</th>
                                            <th width="33%"> {{__('Quantity Sold')}}</th>
                                            <th width="33%"> {{__('Amount')}}</th>
                                            <th class="text-end"> {{__('Average Price')}}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($invoiceItems as $invoiceItem)
                                                <tr>
                                                    <td>{{ $invoiceItem['name']}}</td>
                                                    <td>{{ $invoiceItem['quantity']}}</td>
                                                    <td>{{ currency_format_with_sym($invoiceItem['price']) }}</td>
                                                    <td>{{ currency_format_with_sym($invoiceItem['avg_price']) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <div class="tab-pane fade fade" id="customer" role="tabpanel" aria-labelledby="profile-tab3">
                                    <table class="table table-flush" id="report-dataTable">
                                        <thead>
                                        <tr>
                                            <th width="33%"> {{__('Customer Name')}}</th>
                                            <th width="33%"> {{__('Invoice Count')}}</th>
                                            <th width="33%"> {{__('Sales')}}</th>
                                            <th class="text-end"> {{__('Sales With Tax')}}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($invoiceCustomers as $invoiceCustomer)
                                                <tr>
                                                    <td>{{ $invoiceCustomer['name'] }}</td>
                                                    <td>{{ $invoiceCustomer['invoice_count']}}</td>
                                                    <td>{{ currency_format_with_sym($invoiceCustomer['price']) }}</td>
                                                    <td>{{ currency_format_with_sym($invoiceCustomer['price'] + $invoiceCustomer['total_tax']) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

@endsection
