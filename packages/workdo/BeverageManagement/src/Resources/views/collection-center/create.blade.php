{{ Form::open(['route' => 'collection-center.store','class'=>'needs-validation','novalidate']) }}
<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <div class="form-group col-md-12">
                {{ Form::label('location_name', __('Location Name'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::text('location_name', '', ['class' => 'form-control', 'required' => 'required','placeholder'=>'Enter Location Name']) }}
            </div>
            <div class="form-group col-md-12">
                {{ Form::label('status', __('Status'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::select('status', ['' => 'Select Status', 1 => 'Active', 0 => 'Inactive'], null, array('class' => 'form-control select','required'=>'required')) }}
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
        <input type="button" value="{{ __('Cancel') }}" class="btn  btn-light" data-bs-dismiss="modal">
        <input type="submit" value="{{ __('Create') }}" class="btn  btn-primary">
</div>
{{ Form::close() }}
