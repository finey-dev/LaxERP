{{ Form::open(['route' => ['repair.invoice.payment.store', $repair_invoice->id], 'method' => 'POST','class'=>'needs-validation','novalidate']) }}
<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('amount', __('Amount').' ' .' (' . (!empty(company_setting('defult_currancy_symbol')) ? company_setting('defult_currancy_symbol') : '$') . ')'  , ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::number('amount', $repair_invoice->repairOrderRequest->getDue(), ['class' => 'form-control', 'required' => 'required' ,'placeholder' => __('Enter Amount')]) }}
            </div>
        </div>
    </div>
</div>


<div class="modal-footer">
    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
    <button type="submit" class="btn btn-primary">{{ __('Pay') }}</button>
</div>

{{ Form::close() }}
