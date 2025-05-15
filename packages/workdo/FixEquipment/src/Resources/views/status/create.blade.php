{{ Form::open(['route' => 'fix.equipment.status.store','class'=>'needs-validation','novalidate', 'method' => 'POST']) }}
<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('Status', __('Status'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::text('status', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Status')]) }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('Status Color', __('Status Color'), ['class' => 'form-label']) }}
                <input class="jscolor form-control" value="FFFFFF" name="color" id="color" required>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
    {{ Form::submit(__('Create'), ['class' => 'btn  btn-primary']) }}
</div>
{{ Form::close() }}
