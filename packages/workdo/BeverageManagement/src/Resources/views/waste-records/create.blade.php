{{ Form::open(['route' => 'waste-records.store','class'=>'needs-validation','novalidate']) }}
<div class="modal-body">
    <div class="row">
        <div class="form-group col-md-6">
            {{ Form::label('item_id', __('Product'), ['class' => 'form-label']) }}<x-required></x-required>
            <select name="item_id" class="form-control select" id="item_id" required>
                <option value="">{{ __('Select Product') }}</option>
                @foreach($products as $product)
                    <option value="{{ $product->item_id }}">{{ $product->productService->name }} - ({{ $product->productService->type }})</option>
                @endforeach
            </select>
            <div class="text-muted text-xs">{{ __('Please Add Product Here.') }} <a href="{{ route('raw-material.index') }}">{{ __('Add Product') }}</a></div>
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('waste_date', __('Waste Date'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::date('waste_date', '', ['class' => 'form-control', 'required' => 'required']) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('waste_categories', __('Waste type'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::text('waste_categories', '', ['class' => 'form-control', 'required' => 'required','placeholder'=>'Enter Waste type']) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('quantity', __('Quantity'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::number('quantity', '', ['class' => 'form-control', 'required' => 'required','placeholder'=>'Enter Quantity']) }}
        </div>
        <div class="form-group col-md-12">
            {{ Form::label('reason', __('Reason'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::text('reason', '', ['class' => 'form-control', 'required' => 'required','placeholder'=>'Enter Reason']) }}
        </div>
        <div class="form-group col-md-12">
            {{ Form::label('comments', __('Comments'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::textarea('comments', '', ['class' => 'form-control', 'required' => 'required','placeholder'=>'Enter Comments','rows'=>3]) }}
        </div>
    </div>
</div>
<div class="modal-footer">
        <input type="button" value="{{ __('Cancel') }}" class="btn  btn-light" data-bs-dismiss="modal">
        <input type="submit" value="{{ __('Create') }}" class="btn  btn-primary">
</div>
{{ Form::close() }}
