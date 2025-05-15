{{ Form::open(['route' => 'ticket-category.store', 'class' => 'needs-validation', 'novalidate']) }}
<div class="modal-body">
    <div class="row">
        <div class="form-group col-md-12">
            {{ Form::label('name', __('Name'), ['class' => 'form-label']) }}<x-required />
            {{ Form::text('name', '', ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Category')]) }}
        </div>
        <div class="col-md-12 form-group">
            {{ Form::label('color', __('Color'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::color('color', '', [
                'class' => 'form-control',
                'required' => 'required',
                'oninput' => 'validateColor()',
            ]) }}
            <div class="invalid-feedback">{{ __('Please choose a color.') }}</div>
        </div>
    </div>
</div>
<div class="modal-footer">
        <input type="button" value="{{ __('Cancel') }}" class="btn  btn-light" data-bs-dismiss="modal">
        <input type="submit" value="{{ __('Create') }}" class="btn  btn-primary">
</div>
{{ Form::close() }}
