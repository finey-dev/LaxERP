{{ Form::model($visitor_badge,['route'=>['visitors-badge.update',$visitor_badge->id],'class'=>'needs-validation','novalidate','method' =>'PUT']) }}

<div class="modal-body">
    <div class="row">
    <div class="form-group col-md-6">
        {{Form::label('visitor',__('Visitor'),['class'=>'form-label'])}}<x-required></x-required>
        {{Form::select('visitor_id',$visitors,null,array('class'=>'form-control font-style','required'=>'required'))}}
    </div>
        <div class="form-group col-md-6">
            {{Form::label('badge_number',__('Badge Number'),['class'=>'form-label'])}}<x-required></x-required>
            {{Form::number('badge_number',null,array('class'=>'form-control visitor-data font-style','placeholder'=>__('Enter Badge Number'),'required'=>'required'))}}
        </div>
        <div class="form-group col-md-6">
            {{Form::label('issue_date',__('Issue Date'),['class'=>'form-label'])}}<x-required></x-required>
            {{Form::datetimeLocal('issue_date',null,array('class'=>'form-control visitor-data font-style','placeholder'=>__('Enter Issue Date'),'required'=>'required'))}}
        </div>
        <div class="form-group col-md-6">
            {{Form::label('return_date',__('Return Date'),['class'=>'form-label'])}}<x-required></x-required>
            {{Form::datetimeLocal('return_date',null,array('class'=>'form-control visitor-data font-style','placeholder'=>__('Enter Return Date'),'required'=>'required'))}}
        </div>

    </div>
</div>
    <div class="modal-footer">
        <input type="button" value="{{__('Cancel')}}" class="btn  btn-light" data-bs-dismiss="modal">
        <input type="submit" value="{{__('Update')}}" class="btn  btn-primary">
    </div>
{{ Form::close() }}
