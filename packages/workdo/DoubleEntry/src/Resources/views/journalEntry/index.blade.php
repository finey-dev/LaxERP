@extends('layouts.main')
@section('page-title')
    {{__('Manage Journal Entry')}}
@endsection

@section('page-breadcrumb')
    {{__('Journal Entry')}}
@endsection

@push('css')
    @include('layouts.includes.datatable-css')
@endpush

@section('page-action')
    <div class="d-flex">
        @stack('addButtonHook')
        @permission('journalentry create')
        <a href="{{ route('journal-entry.create') }}" data-title="{{__('Create New Journal')}}" data-bs-toggle="tooltip"
           title="{{__('Create')}}" class="btn btn-sm btn-primary">
            <i class="ti ti-plus"></i>
        </a>
        @endpermission
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 mt-2">
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