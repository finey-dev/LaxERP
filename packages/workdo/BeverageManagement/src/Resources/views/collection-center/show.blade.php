@extends('layouts.main')
@section('page-title')
{{__('Collection Center Stock Details')}}
@endsection
@push('css')
@include('layouts.includes.datatable-css')
@endpush
@section('page-breadcrumb')
{{__('Collection Center Stock Details')}}
@endsection
@section('page-action')
<div>
    <a href="{{ route('collection-center.index') }}" data-bs-toggle="tooltip" title="{{__('Back')}}" class="btn btn-sm btn-primary">
        <i class="ti ti-arrow-back-up"></i>
    </a>
</div>
@endsection

@section('content')
<div class="row">
    <div class="mt-2" id="multiCollapseExample1">
        <div class="card">
            <div class="card-body">
                <div class="row d-flex align-items-center justify-content-end">

                    <div class="col-xl-2 col-lg-3 col-md-6 col-sm-12 col-12">
                        <div class="btn-box">
                            {{ Form::label('type', __('Type'), ['class' => 'form-label']) }}
                            {{ Form::select('type', ['Raw Material' => __('Raw Material'),'Manufacturing' => __('Manufacturing'),'Bill of Material' => __('Bill of Material'),'Packaging' => __('Packaging'),'add stock' => __('add stock')], '', ['class' => 'form-control ', 'id' => 'type','placeholder' => __('Select Type')]) }}
                        </div>
                    </div>
                    <div class="col-auto float-end ms-2 mt-4">
                        <div class="col-auto d-flex">
                            <a class="btn btn-sm btn-primary me-2" data-bs-toggle="tooltip" title="{{ __('Apply') }}" id="applyfilter" data-original-title="{{ __('apply') }}">
                                <span class="btn-inner--icon"><i class="ti ti-search"></i></span>
                            </a>
                            <a href="#" class="btn btn-sm btn-danger " data-bs-toggle="tooltip" title="{{ __('Reset') }}" id="clearfilter" data-original-title="{{ __('Reset') }}">
                                <span class="btn-inner--icon"><i class="ti ti-trash-off text-white-off "></i></span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-xl-12">
    <div class="card">
        <div class="card-body table-border-style">
            <div class="table-responsive">
                {{ $dataTable->table(['width' => '100%']) }}
            </div>
        </div>
    </div>
</div>
<!-- [ Main Content ] end -->
@endsection
@push('scripts')
@include('layouts.includes.datatable-js')
{{ $dataTable->scripts() }}
@endpush
