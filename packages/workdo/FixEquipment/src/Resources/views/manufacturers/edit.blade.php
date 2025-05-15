{{ Form::open(['route' => ['fix.equipment.manufacturer.update', $manufacturer->id],'class'=>'needs-validation','novalidate', 'method' => 'POST']) }}
<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('Manufacturer Title', __('Manufacturer Title'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::text('manufacturer_title', $manufacturer->title, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Manufacturer name')]) }}
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
    {{ Form::submit(__('Update'), ['class' => 'btn  btn-primary']) }}
</div>
{{ Form::close() }}
