@extends('layouts.main')
@section('page-title')
    {{ __('Manage Appraisal') }}
@endsection
@push('css')
    @include('layouts.includes.datatable-css')
    <style>
        @import url({{ asset('packages/workdo/Performance/src/Resources/assets/css/font-awesome.css') }});
    </style>
    <link rel="stylesheet" href="{{ asset('packages/workdo/Performance/src/Resources/assets/css/custom.css') }}">
@endpush
@push('scripts')
    @include('layouts.includes.datatable-js')
    {{ $dataTable->scripts() }}
    <script src="{{ asset('packages/workdo/Performance/src/Resources/assets/js/bootstrap-toggle.js') }}"></script>
@endpush
@php
    $company_settings = getCompanyAllSetting();
@endphp
@section('page-breadcrumb')
    {{ __('Appraisal') }}
@endsection

@section('page-action')
    <div>
        @permission('appraisal create')
            <a data-url="{{ route('appraisal.create') }}" data-ajax-popup="true" data-size="lg"
                data-title="{{ __('Create Appraisal') }}" data-bs-toggle="tooltip" title="" class="btn btn-sm btn-primary"
                data-bs-original-title="{{ __('Create') }}">
                <i class="ti ti-plus"></i>
            </a>
        @endpermission
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        {{ $dataTable->table(['width' => '100%']) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
