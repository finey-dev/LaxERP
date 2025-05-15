@extends('layouts.main')

@section('page-title')
    {{ __('Manage Knowledge') }}
@endsection

@section('page-breadcrumb')
    {{ __('Knowledge') }}
@endsection
@push('css')
@include('layouts.includes.datatable-css')
<link href="{{  asset('assets/js/plugins/summernote-0.8.18-dist/summernote-lite.min.css')  }}" rel="stylesheet">
@endpush
@section('page-action')
<div class="d-flex align-items-center">
    @stack('addButtonHook')
    @permission('knowledgebase import')
        <a href="#" class="btn btn-sm btn-primary me-2" data-ajax-popup="true" data-title="{{ __('Import Knowledge') }}"
            data-url="{{ route('knowledge.file.import') }}" data-toggle="tooltip" title="{{ __('Import') }}"><i
                class="ti ti-file-import"></i>
        </a>
    @endpermission
    @permission('knowledgebasecategory manage')
        <a href="{{ route('knowledge-category.index') }}" class="btn btn-sm btn-primary me-2" data-toggle="tooltip"
            title="{{ __('Knowledge Category') }}">
            <i class="ti ti-vector-bezier"></i>
        </a>
    @endpermission
    @permission('knowledgebase create')
        <a class="btn btn-sm btn-primary" data-ajax-popup="true" data-size="md" data-title="{{ __('Create Knowledge') }}"
            data-url="{{ route('support-ticket-knowledge.create') }}" data-toggle="tooltip" title="{{ __('Create') }}">
            <i class="ti ti-plus"></i>
        </a>
    @endpermission
</div>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="card">
                <div class="card-header card-body table-border-style">
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
<script src="{{ asset('assets/js/plugins/summernote-0.8.18-dist/summernote-lite.min.js') }}"></script>
@endpush
