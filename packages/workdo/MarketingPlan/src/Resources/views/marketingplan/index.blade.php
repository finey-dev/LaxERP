@extends('layouts.main')
@section('page-title')
    {{ __('Manage Marketing Plan') }}
@endsection
@section('page-breadcrumb')
{{ __('Marketing Plan') }}
@endsection
@push('css')
    @include('layouts.includes.datatable-css')
@endpush
@section('page-action')
<div class="d-flex">
        <a href="{{ route('marketing-plan.grid') }}" class="btn btn-sm btn-primary btn-icon me-2" data-bs-toggle="tooltip"
            title="{{ __('Grid View') }}">
            <i class="text-white ti ti-layout-grid"></i>
        </a>

        <a href="{{ route('marketing-plan.kanban') }}" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Kanban View') }}"
            class="btn btn-sm btn-primary btn-icon me-2"><i class="ti ti-table"></i> </a>

        <a href="{{ route('marketing-plan.treeview') }}" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Tree View') }}"
            class="btn btn-sm btn-primary btn-icon me-2"><i class="ti ti-sitemap"></i> </a>

        @permission('marketing plan create')
            <a href="{{ route('marketing-plan.create',[0]) }}" data-title="{{ __('Create Marking Plan') }}" data-bs-toggle="tooltip"
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
                        {{  $dataTable->table(['width' => '100%'])  }}
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
