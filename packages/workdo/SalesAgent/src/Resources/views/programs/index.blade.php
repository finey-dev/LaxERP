@extends('layouts.main')

@section('page-title')
    {{ __('Manage Programs') }}
@endsection

@section('page-breadcrumb')
    {{ __('Sales Agent') }} ,{{ __('Programs') }}
@endsection

@section('page-action')
    <div>
        @permission('programs create')
            <a href="{{ route('programs.create') }}" class="btn btn-sm btn-primary" data-title="{{ __('Create New Program') }}"
                data-bs-toggle="tooltip" data-bs-original-title="{{ __('Create') }}">
                <i class="ti ti-plus"></i>
            </a>
        @endpermission
    </div>
@endsection

@push('css')
    @include('layouts.includes.datatable-css')
@endpush

@section('content')
    <div class="row">
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
