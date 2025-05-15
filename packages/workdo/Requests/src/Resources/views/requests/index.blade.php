
@extends('layouts.main')

@section('page-title')
   {{ __("Manage Requests") }}
@endsection
@section('page-breadcrumb')
   {{ __("Requests") }}
@endsection
@push('css')
@include('layouts.includes.datatable-css')
<link rel="stylesheet" href="{{ asset('packages/workdo/Requests/src/Resources/assets/css/custome.css')}}">
@endpush

@section('page-action')
<div class="d-flex">
@permission('Requests create')
        <a  data-url="{{ route('requests.create') }}" data-ajax-popup="true"
            data-title="{{ __('Create Requests') }}" data-bs-toggle="tooltip" title="" class="btn me-2 btn-sm btn-primary"
            data-bs-original-title="{{ __('Create') }}" data-size="lg">
            <i class="ti ti-plus"></i>
        </a>
@endpermission
    <a href="#" class="btn btn-sm btn-primary filter" data-toggle="tooltip" title="{{ __('Filter') }}">
        <i class="ti ti-filter"></i>
    </a>
</div>
@endsection
@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card d-none " id="show_filter">
            <div class="card-body">
                <div class="row d-flex align-items-center justify-content-end" >
                    <div class="form-group col-md-3">
                        {{ Form::label('category', __('Category'), ['class' => 'form-label']) }}
                        <div class="input-group">
                            {{ Form::select('category', $requestscategory, isset($_GET['category']) ? $_GET['category'] : '', ['class' => 'form-control category ', 'placeholder' => 'Select Category']) }}
                        </div>
                    </div>
                    <div class="form-group col-md-3">
                        {{ Form::label('subcategory', __('Sub Category'), ['class' => 'form-label']) }}
                        <div class="input-group">
                            {{ Form::select('subcategory', $requestsubcategory, isset($_GET['subcategory']) ? $_GET['subcategory'] : '', ['class' => 'form-control subcategory', 'placeholder' => 'Select SubCategory']) }}
                        </div>
                    </div>
                    <div class="col-1 d-flex">
                        <a  class="btn btn-sm btn-primary me-2"
                        data-bs-toggle="tooltip" title="{{ __('Apply') }}" id="applyfilter"
                        data-original-title="{{ __('apply') }}">
                        <span class="btn-inner--icon"><i class="ti ti-search"></i></span>
                    </a>
                    <a href="#!" class="btn btn-sm btn-danger "
                        data-bs-toggle="tooltip" title="{{ __('Reset') }}" id="clearfilter"
                        data-original-title="{{ __('Reset') }}">
                        <span class="btn-inner--icon"><i class="ti ti-trash-off text-white-off "></i></span>
                    </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
    <script>
        $(document).on("click",".cp_link",function() {
            var value = $(this).attr('data-link');
                var $temp = $("<input>");
                $("body").append($temp);
                $temp.val(value).select();
                document.execCommand("copy");
                $temp.remove();
                toastrs('success', '{{__('Link Copy on Clipboard')}}', 'success')
        });
        $(".filter").click(function() {
            $("#show_filter").toggleClass('d-none');
        });

    $(document).on('change', '.category', function() {
        var category = $(this).val();
        $.ajax({
            url: '{{ route('request.category') }}',
            type: 'POST',
            data: {
                "category": category
            },
            success: function(response) {
                $('.subcategory').empty();
                $('.subcategory').append('<option value="0">Select SubCategory </option>');
                $.each(response, function(key, value) {
                    $('.subcategory').append('<option value="' + value.id + '">' +
                        value.name + '</option>');


                });
            },
            error: function(xhr, status, error) {
                console.error("AJAX error:", error); // Debugging line
            }
        });
    });

    </script>
@endpush

