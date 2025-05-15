{{ Form::open(['url' => 'meetinghub/meeting-task/store', 'method' => 'post','class'=>'needs-validation','novalidate']) }}
<div class="modal-body">
    <div class="row">
        <input type="hidden" name="meeting_minute_id" value="{{$id}}">
        <div class="col-md-12 form-group">
            {{ Form::label('name', __('Name'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::text('name', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Task Name')]) }}

        </div>
        <div class="col-md-6 form-group">
            {{ Form::label('date', __('Date'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::date('date', 'y-m-d', ['class' => 'form-control', 'required' => 'required']) }}
        </div>
        <div class="col-md-6 form-group">
            {{ Form::label('date', __('Time'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::time('time', null, ['class' => 'form-control', 'required' => 'required']) }}
        </div>
        <div class="col-md-6 form-group">
            {{ Form::label('priority', __('Priority'), ['class' => 'form-label']) }}<x-required></x-required>
            <select name="priority" id="priority" class="form-control" required>
                <option value="" selected disabled>{{ __('Select Priority') }}</option>
                <option value="High">{{ __('High') }}</option>
                <option value="Medium">{{ __('Medium') }}</option>
                <option value="Low">{{ __('Low') }}</option>
            </select>
        </div>
        <div class="col-md-6 form-group">
            {{ Form::label('status', __('Status'), ['class' => 'form-label']) }}
            {{ Form::select('status', $status, isset($_GET['status']) ? $_GET['status'] : '', ['class' => 'form-control']) }}
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
    {{ Form::submit(__('Create'), ['class' => 'btn  btn-primary']) }}
</div>
{{ Form::close() }}
