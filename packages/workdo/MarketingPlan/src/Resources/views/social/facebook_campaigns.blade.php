@extends('layouts.main')
@section('page-title')
    {{ __('Manage Marketing Plan') }}
@endsection
@section('page-breadcrumb')
{{ __('Marketing Plan') }}
@endsection
@push('css')
    @include('layouts.includes.datatable-css')
@endpush
@section('page-action')


@endsection
@section('content')


@endsection
@push('scripts')
    @include('layouts.includes.datatable-js')
    {{ $dataTable->scripts() }}
@endpush
