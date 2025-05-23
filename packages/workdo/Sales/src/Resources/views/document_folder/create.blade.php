{{Form::open(array('url'=>'salesdocument_folder','method'=>'post','class'=>'needs-validation','novalidate'))}}
    <div class="modal-body">
        <div class="row">
            <div class="col-6">
                <div class="form-group">
                    {{Form::label('name',__('Name'),['class'=>'form-label']) }} <x-required></x-required>
                    {{Form::text('name',null,array('class'=>'form-control','placeholder'=>__('Enter Name'),'required'=>'required'))}}
                </div>
            </div>
            <div class="col-6">
                <div class="form-group">
                    {{Form::label('parent',__('Parent'),['class'=>'form-label']) }}
                    {!! Form::select('parent', $parent, null,array('class' => 'form-control')) !!}
                </div>
            </div>
            <div class="col-12">
                <div class="form-group">
                    {{Form::label('description',__('Description'),['class'=>'form-label']) }}
                    {{Form::textarea('description',null,array('class'=>'form-control','rows'=>3,'placeholder'=>__('Enter Description')))}}
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{__('Cancel')}}</button>
        {{Form::submit(__('Create'),array('class'=>'btn  btn-primary '))}}{{Form::close()}}
    </div>
{{Form::close()}}
