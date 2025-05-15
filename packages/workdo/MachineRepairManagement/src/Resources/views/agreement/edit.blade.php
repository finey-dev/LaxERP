{{ Form::model($machineserviceagreement, ['route' => ['machine-service-agreement.update', $machineserviceagreement->id], 'method' => 'PUT', 'class'=>'needs-validation','novalidate']) }}
    <div class="modal-body">
        <div class="row">
            <div class="form-group col-md-6 col-12 mb-3">
                {{ Form::label('customer_id', __('Customer'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::text('customer_id',null, ['class' => 'form-control', 'placeholder' => 'Enter Customer', 'required' => 'required']) }}
            </div>
            <div class="form-group col-md-6 col-12 mb-3">
                {{ Form::label('start_date', __('Start Date'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::text('start_date', null, ['class' => 'form-control flatpickr-input','id'=>'start_date' , 'placeholder' => __('Enter Start Date'), 'required' => 'required']) }}
                <p class="text-danger d-none" id="start_date_validation">{{ __('Start Date is required.') }}</p>
            </div>
            <div class="form-group col-md-6 col-12 mb-3">
                {{ Form::label('end_date', __('End Date'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::text('end_date', null, ['class' => 'form-control flatpickr-input','id'=>'end_date', 'placeholder' => __('Enter End Date'), 'required' => 'required']) }}
                <p class="text-danger d-none" id="end_date_validation">{{ __('End Date is required.') }}</p>
                <p class="text-danger d-none" id="date_comparison_validation">{{ __('End Date cannot be before Start Date.') }}</p>
            </div>
            <div class="form-group col-md-12 col-12 mb-3">
                {{ Form::label('coverage_details', __('Coverage Details'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::textarea('coverage_details', null, ['class' => 'form-control', 'placeholder' => 'Enter Coverage Details', 'required' => 'required', 'rows' => '3']) }}
            </div>
            <div class="form-group col-md-12 col-12 mb-3">
                {{ Form::label('details', __('Details'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::textarea('details', null, ['class' => 'form-control', 'placeholder' => 'Enter Details', 'required' => 'required','rows' => '3']) }}
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
        <button type="submit" id="submit" class="btn btn-primary">{{ __('Update') }}</button>
    </div>
{{ Form::close() }}
<script>
    $(document).ready(function() {
        $("#submit").click(function(event) {
            var startDate = $("#start_date").val();
            var endDate = $("#end_date").val();
            var isValid = true;

            // Check if both dates are entered
            if (!startDate) {
                $('#start_date_validation').removeClass('d-none');
                isValid = false;
            } else {
                $('#start_date_validation').addClass('d-none');
            }

            if (!endDate) {
                $('#end_date_validation').removeClass('d-none');
                isValid = false;
            } else {
                $('#end_date_validation').addClass('d-none');
            }

            // Check if end date is later than or equal to start date
            if (startDate && endDate) {
                var start = new Date(startDate);
                var end = new Date(endDate);

                if (end < start) {
                    $('#date_comparison_validation').removeClass('d-none');
                    isValid = false;
                } else {
                    $('#date_comparison_validation').addClass('d-none');
                }
            }

            // Prevent form submission if validation fails
            if (!isValid) {
                event.preventDefault();
            }
        });
    });

    $(function() {
        $("#submit").click(function(event) {
            var start_date = $("#start_date").val().trim();
            if (start_date === '') {
                $('#start_date_validation').removeClass('d-none');
                event.preventDefault(); // Prevent form submission
            }
            var end_date = $("#end_date").val().trim();
            if (end_date === '') {
                $('#end_date_validation').removeClass('d-none');
                event.preventDefault(); // Prevent form submission
            }
        });
    });

</script>
