{{ Form::open(['route' => 'add-stock.packaging.store', 'class'=>'needs-validation','novalidate']) }}
<div class="modal-body">
    <div class="row">
        {{ Form::hidden('packaging_id',$packaging->id, array('class' => 'form-control','id' => 'packaging_id')) }}

        <div class="form-group col-md-6">
            {{Form::label('to_collection_center',__('To Collection Center'),array('class'=>'form-label')) }}<x-required></x-required>
            {{ Form::select('to_collection_center', $collection_centers,null, array('class' => 'form-control select','id' => 'to_collection_center' ,'required'=>'required')) }}
        </div>

        <div class="form-group col-md-6">
            {{Form::label('warehouse',__('Warehouse'),array('class'=>'form-label')) }}<x-required></x-required>
            {{ Form::select('warehouse', $warehouses,null, array('class' => 'form-control select','id' => 'warehouse' ,'required'=>'required')) }}
        </div>

        <div class="form-group col-md-6 item_id">
            {{ Form::label('item_id', __('Items'),['class'=>'form-label']) }}<x-required></x-required>
            {!! Form::select('item_id', ['' => 'Select Item '], null, ['class' => 'form-control', 'id' => 'item_id','required' => 'required']) !!}
        </div>

        <div class="form-group col-md-6" id="qty_div">
            {{ Form::label('quantity', __('Quantity'),['class'=>'form-label']) }}<x-required></x-required>
            {{ Form::number('quantity',null, array('class' => 'form-control','id' => 'quantity','min' => '1','required' => 'required','placeholder'=>'Enter Quantity')) }}
        </div>
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Add')}}" class="btn btn-primary">
</div>
{{ Form::close() }}
<script>
    $(document).ready(function() {
        $('#warehouse').on('change', function() {
            var warehouse = $(this).val();
            $.ajax({
                url: "{{ route('warehouse.item') }}",
                type: "POST",
                data: {
                    warehouse: warehouse,
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
                success: function(result) {
                    console.log(result);
                    $('#item_id').html('<option value="">Select Item</option>');
                    $.each(result, function(key, value) {
                        $("#item_id").append('<option value="' + value.id + '">' + value.name + '</option>');
                    });
                }
            });
        });
    });
    $(document).ready(function() {
        $('#quantity').on('change', function() {
            var quantity = $(this).val();
            if (quantity === '-1') {
                $(this).val('');
                alert('Please enter a valid quantity.');
            }
        });
    });
</script>