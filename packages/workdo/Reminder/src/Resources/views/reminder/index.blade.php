@extends('layouts.main')
@section('page-title')
    {{ __('Manage Reminder') }}
@endsection
@push('scripts')
@endpush
@section('page-breadcrumb')
    {{ __('Reminder') }}
@endsection
@push('css')
    @include('layouts.includes.datatable-css')
@endpush
@section('page-action')
    <div>
        @permission('reminder create')
            <a class="btn btn-sm btn-primary btn-icon" data-bs-toggle="tooltip" data-bs-placement="top"
                title="{{ __('Create') }}"  data-size="md"
                data-title="{{ __('Create Reminder') }}" href="{{route('reminder.create')}}"><i
                    class="ti ti-plus text-white"></i></a>
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
