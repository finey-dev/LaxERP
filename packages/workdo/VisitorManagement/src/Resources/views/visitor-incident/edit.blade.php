{{ Form::model($visitor_incident,['route'=>['visitors-incidents.update',$visitor_incident->id],'class'=>'needs-validation','novalidate','method' =>'PUT']) }}
<div class="modal-body">
    <div class="row">
        <div class="form-group col-md-6">
            {{ Form::label('visitor', __('Visitor'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::select('visitor_id', $visitors, null, ['class' => 'form-control font-style', 'required' => 'required']) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('incident_date', __('Incident Date'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::datetimeLocal('incident_date', null, ['class' => 'form-control visitor-data font-style', 'placeholder' => __('Enter Incident Date'), 'required' => 'required']) }}
        </div>
        <div class="form-group col-md-12">
            {{ Form::label('action_taken', __('Action Taken'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::text('action_taken', null, ['class' => 'form-control visitor-data font-style', 'placeholder' => __('Enter Action Taken'), 'required' => 'required']) }}
        </div>
        <div class="form-group col-md-12">
            {{ Form::label('action_taken', __('Incident Description'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::textarea('incident_description', null, ['class' => 'form-control visitor-data font-style', 'placeholder' => __('Enter Incident Description'), 'required' => 'required','rows'=>3]) }}

        </div>


    </div>
</div>
    <div class="modal-footer">
        <input type="button" value="{{__('Cancel')}}" class="btn  btn-light" data-bs-dismiss="modal">
        <input type="submit" value="{{__('Update')}}" class="btn  btn-primary">
    </div>
{{ Form::close() }}
