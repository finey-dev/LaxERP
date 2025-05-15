@extends('layouts.main')

@section('page-title')
    {{ __('Manage Custom Question for interview') }}
@endsection
@section('page-breadcrumb')
   {{ __('Custom Question') }}
@endsection
@push('css')
    @include('layouts.includes.datatable-css')
@endpush
@section('page-action')
<div>
    @permission('rfx custom question create')
        <a  data-url="{{ route('rfx-custom-question.create') }}" data-ajax-popup="true"
            data-title="{{ __('Create Custom Question') }}" data-bs-toggle="tooltip" title=""
            class="btn btn-sm btn-primary" data-bs-original-title="{{ __('Create') }}">
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
