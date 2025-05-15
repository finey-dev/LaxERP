{{ Form::open(['route' => 'fix.equipment.licence.store','class'=>'needs-validation','novalidate']) }}
<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('Title', __('Title'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::text('title', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Title')]) }}
            </div>
        </div>
        <div class="col-md-12">
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
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('Asset', __('Asset'), ['class' => 'form-label']) }}<x-required></x-required>
                <select name="asset" id="asset" name="asset" class="form-control" required>
                    <option value="">{{ __('Select Asset') }}</option>
                    @foreach ($assets as $asset)
                        <option value="{{ $asset->id }}">{{ $asset->title }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('License Number', __('License Number'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::number('license_number', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter License Number')]) }}
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
                {{ Form::label('Expire Date', __('Expire Date'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::date('expire_date', null, ['class' => 'form-control', 'required' => 'required']) }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('Purchase Price', __('Purchase Price'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::number('purchase_price', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Price')]) }}
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
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
    {{ Form::submit(__('Create'), ['class' => 'btn  btn-primary']) }}
</div>
{{ Form::close() }}
