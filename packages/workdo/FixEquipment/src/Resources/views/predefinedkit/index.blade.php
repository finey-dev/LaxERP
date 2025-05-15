@extends('layouts.main')

@section('page-title')
    {{ __('Manage Pre Defined Kit') }}
@endsection

@section('page-breadcrumb')
    {{ __('Pre Defined Kit') }}
@endsection

@push('css')
    @include('layouts.includes.datatable-css')
@endpush

@section('page-action')
    <div>
        @permission('predefined kit create')
            <a class="btn btn-sm btn-primary" data-ajax-popup="true" data-size="md" data-title="{{ __('Create Pre Defined Kit') }}"
                data-url="{{ route('fix.equipment.pre.definded.kit.create') }}" data-bs-toggle="tooltip" data-bs-original-title="{{ __('Create') }}">
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
