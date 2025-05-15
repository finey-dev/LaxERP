

@extends('layouts.main')

@section('page-title')
   {{ $Requests->name . __("'s Form Field") }}

@endsection
@push('css')
@include('layouts.includes.datatable-css')
@endpush
@section('page-breadcrumb')
   {{ __("Form Field") }}
@endsection

@section('page-action')
<div class="d-flex">
    <a href="{{ route('requests.index') }}" class="btn btn-sm me-2 btn-primary btn-icon" data-bs-toggle="tooltip"
data-bs-placement="top" title="{{ __('Back') }}"><i class="ti ti-arrow-left text-white"></i></a>
@permission('Requests formfield create')
        <a  data-url="{{ route('formfield.create' ,$Requests->id) }}" data-ajax-popup="true"
            data-title="{{ __('Create Form Field') }}" data-bs-toggle="tooltip" title="" class="btn btn-sm btn-primary"
            data-bs-original-title="{{ __('Create') }}" data-size="lg">
            <i class="ti ti-plus"></i>
        </a>
@endpermission

</div>
@endsection

@section('content')
<div class="row">
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
