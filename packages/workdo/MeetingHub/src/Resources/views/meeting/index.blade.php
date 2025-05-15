@extends('layouts.main')
@section('page-title')
    {{ __('Manage Meeting Hub') }}
@endsection

@section('page-breadcrumb')
    {{ __('Meeting List') }}
@endsection
@section('page-action')
    <div>
        @permission('meetinghub create')
            <a class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="{{ __('Create') }}" data-ajax-popup="true"
                data-url="{{ route('meetings.create') }}" data-size="lg" data-title="{{ __('Create Meeting') }}">
                <i class="ti ti-plus"></i>
            </a>
        @endpermission
    </div>
@endsection

@push('css')
    @include('layouts.includes.datatable-css')
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
