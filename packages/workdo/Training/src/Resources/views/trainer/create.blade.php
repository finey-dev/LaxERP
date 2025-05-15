@php
    $company_settings = getCompanyAllSetting();
@endphp
{{ Form::open(['url' => 'trainer', 'method' => 'post', 'class' => 'needs-validation', 'novalidate']) }}
<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('branch', !empty($company_settings['hrm_branch_name']) ? $company_settings['hrm_branch_name'] : __('Branch'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::select('branch', $branches, null, ['class' => 'form-control ', 'placeholder' => __('Select Branch'), 'required' => 'required']) }}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('firstname', __('First Name'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::text('firstname', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter First Name')]) }}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('lastname', __('Last Name'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::text('lastname', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Last Name')]) }}
            </div>
        </div>
        <x-mobile divClass="col-md-6" name="contact" label="{{ __('Contact') }}"
            placeholder="{{ __('Enter Contact') }}" id="contact" required>
        </x-mobile>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('email', __('Email'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::email('email', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Email')]) }}
            </div>
        </div>
        <div class="form-group col-lg-12">
            {{ Form::label('expertise', __('Expertise'), ['class' => 'form-label']) }}
            {{ Form::textarea('expertise', null, ['class' => 'form-control', 'placeholder' => __('Enter Expertise'), 'rows' => '3']) }}
        </div>
        <div class="form-group col-lg-12">
            {{ Form::label('address', __('Address'), ['class' => 'form-label']) }}
            {{ Form::textarea('address', null, ['class' => 'form-control', 'placeholder' => __('Enter Address'), 'rows' => '3']) }}
        </div>
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{ __('Cancel') }}" class="btn btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Create') }}" class="btn btn-primary">
</div>
{{ Form::close() }}
