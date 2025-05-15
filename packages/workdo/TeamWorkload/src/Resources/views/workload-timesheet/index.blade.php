@extends('layouts.main')
@push('css')
    @include('layouts.includes.datatable-css')
@endpush
@section('page-title')
    {{__('Manage Workload Timesheet')}}
@endsection
@section('title')
    {{__('Workload Timesheet')}}
@endsection
@section('page-breadcrumb')
   {{__('Workload Timesheet')}}
@endsection
@section('page-action')
<div>
    @permission('workload holidays create')
        <a data-url="{{ route('workload-timesheet.create') }}" data-size="lg" data-ajax-popup="true" data-bs-toggle="tooltip"data-title="{{__('Create New Timesheet')}}"title="{{__('Create')}}" class="btn btn-sm btn-primary">
            <i class="ti ti-plus"></i>
        </a>
    @endpermission
</div>
@endsection
@section('content')

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body table-border-style">
                <div class="table-responsive overflow_hidden">
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
