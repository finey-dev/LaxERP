@extends('layouts.main')
@section('page-title')
    {{ __('Manage Charters') }}
@endsection
@section('page-breadcrumb')
    {{ __('Charters') }}
@endsection
@push('css')
    @include('layouts.includes.datatable-css')
@endpush
@section('page-action')
    <div class="d-flex">
        <a href="{{ route('charters.grid') }}" class="btn btn-sm btn-primary btn-icon me-2"
            data-bs-toggle="tooltip"title="{{ __('Grid View') }}">
            <i class="text-white ti ti-layout-grid"></i>
        </a>

        <a href="{{ route('charters.kanban') }}" data-bs-toggle="tooltip" data-bs-placement="top"
            title="{{ __('Kanban View') }}" class="btn btn-sm btn-primary btn-icon me-2"><i class="ti ti-table"></i> </a>

        <a href="{{ route('charters.treeview') }}" data-bs-toggle="tooltip" data-bs-placement="top"
            title="{{ __('Tree View') }}" class="btn btn-sm btn-primary btn-icon me-2"><i class="ti ti-sitemap"></i> </a>

        @permission('charters create')
            <a href="{{ route('charters.create', [0]) }}" data-title="{{ __('Create Charter') }}" data-bs-toggle="tooltip"
                title="" class="btn btn-sm btn-primary" data-bs-original-title="{{ __('Create') }}">
                <i class="ti ti-plus"></i>
            </a>
        @endpermission

    </div>
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
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

