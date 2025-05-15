@extends('layouts.main')
@section('page-title')
    {{ __('Manage Goals') }}
@endsection
@section('page-breadcrumb')
    {{ __('Goals') }}
@endsection

@section('page-action')
    <div class="d-flex">
        @permission('goal import')
            <a href="#" data-size="md" class="btn btn-sm btn-primary me-2" data-ajax-popup="true" data-title="{{ __('Import Goal') }}"
                data-url="{{ route('goal.file.import') }}" data-toggle="tooltip" title="{{ __('Import') }}"><i
                    class="ti ti-file-import"></i>
            </a>
        @endpermission
        @permission('goal create')
            <a data-url="{{ route('goal.create') }}" data-bs-toggle="tooltip" data-size="md" title="{{ __('Create') }}"
                data-ajax-popup="true" data-title="{{ __('Create Goal') }}" class="btn btn-sm btn-primary">
                <i class="ti ti-plus"></i>
            </a>
        @endpermission
    </div>
@endsection
@push('css')
    @include('layouts.includes.datatable-css')
@endpush
@push('scripts')
    @include('layouts.includes.datatable-js')
    {{ $dataTable->scripts() }}
@endpush
@section('content')
    <div class="row">
        <div class="col-xl-12">
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
