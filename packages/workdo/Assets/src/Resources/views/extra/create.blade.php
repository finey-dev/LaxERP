{{ Form::open(['route' => ['extra.store',$asset->id], 'method' => 'post','class'=>'needs-validation','novalidate']) }}
<div class="modal-body">
    <div class="text-end">
        @if (module_is_active('AIAssistant'))
            @include('aiassistant::ai.generate_ai_btn', ['template_module' => 'asset', 'module' => 'Assets'])
        @endif
    </div>
    <div class="row">
        <div class="form-group col-md-12">
            {{ Form::label('serial_code', __('Serial Code'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::text('serial_code',$asset->serial_code,['class' => 'form-control', 'required' => 'required', 'step' => '1', 'placeholder' => __('Enter serial Code')]) }}
        </div>

        <div class="form-group col-md-6">
            {{ Form::label('quantity', __('Quantity'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::number('quantity', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Quantity')]) }}
        </div>

        <div class="form-group col-md-6">
            {{ Form::label('date', __('Date'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::date('date',date('Y-m-d'), ['class' => 'form-control ','required'=>'required','placeholder' => __('Select Date')]) }}
        </div>

        <div class="form-group col-md-12">
            {{ Form::label('description', __('Description'), ['class' => 'form-label']) }}
            {{ Form::textarea('description', null, ['class' => 'form-control', 'rows' => 3, 'placeholder' => __('Enter Description')]) }}
        </div>


    </div>
</div>
<div class="modal-footer">
    <div class="text-end">
        <input type="button" value="{{ __('Cancel') }}" class="btn  btn-light me-1" data-bs-dismiss="modal">
        <input type="submit" value="{{ __('Create') }}" class="btn  btn-primary">
    </div>
</div>
{{ Form::close() }}
