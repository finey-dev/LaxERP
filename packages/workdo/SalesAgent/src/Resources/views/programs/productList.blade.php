@extends('layouts.main')

@section('page-title')
    {{ __('Sales Agent') }}
@endsection

@section('page-breadcrumb')
    {{ __('Sales Agent') }} , {{ __('Products List') }}
@endsection

@push('css')
    @include('layouts.includes.datatable-css')
@endpush

@section('content')
    <div class="row">
        <div class="col-md-12 ">
            <div class="card px-3 p-4">
                <div class="d-flex align-items-center justify-content-end">
                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                        <div class="btn-box">
                            {{ Form::label('category', __('Category'), ['class' => 'text-type form-label d-none']) }}
                            {{ Form::select('program', $programs ?? [], !empty($_GET['program']) ? $_GET['program'] : null, ['class' => 'form-control program_id ', 'required' => 'required', 'placeholder' => 'Select program...', 'id' => 'programs_select']) }}
                        </div>
                    </div>
                    <div class="col-auto ms-3">
                        <div class="row">
                            <div class="col-auto">
                                <a class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="{{ __('Apply') }}"
                                    id="applyfilter" data-original-title="{{ __('apply') }}">
                                    <span class="btn-inner--icon"><i class="ti ti-search"></i></span>
                                </a>
                                <a href="#!" class="btn btn-sm btn-danger " data-bs-toggle="tooltip"
                                    title="{{ __('Reset') }}" id="clearfilter" data-original-title="{{ __('Reset') }}">
                                    <span class="btn-inner--icon"><i class="ti ti-trash-off text-white-off "></i></span>
                                </a>
                            </div>
                        </div>
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

@push('scripts')
    @include('layouts.includes.datatable-js')
    {{ $dataTable->scripts() }}
@endpush
