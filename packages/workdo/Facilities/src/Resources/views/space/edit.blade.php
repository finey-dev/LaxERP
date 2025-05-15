{{ Form::open(array('route' => array('facilities-space.update',$space->id) , 'method' => 'PUT', 'class'=>'needs-validation','novalidate')) }}

    <div class="modal-body">
        <div class="row">
            <div class="form-group col-12">
                {{ Form::label('name', __('Name'),['class'=>'col-form-label']) }}<x-required></x-required>
                {{ Form::text('name', $space->name, ['class' => 'form-control','placeholder'=> 'Enter Name' ,'required'=>'required']) }}
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{__('Cancel')}}</button>
        <button type="submit" class="btn  btn-primary">{{__('Update')}}</button>
    </div>

{{ Form::close() }}
