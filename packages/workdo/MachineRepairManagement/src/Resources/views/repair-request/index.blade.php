@extends('layouts.main')
@section('page-title')
    {{ __('Manage Machine Repair Request') }}
@endsection

@push('css')
    @include('layouts.includes.datatable-css')
@endpush

@section('page-breadcrumb')
    {{ __('Machine Repair Request') }}
@endsection

@section('page-action')
    <div>
        @permission('repair request create')
            <a class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="{{ __('Create') }}" data-ajax-popup="true"
                data-url="{{ route('machine-repair-request.create') }}" data-size="lg" data-title="{{ __('Create Machine Repair Request') }}">
                <i class="ti ti-plus"></i>
            </a>
        @endpermission
    </div>
@endsection

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
