@extends('layouts.main')
@section('page-title')
    {{ __('Manage Verification') }}
@endsection
@section('page-breadcrumb')
    {{ __('Verification') }}
@endsection
@push('css')
    @include('layouts.includes.datatable-css')
@endpush
@section('page-action')
    <div>
        @permission('verification create')
            @if (Auth::user()->type != 'super admin')
                @if (is_null($status) || $status == 2)
                    <a class="btn btn-sm btn-primary" data-ajax-popup="true" data-size="md"
                        data-title="{{ __('Upload Verification Document') }}" data-url="{{ route('file-verification.create') }}"
                        data-toggle="tooltip" title="{{ __('Create') }}">
                        <i class="ti ti-plus"></i>
                    </a>
                @endif
            @endif
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
