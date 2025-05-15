{{ Form::open(['route' => ['repair.request.invoice.store', $repair_id], 'method' => 'POST','class'=>'needs-validation','novalidate']) }}

<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('repair_charge', __('Repair Charge').' ' .' (' . (!empty(company_setting('defult_currancy_symbol')) ? company_setting('defult_currancy_symbol') : '$') . ')'  , ['class' => 'form-label']) }}<x-required></x-required>

                {{ Form::text('repair_charge', '', ['class' => 'form-control', 'required' => 'required' ,'placeholder' => __('Enter Repair Charge')]) }}
            </div>
        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
    <button type="submit" class="btn btn-primary">{{ __('Create') }}</button>
</div>

{{ Form::close() }}
