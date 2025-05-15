@extends('layouts.main')
@section('page-title')
{{__('Create Bill Of Material ')}}
@endsection
@push('script-page')
@endpush
@section('page-breadcrumb')
{{__('Bill Of Material')}}
@endsection
@section('content')
<div class="row">
    {{ Form::open(['route' => 'bill-of-material.store', 'class' => 'w-100','id' => 'bill_material_form', 'class'=>'needs-validation','novalidate']) }}
    <input type="hidden" name="raw_item_array" id="raw_item_array" value="">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group" id="customer-box">
                            {{ Form::label('item_id', __('Name'), ['class' => 'form-label']) }}<x-required></x-required>
                            <select name="item_id" class="form-control select" id="item_id" required>
                                <option value="">{{ __('Select Raw Material Product') }}</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }} - ({{ $product->type }})</option>
                                @endforeach
                            </select>
                            <div class="text-muted text-xs mt-1">{{ __('Please Add Product Here.') }} <a href="{{ route('raw-material.index') }}">{{ __('Add Product') }}</a></div>
                        </div>
                    </div>
                    <div class="col-md-3">

                        <div class="form-group">
                            {{ Form::label('collection_center_id', __('Collection Center'), ['class' => 'form-label']) }}<x-required></x-required>
                            {{ Form::select('collection_center_id', $collection_centers, null, ['class' => 'form-control collection_center_id', 'required' => 'required', 'placeholder' => __('Select Collection Center')]) }}
                            @if (count($collection_centers) <= 0) <div class="text-muted text-xs mt-1">{{ __('Please create new collection center') }} <a href="{{ route('collection-center.index') }}">{{ __('here') }}</a>
                                @endif
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {{ Form::label('quantity', __('Quantity'), ['class' => 'form-label']) }}<x-required></x-required>
                            <div class="form-icon-user">
                                {{ Form::number('quantity', null, ['class' => 'form-control','id' => 'quantity', 'required' => 'required', 'min' => '1','placeholder'=>'Enter Quantity']) }}
                            </div>
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
        <input type="button" value="{{ __('Cancel') }}" onclick="location.href = '{{ route('bill-of-material.index') }}';" class="btn btn-light me-2">
        <input type="submit" value="{{ __('Create') }}" class="btn  btn-primary">
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
        $(document).on('change', '#quantity', function() {
        var quantity = $(this).val();
        if (quantity === '-1') {
            $(this).val('');
            alert('Please enter a valid quantity.');
        }
    });

        $(document).ready(function() {
            var collection_center_id = $('.collection_center_id').val();
            SectionGet(collection_center_id);
        });
        $(document).on('change', ".collection_center_id", function() {
            var collection_center_id = $(this).val();
            SectionGet(collection_center_id);

        });

        function SectionGet(collection_center_id) {

            $.ajax({
                type: 'post',
                url: "{{ route('center.raw.material') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    collection_center_id: collection_center_id,
                    action: 'create',
                },
                beforeSend: function() {
                    $("#loader").removeClass('d-none');
                },
                success: function(response) {
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
