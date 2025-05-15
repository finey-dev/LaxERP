{{ Form::open(['route' => 'collection-center.qty.store', 'class'=>'needs-validation','novalidate']) }}
<div class="modal-body">
    <div class="row">
        <div class="form-group col-md-6">
            {{ Form::label('from_collection_center', __('From Collection Center'),['class'=>'form-label']) }}<x-required></x-required>
            <select class="form-control select" name="from_collection_center" id="from_collection_center" placeholder="Select Collection Center">
                    <option value="{{ $common_data->collection_center_id }}">{{ $common_data->collectionCenter->location_name }}</option>
            </select>
        </div>
        <div class="form-group col-md-6">
            {{Form::label('to_collection_center',__('To Collection Center'),array('class'=>'form-label')) }}<x-required></x-required>
            {{ Form::select('to_collection_center', $to_collection_centers,null, array('class' => 'form-control to_collection_center','required'=>'required','placeholder' => 'Select Collection Center')) }}
        </div>

        <div class="form-group col-md-6" id="qty_div">
            {{ Form::label('quantity', __('Quantity'),['class'=>'form-label']) }}<x-required></x-required>
            {{ Form::number('quantity',null, array('class' => 'form-control','id' => 'quantity','required'=>'required','min' => '1','placeholder'=>'Enter Quantity')) }}
            {{ Form::hidden('type',$type, array('class' => 'form-control')) }}
            {{ Form::hidden('id',$common_data->id, array('class' => 'form-control')) }}
        </div>
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Move')}}" class="btn btn-primary">
</div>
{{ Form::close() }}
<script>
    $(document).ready(function() {
        $('#from_collection_center').on('change', function() {
            var from_collection_center = $(this).val();
            $.ajax({
                url: "{{ route('collection-center.move') }}",
                type: "POST",
                data: {
                    from_collection_center: from_collection_center,
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
                success: function(result) {
                    console.log(result);
                    $('.to_collection_center').html('<option value="">Select Collection Center</option>');
                    $.each(result, function(key, value) {
                        $(".to_collection_center").append('<option value="' + value.id + '">' + value.location_name + '</option>');
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
