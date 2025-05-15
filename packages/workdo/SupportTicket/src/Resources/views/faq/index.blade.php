@extends('layouts.main')

@section('page-title')
    {{ __('Manage FAQ') }}
@endsection
@section('page-breadcrumb')
    {{ __('FAQ') }}
@endsection
@section('page-action')
<div class="d-flex align-items-center">
    @permission('faq import')
        <a href="#" class="btn btn-sm btn-primary me-2" data-ajax-popup="true" data-title="{{ __('Import Faq') }}"
            data-url="{{ route('faq.file.import') }}" data-toggle="tooltip" title="{{ __('Import') }}"><i
                class="ti ti-file-import"></i>
        </a>
    @endpermission
    @permission('faq create')
        <a data-url="{{ route('support-ticket-faq.create') }}" data-size="md" title="{{ __('Create') }}"
            data-title="{{ __('Create FAQ') }}" data-ajax-popup="true" class="btn btn-sm btn-primary btn-icon"
            data-bs-toggle="tooltip" data-bs-placement="top" title=""><i class="ti ti-plus text-white"></i>
        </a>
    @endpermission
</div>
@endsection
@push('css')
@include('layouts.includes.datatable-css')
<link href="{{  asset('assets/js/plugins/summernote-0.8.18-dist/summernote-lite.min.css')  }}" rel="stylesheet">
@endpush
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
