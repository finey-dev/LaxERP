{{ Form::model($pre_registration,['route'=>['visitors-pre-registration.update',$pre_registration->id],'class'=>'needs-validation','novalidate','method' =>'PUT']) }}
<div class="modal-body">
    <div class="row">
        <div class="form-group col-md-12">
            {{ Form::label('visitor', __('Visitor'), ['class' => 'form-label']) }}
            {{ Form::select('visitor_id', $visitors, null, ['class' => 'form-control font-style']) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('appointment_date', __('Incident Date'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::datetimeLocal('appointment_date', null, ['class' => 'form-control visitor-data font-style', 'placeholder' => __('Enter Incident Date'), 'required' => 'required']) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('status', __('Status'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::select('status', ['' => 'Select Status', 0 => 'Pending', 1 => 'Confirmed',2 =>'Cancelled'], null, array('class' => 'form-control select','required'=>'required')) }}
        </div>
    </div>
</div>
    <div class="modal-footer">
        <input type="button" value="{{__('Cancel')}}" class="btn  btn-light" data-bs-dismiss="modal">
        <input type="submit" value="{{__('Update')}}" class="btn  btn-primary">
    </div>
{{ Form::close() }}
