{{ Form::open(['route' => ['fix.equipment.consumables.update', $consumables->id],'class'=>'needs-validation','novalidate']) }}
<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('Title', __('Title'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::text('title', $consumables->title, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Title')]) }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('Category', __('Category'), ['class' => 'form-label']) }}<x-required></x-required>
                <select name="category" id="category" class="form-control" required>
                    <option value="">{{ __('Select Category') }}</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}"
                            {{ $consumables->category == $category->id ? 'selected' : '' }}>{{ $category->title }}
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
                        <option value="{{ $asset->id }}" {{ $consumables->asset == $asset->id ? 'selected' : '' }}>
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
                    @foreach ($manufacturers as $manufacturer)
                        <option value="{{ $manufacturer->id }}"
                            {{ $consumables->manufacturer == $manufacturer->id ? 'selected' : '' }}>
                            {{ $manufacturer->title }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('Date', __('Date'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::date('date', $consumables->date, ['class' => 'form-control', 'required' => 'required']) }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('Price', __('Price'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::number('price', $consumables->price, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Price')]) }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('Quantity', __('Quantity'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::number('quantity', $consumables->quantity, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Quantity')]) }}
            </div>
        </div>
        @if (module_is_active('DoubleEntry'))
            <div class="col-md-12 form-group">
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
