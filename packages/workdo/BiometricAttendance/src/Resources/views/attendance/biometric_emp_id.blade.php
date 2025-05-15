<div class="form-group col-md-6">
    {!! Form::label('biometric_emp_id', __('Employee Code'), ['class' => 'form-label']) !!}<x-required></x-required>
    {!! Form::text('biometric_emp_id', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Employee Code')]) !!}
</div>