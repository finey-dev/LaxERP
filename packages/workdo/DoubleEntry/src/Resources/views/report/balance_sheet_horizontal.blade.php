@extends('layouts.main')
@section('page-title')
    {{ __('Manage Balance Sheet') }}
@endsection
@section('page-breadcrumb')
    {{__('Balance Sheet')}}
@endsection
@push('css')
    <link rel="stylesheet" href="{{ asset('packages/workdo/DoubleEntry/src/Resources/assets/css/app.css') }}">
@endpush

@push('scripts')
    <script type="text/javascript" src="{{ asset('js/html2pdf.bundle.min.js') }}"></script>
    <script>
        var filename = $('#filename').val();

        function saveAsPDF() {
            var printContents = document.getElementById('printableArea').innerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
        }
    </script>

    <script>
        $(document).ready(function () {
            $("#filter").click(function () {
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
@endpush


@section('page-action')
    <div class="d-flex">
        @stack('addButtonHook')
        <input type="hidden" name="start_date" class="start_date">
        <input type="hidden" name="end_date" class="end_date">
        <a href="#" onclick="saveAsPDF()" class="btn btn-sm btn-primary me-2" data-bs-toggle="tooltip"
           title="{{ __('Print') }}"
           data-original-title="{{ __('Print') }}"><i class="ti ti-printer"></i></a>

        <button id="filter" class="btn btn-sm btn-primary me-2" data-bs-toggle="tooltip"
                title="{{ __('Filter') }}"><i class="ti ti-filter"></i></button>
                
        <a href="{{ route('report.balance.sheet', 'vertical') }}" class="btn btn-sm btn-primary"
            data-bs-toggle="tooltip"
            title="{{ __('Vertical View') }}" data-original-title="{{ __('Vertical View') }}"><i
                class="ti ti-separator-horizontal"></i></a>
    </div>
@endsection

@section('content')
    <div class="mt-4">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="mt-2" id="multiCollapseExample1">
                    <div class="card" id="show_filter" style="display:none;">
                        <div class="card-body">
                            {{ Form::open(['route' => ['report.balance.sheet'], 'method' => 'GET', 'id' => 'report_balance_sheet']) }}
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
                                               onclick="document.getElementById('report_balance_sheet').submit(); return false;"
                                               data-bs-toggle="tooltip" title="{{ __('Apply') }}"
                                               data-original-title="{{ __('apply') }}">
                                                <span class="btn-inner--icon"><i class="ti ti-search"></i></span>
                                            </a>

                                            <a href="{{ route('report.balance.sheet' ,'horizontal')}}"
                                               class="btn btn-sm btn-danger "
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
        <div class="row justify-content-center" id="printableArea">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body {{ $collapseview == 'expand' ? 'collapse-view' : '' }}">
                        <div class="account-main-title mb-5">
                            <h5>{{ 'Balance Sheet of ' . $user->name . ' as of ' . $filter['startDateRange'] . ' to ' . $filter['endDateRange'] }}
                                </h4>
                        </div>

                        @php
                            $totalAmount = 0;
                        @endphp

                        <div class="row">
                            <div class="col-md-6">
                                <div class="account-title d-flex align-items-center justify-content-between border py-2">
                                    <h5 class="mb-0 ms-3">{{ __('Liabilities & Equity') }}</h5>
                                </div>
                                <div class="border-start border-end">
                                    @foreach ($totalAccounts as $type => $accounts)
                                        @if ($accounts != [] && $type != 'Assets')
                                            <div class="account-main-inner py-2">
                                                <p class="fw-bold ps-2 mb-2">{{ $type }}</p>
                                                @php
                                                    $total = 0;
                                                @endphp
                                                @foreach ($accounts as $account)
                                                    <div class="border-bottom py-2">
                                                        <p class="fw-bold ps-4 mb-2">
                                                            {{ $account['subType'] == true ? $account['subType'] : '' }}
                                                        </p>
                                                        @foreach ($account['account'] as $records)
                                                            @if ($collapseview == 'collapse')
                                                                @foreach ($records as $key => $record)
                                                                    @if ($record['account'] == 'parentTotal')
                                                                        <div
                                                                            class="account-inner d-flex align-items-center justify-content-between ps-5">
                                                                            <div class="mb-2 account-arrow">
                                                                                <div class="">
                                                                                    <a
                                                                                        href="{{ route('report.balance.sheet', ['horizontal', 'expand']) }}"><i
                                                                                            class="ti ti-chevron-down account-icon"></i></a>
                                                                                </div>
                                                                                <a href="{{ route('report.ledger', $record['account_id']) }}?account={{ $record['account_id'] }}"
                                                                                   class="text-primary">{{ str_replace('Total ', '', $record['account_name']) }}</a>
                                                                            </div>
                                                                            <p class="mb-2 ms-3 text-center">
                                                                                {{ $record['account_code'] }}</p>
                                                                            <p
                                                                                class="text-primary mb-2 float-end text-end me-3">
                                                                                {{ currency_format_with_sym($record['netAmount']) }}
                                                                            </p>
                                                                        </div>
                                                                    @endif
                                                                    @if (($key < count($account['account']) - 1 && $record['account'] == '') || $record['account'] != 'subAccount')
                                                                        @if (
                                                                            (!preg_match('/\btotal\b/i', $record['account_name']) && $record['account'] == '') ||
                                                                                $record['account'] == 'subAccount')
                                                                            <div
                                                                                class="account-inner d-flex align-items-center justify-content-between ps-5">
                                                                                <p class="mb-2 ms-3"><a
                                                                                        href="{{ route('report.ledger', $record['account_id']) }}?account={{ $record['account_id'] }}"
                                                                                        class="text-primary">{{ $record['account_name'] }}</a>
                                                                                </p>
                                                                                <p class="mb-2 text-center">
                                                                                    {{ $record['account_code'] }}</p>
                                                                                <p
                                                                                    class="text-primary mb-2 float-end text-end me-3">
                                                                                    {{ currency_format_with_sym($record['netAmount']) }}
                                                                                </p>
                                                                            </div>
                                                                        @endif
                                                                    @endif
                                                                @endforeach
                                                            @else
                                                                @foreach ($records as $key => $record)
                                                                    @if ($record['account'] == 'parent' || $record['account'] == 'parentTotal')
                                                                        <div
                                                                            class="account-inner d-flex align-items-center justify-content-between ps-5">
                                                                            @if ($record['account'] == 'parent')
                                                                                <div class="mb-2 account-arrow">
                                                                                    <div class="">
                                                                                        <a
                                                                                            href="{{ route('report.balance.sheet', ['horizontal', 'collapse']) }}"><i
                                                                                                class="ti ti-chevron-down account-icon"></i></a>
                                                                                    </div>
                                                                                    <a href="{{ route('report.ledger', $record['account_id']) }}?account={{ $record['account_id'] }}"
                                                                                       class="{{ $record['account'] == 'parent' ? 'text-primary' : 'text-dark' }} fw-bold">{{ $record['account_name'] }}</a>
                                                                                </div>
                                                                            @else
                                                                                <p class="mb-2"><a href="#"
                                                                                                   class="text-dark fw-bold">{{ $record['account_name'] }}</a>
                                                                                </p>
                                                                            @endif
                                                                            <p class="mb-2 ms-3 text-center">
                                                                                {{ $record['account_code'] }}</p>
                                                                            <p
                                                                                class="text-dark fw-bold mb-2 float-end text-end me-3">
                                                                                {{ currency_format_with_sym($record['netAmount']) }}
                                                                            </p>
                                                                        </div>
                                                                    @endif
                                                                    @if (($key < count($account['account']) - 1 && $record['account'] == '') || $record['account'] == 'subAccount')
                                                                        @if (
                                                                            (!preg_match('/\btotal\b/i', $record['account_name']) && $record['account'] == '') ||
                                                                                $record['account'] == 'subAccount')
                                                                            <div
                                                                                class="account-inner d-flex align-items-center justify-content-between ps-5">
                                                                                <p class="mb-2 ms-3"><a
                                                                                        href="{{ route('report.ledger', $record['account_id']) }}?account={{ $record['account_id'] }}"
                                                                                        class="text-primary">{{ $record['account_name'] }}</a>
                                                                                </p>
                                                                                <p class="mb-2 text-center">
                                                                                    {{ $record['account_code'] }}</p>
                                                                                <p
                                                                                    class="text-primary mb-2 float-end text-end me-3">
                                                                                    {{ currency_format_with_sym($record['netAmount']) }}
                                                                                </p>
                                                                            </div>
                                                                        @endif
                                                                    @endif
                                                                @endforeach
                                                            @endif
                                                        @endforeach
                                                        <div
                                                            class="account-inner d-flex align-items-center justify-content-between ps-4">
                                                            <p class="fw-bold mb-2">
                                                                {{ $record['account_name'] ? $record['account_name'] : '' }}
                                                            </p>
                                                            <p class="fw-bold mb-2 text-end me-3">
                                                                {{ $record['netAmount'] ? currency_format_with_sym($record['netAmount']) : currency_format_with_sym(0) }}
                                                            </p>
                                                        </div>
                                                    </div>

                                                    @php
                                                        $total += $record['netAmount'] ? $record['netAmount'] : 0;
                                                    @endphp
                                                @endforeach
                                                <div
                                                    class="account-title d-flex align-items-center justify-content-between border-top border-bottom py-2 px-2 pe-0">
                                                    <h6 class="fw-bold mb-0">{{ 'Total for ' . $type }}</h6>
                                                    <h6 class="fw-bold mb-0 text-end me-3">
                                                        {{ currency_format_with_sym($total) }}</h6>
                                                </div>
                                                @php
                                                    if ($type != 'Assets') {
                                                        $totalAmount += $total;
                                                    }
                                                @endphp
                                            </div>
                                        @endif
                                    @endforeach
                                    @if ($totalAmount != 0)
                                        <div
                                            class="d-flex align-items-center justify-content-between border-bottom py-2 px-0">
                                            <h6 class="fw-bold mb-0 ms-2">{{ 'Total for Liabilities & Equity' }}</h6>
                                            <h6 class="fw-bold mb-0 text-end me-3">
                                                {{ currency_format_with_sym($totalAmount) }}</h6>
                                        </div>
                                    @endif
                                </div>

                            </div>

                            @php
                                $total = 0;
                                $totalAmounts = 0;
                            @endphp

                            <div class="col-md-6">
                                <div class="account-title d-flex align-items-center justify-content-between border py-2">
                                    <h5 class="mb-0 ms-3">{{ __('Assets') }}</h5>
                                </div>
                                <div class="border-start border-end">
                                    @foreach ($totalAccounts as $type => $accounts)
                                        @if ($accounts != [] && $type == 'Assets')
                                            <div class="account-main-inner py-2">
                                                @if ($type == 'Liabilities')
                                                    <p class="fw-bold mb-3"> {{ __('Liabilities & Equity') }}</p>
                                                @endif
                                                <p class="fw-bold ps-2 mb-2">{{ $type }}</p>
                                                @foreach ($accounts as $account)
                                                    <div class="border-bottom py-2">
                                                        <p class="fw-bold ps-4 mb-2">
                                                            {{ $account['subType'] == true ? $account['subType'] : '' }}
                                                        </p>
                                                        @foreach ($account['account'] as $records)
                                                            @if ($collapseview == 'collapse')
                                                                @foreach ($records as $key => $record)
                                                                    @if ($record['account'] == 'parentTotal')
                                                                        <div
                                                                            class="account-inner d-flex align-items-center justify-content-between ps-5">
                                                                            <div class="mb-2 account-arrow">
                                                                                <div class="">
                                                                                    <a
                                                                                        href="{{ route('report.balance.sheet', ['horizontal', 'expand']) }}"><i
                                                                                            class="ti ti-chevron-down account-icon"></i></a>
                                                                                </div>
                                                                                <a href="{{ route('report.ledger', $record['account_id']) }}?account={{ $record['account_id'] }}"
                                                                                   class="text-primary">{{ str_replace('Total ', '', $record['account_name']) }}</a>
                                                                            </div>
                                                                            <p class="mb-2 ms-3 text-center">
                                                                                {{ $record['account_code'] }}</p>
                                                                            <p
                                                                                class="text-primary mb-2 float-end text-end me-3">
                                                                                {{ currency_format_with_sym($record['netAmount']) }}
                                                                            </p>
                                                                        </div>
                                                                    @endif
                                                                    @if (($key < count($account['account']) - 1 && $record['account'] == '') || $record['account'] != 'subAccount')
                                                                        @if (
                                                                            (!preg_match('/\btotal\b/i', $record['account_name']) && $record['account'] == '') ||
                                                                                $record['account'] == 'subAccount')
                                                                            <div
                                                                                class="account-inner d-flex align-items-center justify-content-between ps-5">
                                                                                <p class="mb-2 ms-3"><a
                                                                                        href="{{ route('report.ledger', $record['account_id']) }}?account={{ $record['account_id'] }}"
                                                                                        class="text-primary">{{ $record['account_name'] }}</a>
                                                                                </p>
                                                                                <p class="mb-2 text-center">
                                                                                    {{ $record['account_code'] }}</p>
                                                                                <p
                                                                                    class="text-primary mb-2 float-end text-end me-3">
                                                                                    {{ currency_format_with_sym($record['netAmount']) }}
                                                                                </p>
                                                                            </div>
                                                                        @endif
                                                                    @endif
                                                                @endforeach
                                                            @else
                                                                @foreach ($records as $key => $record)
                                                                    @if ($record['account'] == 'parent' || $record['account'] == 'parentTotal')
                                                                        <div
                                                                            class="account-inner d-flex align-items-center justify-content-between ps-5">
                                                                            @if ($record['account'] == 'parent')
                                                                                <div class="mb-2 account-arrow">
                                                                                    <div class="">
                                                                                        <a
                                                                                            href="{{ route('report.balance.sheet', ['horizontal', 'collapse']) }}"><i
                                                                                                class="ti ti-chevron-down account-icon"></i></a>
                                                                                    </div>
                                                                                    <a href="{{ route('report.ledger', $record['account_id']) }}?account={{ $record['account_id'] }}"
                                                                                       class="{{ $record['account'] == 'parent' ? 'text-primary' : 'text-dark' }} fw-bold">{{ $record['account_name'] }}</a>
                                                                                </div>
                                                                            @else
                                                                                <p class="mb-2"><a href="#"
                                                                                                   class="text-dark fw-bold">{{ $record['account_name'] }}</a>
                                                                                </p>
                                                                            @endif
                                                                            <p class="mb-2 ms-3 text-center">
                                                                                {{ $record['account_code'] }}</p>
                                                                            <p
                                                                                class="text-dark fw-bold mb-2 float-end text-end me-3">
                                                                                {{ currency_format_with_sym($record['netAmount']) }}
                                                                            </p>
                                                                        </div>
                                                                    @endif
                                                                    @if (($key < count($account['account']) - 1 && $record['account'] == '') || $record['account'] == 'subAccount')
                                                                        @if (
                                                                            (!preg_match('/\btotal\b/i', $record['account_name']) && $record['account'] == '') ||
                                                                                $record['account'] == 'subAccount')
                                                                            <div
                                                                                class="account-inner d-flex align-items-center justify-content-between ps-5">
                                                                                <p class="mb-2 ms-3"><a
                                                                                        href="{{ route('report.ledger', $record['account_id']) }}?account={{ $record['account_id'] }}"
                                                                                        class="text-primary">{{ $record['account_name'] }}</a>
                                                                                </p>
                                                                                <p class="mb-2 text-center">
                                                                                    {{ $record['account_code'] }}</p>
                                                                                <p
                                                                                    class="text-primary mb-2 float-end text-end me-3">
                                                                                    {{ currency_format_with_sym($record['netAmount']) }}
                                                                                </p>
                                                                            </div>
                                                                        @endif
                                                                    @endif
                                                                @endforeach
                                                            @endif
                                                        @endforeach

                                                        <div
                                                            class="account-inner d-flex align-items-center justify-content-between ps-4">
                                                            <p class="fw-bold mb-2">
                                                                {{ $record['account_name'] ? $record['account_name'] : 0 }}
                                                            </p>
                                                            <p class="fw-bold mb-2 text-end me-3">
                                                                {{ $record['netAmount'] ? currency_format_with_sym($record['netAmount']) : currency_format_with_sym(0) }}
                                                            </p>
                                                        </div>
                                                    </div>

                                                    @php
                                                        $total += $record['netAmount'] ? $record['netAmount'] : 0;
                                                    @endphp
                                                @endforeach
                                                @php
                                                    if ($type == 'Assets') {
                                                        $totalAmounts += $total;
                                                    }
                                                @endphp

                                            </div>
                                        @endif
                                    @endforeach
                                    @if ($totalAmounts != 0)
                                        <div
                                            class="d-flex align-items-center justify-content-between border-bottom py-2 px-0">
                                            <h6 class="fw-bold mb-0 ms-2">{{ 'Total for Assets' }}</h6>
                                            <h6 class="fw-bold mb-0 text-end me-3">
                                                {{ currency_format_with_sym($totalAmounts) }}</h6>
                                        </div>
                                    @endif
                                </div>

                            </div>
                        </div>


                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
