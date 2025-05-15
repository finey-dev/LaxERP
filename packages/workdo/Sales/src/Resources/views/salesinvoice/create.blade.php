{{ Form::open(['url' => 'salesinvoice', 'method' => 'post','class'=>'needs-validation','novalidate', 'enctype' => 'multipart/form-data']) }}
<div class="modal-body">
    <div class="text-end">
        @if (module_is_active('AIAssistant'))
            @include('aiassistant::ai.generate_ai_btn',['template_module' => 'salesinvoice','module'=>'Sales'])
        @endif
    </div>
    <div class="row">
        <div class="col-6">
            <div class="form-group">
                {{ Form::label('name', __('Name'), ['class' => 'form-label']) }} <x-required></x-required>
                {{ Form::text('name', null, ['id' => 'name', 'class' => 'form-control', 'placeholder' => __('Enter Name'), 'required' => 'required']) }}
            </div>
        </div>
        @if ($type == 'salesorder')
            <div class="col-6">
                <div class="form-group">
                    {{ Form::label('salesorder', __('Sales Orders'), ['class' => 'form-label']) }}
                    {!! Form::select('salesorder', $salesorder, $id, ['id' => 'salesorder', 'class' => 'form-control']) !!}
                </div>
            </div>
        @else
            <div class="col-6">
                <div class="form-group">
                    {{ Form::label('salesorder', __('Sales Orders'), ['class' => 'form-label']) }}
                    {!! Form::select('salesorder', $salesorder, null, ['id' => 'salesorder', 'class' => 'form-control salesorder_id']) !!}
                </div>
            </div>
        @endif
        <div class="col-6">
            <div class="form-group">
                {{ Form::label('account', __('Account'), ['class' => 'form-label']) }}
                {{ Form::text('account', null, ['id' => 'account_name', 'class' => 'form-control', 'placeholder' => __('Enter Account'), 'disabled']) }}
                <input type="hidden" name="account" id="account_id">
            </div>
        </div>
        @if ($type == 'quote')
            <div class="col-6">
                <div class="form-group">
                    {{ Form::label('quote', __('Quote'), ['class' => 'form-label']) }}
                    {!! Form::select('quote', $quote, $id, ['class' => 'form-control']) !!}
                </div>
            </div>
        @else
            <div class="col-6">
                <div class="form-group">
                    {{ Form::label('quote', __('Quote'), ['class' => 'form-label']) }}
                    {!! Form::select('quote', $quote, null, ['class' => 'form-control salesorder_quote ']) !!}
                </div>
            </div>
        @endif
        <div class="col-6">
            <div class="form-group">
                {{ Form::label('date_invoice', __('Date Invoice'), ['class' => 'form-label']) }}
                {{ Form::date('date_quoted', date('Y-m-d'), ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Date')]) }}

            </div>
        </div>
        <div class="col-6">
            <div class="form-group">
                {{ Form::label('opportunity', __('Opportunity'), ['class' => 'form-label']) }}
                {!! Form::select('opportunity', $opportunities, null, [
                    'id' => 'opportunity',
                    'class' => 'form-control salesorder_opportunity',
                    'required' => 'required',
                    'placeholder' => 'Select Opportunity',
                ]) !!}
            </div>
        </div>



        <div class="col-6">
            <div class="form-group">
                {{ Form::label('quote_number', __('Quote Number'), ['class' => 'form-label']) }} <x-required></x-required>
                {{ Form::text('quote_number', null, ['class' => 'form-control', 'placeholder' => __('Enter Quote Number'), 'required' => 'required']) }}
            </div>
        </div>
        <div class="col-6">
            <div class="action-btn float-end mt-4">
                <a class="mx-3 btn btn-sm align-items-center text-white bg-primary" id="billing_data"
                    data-toggle="tooltip" data-placement="top" title="{{__('Same As Billing Address')}}"><i
                        class="fas fa-copy"></i></a>
                <span class="clearfix"></span>
            </div>
        </div>
        <div class="col-6">
            <div class="form-group">
                {{ Form::label('billing_address', __('Billing Address'), ['class' => 'form-label']) }} <x-required></x-required>
                {{ Form::text('billing_address', null, ['id' => 'billing_address', 'class' => 'form-control', 'placeholder' => __('Billing Address'), 'required' => 'required']) }}
            </div>
        </div>
        <div class="col-6">
            <div class="form-group">
                {{ Form::label('shipping_address', __('Shipping Address'), ['class' => 'form-label']) }} <x-required></x-required>
                {{ Form::text('shipping_address', null, ['id' => 'shipping_address', 'class' => 'form-control', 'placeholder' => __('Shipping Address'), 'required' => 'required']) }}
            </div>
        </div>
        <div class="col-3">
            <div class="form-group">
                {{ Form::text('billing_city', null, ['id' => 'billing_city', 'class' => 'form-control', 'placeholder' => __('Billing city'), 'required' => 'required']) }}
            </div>
        </div>
        <div class="col-3">
            <div class="form-group">
                {{ Form::text('billing_state', null, ['id' => 'billing_state', 'class' => 'form-control', 'placeholder' => __('Billing State'), 'required' => 'required']) }}
            </div>
        </div>
        <div class="col-3">
            <div class="form-group">
                {{ Form::text('shipping_city', null, ['id' => 'shipping_city', 'class' => 'form-control', 'placeholder' => __('Shipping City'), 'required' => 'required']) }}
            </div>
        </div>
        <div class="col-3">
            <div class="form-group">
                {{ Form::text('shipping_state', null, ['id' => 'shipping_state', 'class' => 'form-control', 'placeholder' => __('Shipping State'), 'required' => 'required']) }}
            </div>
        </div>
        <div class="col-3">
            <div class="form-group">
                {{ Form::text('billing_country', null, ['id' => 'billing_country', 'class' => 'form-control', 'placeholder' => __('Billing Country'), 'required' => 'required']) }}
            </div>
        </div>
        <div class="col-3">
            <div class="form-group">
                {{ Form::text('billing_postalcode', null, ['id' => 'billing_postalcode', 'class' => 'form-control', 'placeholder' => __('Billing Postal Code'), 'required' => 'required']) }}
            </div>
        </div>
        <div class="col-3">
            <div class="form-group">
                {{ Form::text('shipping_country', null, ['id' => 'shipping_country', 'class' => 'form-control', 'placeholder' => __('Shipping Country'), 'required' => 'required']) }}
            </div>
        </div>
        <div class="col-3">
            <div class="form-group">
                {{ Form::number('shipping_postalcode', null, ['id' => 'shipping_postalcode', 'class' => 'form-control', 'placeholder' => __('Shipping Postal Code'), 'required' => 'required']) }}
            </div>
        </div>
        <div class="col-4">
            <div class="form-group">
                {{ Form::label('billing_contact', __('Billing Contact'), ['class' => 'form-label']) }}
                {!! Form::select('billing_contact', $contact, null, ['class' => 'form-control']) !!}
            </div>
        </div>
        <div class="col-4">
            <div class="form-group">
                {{ Form::label('shipping_contact', __('Shipping Contact'), ['class' => 'form-label']) }}
                {!! Form::select('shipping_contact', $contact, null, ['class' => 'form-control']) !!}
            </div>
        </div>
        <div class="col-4">
            <div class="form-group">
                {{ Form::label('shipping_provider', __('Shipping Provider'), ['class' => 'form-label']) }} <x-required></x-required>
                {!! Form::select('shipping_provider', $shipping_provider, null, [
                    'class' => 'form-control',
                    'required' => 'required',
                    'placeholder' => __('Shipping Provider'),
                ]) !!}
            </div>
        </div>

        <div class="col-12">
            <div class="form-group">
                {{ Form::label('description', __('Description'), ['class' => 'form-label']) }}
                {{ Form::textarea('description', null, ['class' => 'form-control', 'rows' => 3, 'placeholder' => __('Enter Description')]) }}
            </div>
        </div>
        @if(module_is_active('CustomField') && !$customFields->isEmpty())
            <div class="col-md-12">
                <div class="tab-pane fade show" id="tab-2" role="tabpanel">
                    @include('custom-field::formBuilder')
                </div>
            </div>
        @endif
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{__('Cancel')}}</button>
    {{ Form::submit(__('Create'), ['class' => 'btn btn-primary','id'=>'submit']) }}{{ Form::close() }}
</div>
{{ Form::close() }}

<script>

    $(document).on('change','.salesorder_id',function(){
        var salesorder_id = $(this).val();
        url = '{{route('salesorder.details')}}'
        $.ajax({
            url: url,
            type: 'GET',
            data:{
                'salesorder_id':salesorder_id
            },
            success: function(data) {
                $('.salesorder_opportunity').val(data.opportunity).trigger("change").prop('disabled' ,true);
                $('.salesorder_quote').val(data.quote).prop('disabled' ,true);

            },
            error: function(xhr, status, error) {
            }
        })
    });
</script>

