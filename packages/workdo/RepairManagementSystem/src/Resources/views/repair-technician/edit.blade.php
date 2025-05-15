
{{ Form::model($repair_technician, array('route' => array('repair-technician.update', $repair_technician->id), 'method' => 'PUT', 'class'=>'needs-validation','novalidate')) }}
    <div class="modal-body">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    {{ Form::label('name', __('Name'), ['class' => 'col-form-label']) }}<x-required></x-required>
                    {{ Form::text('name', null, ['class' => 'form-control','required'=>'required','placeholder' => __('Enter Name')]) }}
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    {{ Form::label('email', __('Email'), ['class' => 'col-form-label']) }}<x-required></x-required>
                    {{ Form::email('email', null, ['class' => 'form-control','required'=>'required','placeholder' => __('Enter Email')]) }}
                </div>
            </div>
            <div class="col-md-12">
                <x-mobile required></x-mobile>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <input type="button" value="{{ __('Cancel') }}" class="btn btn-light" data-bs-dismiss="modal">
        <input type="submit" value="{{ __('Update') }}" class="btn btn-primary">
    </div>
    {{ Form::close() }}

