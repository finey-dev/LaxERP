{{ Form::open(['route' => ['fix.equipment.accessories.update', $accessories->id],'class'=>'needs-validation','novalidate']) }}
<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('Title', __('Title'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::text('title', $accessories->title, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Title')]) }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('Category', __('Category'), ['class' => 'form-label']) }}<x-required></x-required>
                <select name="category" id="category" class="form-control" required>
                    <option value="">{{ __('Select Category') }}</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}"
                            {{ $accessories->category == $category->id ? 'selected' : '' }}>{{ $category->title }}
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
                        <option value="{{ $asset->id }}" {{ $accessories->asset == $asset->id ? 'selected' : '' }}>
                            {{ $asset->title }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('Manufacturer', __('Manufacturer'), ['class' => 'form-label']) }}<x-required></x-required>
                <select name="manufacturer" id="manufacturer" class="form-control" required>
                    <option value="">{{ __('Select Manufacturer') }}</option>
                    @foreach ($manufaturers as $manufacturer)
                        <option value="{{ $manufacturer->id }}"
                            {{ $accessories->manufacturer == $manufacturer->id ? 'selected' : '' }}>
                            {{ $manufacturer->title }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('Supplier', __('Supplier'), ['class' => 'form-label']) }}<x-required></x-required>
                <select name="supplier" id="supplier" class="form-control" required>
                    <option value="">{{ __('Select Supplier') }}</option>
                    @foreach ($suppliers as $supplier)
                        <option value="{{ $supplier->id }}"
                            {{ $accessories->supplier == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('Price', __('Price'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::number('price', $accessories->price, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Price')]) }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('Quantity', __('Quantity'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::number('quantity', $accessories->quantity, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Quantity')]) }}
            </div>
        </div>
        @if (module_is_active('DoubleEntry'))
            <div class="col-ms-12 form-group">
                {{ Form::label('Account', __('Account'), ['class' => 'form-label']) }}<x-required></x-required>
                <select name="account" class="form-control" required="required">
                    @foreach ($chartAccounts as $chartAccount)
                        <option value="{{ $chartAccount['id'] }}" class="subAccount"
                            {{ optional($account)->id == $chartAccount['id'] ? 'selected' : '' }}>
                            {{ $chartAccount['code'] }} - {{ $chartAccount['name'] }}</option>
                        @foreach ($subAccounts as $subAccount)
                            @if ($chartAccount['id'] == $subAccount['account'])
                                <option value="{{ $subAccount['id'] }}" class="ms-5"
                                    {{ optional($account)->id == $subAccount['id'] ? 'selected' : '' }}> &nbsp;
                                    &nbsp;&nbsp; {{ $subAccount['code'] }} -
                                    {{ $subAccount['name'] }}</option>
                            @endif
                        @endforeach
                    @endforeach
                </select>
            </div>
        @endif
    </div>
</div>
</div>
<div class="modal-footer">
    <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
    {{ Form::submit(__('Update'), ['class' => 'btn  btn-primary']) }}
</div>
{{ Form::close() }}
