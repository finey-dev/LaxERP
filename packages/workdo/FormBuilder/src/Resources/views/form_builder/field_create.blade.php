
{{ Form::open(array('route' => ['form.field.store',$formbuilder->id],'class'=>'needs-validation','novalidate')) }}
<div class="modal-body">
    <div class="row" id="frm_field_data">
        <div class="col-6 form-group">
            {{ Form::label('name', __('Question Name'),['class'=>'form-label']) }} <x-required></x-required>
            {{ Form::text('name', '', array('class' => 'form-control','required'=>'required','placeholder' => __('Enter Question Name'))) }}
        </div>
        <div class="col-6 form-group">
            {{ Form::label('type', __('Type'),['class'=>'form-label']) }} <x-required></x-required>
            {{ Form::select('type', $types,null, array('class' => 'form-control','required'=>'required','id'=>'type')) }}
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{__('Cancel')}}</button>
    <button type="submit" class="btn  btn-primary">{{__('Create')}}</button>
</div>

{{ Form::close() }}

