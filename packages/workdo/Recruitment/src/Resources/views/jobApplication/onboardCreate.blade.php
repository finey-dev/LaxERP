{{ Form::open(['route' => ['job.on.board.store', $id], 'method' => 'post', 'class' => 'needs-validation', 'novalidate']) }}
<div class="modal-body">
    <div class="row">
        <div class="form-group col-md-6">
            {{ Form::label('type', __('Type'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::select('type', $recruitment_type, null, ['class' => 'form-control select', 'id' => 'recruitment_type', 'required' => 'required']) }}
            <p class="text-danger d-none" id="{{ 'recruitment_type_validation' }}">
                {{ __('This field is required.') }}</p>
        </div>

        @if (module_is_active('Hrm'))
            <div class="form-group col-md-6" id="branch_id" style="display: none;">
                {{ Form::label('branch_id', __('Account'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::select('branch_id', $branches, null, ['class' => 'form-control', 'placeholder' => __('Select Branch')]) }}
            </div>
        @endif

        <div class="col-6 form-group" id="users" style="display: none;">
            {{ Form::label('user_id', __('Account'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::select('user_id', $users, null, ['class' => 'form-control']) }}
            @if (empty($users->count()))
                <div class="text-muted text-xs">
                    {{ __('Please create new client') }} <a href="{{ route('users.index') }}">{{ __('here') }}</a>.
                </div>
            @endif
            <p class="text-danger d-none" id="user_id_validation">
                {{ __('This field is required.') }}
            </p>
        </div>
        @if ($id == 0)
            <div class="form-group col-md-6">
                {{ Form::label('application', __('Job Candidate'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::select('application', $applications, null, ['class' => 'form-control select2', 'required' => 'required']) }}
            </div>
        @endif
        <div class="form-group col-md-6">
            {!! Form::label('joining_date', __('Joining Date'), ['class' => 'form-label']) !!}<x-required></x-required>
            {!! Form::date('joining_date', null, [
                'class' => 'form-control ',
                'autocomplete' => 'off',
                'required' => 'required',
            ]) !!}
        </div>

        <div class="form-group col-md-6">
            {!! Form::label('days_of_week', __('Days Of Week'), ['class' => 'form-label']) !!}
            {!! Form::number('days_of_week', null, ['class' => 'form-control ', 'autocomplete' => 'off', 'min' => '0' , 'placeholder' => __('Enter Days Of Week')]) !!}
        </div>
        <div class="form-group col-md-6">
            {!! Form::label('salary', __('Salary'), ['class' => 'form-label']) !!}
            {!! Form::number('salary', null, ['class' => 'form-control ', 'autocomplete' => 'off', 'min' => '0' , 'placeholder' => __('Enter Salary')]) !!}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('salary_type', __('Salary Type'), ['class' => 'form-label']) }}
            {{ Form::select('salary_type', $salary_type, null, ['class' => 'form-control select']) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('salary_duration', __('Salary Duration'), ['class' => 'form-label']) }}
            {{ Form::select('salary_duration', $salary_duration, null, ['class' => 'form-control select']) }}
        </div>
        <div class="form-group col-md-6 ">
            {{ Form::label('job_type', __('Job Type'), ['class' => 'form-label']) }}
            {{ Form::select('job_type', $job_type, null, ['class' => 'form-control select']) }}
        </div>
        <div class="form-group col-md-6 ">
            {{ Form::label('status', __('Status'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::select('status', $status, null, ['class' => 'form-control select', 'required' => 'required']) }}
        </div>
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{ __('Cancel') }}" class="btn btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Create') }}" class="btn btn-primary">
</div>
{{ Form::close() }}


<script>
    $(document).ready(function() {
        function toggleFormGroups() {
            var selectedType = $('#recruitment_type').val();
            if (selectedType === 'internal') {
                $('#branch_id').show();
                $('#users').hide();
                $('#branch select').prop('required', true);
                $('#users select').prop('required', false);
            } else if (selectedType === 'client') {
                $('#branch_id').hide();
                $('#users').show();
                $('#users select').prop('required', true);
                $('#branch select').prop('required', false);
            } else {
                $('#branch_id').hide();
                $('#users').hide();
                $('#users select').prop('required', false);
                $('#branch select').prop('required', false);
            }
        }

        toggleFormGroups();

        $('#recruitment_type').change(toggleFormGroups);
    });
</script>
