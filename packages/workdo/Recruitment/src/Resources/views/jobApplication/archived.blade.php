@extends('layouts.main')
@section('page-title')
    {{ __('Manage Archive Job Application') }}
@endsection

@section('page-breadcrumb')
    {{ __('Archive Job Application') }}
@endsection
@push('css')
    @include('layouts.includes.datatable-css')
    <link href="{{ asset('packages/workdo/Recruitment/src/Resources/assets/css/bootstrap-tagsinput.css') }}"
        rel="stylesheet" />
    <link href="{{ asset('packages/workdo/Recruitment/src/Resources/assets/css/custom.css') }}" rel="stylesheet" />
@endpush
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
