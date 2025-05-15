{{ Form::open(['route' => ['fix.equipment.pre.definded.kit.update', $kit->id],'class'=>'needs-validation','novalidate']) }}
<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('Kit Title', __('Kit Title'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::text('kit_title', $kit->title, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Kit Title')]) }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('Asset', __('Asset'), ['class' => 'form-label']) }}<x-required></x-required>
                <select name="asset" id="asset" class="form-control" required>
                    <option value="">{{ __('Select Asset') }}</option>
                    @foreach ($assets as $asset)
                        <option value="{{ $asset->id }}" {{ $kit->asset == $asset->id ? 'selected' : '' }}>{{ $asset->title }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('Component', __('Component'), ['class' => 'form-label']) }}<x-required></x-required>
                <select name="component" id="component" class="form-control" required>
                    <option value="">{{ __('Select Component') }}</option>
                    @foreach ($components as $component)
                        <option value="{{ $component->id }}" {{ $kit->component == $component->id ? 'selected' : '' }}>{{ $component->title }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
    {{ Form::submit(__('Update'), ['class' => 'btn  btn-primary']) }}
</div>
{{ Form::close() }}
