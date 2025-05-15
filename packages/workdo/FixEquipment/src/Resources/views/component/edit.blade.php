{{ Form::open(['route' => ['fix.equipment.component.update', $component->id],'class'=>'needs-validation','novalidate']) }}
<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('Title', __('Title'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::text('title', $component->title, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Title')]) }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('Category', __('Category'), ['class' => 'form-label']) }}<x-required></x-required>
                <select name="category" id="category" class="form-control" required>
                    <option value="">{{ __('Select Category') }}</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}"
                            {{ $component->category == $category->id ? 'selected' : '' }}>{{ $category->title }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('Asset', __('Asset'), ['class' => 'form-label']) }}<x-required></x-required>
                <select name="asset" id="asset" class="form-control" required>
                    <option value="">{{ __('Select Asset') }}</option>
                    @foreach ($assets as $asset)
                        <option value="{{ $asset->id }}" {{ $component->asset == $asset->id ? 'selected' : '' }}>
                            {{ $asset->title }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('Price', __('Price'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::number('price', $component->price, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Price')]) }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('Quantity', __('Quantity'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::number('quantity', $component->quantity, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Quantity')]) }}
            </div>
        </div>
    </div>
</div>
</div>
<div class="modal-footer">
    <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
    {{ Form::submit(__('Update'), ['class' => 'btn  btn-primary']) }}
</div>
{{ Form::close() }}
