@extends('layouts.main')

@section('page-title')
    {{__('Manage Form Builder')}}
@endsection

@push('css')
    @include('layouts.includes.datatable-css')
    <link rel="stylesheet" href="{{ asset('packages/workdo/Sales/src/Resources/assets/css/custom.css') }}">
@endpush

@section('page-action')
@permission('formbuilder create')
    <div class="row align-items-center m-1">
        <div class="col-auto pe-0">
            <a class="btn btn-sm btn-primary btn-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Create')}}" data-ajax-popup="true" data-size="md" data-title="{{__('Create Form')}}" data-url="{{route('form_builder.create')}}"><i class="ti ti-plus text-white"></i></a>
        </div>
    </div>
@endpermission

@endsection

@section('page-breadcrumb')
   {{__('Form Builder')}}
@endsection


@section('content')
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <h5></h5>
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

    <script>
        $(document).on("click",".cp_link",function() {
            var value = $(this).attr('data-link');
                var $temp = $("<input>");
                $("body").append($temp);
                $temp.val(value).select();
                document.execCommand("copy");
                $temp.remove();
                toastrs('success', "{{__('Link Copy on Clipboard')}}", 'success')
        });
    </script>
@endpush
