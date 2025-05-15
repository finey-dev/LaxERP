<div class="bg-none card-box">
    {{ Form::open(['route' => 'repair.request.store','class'=>'needs-validation','novalidate']) }}
    <div class="modal-body">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    {{ Form::label('product_name', __('Product Name'), ['class' => 'col-form-label']) }}<x-required></x-required>
                    {{ Form::text('product_name', null, ['class' => 'form-control','required'=>'required','placeholder' => __('Enter Product Name')]) }}
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    {{ Form::label('product_quantity', __('Product Quantity'), ['class' => 'col-form-label']) }}<x-required></x-required>
                    {{ Form::number('product_quantity', null, ['class' => 'form-control','required'=>'required','placeholder' => __('Enter Product Quantity')]) }}
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    {{ Form::label('customer_name', __('Customer Name'), ['class' => 'col-form-label']) }}<x-required></x-required>
                    {{ Form::text('customer_name', null, ['class' => 'form-control','required'=>'required','placeholder' => __('Enter Customer Name')]) }}
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    {{ Form::label('customer_email', __('Customer Email'), ['class' => 'col-form-label']) }}<x-required></x-required>
                    {{ Form::email('customer_email', null, ['class' => 'form-control','required'=>'required','placeholder' => __('Enter Customer Email')]) }}
                </div>
            </div>
            <div class="col-md-6 mt-2">
                <x-mobile label="Customer Mobile No" name="customer_mobile_no" placeholder="Enter Customer Mobile No" required="true" class="form-control mt-2"></x-mobile>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    {{ Form::label('date', __('Date'), ['class' => 'col-form-label']) }}<x-required></x-required>
                    {{ Form::date('date', null, ['class' => 'form-control','required'=>'required','id'=> 'date','placeholder' => __('Enter Date')]) }}
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    {{ Form::label('expiry_date', __('Expiry Date'), ['class' => 'col-form-label']) }}<x-required></x-required>
                    {{ Form::date('expiry_date', null, ['class' => 'form-control','required'=>'required','id'=> 'expiry_date','placeholder' => __('Enter Expiry Date')]) }}
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    {{ Form::label('repair_technician', __('Technician'), ['class' => 'col-form-label']) }}<x-required></x-required>
                    {{ Form::select('repair_technician', $repair_technicians, null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => 'Select Technician']) }}
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <input type="button" value="{{ __('Cancel') }}" class="btn btn-light" data-bs-dismiss="modal">
        <input type="submit" value="{{ __('Create') }}" class="btn btn-primary">
    </div>
    {{ Form::close() }}
</div>
<script>
    $(document).ready(function() {
        var dtToday = new Date();
        var month = dtToday.getMonth() + 1;
        var day = dtToday.getDate();
        var year = dtToday.getFullYear();
        if (month < 10)
            month = '0' + month.toString();
        if (day < 10)
            day = '0' + day.toString();

        var maxDate = year + '-' + month + '-' + day;
        $('#date').attr('min', maxDate);
        $('#expiry_date').attr('min', maxDate);

        $("#date").on("change", function() {
            $("#expiry_date").attr("min", $(this).val());
        });
    });
</script>
