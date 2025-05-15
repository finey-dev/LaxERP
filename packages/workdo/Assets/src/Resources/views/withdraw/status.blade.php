{{ Form::open(['route' => ['assets.defective.store',$assetdefective->id], 'method' => 'post']) }}
<div class="modal-body">
    <div class="row">
        <div class="form-group col-md-12">
            {{ Form::label('name', __('Name'), ['class' => 'form-label']) }}
            {{ Form::text('name', $assetdefective->module->name, ['class' => 'form-control', 'required' => 'required','readonly' => 'readonly']) }}
        </div>

        <div class="form-group col-md-12">
            {{ Form::label('reason', __('Reason'), ['class' => 'form-label']) }}
            {{ Form::text('reason',$assetdefective->reason, ['class' => 'form-control' , 'required' => 'required','readonly' => 'readonly']) }}
        </div>

        <div class="form-group col-md-12">
            {{ Form::label('quantity', __('Quantity'), ['class' => 'form-label']) }}
            {{ Form::number('quantity', $assetdefective->quantity, ['class' => 'form-control','readonly' => 'readonly']) }}
        </div>

        <div class="form-group col-md-12">
            {{ Form::label('code', __('Code'), ['class' => 'form-label']) }}
            {{ Form::text('code',$assetdefective->code, ['class' => 'form-control','required' => 'required','readonly' => 'readonly']) }}
        </div>
        @if($assetdefective->status != 'Fail')
            <div class="form-group col-md-12">
                <label for="status" class="form-label">{{ __('Status') }}</label>
                <select name="status" class='form-control font-style'>
                    @foreach (\Workdo\Assets\Entities\AssetDefective::$type as $key => $value)
                        <option value="{{ $value }}">{{ $value }}</option>
                    @endforeach
                </select>
            </div>
        @endif
    </div>
</div>
<div class="modal-footer">
        <input type="button" value="{{ __('Cancel') }}" class="btn  btn-light" data-bs-dismiss="modal">
        <input type="submit" value="{{ __('Update') }}" class="btn  btn-primary">
</div>
{{ Form::close() }}

