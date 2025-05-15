{{ Form::open(['route' => 'fix.equipment.category.store','class'=>'needs-validation','novalidate', 'method' => 'POST']) }}
<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('Category Title', __('Category Title'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::text('category_title', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Category Title')]) }}
            </div>
        </div>

        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('Category Type', __('Category Type'), ['class' => 'form-label']) }}<x-required></x-required>
                <select name="category_type" id="category_type" class="form-control" required>
                    <option value="">{{ __('Select Category') }}</option>
                    @foreach ($categoryTypes as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
    {{ Form::submit(__('Create'), ['class' => 'btn  btn-primary']) }}
</div>
{{ Form::close() }}
