@php
    $company_settings = getCompanyAllSetting();
@endphp
{{ Form::model($training, ['route' => ['training.update', $training->id], 'method' => 'PUT', 'class' => 'needs-validation', 'novalidate']) }}
<div class="modal-body">
    <div class="text-end">
        @if (module_is_active('AIAssistant'))
            @include('aiassistant::ai.generate_ai_btn',['template_module' => 'training','module'=>'Training'])
        @endif
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('branch', !empty($company_settings['hrm_branch_name']) ? $company_settings['hrm_branch_name'] : __('Branch'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::select('branch', $branches, null, ['class' => 'form-control ', 'placeholder' => __('Select Branch'), 'required' => 'required']) }}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('trainer_option', __('Trainer Option'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::select('trainer_option', $options, null, ['class' => 'form-control ', 'placeholder' => __('Select Trainer Option'), 'required' => 'required']) }}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('training_type', __('Training Type'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::select('training_type', $trainingTypes, null, ['class' => 'form-control ', 'placeholder' => __('Select Training Type'), 'required' => 'required']) }}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('trainer', __('Trainer'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::select('trainer', $trainers, null, ['class' => 'form-control ', 'placeholder' => __('Select Trainer'), 'required' => 'required']) }}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('training_cost', __('Training Cost'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::number('training_cost', null, ['class' => 'form-control', 'step' => '1', 'min' => '0', 'required' => 'required', 'placeholder' => __('Enter Training Cost')]) }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('employee', __('Employee'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::select('employee', $employees, null, ['class' => 'form-control ', 'placeholder' => __('Select Employee'), 'required' => 'required']) }}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('start_date', __('Start Date'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::date('start_date', null, ['class' => 'form-control ', 'autocomplete' => 'off', 'required' => 'required']) }}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('end_date', __('End Date'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::date('end_date', null, ['class' => 'form-control ', 'autocomplete' => 'off', 'required' => 'required']) }}
            </div>
        </div>
        <div class="form-group col-lg-12">
            {{ Form::label('description', __('Description'), ['class' => 'form-label']) }}
            {{ Form::textarea('description', null, ['class' => 'form-control', 'placeholder' => __('Enter Description'), 'rows' => '3']) }}
        </div>
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{ __('Cancel') }}" class="btn btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Update') }}" class="btn btn-primary">
</div>
{{ Form::close() }}
