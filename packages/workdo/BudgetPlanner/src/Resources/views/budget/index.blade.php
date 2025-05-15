@extends('layouts.main')
@section('page-title')
    {{__('Manage Budget Planner')}}
@endsection
@section('page-breadcrumb')
    {{__('Budget Planner')}}
@endsection
@push('css')
    @include('layouts.includes.datatable-css')
@endpush

@section('page-action')
    @permission('budget plan create')
        <div class="float-end">
            <a href="{{ route('budget.create') }}" data-bs-toggle="tooltip" title="{{__('Create')}}" class="btn btn-sm btn-primary">
                <i class="ti ti-plus"></i>
            </a>
        </div>
    @endpermission
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

