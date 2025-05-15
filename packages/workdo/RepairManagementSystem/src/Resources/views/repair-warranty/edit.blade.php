<div class="bg-none card-box">
    {{ Form::model($repair_warranty, array('route' => array('repair-warranty.update', $repair_warranty->id), 'method' => 'PUT', 'class'=>'needs-validation','novalidate')) }}
    <div class="modal-body">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    {{ Form::label('repair_order_id', __('Repair Order'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::select('repair_order_id', $repair_orders, null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => 'Select Repair Order']) }}
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    {{ Form::label('part_id', __('Part'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::select('part_id', $repair_parts, null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => 'Select Part']) }}
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    {{ Form::label('warranty_number', __('Warranty Number'), ['class' => 'col-form-label']) }}<x-required></x-required>
                    {{ Form::text('warranty_number', null, ['class' => 'form-control','required'=>'required','placeholder' => __('Enter Warranty Number')]) }}
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    {{ Form::label('start_date', __('Start Date'), ['class' => 'form-label']) }}
                    {{ Form::date('start_date', null, ['class' => 'form-control', 'placeholder' => __('Enter Start Date'), 'required' => 'required']) }}
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    {{ Form::label('end_date', __('End Date'), ['class' => 'form-label']) }}
                    {{ Form::date('end_date', null, ['class' => 'form-control', 'placeholder' => __('Enter End Date'), 'required' => 'required']) }}
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    {{ Form::label('warranty_terms', __('Warranty Terms'), ['class' => 'col-form-label']) }}<x-required></x-required>
                    {{ Form::text('warranty_terms', null, ['class' => 'form-control','required'=>'required','placeholder' => __('Enter Warranty Terms')]) }}
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    {{ Form::label('claim_status', __('Claim Status'), ['class' => 'col-form-label']) }}<x-required></x-required>
                    {{ Form::text('claim_status', null, ['class' => 'form-control','required'=>'required','placeholder' => __('Enter Claim Status')]) }}
                </div>
            </div>

        </div>
    </div>
    <div class="modal-footer">
        <input type="button" value="{{ __('Cancel') }}" class="btn btn-light" data-bs-dismiss="modal">
        <input type="submit" value="{{ __('Update') }}" class="btn btn-primary">
    </div>
    {{ Form::close() }}
</div>
<script>
    $(document).ready(function () {
        $('#repair_order_id').change(function () {
            var repairOrderId = $(this).val();
            if (repairOrderId) {
                $.ajax({
                    url: '{{ route("get-repair-parts") }}',
                    type: 'GET',
                    data: {
                        repair_order_id: repairOrderId
                    },
                    success: function (data) {
                        $('#part_id').empty();
                        $('#part_id').append('<option value="">' + 'Select Part' + '</option>');

                        $.each(data, function (key, value) {
                            $('#part_id').append('<option value="' + key + '">' + value + '</option>');
                        });
                    }
                });
            } else {
                $('#part_id').empty();
                $('#part_id').append('<option value="">' + 'Select Part' + '</option>');
            }
        });
    });
</script>
