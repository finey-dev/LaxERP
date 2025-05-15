{{ Form::model($salesAgent, ['route' => ['salesagents.update', $user->id], 'method' => 'patch','class'=>'needs-validation','novalidate']) }}
<div class="modal-body">
    <input type="hidden" name="user_id" value="{{ $user->id }}">
    <h6 class="sub-title">{{ __('Basic Info') }}</h6>
    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-6">
            <div class="form-group">
                {{ Form::label('name', __('Name'), ['class' => 'form-label']) }}<x-required></x-required>
                <div class="form-icon-user">
                    <span><i class="ti ti-address-card"></i></span>
                    {{ Form::text('name', !empty($salesAgent->name) ? $salesAgent->name : $user->name, ['class' => 'form-control', 'required' => 'required', 'placeholder' => 'Enter Name']) }}
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-6">
            <div class="form-group">
                {{ Form::label('contact', __('Contact'), ['class' => 'form-label']) }}<x-required></x-required>
                <div class="form-icon-user">
                    <span><i class="ti ti-mobile-alt"></i></span>
                    {{ Form::text('contact', !empty($salesAgent->contact) ? $salesAgent->contact : $user->mobile_no, ['class' => 'form-control', 'required' => 'required', 'placeholder' => 'Enter Contact']) }}
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-6">
            <div class="form-group">
                {{ Form::label('email', __('Email'), ['class' => 'form-label']) }}<x-required></x-required>
                <div class="form-icon-user">
                    {{ Form::email('email', !empty($salesAgent->email) ? $salesAgent->email : $user->email, ['class' => 'form-control', 'required' => 'required', 'placeholder' => 'Enter Email']) }}
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-6">
            <div class="form-group">
                {{ Form::label('tax_number', __('Tax Number'), ['class' => 'form-label']) }}
                <div class="form-icon-user">
                    <span><i class="ti ti-crosshairs"></i></span>
                    {{ Form::text('tax_number', null, ['class' => 'form-control', 'placeholder' => 'Enter Tax Number']) }}
                </div>
            </div>
        </div>
        @if (module_is_active('CustomField') && !$customFields->isEmpty())
            <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="form-group">
                    <div class="tab-pane fade show" id="tab-2" role="tabpanel">
                        @include('custom-field::formBuilder', [
                            'fildedata' => $salesAgent->customField,
                        ])
                    </div>
                </div>
            </div>
        @endif
    </div>
    <h6 class="sub-title">{{ __('BIlling Address') }}</h6>
    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-6">
            <div class="form-group">
                {{ Form::label('billing_name', __('Name'), ['class' => 'form-label']) }}<x-required></x-required>
                <div class="form-icon-user">
                    {{ Form::text('billing_name', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => 'Enter Name']) }}
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-6">
            <div class="form-group">
                {{ Form::label('billing_phone', __('Phone'), ['class' => 'form-label']) }}<x-required></x-required>
                <div class="form-icon-user">
                    {{ Form::text('billing_phone', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => 'Enter Phone']) }}
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('billing_address', __('Address'), ['class' => 'form-label']) }}<x-required></x-required>
                <div class="input-group">
                    {{ Form::textarea('billing_address', null, ['class' => 'form-control', 'rows' => 3, 'required' => 'required', 'placeholder' => 'Enter Address']) }}
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-6">
            <div class="form-group">
                {{ Form::label('billing_city', __('City'), ['class' => 'form-label']) }}<x-required></x-required>
                <div class="form-icon-user">
                    {{ Form::text('billing_city', null, ['class' => 'form-control', 'required' => 'required','placeholder' => 'Enter City']) }}
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-6">
            <div class="form-group">
                {{ Form::label('billing_state', __('State'), ['class' => 'form-label']) }}<x-required></x-required>
                <div class="form-icon-user">
                    {{ Form::text('billing_state', null, ['class' => 'form-control', 'required' => 'required','placeholder' => 'Enter State']) }}
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-6">
            <div class="form-group">
                {{ Form::label('billing_country', __('Country'), ['class' => 'form-label']) }}<x-required></x-required>
                <div class="form-icon-user">
                    {{ Form::text('billing_country', null, ['class' => 'form-control', 'required' => 'required','placeholder' => 'Enter Country']) }}
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-6">
            <div class="form-group">
                {{ Form::label('billing_zip', __('Zip Code'), ['class' => 'form-label']) }}<x-required></x-required>
                <div class="form-icon-user">
                    {{ Form::text('billing_zip', null, ['class' => 'form-control', 'required' => 'required','placeholder' => 'Enter Zip Code']) }}
                </div>
            </div>
        </div>

    </div>
    <div class="col-12">
        <div class="action-btn float-end">
            <a class="mx-3 btn btn-sm align-items-center text-white bg-primary" id="billing_data"
                data-bs-toggle="tooltip" data-placement="top" title="{{ __('Same As Billing Address') }}"><i
                    class="fas fa-copy"></i></a>
            <span class="clearfix"></span>
        </div>
    </div>
    <hr>

    <h6 class="sub-title">{{ __('Shipping Address') }}</h6>
    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-6">
            <div class="form-group">
                {{ Form::label('shipping_name', __('Name'), ['class' => 'form-label']) }}<x-required></x-required>
                <div class="form-icon-user">
                    {{ Form::text('shipping_name', null, ['class' => 'form-control', 'required' => 'required','placeholder' => 'Enter Name']) }}
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-6">
            <div class="form-group">
                {{ Form::label('shipping_phone', __('Phone'), ['class' => 'form-label']) }}<x-required></x-required>
                <div class="form-icon-user">
                    {{ Form::text('shipping_phone', null, ['class' => 'form-control', 'required' => 'required','placeholder' => 'Enter Phone']) }}
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('shipping_address', __('Address'), ['class' => 'form-label']) }}<x-required></x-required>
                <div class="input-group">
                    {{ Form::textarea('shipping_address', null, ['class' => 'form-control', 'rows' => 3, 'required' => 'required','placeholder' => 'Enter Address']) }}
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-6">
            <div class="form-group">
                {{ Form::label('shipping_city', __('City'), ['class' => 'form-label']) }}<x-required></x-required>
                <div class="form-icon-user">
                    {{ Form::text('shipping_city', null, ['class' => 'form-control', 'required' => 'required','placeholder' => 'Enter City']) }}
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-6">
            <div class="form-group">
                {{ Form::label('shipping_state', __('State'), ['class' => 'form-label']) }}<x-required></x-required>
                <div class="form-icon-user">
                    {{ Form::text('shipping_state', null, ['class' => 'form-control', 'required' => 'required','placeholder' => 'Enter State']) }}
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-6">
            <div class="form-group">
                {{ Form::label('shipping_country', __('Country'), ['class' => 'form-label']) }}<x-required></x-required>
                <div class="form-icon-user">
                    {{ Form::text('shipping_country', null, ['class' => 'form-control', 'required' => 'required','placeholder' => 'Enter Country']) }}
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-6">
            <div class="form-group">
                {{ Form::label('shipping_zip', __('Zip Code'), ['class' => 'form-label']) }}<x-required></x-required>
                <div class="form-icon-user">
                    {{ Form::text('shipping_zip', null, ['class' => 'form-control', 'required' => 'required','placeholder' => 'Enter Zip Code']) }}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
    {{ Form::submit(__('Update'), ['class' => 'btn  btn-primary']) }}
</div>

{{ Form::close() }}
