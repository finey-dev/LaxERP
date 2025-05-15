{{ Form::open(['url' => 'fixequipment/store', 'enctype' => 'multipart/form-data','class'=>'needs-validation','novalidate']) }}
<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('Asset Name', __('Asset Name'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::text('asset_name', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter asset name')]) }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('Model', __('Model'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::text('model', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter model')]) }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('Asset Image', __('Asset Image'), ['class' => 'form-label']) }}<x-required></x-required>
                <input type="file" name="asset_image" class="form-control" required>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('Serial Number', __('Serial Number'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::text('serial_number', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Serial Number')]) }}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('Purchase Date', __('Purchase Date'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::date('purchase_date', null, ['class' => 'form-control', 'required' => 'required']) }}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('Purchase Price', __('Purchase Price'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::number('purchase_price', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Purchase Price')]) }}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('Location', __('Location'), ['class' => 'form-label']) }}<x-required></x-required>
                <select name="location" id="location" class="form-control" required>
                    <option value="">{{ __('Select Location') }}</option>
                    @foreach ($locations as $location)
                        <option value="{{ $location->id }}"> {{ $location->location_name }} </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('Supplier', __('Supplier'), ['class' => 'form-label']) }}<x-required></x-required>
                <select name="supplier" id="supplier" class="form-control" required>
                    <option value="">{{ __('Select Supplier') }}</option>
                    @foreach ($suppliers as $supplier)
                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('manufacturer', __('Manufacturer'), ['class' => 'form-label']) }}<x-required></x-required>
                <select name="manufacturer" id="manufacturer" class="form-control" required>
                    <option value="">{{ __('Select Manufacturer') }}</option>
                    @foreach ($manufaturers as $manufaturer)
                        <option value="{{ $manufaturer->id }}">{{ $manufaturer->title }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('Category', __('Category'), ['class' => 'form-label']) }}<x-required></x-required>
                <select name="category" id="category" class="form-control" required>
                    <option value="">{{ __('Select Category') }}</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->title }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('Status', __('Status'), ['class' => 'form-label']) }}<x-required></x-required>
                <select name="status" id="status" class="form-control" required>
                    <option value="">{{ __('Select Status') }}</option>
                    @foreach ($status as $st)
                        <option value="{{ $st->id }}">{{ $st->title }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('Depreciation', __('Depreciation'), ['class' => 'form-label']) }}<x-required></x-required>
                <select name="depreciation" id="depreciation" class="form-control" required>
                    <option value="">{{ __('Select Depreciation') }}</option>
                    @foreach ($depreciations as $depreciation)
                        <option value="{{ $depreciation->id }}">{{ $depreciation->title }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        @if (module_is_active('DoubleEntry'))
            <div class="col-md-12 form-group">
                {{ Form::label('Account', __('Account'), ['class' => 'form-label']) }}<x-required></x-required>
                <select name="account" class="form-control" required="required">
                    @foreach ($chartAccounts as $chartAccount)
                        <option value="{{ $chartAccount['id'] }}" class="subAccount">
                            {{ $chartAccount['code'] }} - {{ $chartAccount['name'] }}</option>
                        @foreach ($subAccounts as $subAccount)
                            @if ($chartAccount['id'] == $subAccount['account'])
                                <option value="{{ $subAccount['id'] }}" class="ms-5"> &nbsp;
                                    &nbsp;&nbsp; {{ $subAccount['code'] }} -
                                    {{ $subAccount['name'] }}</option>
                            @endif
                        @endforeach
                    @endforeach
                </select>
            </div>
        @endif

        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('description', __('Description'), ['class' => 'form-label']) }}
                {{ Form::textarea('description', null, ['class' => 'form-control', 'placeholder' => __('Enter Description'), 'rows' => '3']) }}
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
    {{ Form::submit(__('Create'), ['class' => 'btn  btn-primary']) }}
</div>
{{ Form::close() }}
