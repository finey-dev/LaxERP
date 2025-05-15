{{ Form::open(['route' => 'courier-returns.store', 'class' => 'needs-validation', 'novalidate']) }}
<div class="modal-body">
    <div class="row">
        <div class="form-group col-md-6">
            {{ Form::label('package_id', __('Package'), ['class' => 'form-label']) }}<x-required></x-required>
            <select name="package_id" class="form-control select" required>
                <option value="">{{ __('Select Package') }}</option>
                @foreach ($pacakges as $pacakge)
                    <option value="{{ $pacakge->id }}">{{ $pacakge->package_title }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('customer_id', __('Customer'), ['class' => 'form-label']) }}<x-required></x-required>
            <select name="customer_id" class="form-control select" required>
                <option value="">{{ __('Select Customer') }}</option>
                @foreach ($customers as $customer)
                    <option value="{{ $customer->id }}">{{ $customer->receiver_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('return_date', __('Return Date'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::date('return_date', '', ['class' => 'form-control', 'required' => 'required']) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('status', __('Status'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::select('status', ['' => __('Select Status'), 0 => __('Pending'), 1 => __('Processed')], null, ['class' => 'form-control select', 'required' => 'required']) }}
            @error('status')
                <small class="invalid-name" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </small>
            @enderror
        </div>
        <div class="form-group col-md-12">
            {{ Form::label('return_reason', __('Reason'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::textarea('return_reason', '', ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Reason'),'rows'=>3]) }}
        </div>



    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{ __('Cancel') }}" class="btn  btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Create') }}" class="btn  btn-primary">
</div>
{{ Form::close() }}
