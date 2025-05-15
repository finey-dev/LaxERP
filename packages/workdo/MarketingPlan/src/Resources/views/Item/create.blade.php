{{ Form::open(array('route' => array('marketing-plan-item.store',$MarketingPlan->id),'enctype' => 'multipart/form-data','class'=>'needs-validation','novalidate')) }}

    <div class="modal-body">
        <div class="row">
            <div class="form-group">
                {{ Form::label('item_type', __('Item Type'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::select('item_type', $item_types, null, ['class' => 'form-control select', 'required' => 'required', 'placeholder' => 'Select Item Type', 'id' => 'item_type']) }}
            </div>
            <div class="form-group">
                {{ Form::label('item', __('Item'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::select('item', [], null, ['class' => 'form-control select', 'required' => 'required', 'placeholder' => 'Select Item', 'id' => 'item']) }}
            </div>
        </div>
    <div>

    <div class="modal-footer">
        <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{__('Cancel')}}</button>
        <button type="submit" class="btn  btn-primary">{{__('Create')}}</button>
    </div>

{{ Form::close() }}

<script>

    $(document).ready(function() {
        $('#item_type').on('change', function() {
            var item_type = $(this).val();

            if(item_type) {
                $.ajax({
                    url: "{{ route('marketing-plan-item.items') }}",
                    type: "GET",
                    dataType: "json",
                    data: {
                        item_type: item_type
                    },
                    success:function(data) {
                        $('#item').empty();
                        $('#item').append('<option value="">Select Item</option>');
                        $.each(data, function(id, name) {
                            $('#item').append('<option value="'+ id +'">'+ name +'</option>');
                        });
                    }
                });
            } else {
                $('#item').empty();
                $('#item').append('<option value="">Select Item</option>');
            }
        });
    });
</script>


