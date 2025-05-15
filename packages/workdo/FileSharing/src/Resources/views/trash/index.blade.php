@extends('layouts.main')
@section('page-title')
    {{ __('Manage Trash Files') }}
@endsection
@section('page-breadcrumb')
    {{ __('Trash Files') }}
@endsection
@push('css')
    @if (isset($status) && $status == 1)
        @include('layouts.includes.datatable-css')
    @endif
@endpush
@section('page-action')
@endsection
@section('content')
    <div class="row">
        @if (isset($status) && $status == 1)
            <div class="mt-2" id="multiCollapseExample1">
                <div class="card">
                    <div class="card-body">
                        {{ Form::open(['route' => ['files-trash.index'], 'method' => 'GET', 'id' => 'trash-form']) }}
                        <div class="row row-gap align-items-center justify-content-end">
                            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                <div class="btn-box">
                                    {{ Form::label('delet_date', __('Date'), ['class' => 'text-type']) }}
                                    {{ Form::text('delet_date', isset($_GET['delet_date']) ? $_GET['delet_date'] : null, ['class' => 'form-control flatpickr-to-input', 'placeholder' => __('Select Date')]) }}
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                <div class="btn-box">
                                    {{ Form::label('file_status', __('Status'), ['class' => 'text-type']) }}
                                    {{ Form::select('file_status', ['' => 'Select status'] + $file_status, isset($_GET['file_status']) ? $_GET['file_status'] : '', ['class' => 'form-control']) }}
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                <div class="btn-box">
                                    {{ Form::label('user', __('User'), ['class' => 'text-type']) }}
                                    {{ Form::select('user', $users, isset($_GET['user']) ? $_GET['user'] : '', ['class' => 'form-control', 'placeholder' => __('Select User')]) }}
                                </div>
                            </div>
                            <div class="col-lg-auto col-md-6 float-end mt-md-4 d-flex">
                                <a href="#" class="btn btn-sm btn-primary me-2"
                                    onclick="document.getElementById('trash-form').submit(); return false;"
                                    data-bs-toggle="tooltip" title="{{ __('Apply') }}" id="applyfilter"
                                    data-original-title="{{ __('apply') }}">
                                    <span class="btn-inner--icon"><i class="ti ti-search"></i></span>
                                </a>
                                <a href="{{ route('files-trash.index') }}" class="btn btn-sm btn-danger"
                                    data-bs-toggle="tooltip" id="clearfilter" title="{{ __('Reset') }}" data-original-title="{{ __('Reset') }}">
                                    <span class="btn-inner--icon"><i class="ti ti-trash-off text-white-off"></i></span>
                                </a>
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        @endif
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <h5></h5>
                    @if (isset($status) && $status == 1)
                        <div class="table-responsive">
                            {{ $dataTable->table(['width' => '100%']) }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    @if (isset($status) && $status == 1)
        @include('layouts.includes.datatable-js')
        {{ $dataTable->scripts() }}
    @endif
@endpush
