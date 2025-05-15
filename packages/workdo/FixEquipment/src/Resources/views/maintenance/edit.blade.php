{{ Form::open(['route' => ['fix.equipment.maintenance.update', $maintenance->id],'class'=>'needs-validation','novalidate']) }}
<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('Maintenance Type', __('Mintenance Type'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::text('maintenance_type', $maintenance->maintenance_type, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Maintenance Type')]) }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('Asset', __('Asset'), ['class' => 'form-label']) }}<x-required></x-required>
                <select name="asset" id="asset" class="form-control" required>
                    <option value="">{{ __('Select Asset') }}</option>
                    @foreach ($assets as $asset)
                        <option value="{{ $asset->id }}" {{ $maintenance->asset == $asset->id ? 'selected' : '' }}>
                            {{ $asset->title }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('Price', __('Price'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::number('price', $maintenance->price, ['class' => 'form-control', 'placeholder' => __('Enter Price'), 'rows' => '4']) }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('Mintenance Date', __('Maintenance Date'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::date('maintenance_date', $maintenance->maintenance_date, ['class' => 'form-control', 'required' => 'required']) }}
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
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('description', __('Description'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::textarea('description', $maintenance->description, ['class' => 'form-control', 'placeholder' => __('Enter Description'), 'rows' => '3']) }}
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
    {{ Form::submit(__('Update'), ['class' => 'btn  btn-primary']) }}
</div>
{{ Form::close() }}
