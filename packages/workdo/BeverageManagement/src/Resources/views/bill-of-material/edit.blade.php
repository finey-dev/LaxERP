@extends('layouts.main')
@section('page-title')
{{__('Edit Bill OF Material')}}
@endsection
@push('script-page')
@endpush
@section('page-breadcrumb')
{{__('Bill OF Material')}}
@endsection
@section('content')
<div class="row">
    {{ Form::model($bill_of_material, ['route' => ['bill-of-material.update', $bill_of_material->id], 'method' => 'PUT', 'class' => 'w-100', 'id' => 'bill_material_form', 'class'=>'needs-validation','novalidate']) }}
    {{ Form::hidden('id', $bill_of_material->id) }}
    <input type="hidden" name="action_type" id="action_type" value="edit">
    <input type="hidden" name="raw_item_array" id="raw_item_array" value="">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            {{ Form::label('item_id', __('Name'), ['class' => 'form-label']) }}<x-required></x-required>
                            <select name="item_id" class="form-control select" id="item_id" required>
                                <option value="">{{ __('Select Raw Material Product') }}</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" {{ $bill_of_material->item_id == $product->id ? 'selected' : '' }}>{{ $product->name }} - ({{ $product->type }})</option>
                                @endforeach
                            </select>
                            <div class="text-muted text-xs">{{ __('Please Add Product Here.') }} <a href="{{ route('raw-material.index') }}">{{ __('Add Product') }}</a></div>
                        </div>
                    </div>
                    <div class="col-md-3">

                        <div class="form-group">
                            {{ Form::label('collection_center_id', __('Collection Center'), ['class' => 'form-label']) }}<x-required></x-required>
                            {{ Form::select('collection_center_id', $collection_centers, null, ['class' => 'form-control collection_center_id', 'required' => 'required', 'placeholder' => __('Select Collection Center')]) }}
                            @if (count($collection_centers) <= 0) <div class="text-muted text-xs">{{ __('Please create new collection center') }} <a href="{{ route('collection-center.index') }}">{{ __('here') }}</a>
                                @endif
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {{ Form::label('main_quantity', __('Quantity'), ['class' => 'form-label']) }}<x-required></x-required>
                            <div class="form-icon-user">
                                {{ Form::number('main_quantity', $bill_of_material->quantity, ['class' => 'form-control','id' => 'quantity', 'required' => 'required', 'min' => 0,'placeholder'=>'Enter Quantity']) }}
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
        <input type="submit" value="{{ __('Update') }}" class="btn btn-primary">
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
            SectionGet(collection_center_id,action);
        });
        $(document).on('change', "#collection_center_id", function() {
            var collection_center_id = $(this).val();
            var action = 'edit';
            SectionGet(collection_center_id,action);
        });

        function SectionGet(collection_center_id,action) {
            var bill_material_id = {{$bill_of_material->id}};

            $.ajax({
                type: 'POST',
                url: "{{ route('center.raw.material') }}",
                data: {
                    collection_center_id: collection_center_id,
                    action: action,
                    bill_material_id: bill_material_id,
                    _token: "{{ csrf_token() }}"
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
