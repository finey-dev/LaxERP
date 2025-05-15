@extends('layouts.main')

@section('page-title')
    {{ __('Courier Details') }}
@endsection

@section('page-breadcrumb')
    {{ __('Courier Details') }},
    {{ __($courierDetails->tracking_id) }}
@endsection
@push('css')
    <link rel="stylesheet" href="{{ asset('packages/workdo/CourierManagement/src/Resources/assets/css/style.css') }}">
@endpush
<style>
    .card-body .form-label {
        font-weight: bold !important;
    }
</style>

@php
    $company_settings = getCompanyAllSetting();
@endphp

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="">
                <h3 class="my-3">{{ __('Tracking Details') }}</h3>
                <div class="">
                    <div class="row mt-5">

                        <div class="col-md-6 d-flex">
                            <div class="card em-card d-flex w-100">
                                <div class="card-header">
                                    <h6 class="mb-0">{{ __('Sender Details') }}</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row p-3">
                                        <div class="col-12">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label class="form-label" for="tracking_id"
                                                        class="form-label">{{ __('Tracking Id: ') }}</label><br>
                                                </div>
                                                <div class="col-md-6">
                                                    {{ $courierDetails->tracking_id }}
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label class="form-label" for="sender_name"
                                                        class="form-label">{{ __('Sender Name : ') }}</label><br>
                                                </div>
                                                <div class="col-md-6">
                                                    {{ $courierDetails->sender_name }}
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label class="form-label" for="sender_mobileno"
                                                        class="form-label">{{ __('Sender Mobile No : ') }}</label><br>
                                                </div>
                                                <div class="col-md-6">
                                                    {{ $courierDetails->sender_mobileno }}

                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label class="form-label" for="sender_email"
                                                        class="form-label">{{ __('Sender Email Address : ') }}</label><br>
                                                </div>
                                                <div class="col-md-6">
                                                    {{ $courierDetails->sender_email }}
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label class="form-label" for="sender_email"
                                                        class="form-label">{{ __('Courier Request Status : ') }}</label><br>
                                                </div>
                                                <div class="col-md-6">
                                                    <span
                                                        class="badge fix_badge
                                            @if ($courierDetails->is_approve === null) bg-warning   @elseif ($courierDetails->is_approve == 0)  bg-danger  @elseif ($courierDetails->is_approve == 1) bg-success @endif p-2 px-3">
                                                        @if ($courierDetails->is_approve === null)
                                                            {{ __('Pending') }}
                                                        @elseif ($courierDetails->is_approve == 0)
                                                            {{ __('Rejected') }}
                                                        @elseif ($courierDetails->is_approve == 1)
                                                            {{ __('Accepted') }}
                                                        @endif
                                                    </span>
                                                </div>
                                            </div>

                                            @if (!empty($customFields) && count($courierDetails->customField) > 0)
                                                @foreach ($customFields as $field)
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <label class="form-label">{{ $field->name }} :</label>
                                                            <div class="col-md-6">
                                                                @if ($field->type == 'attachment' && !empty($courierDetails->customField[$field->id]))
                                                                    <a href="{{ get_file($courierDetails->customField[$field->id]) }}"
                                                                        target="_blank">
                                                                        <img src="{{ get_file($courierDetails->customField[$field->id]) }}"
                                                                            class="wid-75 rounded me-3">
                                                                    </a>
                                                                @else
                                                                    {{ !empty($courierDetails->customField[$field->id]) ? $courierDetails->customField[$field->id] : '-' }}
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endif

                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 d-flex">
                            <div class="card em-card d-flex w-100">
                                <div class="card-header">
                                    <h6 class="mb-0">{{ __('Receiver Details') }}</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row p-3">
                                        <div class="col-12">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label class="form-label" for="receiver_name"
                                                        class="form-label">{{ __('Receiver Name: ') }}</label><br>
                                                </div>
                                                <div class="col-md-6">
                                                    {{ $courierDetails->receiver_name }}
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label class="form-label" for="receiver_mobile"
                                                        class="form-label">{{ __('Receiver Mobile No : ') }}</label><br>
                                                </div>
                                                <div class="col-md-6">
                                                    {{ $courierDetails->receiver_mobileno }}
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label class="form-label" for="delivery_address"
                                                        class="form-label">{{ __('Delivery Address : ') }}</label><br>
                                                </div>
                                                <div class="col-md-6" style="max-height: 100px; overflow-y: auto;">
                                                    {{ $courierDetails->delivery_address }}

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 ">
                            <div class="card em-card">
                                <div class="card-header">
                                    <h6 class="mb-0">{{ __('Package Details') }}</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row p-3">
                                        <div class="col-12">
                                            <div class="row package-detail-info">
                                                <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                                                    <div class="row">
                                                        <div class="col-md-6 ">
                                                            <label class="form-label" for="package_title"
                                                                class="form-label">{{ __('Package Title: ') }}</label><br>
                                                        </div>
                                                        <div class="col-md-6">
                                                            {{ $courierDetails->package_title }}
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <label class="form-label" for="height"
                                                                class="form-label">{{ __('Height : ') }}</label><br>
                                                        </div>
                                                        <div class="col-md-6">
                                                            {{ $courierDetails->height }}

                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <label class="form-label" for="width"
                                                                class="form-label">{{ __('Width : ') }}</label><br>
                                                        </div>
                                                        <div class="col-md-6">
                                                            {{ $courierDetails->width }}
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <label class="form-label" for="weight"
                                                                class="form-label">{{ __('Weight : ') }}</label><br>
                                                        </div>
                                                        <div class="col-md-6">
                                                            {{ $courierDetails->weight }}
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <label class="form-label" for="package_category"
                                                                class="form-label">{{ __('Package Category : ') }}</label><br>
                                                        </div>
                                                        <div class="col-md-6">
                                                            {{ $courierDetails->packageInformarmation->courier_category->category }}
                                                        </div>
                                                    </div>

                                                </div>

                                                <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <label class="form-label" for="tracking_status"
                                                                class="form-label">{{ __('Tracking Status : ') }}</label><br>
                                                        </div>
                                                        <div class="col-md-6">
                                                            {{ isset($courierDetails->packageInformarmation->getTrackingStatus->status_name) ? $courierDetails->packageInformarmation->getTrackingStatus->status_name : 'Pending' }}
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <label class="form-label" for="price"
                                                                class="form-label">{{ __('Price : ') }}</label><br>
                                                        </div>
                                                        <div class="col-md-6">
                                                            {{ $courierDetails->price }}

                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <label class="form-label" for="delivery_date"
                                                                class="form-label">{{ __('Expected Delivery Date : ') }}</label><br>
                                                        </div>
                                                        <div class="col-md-6">
                                                            {{ $courierDetails->expected_delivery_date }}
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <label class="form-label" for="service_type"
                                                                class="form-label">{{ __('Service Type : ') }}</label><br>
                                                        </div>
                                                        <div class="col-md-6">
                                                            {{ $courierDetails->getServiceType->service_type }}
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <label class="form-label" for="source"
                                                                class="form-label">{{ __('Source : ') }}</label><br>
                                                        </div>
                                                        <div class="col-md-6">
                                                            {{ $courierDetails->getSourceBranch->branch_name }}
                                                        </div>
                                                    </div>
                                                </div>


                                                <div class="col-lg-4 col-md-6 col-sm-6 col-12">

                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <label class="form-label" for="destination"
                                                                class="form-label">{{ __('Destination : ') }}</label><br>
                                                        </div>
                                                        <div class="col-md-6">
                                                            {{ $courierDetails->getDestinationBranch->branch_name }}
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <label class="form-label" for="payment_type"
                                                                class="form-label">{{ __('Payment Type : ') }}</label><br>
                                                        </div>
                                                        <div class="col-md-6">
                                                            {{ isset($courierDetails->payment_type) ? $courierDetails->payment_type : '----' }}
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <label class="form-label" for="payment_status"
                                                                class="form-label">{{ __('Payment Status : ') }}</label><br>
                                                        </div>
                                                        <div class="col-md-6">
                                                            {{ $courierDetails->payment_status }}
                                                        </div>
                                                    </div>

                                                </div>


                                                <div class="col-md-12">
                                                    <label class="form-label" for="package_description"
                                                        class="form-label">{{ __('Package Description : ') }}</label><br>
                                                </div>
                                                <div class="col-md-12" style="max-height: 100px; overflow-y: auto;">
                                                    {{ $courierDetails->package_description }}
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if ($courierDetails->is_courier_delivered == 0)
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card em-card">
                                    <div class="card-header">
                                        <h6 class="mb-0">{{ __('Manage Package Tracking') }}</h6>
                                    </div>
                                    <div class="card-body">
                                        <form action="{{ route('update.trackingstatus', ['trackingId' => encrypt($courierDetails->tracking_id)]) }}" method="POST">
                                            @csrf
                                            <div class="d-flex align-items-baseline">
                                                <div class="form-group col-md-4">
                                                    <select class="form-select" aria-label="Default select example"
                                                        required name="tracking_status">
                                                        <option selected disabled>Select Tracking Status</option>
                                                        @foreach ($trackingStatus as $status)
                                                            <option value="{{ $status['id'] }}"
                                                                {{ $courierDetails->tracking_status == $status['id'] ? 'selected' : '' }}>
                                                                {{ $status['status_name'] }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <button type="submit" id="submit"
                                                    class="btn  btn-primary mx-3">{{ 'Update' }}</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif


                    <div class="row">
                        <div class="col-md-12   ">
                            <div class="card em-card">
                                <div class="card-header">
                                    <h6 class="mb-0">{{ __('Package Tracking') }}</h6>
                                </div>
                                <div class="card-body">
                                    <ul class="packege-progress">
                                        @foreach ($allTrackingStatus as $trackingstatus)
                                            <li
                                                @foreach ($currentTrackingStatus as $currentStatus)
                                            @if ($trackingstatus->id == $currentStatus->tracking_status_id)
                                                class="active"
                                            @endif @endforeach>
                                                <div class="icon-div">
                                                    <span class="icon-bg">
                                                        <i class="{{ $trackingstatus->icon_name }}"></i>
                                                    </span>
                                                </div>
                                                <div class="progress-text">
                                                    <p class="m-0"><strong>{{ $trackingstatus->status_name }}</strong>
                                                    </p>
                                                    @foreach ($currentTrackingStatus as $currentStatus)
                                                        @if ($trackingstatus->id == $currentStatus->tracking_status_id)
                                                            <p class="m-0">
                                                                <strong>{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $currentStatus->date)->format('d/m/Y h:i:s A') }}</strong>
                                                            </p>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get the select element
            var selectElement = document.querySelector('select[name="tracking_status"]');


            for (var i = 1; i < selectElement.options.length; i++) {
                if (i != 1) {
                    selectElement.options[i].style.display = 'none';

                }
            }
        });
    </script>
@endpush
