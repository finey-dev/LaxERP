{{ Form::open(['url' => 'salesaccount', 'method' => 'post','class'=>'needs-validation','novalidate', 'enctype' => 'multipart/form-data']) }}
<div class="modal-body">
    <div class="text-end">
        @if (module_is_active('AIAssistant'))
            @include('aiassistant::ai.generate_ai_btn', [
                'template_module' => 'account',
                'module' => 'Sales',
            ])
        @endif
    </div>
    <div class="row">
        <div class="col-6">
            <div class="form-group">
                {{ Form::label('name', __('Name'), ['class' => 'form-label']) }} <x-required></x-required>
                {{ Form::text('name', null, ['class' => 'form-control', 'placeholder' => __('Enter Name'), 'required' => 'required']) }}
            </div>
        </div>
        <div class="col-6">
            <div class="form-group">
                {{ Form::label('email', __('Email'), ['class' => 'form-label']) }} <x-required></x-required>
                {{ Form::email('email', null, ['class' => 'form-control', 'placeholder' => __('Enter Email'), 'required' => 'required']) }}
            </div>
        </div>
        <div class="col-6">
            <x-mobile label="Phone" name="phone" required="required" placeholder="{{__('Enter Phone')}}"></x-mobile>

        </div>
        <div class="col-6">
            <div class="form-group">
                {{ Form::label('website', __('Website'), ['class' => 'form-label']) }} <x-required></x-required>
                {{ Form::text('website', null, ['class' => 'form-control', 'placeholder' => __('Enter Website'), 'required' => 'required']) }}
            </div>
        </div>
        <div class="col-12">
            <div class="action-btn float-end">
                <a class="mx-3 btn btn-sm align-items-center text-white bg-primary" id="billing_data"
                    data-toggle="tooltip" title="{{ __('Same As Billing Address') }}"><i
                        class="fas fa-copy"></i></a>
                <span class="clearfix"></span>
            </div>
        </div>
        <div class="col-6">
            <div class="form-group">
                {{ Form::label('billingaddress', __('Billing Address'), ['class' => 'form-label']) }} <x-required></x-required>
                {{ Form::text('billing_address', null, ['class' => 'form-control', 'placeholder' => __('Billing Address'), 'required' => 'required']) }}
            </div>
        </div>
        <div class="col-6">
            <div class="form-group">
                {{ Form::label('shippingaddress', __('Shipping Address'), ['class' => 'form-label']) }} <x-required></x-required>
                {{ Form::text('shipping_address', null, ['class' => 'form-control', 'placeholder' => __('Shipping Address'), 'required' => 'required']) }}
            </div>
        </div>
        <div class="col-3">
            <div class="form-group">
                {{ Form::text('billing_city', null, ['class' => 'form-control', 'placeholder' => __('Billing City'), 'required' => 'required']) }}
            </div>
        </div>
        <div class="col-3">
            <div class="form-group">
                {{ Form::text('billing_state', null, ['class' => 'form-control', 'placeholder' => __('Billing State'), 'required' => 'required']) }}
            </div>
        </div>
        <div class="col-3">
            <div class="form-group">
                {{ Form::text('shipping_city', null, ['class' => 'form-control', 'placeholder' => __('Shipping City'), 'required' => 'required']) }}
            </div>
        </div>
        <div class="col-3">
            <div class="form-group">
                {{ Form::text('shipping_state', null, ['class' => 'form-control', 'placeholder' => __('Shipping State'), 'required' => 'required']) }}
            </div>
        </div>
        <div class="col-3">
            <div class="form-group">
                {{ Form::text('billing_country', null, ['class' => 'form-control', 'placeholder' => __('Billing Country'), 'required' => 'required']) }}
            </div>
        </div>
        <div class="col-3">
            <div class="form-group">
                {{ Form::number('billing_postalcode', null, ['class' => 'form-control', 'placeholder' => __('Billing Postal Code'), 'required' => 'required']) }}
            </div>
        </div>
        <div class="col-3">
            <div class="form-group">
                {{ Form::text('shipping_country', null, ['class' => 'form-control', 'placeholder' => __('Shipping Country'), 'required' => 'required']) }}
            </div>
        </div>
        <div class="col-3">
            <div class="form-group">
                {{ Form::number('shipping_postalcode', null, ['class' => 'form-control', 'placeholder' => __('Shipping Postal Code'), 'required' => 'required']) }}
            </div>
        </div>
        <div class="col-12">
            <hr class="mt-2 mb-2">
            <h4>{{ __('Detail') }}</h4>
        </div>
        <div class="col-6">
            <div class="form-group">
                {{ Form::label('user', __('Assign User'), ['class' => 'form-label']) }}
                {!! Form::select('user', $user, null, ['class' => 'form-control']) !!}
            </div>
        </div>
        <div class="col-6">
            <div class="form-group">
                {{ Form::label('type', __('Type'), ['class' => 'form-label']) }} <x-required></x-required>
                {!! Form::select('type', $accountype, null, [
                    'class' => 'form-control',
                    'placeholder' => 'Select Type',
                    'required' => 'required',
                ]) !!}
            </div>
        </div>
        <div class="col-6">
            <div class="form-group">
                {{ Form::label('industry', __('Industry'), ['class' => 'form-label']) }} <x-required></x-required>
                {!! Form::select('industry', $industry, null, [
                    'class' => 'form-control',
                    'placeholder' => 'Select Industry',
                    'required' => 'required',
                ]) !!}
            </div>
        </div>
        <div class="col-6">
            <div class="form-group">
                {{ Form::label('document_id', __('Document'), ['class' => 'form-label']) }}
                {!! Form::select('document_id', $document_id, null, ['class' => 'form-control']) !!}
            </div>
        </div>
        <div class="col-12">
            <div class="form-group">
                {{ Form::label('Description', __('Description'), ['class' => 'form-label']) }}
                {{ Form::textarea('description', null, ['class' => 'form-control', 'rows' => 3, 'placeholder' => __('Enter Description')]) }}
            </div>
        </div>
        @if (module_is_active('CustomField') && !$customFields->isEmpty())
            <div class="col-6">
                <div class="tab-pane fade show" id="tab-2" role="tabpanel">
                    @include('custom-field::formBuilder')
                </div>
            </div>
        @endif
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
    {{ Form::submit(__('Create'), ['class' => 'btn btn-primary ']) }}
</div>
{{ Form::close() }}

    <script>
        $(document).on('click', '#billing_data', function() {
            $("[name='shipping_address']").val($("[name='billing_address']").val());
            $("[name='shipping_city']").val($("[name='billing_city']").val());
            $("[name='shipping_state']").val($("[name='billing_state']").val());
            $("[name='shipping_country']").val($("[name='billing_country']").val());
            $("[name='shipping_postalcode']").val($("[name='billing_postalcode']").val());
        })
    </script>
