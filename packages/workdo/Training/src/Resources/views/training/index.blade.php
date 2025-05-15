@extends('layouts.main')

@section('page-title')
    {{ __('Manage Training') }}
@endsection

@section('page-breadcrumb')
    {{ __('Training') }}
@endsection
@push('css')
    @include('layouts.includes.datatable-css')
@endpush
@php
    $company_settings = getCompanyAllSetting();
@endphp

@section('page-action')
    <div>
        @permission('training create')
            <a href="#" data-url="{{ route('training.create') }}" data-ajax-popup="true" data-size="lg"
                data-title="{{ __('Create Training') }}" data-bs-toggle="tooltip" title="" class="btn btn-sm btn-primary"
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
@push('scripts')
    @include('layouts.includes.datatable-js')
    {{ $dataTable->scripts() }}
@endpush
