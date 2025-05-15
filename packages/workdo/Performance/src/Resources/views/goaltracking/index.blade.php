@extends('layouts.main')
@section('page-title')
    {{ __('Manage Goal Tracking') }}
@endsection

@section('page-action')
<div class="d-flex">
        <a href="{{ route('goaltracking.grid') }}" class="btn btn-sm btn-primary btn-icon me-2"
            data-bs-toggle="tooltip"title="{{ __('Grid View') }}">
            <i class="ti ti-layout-grid text-white"></i>
        </a>
        @permission('goaltracking create')
            <a data-url="{{ route('goaltracking.create') }}" data-ajax-popup="true" data-size="lg"
                data-title="{{ __('Create Goal Tracking') }}" data-bs-toggle="tooltip" title=""
                class="btn btn-sm btn-primary" data-bs-original-title="{{ __('Create') }}">
                <i class="ti ti-plus"></i>
            </a>
        @endpermission
    </div>
@endsection
@push('css')
    @include('layouts.includes.datatable-css')
    <link rel="stylesheet" href="{{ asset('packages/workdo/Performance/src/Resources/assets/css/custom.css') }}">
@endpush
@section('page-breadcrumb')
    {{ __('Goal Tracking') }}
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
