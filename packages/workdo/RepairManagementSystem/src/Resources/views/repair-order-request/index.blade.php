@extends('layouts.main')
@section('page-title')
{{__('Manage Repair Order Request')}}
@endsection
@section('page-breadcrumb')
{{ __('Repair Order Request') }}
@endsection
@push('css')
    @include('layouts.includes.datatable-css')
@endpush
@section('page-action')
@permission('repair order request create')
<div>
    <a href="#" class="btn btn-sm btn-primary" data-url="{{ route('repair.request.create') }}" data-size="md" data-bs-toggle="tooltip" data-bs-original-title="{{ __('Create') }}" data-ajax-popup="true" data-title="{{__('Create Repair Order')}}">
        <i class="ti ti-plus"></i>
    </a>
</div>
@endpermission
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
