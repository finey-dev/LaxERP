@extends('layouts.main')
@section('page-title')
{{__('Edit Packaging')}}
@endsection
@push('script-page')
@endpush
@section('page-breadcrumb')
{{__('Packaging')}}
@endsection
@php
$company_settings = getCompanyAllSetting();
@endphp
@section('content')
<div class="row">
    {{ Form::model($packaging, ['route' => ['packaging.update', $packaging->id], 'method' => 'PUT', 'class' => 'w-100', 'id' => 'packaging_form', 'class'=>'needs-validation','novalidate']) }}
    <input type="hidden" name="raw_item_array" id="raw_item_array" value="">
    <input type="hidden" name="action_type" id="action_type" value="edit">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            {{ Form::label('manufacturing_id', __('Manufacture'), ['class' => 'form-label']) }}<x-required></x-required>
                            <select class="form-control manufacturing" name="manufacturing_id" id="manufacturing" placeholder="Select Manufacture" required>
                                <option value="">{{__('Select Manufacture')}}</option>
                                @foreach($manufacturings as $manufacturing)
                                <option value="{{ $manufacturing->id }}" @if($manufacturing->id == $packaging->manufacturing_id) selected @endif>{{ $manufacturing->productService->name }}</option>
                                @endforeach
                            </select>
                            @if (count($manufacturings) <= 0) <div class="text-muted text-xs">{{ __('Please create new Manufacture') }} <a href="{{ route('bill-of-material.index') }}">{{ __('here') }}</a>
                                @endif
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group" id="customer-box">
                            {{ Form::label('collection_center_id', __('Collection Center'), ['class' => 'form-label']) }}<x-required></x-required>
                            {{ Form::select('collection_center_id', $collection_centers, null, ['class' => 'form-control select', 'required' => 'required', 'placeholder' => 'Select Collection Center']) }}
                            @if (count($collection_centers) <= 0) <div class="text-muted text-xs">{{ __('Please create new collection center') }} <a href="{{ route('collection-center.index') }}">{{ __('here') }}</a>
                                @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="loader" class="card card-flush">
        <div class="card-body">
            <div class="row">
                <img class="loader" src="{{ asset('public/images/loader.gif') }}" alt="">
            </div>
        </div>
    </div>
    <div class="col-12 section_div">

    </div>
    <div class="modal-footer">
        <input type="button" value="{{ __('Cancel') }}" onclick="location.href = '{{ route('packaging.index') }}';" class="btn btn-light me-2">
        <input type="submit" value="{{ __('Update') }}" class="btn  btn-primary">
    </div>
    {{ Form::close() }}
</div>
@endsection
@push('scripts')
<script src="{{ asset('js/jquery.min.js') }}"></script>
<script src="{{ asset('js/jquery.repeater.min.js') }}"></script>
<script src="{{ asset('js/jquery-searchbox.js') }}"></script>
<script>
    $(document).ready(function() {

        $(document).ready(function() {
            var collection_center_id = $('#collection_center_id').val();
            var action = $("#action_type").val();
            SectionGet(collection_center_id, action);
        });
        $(document).on('change', "#collection_center_id", function() {
            var collection_center_id = $(this).val();
            var action = 'edit';
            SectionGet(collection_center_id, action);
        });

        function SectionGet(collection_center_id, action) {
            var packaging_id = {{$packaging->id}};

            $.ajax({
                type: 'POST',
                url: "{{ route('package.center.raw.material') }}",
                data: {
                    collection_center_id: collection_center_id,
                    action: action,
                    packaging_id: packaging_id,
                    _token: "{{ csrf_token() }}"
                },
                beforeSend: function() {
                    $("#loader").removeClass('d-none');
                },
                success: function(response) {
                    console.log(response);
                    if (response != false) {
                        $('.section_div').html(response.html);
                        $("#loader").addClass('d-none');
                        // for item SearchBox ( this function is  custom Js )
                        JsSearchBox();
                    } else {
                        $('.section_div').html('');
                        toastrs('Error', 'Something went wrong please try again !', 'error');
                    }
                },
            });
        }
    });
</script>
@endpush
