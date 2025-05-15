@extends('layouts.main')
@section('page-title')
    {{ __('Manage RFx Applicant') }}
@endsection

@section('page-breadcrumb')
    {{ __('RFx Applicant') }}
@endsection
@push('css')
    @include('layouts.includes.datatable-css')
    <link href="{{ asset('packages/workdo/Procurement/src/Resources/assets/css/bootstrap-tagsinput.css') }}"
        rel="stylesheet" />
    <link href="{{ asset('packages/workdo/Procurement/src/Resources/assets/css/custom.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/js/plugins/summernote-0.8.18-dist/summernote-lite.min.css') }}" rel="stylesheet">
@endpush

@push('scripts')
    <script src="{{ asset('assets/js/plugins/summernote-0.8.18-dist/summernote-lite.min.js') }}"></script>
    @include('layouts.includes.datatable-js')
    {{ $dataTable->scripts() }}
@endpush

@section('page-action')
    <div>
        @permission('rfx applicant create')
            <a href="{{ route('rfx-applicant.create') }}" class="btn btn-sm btn-primary btn-icon" data-bs-toggle="tooltip"
                data-bs-placement="top" data-title="{{ __('Create New RFx Applicant') }}" title="{{ __('Create') }}">
                <i class="ti ti-plus text-white"></i>
            </a>
        @endpermission
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12 col-lg-12 col-xl-12 col-md-12">
            <div class=" mt-2 " id="multiCollapseExample1" style="">
                <div class="card">
                    <div class="card-body">
                        {{ Form::open(['route' => ['rfx-applicant.index'], 'method' => 'get', 'id' => 'applicarion_filter']) }}
                        <div class="d-flex align-items-center justify-content-end">

                            <div class="col-xl-2 col-lg-3 col-md-6 col-sm-6 col-6 me-2">
                                <label class="form-label">{{ __('Gender') }}</label> <br>
                                <div class="form-check form-check-inline form-group">
                                    <input type="radio" id="male" value="male" name="gender"
                                        class="form-check-input"
                                        {{ isset($_GET['gender']) && $_GET['gender'] == 'male' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="male">{{ __('Male') }}</label>
                                </div>
                                <div class="form-check form-check-inline form-group">
                                    <input type="radio" id="female" value="female" name="gender"
                                        class="form-check-input"
                                        {{ isset($_GET['gender']) && $_GET['gender'] == 'female' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="female">{{ __('Female') }}</label>
                                </div>
                            </div>
                            <div class="col-xl-2 col-lg-3 col-md-6 col-sm-6 col-6 me-2">
                                <div class="btn-box">
                                    {{ Form::label('name', __('Name'), ['class' => 'form-label']) }}
                                    {{ Form::text('name', $filter['name'], ['class' => 'form-control select ', 'placeholder' => 'Enter name']) }}
                                </div>
                            </div>
                            <div class="col-xl-2 col-lg-3 col-md-6 col-sm-6 col-6 me-2">
                                <div class="btn-box">
                                    {{ Form::label('country', __('Country'), ['class' => 'form-label']) }}
                                    {{ Form::select('country', $rfx_applicant_country, $filter['country'], ['class' => 'form-control select ']) }}
                                </div>
                            </div>

                            <div class="col-xl-2 col-lg-3 col-md-6 col-sm-6 col-6 me-2">
                                <div class="btn-box">
                                    {{ Form::label('state', __('State'), ['class' => 'form-label']) }}
                                    {{ Form::select('state', $rfx_applicant_state, $filter['state'], ['class' => 'form-control select ']) }}
                                </div>
                            </div>

                            <div class="col-auto float-end  mt-4">
                                <a class="btn btn-sm btn-primary me-1" data-bs-toggle="tooltip" title="{{ __('Apply') }}"
                                    id="applyfilter" data-original-title="{{ __('apply') }}">
                                    <span class="btn-inner--icon"><i class="ti ti-search"></i></span>
                                </a>
                                <a href="#!" class="btn btn-sm btn-danger " data-bs-toggle="tooltip"
                                    title="{{ __('Reset') }}" id="clearfilter" data-original-title="{{ __('Reset') }}">
                                    <span class="btn-inner--icon"><i class="ti ti-trash-off text-white-off "></i></span>
                                </a>
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>

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
