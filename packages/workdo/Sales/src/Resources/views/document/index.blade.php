@extends('layouts.main')
@section('page-title')
    {{ __('Manage Sales Document') }}
@endsection
@section('title')
    {{ __('Document') }}
@endsection
@section('page-breadcrumb')
   {{ __('Sales Document') }}
@endsection
@section('page-action')
<div class="d-flex">
    @stack('addButtonHook')

    <a href="{{ route('salesdocument.grid') }}" class="btn btn-sm btn-primary btn-icon me-2" data-bs-toggle="tooltip"
    title="{{ __('Grid View') }}">
    <i class="ti ti-layout-grid text-white"></i>
    </a>

    @permission('salesdocument create')
        <a data-size="lg" data-url="{{ route('salesdocument.create', ['document', 0]) }}" data-ajax-popup="true"
        data-bs-toggle="tooltip" data-title="{{ __('Create Sales Document') }}" title="{{ __('Create') }}"
        class="btn btn-sm btn-primary btn-icon">
        <i class="ti ti-plus"></i>
        </a>
    @endpermission
</div>
@endsection
@push('css')
    @include('layouts.includes.datatable-css')
    <link rel="stylesheet" href="{{ asset('packages/workdo/Sales/src/Resources/assets/css/custom.css') }}">
@endpush

@section('content')
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <h5></h5>
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