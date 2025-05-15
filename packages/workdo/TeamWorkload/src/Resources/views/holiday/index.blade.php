@extends('layouts.main')
@push('css')
    @include('layouts.includes.datatable-css')
@endpush
@section('page-title')
    {{ __('Manage Holiday') }}
@endsection
@section('page-breadcrumb')
{{ __('Holiday') }}
@endsection
@section('page-action')
<div class="d-flex">

        {{-- <a href="#"  class="btn btn-sm btn-primary" data-ajax-popup="true" data-title="{{__('Holiday Import')}}" data-url="{{ route('workload-holiday.file.import') }}"  data-toggle="tooltip" title="{{ __('Import') }}"><i class="ti ti-file-import"></i>
        </a> --}}
    <a href="{{ route('holidays.calender') }}" class="btn btn-sm btn-primary me-2" data-bs-toggle="tooltip"
        data-bs-original-title="{{ __('Calendar View') }}">
        <i class="ti ti-calendar"></i>
    </a>
    @permission('workload holidays create')
        <a  class="btn btn-sm btn-primary me-2" data-ajax-popup="true" data-size="md" data-title="{{ __('Create New Holiday') }}" data-url="{{route('holidays.create')}}" data-bs-toggle="tooltip"  data-bs-original-title="{{ __('Create') }}">
            <i class="ti ti-plus"></i>
        </a>
    @endpermission
</div>
@endsection
@section('content')
<div class="row">
    <div class="col-sm-12 col-lg-12 col-xl-12 col-md-12">
            <div class="card">
                <div class="card-body">
                    {{-- {{ Form::open(['route' => ['holidays.index'], 'method' => 'get', 'id' => 'holiday_filter']) }} --}}
                    <div class="d-flex align-items-center justify-content-end">
                        <div class=" col-xl-3 col-lg-12 col-12 mx-2">
                            <div class="btn-box">
                                {{ Form::label('start_date', __('Start Date'), ['class' => 'form-label']) }}
                                {{ Form::date('start_date',isset($_GET['start_date']) ? $_GET['start_date'] : '', ['class' => 'form-control ','placeholder'=>"Select Date"]) }}
                            </div>
                        </div>
                        <div class=" col-xl-3 col-lg-12 col-12 mx-2">
                            <div class="btn-box">
                                {{ Form::label('end_date', __('End Date'), ['class' => 'form-label']) }}
                                {{ Form::date('end_date',isset($_GET['end_date']) ? $_GET['end_date'] : '', ['class' => 'form-control ','placeholder'=>"Select Date"]) }}
                            </div>
                        </div>
                        <div class="col-auto float-end ms-2 mt-4">
                            {{-- <a  class="btn btn-sm btn-primary"
                                onclick="document.getElementById('holiday_filter').submit(); return false;"
                                data-bs-toggle="tooltip" title="" data-bs-original-title="apply">
                                <span class="btn-inner--icon"><i class="ti ti-search"></i></span>
                            </a> --}}
                            <a  class="btn btn-sm btn-primary me-2" id="applyfilter"
                                data-bs-toggle="tooltip" title="{{ __('Apply') }}" data-bs-original-title="{{ __('Apply') }}">
                                <span class="btn-inner--icon"><i class="ti ti-search"></i></span>
                            </a>
                            <a href="#!" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" id="clearfilter"
                                title="{{ __('Reset') }}" data-bs-original-title="{{ __('Reset') }}">
                                <span class="btn-inner--icon"><i class="ti ti-trash-off text-white-off "></i></span>
                            </a>
                        </div>
                    </div>
                    {{-- {{ Form::close() }} --}}
                </div>
            </div>
        </div>

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
