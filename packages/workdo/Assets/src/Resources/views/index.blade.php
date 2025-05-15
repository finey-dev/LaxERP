@extends('layouts.main')
@section('page-title')
    {{__('Manage Assets')}}
@endsection
@section("page-breadcrumb")
    {{__('Assets')}}
@endsection
@push('css')
    @include('layouts.includes.datatable-css')
@endpush
@section('page-action')
    <div class="d-flex">
        @stack('addButtonHook')
        @permission('assets import')
            <a href="#"  class="btn btn-sm btn-primary me-2" data-ajax-popup="true" data-title="{{__('Import Assets')}}" data-url="{{ route('assets.file.import') }}"  data-toggle="tooltip" title="{{ __('Import') }}"><i class="ti ti-file-import"></i>
            </a>
        @endpermission
        @permission('assets create')
            <a  class="btn btn-sm btn-primary" data-size="lg" data-url="{{ route('asset.create') }}" data-ajax-popup="true" data-title="{{__('Create Asset')}}"  data-bs-toggle="tooltip"  data-bs-original-title="{{ __('Create') }}">
                <i class="ti ti-plus"></i>
            </a>
        @endpermission
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header card-body table-border-style">
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
