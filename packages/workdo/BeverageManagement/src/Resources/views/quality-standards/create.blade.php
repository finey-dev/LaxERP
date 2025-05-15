{{ Form::open(['route' => 'quality-standards.store','class'=>'needs-validation','novalidate']) }}
<div class="modal-body">
    <div class="row">
        <div class="form-group col-md-12">
            {{ Form::label('item_id', __('Product'), ['class' => 'form-label']) }}<x-required></x-required>
            <select name="item_id" class="form-control select" id="item_id" required>
                <option value="">{{ __('Select Product') }}</option>
                @foreach($products as $product)
                    <option value="{{ $product->item_id }}">{{ $product->productService->name }} - ({{ $product->productService->type }})</option>
                @endforeach
            </select>
            <div class="text-muted text-xs mt-1">{{ __('Please Add Product Here.') }} <a href="{{ route('raw-material.index') }}">{{ __('Add Product') }}</a></div>
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('standard_type', __('Standard Type'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::text('standard_type', '', ['class' => 'form-control', 'required' => 'required','placeholder'=>'Enter Standard Type']) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('value', __('Value'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::text('value', '', ['class' => 'form-control', 'required' => 'required','placeholder'=>'Enter value']) }}

        </div>
    </div>
</div>
<div class="modal-footer">
        <input type="button" value="{{ __('Cancel') }}" class="btn  btn-light" data-bs-dismiss="modal">
        <input type="submit" value="{{ __('Create') }}" class="btn  btn-primary">
</div>
{{ Form::close() }}
