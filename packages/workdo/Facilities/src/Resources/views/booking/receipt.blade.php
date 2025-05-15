@extends('layouts.main')
@section('page-title')
    {{ __('Manage Booking Receipt') }}
@endsection
@section('page-breadcrumb')
    {{ __('Booking Receipt') }}
@endsection
@push('css')
    @include('layouts.includes.datatable-css')
@endpush

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
