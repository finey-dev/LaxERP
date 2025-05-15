{{ Form::model($quality_checks, array('route' => array('quality-checks.update', $quality_checks->id), 'method' => 'PUT','class'=>'needs-validation','novalidate')) }}
<div class="modal-body">
    <div class="row">
        <div class="form-group col-md-6">
            {{ Form::label('item_id', __('Product'), ['class' => 'form-label']) }}<x-required></x-required>
            <select name="item_id" class="form-control select" id="item_id" required>
                <option value="">{{ __('Select Raw Material Product') }}</option>
                @foreach($quality_check as $product)
                    <option value="{{ $product->item_id }}" {{isset($quality_checks) ? $quality_checks->item_id ==  $product->item_id ? 'selected'
                    : '' : ''}}>{{ $product->productService->name }} - ({{ $product->productService->type }})</option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('check_date', __('Check Date'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::date('check_date', null, ['class' => 'form-control', 'required' => 'required','placeholder'=>'Enter Check Date']) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('check_type', __('Check Type'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::text('check_type', null, ['class' => 'form-control', 'required' => 'required','placeholder'=>'Enter Check Type']) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('result', __('Result'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::text('result', null, ['class' => 'form-control', 'required' => 'required','placeholder'=>'Enter Result']) }}
        </div>
        <div class="form-group col-md-12">
            {{ Form::label('comments', __('Comments'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::textarea('comments', null, ['class' => 'form-control', 'required' => 'required','placeholder'=>'Enter Comments' ,'rows'=>3]) }}
        </div>


    </div>
</div>
<div class="modal-footer">
        <input type="button" value="{{ __('Cancel') }}" class="btn  btn-light" data-bs-dismiss="modal">
        <input type="submit" value="{{ __('Update') }}" class="btn  btn-primary">
</div>
{{ Form::close() }}

