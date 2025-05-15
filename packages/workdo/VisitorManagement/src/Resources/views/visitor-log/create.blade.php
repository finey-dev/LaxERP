{{ Form::open(['route'=>['visitor-log.store'],'class'=>'needs-validation','novalidate','method' =>'POST']) }}
    <div class="modal-body">
        <div class="form-group col-md-12">
            {{Form::label('visitor',__('Visitor'),['class'=>'form-label'])}}
            {{Form::select('visitor_id',$visitors,null,array('class'=>'form-control font-style'))}}
            <small class="text-danger">{{ __('Select Visitor If Already Visited otherwise Create New!') }}</small>
        </div>
        <div class="row">
            <div class="form-group col-md-6">
                {{Form::label('first_name',__('First Name'),['class'=>'form-label'])}}<x-required></x-required>
                {{Form::text('first_name',null,array('class'=>'form-control visitor-data font-style','placeholder'=>__('Enter First Name'),'required'=>'required'))}}
            </div>
            <div class="form-group col-md-6">
                {{Form::label('last_name',__('Last Name'),['class'=>'form-label'])}}<x-required></x-required>
                {{Form::text('last_name',null,array('class'=>'form-control visitor-data font-style','placeholder'=>__('Enter Last Name'),'required'=>'required'))}}
            </div>
            <div class="form-group col-md-6">
                {{Form::label('email',__('Email'),['class'=>'form-label'])}}<x-required></x-required>
                {{Form::email('email',null,array('class'=>'form-control visitor-data font-style','placeholder'=>__('Enter Email'),'required'=>'required'))}}
            </div>
            <div class="col-md-6">
                <x-mobile name="phone" label="Phone" placeholder="Enter Phone"></x-mobile>
            </div>
            <div class="form-group col-md-6">
                {{Form::label('visit_reason',__('Visit Purpose'),['class'=>'form-label'])}}<x-required></x-required>
                {{Form::select('visit_reason',$visitReason,null,array('class'=>'form-control font-style','required'=>'required'))}}
            </div>
            <div class="form-group col-md-6">
                {{Form::label('check_in_time',__('Visitor Arrival Time'),['class'=>'form-label'])}}<x-required></x-required>
                {{ Form::datetimeLocal('check_in', null, ['class'=>'form-control','placeholder'=> __('Select Date/Time'),'required'=>'required']) }}
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <input type="button" value="{{__('Cancel')}}" class="btn  btn-light" data-bs-dismiss="modal">
        <input type="submit" value="{{__('Create')}}" class="btn  btn-primary">
    </div>
{{ Form::close() }}
