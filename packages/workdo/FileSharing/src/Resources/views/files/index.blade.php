@extends('layouts.main')
@section('page-title')
    {{ __('Manage Files') }}
@endsection
@section('page-breadcrumb')
    {{ __('Files') }}
@endsection
@push('css')
    @if (isset($status) && $status == 1)
        @include('layouts.includes.datatable-css')
    @endif
@endpush
@section('page-action')
    <div class="d-flex">
        <a href="{{ route('file.grid') }}" class="btn btn-sm btn-primary me-2"
            data-bs-toggle="tooltip"title="{{ __('Grid View') }}">
            <i class="ti ti-layout-grid text-white"></i>
        </a>
        @if (isset($status) && $status == 1)
            @permission('files create')
                <a class="btn btn-sm btn-primary" data-ajax-popup="true" data-size="md" data-title="{{ __('Create File') }}"
                    data-url="{{ route('files.create') }}" data-toggle="tooltip" title="{{ __('Create') }}">
                    <i class="ti ti-plus"></i>
                </a>
            @endpermission
        @endif
    </div>
@endsection
@section('content')
    <div class="row">
        @if (isset($status) && $status == 1)
            <div class="mt-2" id="multiCollapseExample1">
                <div class="card">
                    <div class="card-body">
                        {{ Form::open(['route' => ['files.index'], 'method' => 'GET', 'id' => 'file-form']) }}
                        <div class="row row-gap align-items-center justify-content-end">
                            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                <div class="btn-box">
                                    {{ Form::label('file_status', __('Status'), ['class' => 'text-type']) }}
                                    {{ Form::select('file_status', ['' => 'Select status'] + $file_status, isset($_GET['file_status']) ? $_GET['file_status'] : '', ['class' => 'form-control']) }}
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                <div class="btn-box">
                                    {{ Form::label('filesharing_type', __('Share Mode'), ['class' => 'text-type']) }}
                                    {{ Form::select('filesharing_type', ['' => 'Select mode'] + $filesharing_type, isset($_GET['filesharing_type']) ? $_GET['filesharing_type'] : '', ['class' => 'form-control']) }}
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                <div class="btn-box">
                                    {{ Form::label('user', __('User'), ['class' => 'text-type']) }}
                                    {{ Form::select('user', $users, isset($_GET['user']) ? $_GET['user'] : '', ['class' => 'form-control', 'placeholder' => __('Select User')]) }}
                                </div>
                            </div>
                            <div class="col-lg-auto col-md-6 float-end mt-md-4 d-flex">
                                <a href="#" class="btn btn-sm btn-primary me-2"
                                    onclick="document.getElementById('file-form').submit(); return false;"
                                    data-bs-toggle="tooltip" title="{{ __('Apply') }}" id="applyfilter"
                                    data-original-title="{{ __('apply') }}">
                                    <span class="btn-inner--icon"><i class="ti ti-search"></i></span>
                                </a>
                                <a href="{{ route('files.index') }}" class="btn btn-sm btn-danger" data-bs-toggle="tooltip"
                                    title="{{ __('Reset') }}" id="clearfilter" data-original-title="{{ __('Reset') }}">
                                    <span class="btn-inner--icon"><i class="ti ti-trash-off text-white-off"></i></span>
                                </a>
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        @endif
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
    <script>
        function copyToClipboard(element) {
            var copyText = element.id;
            document.addEventListener('copy', function(e) {
                e.clipboardData.setData('text/plain', copyText);
                e.preventDefault();
            }, true);
            document.execCommand('copy');
            toastrs('success', 'Link Copy on Clipboard', 'success');
        }
    </script>
@endpush
