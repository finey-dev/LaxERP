@extends('layouts.main')
@section('page-title')
{{__('Manage Warranties')}}
@endsection
@section('page-breadcrumb')
{{ __('Warranties') }}
@endsection
@push('css')
    @include('layouts.includes.datatable-css')
@endpush
@section('page-action')
@permission('repair technician create')
<div>
    <a href="#" class="btn btn-sm btn-primary" data-url="{{ route('repair-warranty.create') }}" data-size="md" data-bs-toggle="tooltip" data-bs-original-title="{{ __('Create') }}" data-ajax-popup="true" data-title="{{__('Create Repair Warranty')}}">
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
