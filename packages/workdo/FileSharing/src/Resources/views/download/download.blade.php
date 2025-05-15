@extends('layouts.main')
@section('page-title')
    {{ __('Manage Activity') }}
@endsection
@section('page-breadcrumb')
    {{ __('Activity') }}
@endsection
@push('css')
    @if (isset($status) && $status == 1)
        @include('layouts.includes.datatable-css')
    @endif
@endpush
@section('content')
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <h5></h5>
                    @if (isset($status) && $status == 1)
                        <div class="table-responsive">
                            {{ $dataTable->table(['width' => '100%']) }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    @if (isset($status) && $status == 1)
        @include('layouts.includes.datatable-js')
        {{ $dataTable->scripts() }}
    @endif
@endpush
