{{ Form::open(array('route' => 'facilities-space.store', 'class'=>'needs-validation','novalidate')) }}

    <div class="modal-body">
        <div class="row">
            <div class="form-group col-12">
                {{ Form::label('name', __('Name'),['class'=>'col-form-label']) }}<x-required></x-required>
                {{ Form::text('name', '', ['class' => 'form-control','placeholder'=> 'Enter Name' ,'required'=>'required']) }}
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{__('Cancel')}}</button>
        <button type="submit" class="btn  btn-primary">{{__('Create')}}</button>
    </div>

{{ Form::close() }}
