{{ Form::open(['route' => ['defective.store', $asset->id], 'method' => 'post','class'=>'needs-validation','novalidate']) }}
<div class="modal-body">
    <div class="form-group row">
        <div class="btn-box mb-3">
            <label class="d-block form-label">{{ __('Select Type') }}</label>
            <div class="d-flex align-items-center gap-2">
                <div class="form-check form-check-inline">
                    <input type="radio" class="form-check-input type" id="defectives" name="type"
                        value="defective">
                    <label class="custom-control-label mb-0"
                        for="defectives">{{ __('Defective') }}</label>
                </div>
                <div class="form-check form-check-inline">
                    <input type="radio" class="form-check-input type" id="withdraws" name="type"
                        value="withdraw">
                    <label class="custom-control-label mb-0"
                        for="withdraws">{{ __('Withdraw') }}</label>
                </div>
            </div>
        </div>
        @if (module_is_active('Hrm'))
            <div class="form-group col-md-6 d-none" id="branch-fields">
                {{ Form::label('branch', __('Assets Branch'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::select('branch', $branches,null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Branch')]) }}
            </div>
        @endif

        @if (in_array(\Auth::user()->type, \Auth::user()->not_emp_type))
            <div class="form-group col-md-6 d-none" id="user-fields">
                {{ Form::label('employee_id', __('Employee'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::select('employee_id', $employees, null, ['class' => 'form-control', 'required' => 'required']) }}
            </div>
        @else
            {!! Form::hidden('employee_id', $employees->isNotEmpty() ? $employees->keys()->first() : 0, ['id' => 'employee_id']) !!}
        @endif

        <div class="form-group col-md-6 d-none" id="code-fields">
            {{ Form::label('code', __('Code'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::text('code',null, ['class' => 'form-control ', 'placeholder' => __('Enter Code'), 'required' => 'required']) }}
        </div>

        <div class="form-group col-md-6 d-none" id="date-fields">
            {{ Form::label('date', __('Date'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::date('date', date('Y-m-d'), ['class' => 'form-control', 'placeholder' => __('Select Date'), 'required' => 'required']) }}
        </div>

        <div class="form-group col-md-6 d-none" id="reason-fields">
            {{ Form::label('reason', __('Reason'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::text('reason', null, ['class' => 'form-control', 'placeholder' => __('Enter Reason'), 'required' => 'required']) }}
        </div>

        <div class="form-group col-md-6 d-none" id="quantity-fields">
            {{ Form::label('quantity', __('Quantity'), ['class' => 'form-label']) }}
            {{ Form::number('quantity', null, ['class' => 'form-control','placeholder' => __('Enter Quantity')]) }}
        </div>

        <div class="form-group col-md-12 d-none" id="urgency_level">
            {{ Form::label('urgency_level', __('Urgency Level'), ['class' => 'form-label']) }}
            {{ Form::text('urgency_level', null, ['class' => 'form-control', 'placeholder' => __('Enter Urgency Level')]) }}
        </div>

        <div class="form-group col-12 d-none" id="image-fields">
            {{ Form::label('asset_image', __('Image'), ['class' => 'form-label']) }}
            <div class="choose-file w-100">
                    <input type="file" class="form-control" name="asset_image" id="asset_image"
                        data-filename="asset_image" accept="image/*,.jpeg,.jpg,.png"
                        onchange="document.getElementById('blah').src = window.URL.createObjectURL(this.files[0])">
                    <img id="blah" width="25%" class="mt-3">
                </label>
            </div>
        </div>

    </div>
</div>
<div class="modal-footer">
<div class="text-end">
    <input type="button" value="{{ __('Cancel') }}" class="btn  btn-light me-1" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Create') }}" class="btn  btn-primary">
</div>
</div>
{{ Form::close() }}
<script>

$(document).ready(function() {
    $('input[type=radio][name=type]').change(function() {
        $('.form-group.d-none').addClass('d-none');

        if (this.value === 'defective') {

        // Show the fields specific to 'defective'
            $('#branch-fields').removeClass('d-none');
            $('#user-fields').removeClass('d-none');
            $('#code-fields').removeClass('d-none');
            $('#date-fields').removeClass('d-none');
            $('#reason-fields').removeClass('d-none');
            $('#urgency_level').removeClass('d-none');
            $('#image-fields').removeClass('d-none');
            $('#quantity-fields').removeClass('d-none');

        } else if (this.value === 'withdraw') {
        // Show the fields specific to 'withdraw'

            $('#code-fields').removeClass('d-none');
            $('#branch-fields').removeClass('d-none');
            $('#user-fields').removeClass('d-none');
            $('#date-fields').removeClass('d-none');
            $('#reason-fields').removeClass('d-none');
            $('#quantity-fields').removeClass('d-none');
            $('#urgency_level').addClass('d-none');
            $('#image-fields').addClass('d-none');
        }
    });
});

</script>
