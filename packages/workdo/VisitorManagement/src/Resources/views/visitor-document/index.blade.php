@extends('layouts.main')

@section('page-title')
    {{ __('Manage Visitor Documents') }}
@endsection
@section('page-breadcrumb')
    {{ __('Visitor Documents') }}
@endsection

@section('page-action')
    @permission('visitor documents create')
        <div>
            <a class="btn btn-sm btn-primary" data-ajax-popup="true" data-size="md" data-title="{{ __('Create Visitor Document') }}"
                data-url="{{ route('visitors-documents.create') }}" data-toggle="tooltip" data-bs-original-title="{{ __('Create') }}">
                <i class="ti ti-plus text-white"></i>
            </a>
        </div>
    @endpermission
@endsection

@push('css')
    @include('layouts.includes.datatable-css')
@endpush
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
