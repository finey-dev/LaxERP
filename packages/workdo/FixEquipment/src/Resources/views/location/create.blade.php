{{ Form::open(['route' => 'fix.equipment.location.store', 'class'=>'needs-validation','novalidate','enctype' => 'multipart/form-data']) }}
<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('Location Name', __('Location Name'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::text('location_name', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Location name')]) }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('Address', __('Address'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::textarea('address', null, ['class' => 'form-control', 'required' => 'required','placeholder' => __('Enter Address'), 'rows' => '3']) }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('Attachment', __('Attachment'), ['class' => 'form-label']) }}
                <input type="file" class="form-control" name="attachment">
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('description', __('Description'), ['class' => 'form-label']) }}
                {{ Form::textarea('description', null, ['class' => 'form-control', 'placeholder' => __('Enter Description'), 'rows' => '3']) }}
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
    {{ Form::submit(__('Create'), ['class' => 'btn  btn-primary']) }}
</div>
{{ Form::close() }}
