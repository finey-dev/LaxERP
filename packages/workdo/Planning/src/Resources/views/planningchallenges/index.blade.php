@extends('layouts.main')
@section('page-title')
    {{ __('Manage Challenges') }}
@endsection
@section('page-breadcrumb')
    {{ __('Challenges') }}
@endsection
@push('css')
    @include('layouts.includes.datatable-css')
@endpush
@section('page-action')
    <div>
        @permission('planningchallenges create')
            <a class="btn btn-sm btn-primary" data-ajax-popup="true" data-size="lg" data-title="{{ __('Create Challenge') }}"
                data-url="{{ route('planningchallenges.create') }}" data-toggle="tooltip" title="{{ __('Create') }}">
                <i class="ti ti-plus"></i>
            </a>
        @endpermission
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="mt-2" id="multiCollapseExample1">
            <div class="card">
                <div class="card-body">
                    <div class="row d-flex align-items-center justify-content-end">
                        <div class="col-xl-6">
                            <div class="row">
                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12"> <!-- Adjusted column size -->
                                    <div class="btn-box">
                                        {{ Form::label('position', __('Position'), ['class' => 'form-label']) }}
                                        {{ Form::select('position', $position, isset($_GET['position']) ? $_GET['position'] : '', ['class' => 'form-control ', 'placeholder' => 'Select Position']) }}
                                    </div>
                                </div>

                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12"> <!-- Adjusted column size -->
                                    <div class="btn-box">
                                        {{ Form::label('category', __('Category'), ['class' => 'form-label']) }}
                                        {{ Form::select('category', $category, isset($_GET['category']) ? $_GET['category'] : '', ['class' => 'form-control ', 'placeholder' => 'Select Category']) }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-auto mt-4 d-flex align-items-center">
                            <a class="btn btn-sm btn-primary me-2" data-bs-toggle="tooltip" title="{{ __('Apply') }}"
                                data-original-title="{{ __('apply') }}" id="applyfilter">
                                <span class="btn-inner--icon"><i class="ti ti-search"></i></span>
                            </a>
                            <a href="#" class="btn btn-sm btn-danger" data-bs-toggle="tooltip"
                                title="{{ __('Reset') }}" data-original-title="{{ __('Reset') }}" id="clearfilter">
                                <span class="btn-inner--icon"><i class="ti ti-trash-off text-white-off"></i></span>
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
@endsection
@push('scripts')
    @include('layouts.includes.datatable-js')
    {{ $dataTable->scripts() }}
@endpush
