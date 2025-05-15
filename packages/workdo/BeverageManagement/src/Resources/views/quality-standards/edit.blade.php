{{ Form::model($quality_standards, ['route' => ['quality-standards.update', $quality_standards->id], 'method' => 'PUT', 'class' => 'needs-validation', 'novalidate']) }}

<div class="modal-body">
    <div class="row">
        <div class="form-group col-md-12">
            {{ Form::label('item_id', __('Product'), ['class' => 'form-label']) }}<x-required></x-required>
            <select name="item_id" class="form-control select" id="item_id" required>
                <option value="">{{ __('Select Raw Material Product') }}</option>
                @foreach ($quality_standard as $product)
                    <option value="{{ $product->item_id }}"
                        {{ isset($quality_standards) ? ($quality_standards->item_id == $product->item_id ? 'selected' : '') : '' }}>
                        {{ $product->productService->name }} - ({{ $product->productService->type }})</option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('standard_type', __('Standard Type'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::text('standard_type', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => 'Enter Standard Type']) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('value', __('Value'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::text('value', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => 'Enter value']) }}

        </div>
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{ __('Cancel') }}" class="btn  btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Update') }}" class="btn  btn-primary">
</div>
{{ Form::close() }}
