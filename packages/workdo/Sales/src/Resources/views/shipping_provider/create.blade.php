{{Form::open(array('url'=>'shipping_provider','method'=>'post','class'=>'needs-validation','novalidate'))}}
    <div class="modal-body">
        <div class="row">
            <div class="col-12">
                <div class="form-group">
                    {{Form::label('name',__('Shipping Provider'),['class'=>'form-label']) }} <x-required></x-required>
                    {{Form::text('name',null,array('class'=>'form-control','placeholder'=>__('Enter Shipping Provider Name'),'required'=>'required'))}}
                </div>
            </div>
            <div class="col-12">
                <div class="form-group">
                    {{Form::label('website',__('Website'),['class'=>'form-label']) }}
                    {{Form::text('website',null,array('class'=>'form-control','placeholder'=>__('Enter website')))}}
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn  btn-light"
            data-bs-dismiss="modal">{{__('Cancel')}}</button>
            {{Form::submit(__('Create'),array('class'=>'btn  btn-primary '))}}{{Form::close()}}
    </div>
{{Form::close()}}
