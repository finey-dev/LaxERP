@extends('layouts.main')
@section('page-title')
{{__('Manage Collection Centers')}}
@endsection
@push('css')
@include('layouts.includes.datatable-css')
@endpush
@section('page-breadcrumb')
{{__('Manage Collection Centers')}}
@endsection
@section('page-action')
<div>
    @permission('collection center create')
    <a data-size="md" data-url="{{ route('collection-center.create') }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{__('Create')}}" data-title="{{__('Create Collection Center')}}" class="btn btn-sm btn-primary">
        <i class="ti ti-plus"></i>
    </a>
    @endpermission
</div>
@endsection

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
<!-- [ Main Content ] end -->
@endsection
@push('scripts')
@include('layouts.includes.datatable-js')
{{ $dataTable->scripts() }}
@endpush