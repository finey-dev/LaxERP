@extends('layouts.main')

@section('page-title')
    {{ __('Manage Purchase Order') }}
@endsection

@section('page-breadcrumb')
    {{ __('Sales Agent') }} , {{ __('Purchase Order') }}
@endsection

@section('page-action')
    <div>
        @permission('salesagent purchase create')
            <a href="{{ route('salesagents.purchase.order.create') }}" class="btn btn-sm btn-primary"
                data-title="{{ __('Create New Purchase Order') }}" data-bs-toggle="tooltip"
                data-bs-original-title="{{ __('Create') }}">
                <i class="ti ti-plus"></i>
            </a>
            <a class="btn btn-sm btn-primary" data-ajax-popup="true" data-size="lg" data-title="{{ __('Prefix Setting') }}"
                data-url="{{ route('salesagent.purchase.setting.create') }}" data-bs-toggle="tooltip"
                data-bs-original-title="{{ __('Setup') }}">
                <i class="ti ti-settings"></i>
            </a>
        @endpermission
    </div>
@endsection


@push('css')
    @include('layouts.includes.datatable-css')
@endpush

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
