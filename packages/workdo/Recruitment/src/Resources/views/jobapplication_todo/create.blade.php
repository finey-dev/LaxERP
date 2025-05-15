{{ Form::open(['route' => ['jobapplicationtodo.store', 'job_id' => $job->id], 'method' => 'post', 'class' => 'needs-validation', 'novalidate']) }}
<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('title', __('Title'), ['class' => 'form-label']) }}<x-required></x-required>
                <div class="form-icon-user">
                    {{ Form::text('title', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Title')]) }}
                </div>
            </div>
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('start_date', __('Start Date'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::date('start_date', date('Y-m-d'), ['class' => 'form-control', 'placeholder' => __('Select Date'), 'required' => 'required', 'min' => date('Y-m-d')]) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('due_date', __('Due Date'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::date('due_date', date('Y-m-d'), ['class' => 'form-control', 'placeholder' => __('Select Date'), 'required' => 'required', 'min' => date('Y-m-d')]) }}
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('assigned_to', __('Assigned To'), ['class' => 'form-label']) }}<x-required></x-required>
                <div class="form-icon-user">
                    <select class=" multi-select choices" id="users_list" name="assigned_to[]" multiple="multiple"
                        data-placeholder="{{ __('Select Users ...') }}">
                        @foreach ($users as $key => $user)
                            <option value="{{ $key }}">{{ $user }}</option>
                        @endforeach
                    </select>
                </div>

            </div>
        </div>

        <div class="form-group col-md-6">
            <label class="form-label">{{ __('Priority') }}</label><x-required></x-required>
            <select class="form-control form-control-light" name="priority" id="task-priority" required>
                <option value="Low">{{ __('Low') }}</option>
                <option value="Medium">{{ __('Medium') }}</option>
                <option value="High">{{ __('High') }}</option>
            </select>
        </div>

        <div class="col-lg-12">
            <div class="form-group">
                {{ Form::label('description', __('Description'), ['class' => 'form-label']) }}<x-required></x-required>
                <div class="form-icon-user">
                    {{ Form::textarea('description', null, ['class' => 'form-control', 'placeholder' => __('Enter Description'), 'rows' => 3, 'maxlength' => 250, 'required']) }}

                </div>
            </div>
        </div>
    </div>

</div>

<div class="modal-footer">
    <input type="button" value="{{ __('Cancel') }}" class="btn btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Create') }}" class="btn btn-primary" id="submit">
</div>
{{ Form::close() }}