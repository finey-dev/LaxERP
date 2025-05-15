@extends('layouts.main')
@section('page-title')
    {{ __('Manage Space') }}
@endsection
@section('page-breadcrumb')
    {{ __('Space') }}
@endsection
@push('css')
    @include('layouts.includes.datatable-css')
@endpush
@section('page-action')
    <div>
        @permission('facilitiesspace create')
            <a class="btn btn-sm btn-primary btn-icon" data-ajax-popup="true" data-size="md"
                title="{{ __('Create') }}" data-title="{{ __('Create Space') }}" data-bs-toggle="tooltip" data-url="{{ route('facilities-space.create') }}">
                <i class="ti ti-plus text-white"></i>
            </a>
        @endpermission
    </div>
@endsection
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
@push('scripts')
    @include('layouts.includes.datatable-js')
    {{ $dataTable->scripts() }}
@endpush
