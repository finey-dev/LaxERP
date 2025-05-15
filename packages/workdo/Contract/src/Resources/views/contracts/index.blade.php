@extends('layouts.main')

@section('page-title')
    {{ __('Manage Contract') }}
@endsection

@section('page-breadcrumb')
    {{ __('Contract') }}
@endsection

@push('css')
    @include('layouts.includes.datatable-css')
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/dragula.min.css') }}">
    <style>
        .comp-card {
            min-height: 140px;
        }
    </style>
@endpush

@section('page-action')
    <div class="d-flex">
        @stack('addButtonHook')
        <a href="{{ route('contract.grid') }}" class="btn btn-sm btn-primary me-2"
            data-bs-toggle="tooltip"title="{{ __('Grid View') }}">
            <i class="ti ti-layout-grid text-white"></i>
        </a>
        @permission('contract create')
            <a data-url="{{ route('contract.create') }}" data-size="lg" data-ajax-popup="true"
                data-bs-toggle="tooltip"data-title="{{ __('Create Contract') }}"title="{{ __('Create') }}"
                class="btn btn-sm btn-primary me-2">
                <i class="ti ti-plus"></i>
            </a>
        @endpermission
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="m-b-20">{{ __('Total Contracts') }}</h6>
                            <h3 class="text-primary">{{ $cnt_contract['total'] }}</h3>
                        </div>
                        {{-- <div class="col-auto">
                            <i class="fas fa-handshake bg-success text-white"></i>
                        </div> --}}
                        <div class="theme-avtar bg-success badge col-auto">
                            <i class="fas fa-handshake text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="m-b-20">{{ __('This Month Total Contracts') }}</h6>
                            <h3 class="text-info">{{ $cnt_contract['this_month'] }}</h3>
                        </div>
                        <div class="theme-avtar bg-info badge col-auto">
                            <i class="fas fa-handshake text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="m-b-20">{{ __('This Week Total Contracts') }}</h6>
                            <h3 class="text-warning">{{ $cnt_contract['this_week'] }}</h3>
                        </div>
                        <div class="theme-avtar bg-warning badge col-auto">
                            <i class="fas fa-handshake text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="m-b-20">{{ __('Last 30 Days Total Contracts') }}</h6>
                            <h3 class="text-danger">{{ $cnt_contract['last_30days'] }}</h3>
                        </div>
                        <div class="theme-avtar bg-danger badge col-auto">
                            <i class="fas fa-handshake text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12">
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
