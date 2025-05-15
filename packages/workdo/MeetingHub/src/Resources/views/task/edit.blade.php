{{ Form::model($task, ['route' => ['meeting-task.update', $task->id], 'method' => 'PUT','class'=>'needs-validation','novalidate']) }}
<div class="modal-body">
    <div class="row">
        <div class="col-md-12 form-group">
            {{ Form::label('name', __('Name'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::text('name', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Task Name')]) }}

        </div>
        <div class="col-md-6 form-group">
            {{ Form::label('date', __('Date'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::date('date', null, ['class' => 'form-control', 'required' => 'required']) }}
        </div>
        <div class="col-md-6 form-group">
            {{ Form::label('date', __('Time'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::time('time', null, ['class' => 'form-control', 'required' => 'required']) }}
        </div>
        <div class="col-md-6 form-group">
            {{ Form::label('priority', __('Priority'), ['class' => 'form-label']) }}<x-required></x-required>
            <select name="priority" id="priority" class="form-control" required>
                <option value="" selected disabled>{{ __('Select Priority') }}</option>
                <option value="High" @if ($task->priority == 'High') selected @endif>
                    {{ __('High') }}</option>
                <option value="Medium" @if ($task->priority == 'Medium') selected @endif>
                    {{ __('Medium') }}</option>
                <option value="Low" @if ($task->priority == 'Low') selected @endif>
                    {{ __('Low') }}</option>
            </select>
        </div>
        <div class="col-md-6 form-group">
            {{ Form::label('status', __('Status'), ['class' => 'form-label']) }}
            {{ Form::select('status', $status, null, ['class' => 'form-control']) }}
        </div>
    </div>
</div>
<div class="modal-footer">
    <div class="text-end">
        <input type="button" value="{{ __('Cancel') }}" class="btn  btn-light me-1" data-bs-dismiss="modal">
        <input type="submit" value="{{ __('Update') }}" class="btn  btn-primary">
    </div>
</div>
{{ Form::close() }}
