{{ Form::model($visitReason,['route'=>['visit-reason.update',$visitReason->id],'class'=>'needs-validation','novalidate','method' =>'PUT']) }}
    <div class="modal-body">
        <div class="row">
            <div class="form-group col-md-12">
                {{Form::label('reason',__('Purpose'),['class'=>'form-label'])}}<x-required></x-required>
                {{Form::text('reason',null,array('class'=>'form-control font-style','required'=>'required','placeholder'=>__('Enter Purpose')))}}
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <input type="button" value="{{__('Cancel')}}" class="btn  btn-light" data-bs-dismiss="modal">
        <input type="submit" value="{{__('Update')}}" class="btn  btn-primary">
    </div>
{{ Form::close() }}
