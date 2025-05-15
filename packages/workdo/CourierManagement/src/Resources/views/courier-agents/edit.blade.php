{{ Form::model($courier_agents, ['route' => ['courier-agents.update', $courier_agents->id], 'method' => 'PUT', 'class' => 'needs-validation', 'novalidate']) }}

<div class="modal-body">
    <div class="row">
        <div class="form-group col-md-6">
            {{ Form::label('agent_name', __('Agent Name'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::text('name', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Agent Name')]) }}
        </div>
        <x-mobile divClass='col-md-6' name='phone' class='form-control' ></x-mobile>

        <div class="form-group col-md-6">
            {{ Form::label('email', __('E-mail'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::email('email', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter E-mail')]) }}
        </div>

      <div class="form-group col-md-6">
            {{ Form::label('branch_id', __('Branch'), ['class' => 'form-label']) }}<x-required></x-required>
            <select name="branch_id" class="form-control select" id="branch_id" required>
                <option value="">{{ __('Select branch') }}</option>
                @foreach($courier_agent as $branch)
                    <option value="{{ $branch->id }}" {{isset($courier_agents) ? $courier_agents->branch_id ==  $branch->id ? 'selected'
                    : '' : ''}}>{{ $branch->branch_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-12">
            <div class="form-group">
            {{ Form::label('status', __('Status'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::select('status', [null => __('Select Status'), 1 => __('Active'), 0 => __('Inactive')], null, array('class' => 'form-control select','required'=>'required')) }}
                @error('status')
                <small class="invalid-name" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </small>
                @enderror
            </div>
        </div>
        <div class="form-group col-md-12">
            {{ Form::label('address', __('Address'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::textarea('address', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Address'), 'rows'=>3]) }}
        </div>
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{ __('Cancel') }}" class="btn  btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Update') }}" class="btn  btn-primary">
</div>
{{ Form::close() }}
