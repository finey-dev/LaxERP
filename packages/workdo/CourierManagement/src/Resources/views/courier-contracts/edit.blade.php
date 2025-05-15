{{ Form::model($courier_contracts, ['route' => ['courier-contracts.update', $courier_contracts->id], 'method' => 'PUT', 'class' => 'needs-validation', 'novalidate']) }}
<div class="modal-body">
    <div class="row">
        <div class="form-group col-md-6">
            {{ Form::label('customer_name', __('Customer Name'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::text('customer_name', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Customer Name')]) }}

        </div>
        <div class="form-group col-md-6">
                {{ Form::label('status', __('Status'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::select('status', [null => __('Select Status'), 1 => __('Active'), 0 => __('Expired')], null, ['class' => 'form-control select', 'required' => 'required']) }}
                @error('status')
                    <small class="invalid-name" role="alert">
                        <strong class="text-danger">{{ $message }}</strong>
                    </small>
                @enderror
        </div>
        <div class="form-group col-md-12">
            {{ Form::label('service_type', __('Service Type'), ['class' => 'form-label']) }}<x-required></x-required>
            <select name="service_type" class="form-control select" id="service_type" required>
                <option value="">{{ __('Select Service Type') }}</option>
                @foreach ($serviceType as $service)
                    <option value="{{ $service->id }}"
                        {{ isset($courier_contracts) && $courier_contracts->service_type == $service->id ? 'selected' : '' }}>
                        {{ $service->service_type }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('start_date', __('Start Date'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::date('start_date', null, ['class' => 'form-control', 'required' => 'required']) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('end_date', __('End Date'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::date('end_date', null, ['class' => 'form-control', 'required' => 'required']) }}
        </div>
        <div class="form-group col-md-12 page_content">
            {{ Form::label('contract_details', __('Contract Details'), ['class' => 'form-label']) }}<x-required></x-required>
            {!! Form::textarea('contract_details', null, [
                'class' => 'summernote form-control',
                'rows' => '5',
            ]) !!}
        </div>
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{ __('Cancel') }}" class="btn  btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Update') }}" class="btn  btn-primary">
</div>
{{ Form::close() }}
