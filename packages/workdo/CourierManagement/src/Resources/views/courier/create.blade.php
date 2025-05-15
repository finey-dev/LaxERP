@extends('layouts.main')

@section('page-title')
    {{ __('Create Courier') }}
@endsection

@section('page-breadcrumb')
    {{ __('Courier') }},
    {{ __('Create') }}
@endsection
<style>
    .max-with-120 {
        max-width: 120px;
    }

    .em-card {
        min-height: 200px !important;
    }
</style>

@php
    $company_settings = getCompanyAllSetting();
@endphp

@section('content')
    <div class="row">
        <div class="">
            <div class="">
                {{ Form::open(['route' => ['courier.store'], 'method' => 'post', 'enctype' => 'multipart/form-data','class'=>'needs-validation','novalidate']) }}
                <div class="row">
                    <div class="col-md-12">
                        <div class="card em-card">
                            <div class="card-header">
                                <h6 class="mb-0">{{ __('Personal Detail') }}</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="form-group col-md-4">
                                        {!! Form::label('Sender Name', __('Sender Name'), ['class' => 'form-label']) !!}<x-required></x-required>
                                        {!! Form::text('sender_name', old('sender_name'), [
                                            'class' => 'form-control',
                                            'required' => 'required',
                                            'placeholder' => __('Enter Sender Name'),
                                        ]) !!}
                                    </div>
                                    <x-mobile divClass="form-group col-md-4" type="number" name="sender_mobileno" class="form-control" label="{{ __('Sender Mobile Number') }}" placeholder="{{ __('Enter Sender Mobile Number') }}" id="mobileField" required></x-mobile>
                                    <div class="form-group col-md-4">
                                        {!! Form::label('Sender Email Address', __('Sender Email Address'), ['class' => 'form-label']) !!}<x-required></x-required>
                                        {!! Form::email('sender_email_address', old('sender_email_address'), [
                                            'class' => 'form-control',
                                            'required' => 'required',
                                            'placeholder' => __('Enter Sender Email Address'),
                                        ]) !!}
                                        <div class=" text-xs text-danger mt-1">
                                            {{ __('Using this email you can track your courier') }}
                                        </div>
                                    </div>
                                    @if (module_is_active('CustomField') && !$customFields->isEmpty())
                                        <div class="form-group col-md-4">
                                            <div class="tab-pane fade show" id="tab-2" role="tabpanel">
                                                @include('custom-field::formBuilder')
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="card em-card">
                            <div class="card-header">
                                <h6 class="mb-0">{{ __('Delivery Details') }}</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="form-group col-md-4">
                                        {!! Form::label('Receiver Name', __('Receiver Name'), ['class' => 'form-label']) !!}<x-required></x-required>
                                        {!! Form::text('receiver_name', old('receiver_name'), [
                                            'class' => 'form-control',
                                            'required' => 'required',
                                            'placeholder' => __('Enter Receiver Name'),
                                        ]) !!}
                                    </div>

                                    <x-mobile divClass="form-group col-md-4" type="number" name="receiver_mobileno" class="form-control" label="{{ __('Receiver Mobile Number') }}" placeholder="{{ __('Enter Receiver Mobile Number') }}" id="mobileField" required></x-mobile>

                                    <div class="form-group col-md-4">
                                        {{ Form::label('branch_id', __('Service Type'), ['class' => 'form-label']) }}<x-required></x-required>
                                        <select class="form-select" name="service_type" required>
                                            <option selected disabled>Select Service Type</option>
                                            @foreach ($serviceType as $type)
                                                <option value="{{ $type['id'] }}">{{ $type['service_type'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>


                                    <div class="form-group col-md-6">
                                        {{ Form::label('from_branch', __('Source Branch'), ['class' => 'form-label']) }}<x-required></x-required>
                                        <select class="form-select" aria-label="Default select example" required
                                            name="source_branch" id="source_branch">
                                            <option selected disabled>Select Source</option>
                                            @foreach ($courierBranch as $branch)
                                                <option value="{{ $branch['id'] }}">
                                                    {{ $branch['branch_name'] . ' , ' . $branch['city'] . ' , ' . $branch['state'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group col-md-6">
                                        {{ Form::label('to_branch', __('Destination Branch'), ['class' => 'form-label']) }}<x-required></x-required>
                                        <select class="form-select" aria-label="Default select example" required
                                            name="destination_branch" id="destination_branch">
                                            <option selected disabled>Select Destination</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('Receiver Address', __('Receiver Address'), ['class' => 'form-label']) !!}<x-required></x-required>
                                    {!! Form::textarea('receiver_address', old('receiver_address'), [
                                        'class' => 'form-control',
                                        'rows' => 3,
                                        'placeholder' => __('Enter Receiver Address'),
                                        'required' => 'required',
                                    ]) !!}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="card em-card">
                            <div class="card-header">
                                <h6 class="mb-0">{{ __('Package Information') }}</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="form-group col-md-4">
                                        {!! Form::label('Package Title', __('Package Title'), ['class' => 'form-label']) !!}<x-required></x-required>
                                        {!! Form::text('package_title', old('package_title'), [
                                            'class' => 'form-control',
                                            'required' => 'required',
                                            'placeholder' => __('Enter Package Title'),
                                        ]) !!}
                                    </div>

                                    <div class="form-group col-md-4">
                                        {{ Form::label('package_category', __('Select Package Category'), ['class' => 'form-label']) }}<x-required></x-required>
                                        <select class="form-select" aria-label="Default select example" required
                                            name="package_category">
                                            <option selected disabled>Select Package Category</option>
                                            @foreach ($packageCategory as $category)
                                                <option value="{{ $category['id'] }}">
                                                    {{ $category['category'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group col-md-4">
                                        {!! Form::label('weight', __('Weight'), ['class' => 'form-label']) !!}<x-required></x-required>
                                        {!! Form::text('weight', old('weight'), [
                                            'class' => 'form-control',
                                            'required' => 'required',
                                            'placeholder' => __('Enter Package Weight'),
                                        ]) !!}
                                    </div>

                                    <div class="form-group col-md-4">
                                        {!! Form::label('height', __('Height'), ['class' => 'form-label']) !!}<x-required></x-required>
                                        {!! Form::text('height', old('height'), [
                                            'class' => 'form-control',
                                            'required' => 'required',
                                            'placeholder' => __('Enter Package Height'),
                                        ]) !!}
                                    </div>

                                    <div class="form-group col-md-4">
                                        {!! Form::label('width', __('Width'), ['class' => 'form-label']) !!}<x-required></x-required>
                                        {!! Form::text('width', old('width'), [
                                            'class' => 'form-control',
                                            'required' => 'required',
                                            'placeholder' => __('Enter Package Width'),
                                        ]) !!}
                                    </div>
                                    <div class="form-group col-md-4">
                                        {!! Form::label('price', __('Price'), ['class' => 'form-label']) !!}<x-required></x-required>
                                        {!! Form::text('price', old('price'), [
                                            'class' => 'form-control',
                                            'placeholder' => __('Enter Price'),
                                            'required' => 'required',
                                        ]) !!}
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            {!! Form::label('delivery_date', __('Expected Delivery Date'), ['class' => 'form-label']) !!}<x-required></x-required>
                                            {{ Form::date('delivery_date', date('Y-m-d'), ['class' => 'form-control ', 'required' => 'required', 'autocomplete' => 'off', 'placeholder' => __('Select Expected Delivery Date')]) }}
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        {!! Form::label('package_description', __('Package Description'), ['class' => 'form-label']) !!}<x-required></x-required>
                                        {!! Form::textarea('package_description', old('package_description'), [
                                            'class' => 'form-control',
                                            'rows' => 3,
                                            'placeholder' => __('Enter Package Description'),
                                            'required' => 'required',
                                        ]) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex float-end justify-content-between align-items-center">
                <div class="float-end">
                    <a href="{{route('courier')}}">
                        <button type="button" class="btn btn-secondary me-2">{{ __('Cancel') }}</button>
                    </a>
                    <button type="submit" id="submit" class="btn btn-primary">{{ __('Create') }}</button>
                </div>
            </div>


            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#source_branch').change(function() {
                var branchId = $('#source_branch').val();
                $.ajax({
                    url: "{{ route('courier.get.branch') }}",
                    type: 'POST',
                    data: {
                        branchId: branchId
                    },
                    success: function(response) {
                        var branch = response;
                        $('#destination_branch').empty();
                        $('#destination_branch').html(
                            '<option selected disabled>Select Destination</option>');
                        for (var i = 0; i < branch.length; i++) {
                            var optionText = branch[i].branch_name + ', ' + branch[i].city +
                                ', ' + branch[i].state;
                            $('#destination_branch').append('<option value="' + branch[i].id +
                                '">' + optionText + '</option>');
                        }

                    },
                });
            });
        });

        // show only current & next date Disbale Before dates
        var today = new Date().toISOString().split('T')[0];
        document.getElementById('delivery_date').min = today;
    </script>
@endpush
