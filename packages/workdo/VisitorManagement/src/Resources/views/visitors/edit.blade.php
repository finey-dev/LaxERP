{{ Form::model($visitor,['route'=>['visitors.update',$visitor->id],'class'=>'needs-validation','novalidate','method' =>'PUT']) }}
    <div class="modal-body">
        <div class="row">
            <div class="form-group col-md-6">
                {{Form::label('first_name',__('First Name'),['class'=>'form-label'])}}<x-required></x-required>
                {{Form::text('first_name',null,array('class'=>'form-control font-style','placeholder'=>__('Enter First Name'),'required'=>'required'))}}
            </div>
            <div class="form-group col-md-6">
                {{Form::label('last_name',__('Last Name'),['class'=>'form-label'])}}<x-required></x-required>
                {{Form::text('last_name',null,array('class'=>'form-control font-style','placeholder'=>__('Enter Last Name'),'required'=>'required'))}}
            </div>
            <div class="form-group col-md-6">
                {{Form::label('email',__('Email'),['class'=>'form-label'])}}<x-required></x-required>
                {{Form::email('email',null,array('class'=>'form-control font-style','placeholder'=>__('Enter Email'),'required'=>'required'))}}
            </div>
            <div class="col-md-6">
                <x-mobile name="phone" label="Phone"></x-mobile>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <input type="button" value="{{__('Cancel')}}" class="btn  btn-light" data-bs-dismiss="modal">
        <input type="submit" value="{{__('Update')}}" class="btn  btn-primary">
    </div>
{{ Form::close() }}
