{{ Form::model($waste_records, ['route' => ['waste-records.update', $waste_records->id], 'method' => 'PUT', 'class' => 'needs-validation', 'novalidate']) }}

<div class="modal-body">
    <div class="row">
        <div class="form-group col-md-6">
            {{ Form::label('item_id', __('Product'), ['class' => 'form-label']) }}<x-required></x-required>
            <select name="item_id" class="form-control select" id="item_id" required>
                <option value="">{{ __('Select Raw Material Product') }}</option>
                @foreach ($waste_record as $product)
                    <option value="{{ $product->item_id }}"
                        {{ isset($waste_records) ? ($waste_records->item_id == $product->item_id ? 'selected' : null) : null }}>
                        {{ $product->productService->name }} - ({{ $product->productService->type }})</option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('waste_date', __('Waste Date'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::date('waste_date', null, ['class' => 'form-control', 'required' => 'required']) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('waste_categories', __('Waste type'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::text('waste_categories', null, ['class' => 'form-control', 'required' => 'required','placeholder'=>'Enter Waste type']) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('quantity', __('Quantity'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::number('quantity', null, ['class' => 'form-control', 'required' => 'required','placeholder'=>'Enter Quantity']) }}
        </div>
        <div class="form-group col-md-12">
            {{ Form::label('reason', __('Reason'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::text('reason', null, ['class' => 'form-control', 'required' => 'required','placeholder'=>'Enter Reason']) }}
        </div>
        <div class="form-group col-md-12">
            {{ Form::label('comments', __('Comments'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::textarea('comments', null, ['class' => 'form-control', 'required' => 'required','placeholder'=>'Enter Comments','rows'=>3]) }}
        </div>
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{ __('Cancel') }}" class="btn  btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Update') }}" class="btn  btn-primary">
</div>
{{ Form::close() }}
