{{ Form::open(['route' => 'beverage-maintenance.store', 'class' => 'needs-validation', 'novalidate']) }}
<div class="modal-body">
    <div class="row">
        <div class="form-group col-md-6">
            {{ Form::label('maintenance_date', __('Maintenance Date'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::date('maintenance_date', '', ['class' => 'form-control', 'required' => 'required']) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('maintenance_type', __('Maintenance Type'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::text('maintenance_type', '', ['class' => 'form-control', 'required' => 'required', 'placeholder' => 'Enter Maintenance Type']) }}

        </div>
        <div class="form-group col-md-12">
            {{ Form::label('status', __('Status'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::text('status', '', ['class' => 'form-control', 'required' => 'required', 'placeholder' => 'Enter Status']) }}
        </div>
        <div class="form-group col-md-12">
            {{ Form::label('comments', __('Comments'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::textarea('comments', '', ['class' => 'form-control', 'required' => 'required', 'placeholder' => 'Enter Comments', 'rows'=>3]) }}
        </div>

    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{ __('Cancel') }}" class="btn  btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Create') }}" class="btn  btn-primary">
</div>
{{ Form::close() }}
