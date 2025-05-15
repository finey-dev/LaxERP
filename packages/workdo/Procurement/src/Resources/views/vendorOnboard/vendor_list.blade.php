@extends('layouts.main')
@section('page-title')
    {{ __('Manage Vendor') }}
@endsection

@section('page-breadcrumb')
    {{ __('Vendor') }}
@endsection
@push('css')
    <link href="{{ asset('packages/workdo/Procurement/src/Resources/assets/css/bootstrap-tagsinput.css') }}"
        rel="stylesheet" />
    <link href="{{ asset('packages/workdo/Procurement/src/Resources/assets/css/custom.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/dragula.min.css') }}">
    @include('layouts.includes.datatable-css')
@endpush
@push('scripts')
    <script src="{{ asset('assets/js/plugins/dragula.min.js') }}"></script>
    @include('layouts.includes.datatable-js')
    {{ $dataTable->scripts() }}
@endpush
@section('content')
    <div class="row">
        <div class="col-sm-12 col-lg-12 col-xl-12 col-md-12">
            <div class=" mt-2 " id="multiCollapseExample1" style="">
                <div class="card">
                    <div class="card-body">
                        {{ Form::open(['route' => ['rfx.vendor'], 'method' => 'get', 'id' => 'applicarion_filter']) }}
                        <div class="d-flex align-items-center justify-content-end">
                            <div class="col-xl-2 col-lg-3 col-sm-12 col-12 me-2">
                                <div class="btn-box">
                                    {{ Form::label('name', __('Name'), ['class' => 'form-label']) }}
                                    {{ Form::text('name', isset($_GET['name']) ? $_GET['name'] : '', ['class' => 'month-btn form-control', 'placeholder' => 'Enter Name']) }}
                                </div>
                            </div>
                            <div class="col-xl-2 col-lg-3 col-md-6 col-sm-6 col-6 me-2">
                                <div class="btn-box">
                                    {{ Form::label('country', __('Country'), ['class' => 'form-label']) }}
                                    {{ Form::select('country', $country, $filter['country'], ['class' => 'form-control select ']) }}
                                </div>
                            </div>

                            <div class="col-xl-2 col-lg-3 col-md-6 col-sm-6 col-6 me-2">
                                <div class="btn-box">
                                    {{ Form::label('state', __('State'), ['class' => 'form-label']) }}
                                    {{ Form::select('state', $state, $filter['state'], ['class' => 'form-control select ']) }}
                                </div>
                            </div>
                            <div class="col-xl-2 col-lg-3 col-md-6 col-sm-6 col-6 me-2  ">
                                <div class="btn-box">
                                    {{ Form::label('city', __('City'), ['class' => 'form-label']) }}
                                    {{ Form::select('city', $city, $filter['city'], ['class' => 'form-control select ']) }}
                                </div>
                            </div>
                            <div class="col-auto float-end  mt-4">
                                <a class="btn btn-sm btn-primary me-1" data-bs-toggle="tooltip" title="{{ __('Apply') }}"
                                    id="applyfilter" data-original-title="{{ __('apply') }}">
                                    <span class="btn-inner--icon"><i class="ti ti-search"></i></span>
                                </a>
                                <a href="#!" class="btn btn-sm btn-danger " data-bs-toggle="tooltip"
                                    title="{{ __('Reset') }}" id="clearfilter"
                                    data-original-title="{{ __('Reset') }}">
                                    <span class="btn-inner--icon"><i class="ti ti-trash-off text-white-off "></i></span>
                                </a>
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
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
