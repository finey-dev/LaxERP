@extends('layouts.main')
@section('page-title')
    {{ __('Manage Payments') }}
@endsection
@section('page-breadcrumb')
    {{ __('Manage Payments') }}
@endsection

@section('page-action')
    <div class="d-flex">
        @stack('addButtonHook')
    </div>
@endsection

@push('css')
    @include('layouts.includes.datatable-css')
@endpush

@section('content')
    <div class="col-sm-12">
        <div class=" multi-collapse mt-2 " id="multiCollapseExample1">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('payment.info') }}" method="get" id="payment_form">
                        <div class="row align-items-center justify-content-end">
                            <div class="col-xl-10">
                                <div class="row align-items-center justify-content-end">
                                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                        <div class="btn-box">
                                            <label for="">{{ __('Select Date') }}</label>
                                            <input type="text" class="form-control flatpickr-to-input"
                                                placeholder="Select Date" name="date" id="date"
                                                value="{{ isset($_GET['date']) ? $_GET['date'] : null }}">
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                        <div class="btn-box">
                                            <label for="">{{ __('Select Tracking Status') }}</label>
                                            <select name="tracking_status" class="form-control" id="tracking_status">
                                                <option disabled selected>{{ __('Select Tracking Status') }}</option>

                                                @foreach ($trackingStatus as $status)
                                                    @php
                                                        $seachData = isset($_GET['tracking_status']) ? $_GET['tracking_status'] : null;
                                                    @endphp
                                                    <option value="{{ $status->id }}"
                                                        {{ $seachData == $status->id ? 'selected' : '' }}>
                                                        {{ $status->status_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto mt-4">
                                <div class="row">
                                    <div class="col-auto d-flex">
                                        <a class="btn btn-sm btn-primary me-2"
                                            data-toggle="tooltip" title="{{ __('Apply') }}"
                                            data-original-title="{{ __('apply') }}" id="applyfilter">
                                            <span class="btn-inner--icon"><i class="ti ti-search"></i></span>
                                        </a>
                                        <a href="#" class="btn btn-sm btn-danger" id="clearfilter"
                                            data-bs-toggle="tooltip" title="{{ __('Reset') }}">
                                            <span class="btn-inner--icon">
                                                <i class="ti ti-trash-off text-white-off "></i>
                                            </span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="card">
                <div class="card-header card-body table-border-style">
                    <div class="table-responsive">
                        {{ $dataTable->table(['width' => '100%']) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
@include('layouts.includes.datatable-js')
{{ $dataTable->scripts() }}
    <script>
        function copy_link(link) {
            var value = $(link).attr('data-link');
            var $temp = $("<input>");
            $("body").append($temp);
            $temp.val(value).select();
            document.execCommand("copy");
            $temp.remove();
            toastrs('Success', '{{ __('Link Copy on Clipboard') }}', 'success')
        }
    </script>
@endpush
