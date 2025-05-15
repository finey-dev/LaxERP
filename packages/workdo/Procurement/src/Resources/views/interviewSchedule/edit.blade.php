{{ Form::model($interviewSchedule, ['route' => ['rfx-interview-schedule.update', $interviewSchedule->id], 'method' => 'PUT','class'=>'needs-validation','novalidate']) }}
<div class="modal-body">
    <div class="text-end">
        @if (module_is_active('AIAssistant'))
            @include('aiassistant::ai.generate_ai_btn',['template_module' => 'interview-schedule','module'=>'Procurement'])
        @endif
    </div>
    <div class="row">
        <div class="form-group col-md-12">
            {{ Form::label('applicant', __('Interviewer'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::select('applicant', $applicants, null, ['class' => 'form-control ', 'required' => 'required']) }}
        </div>
        <div class="form-group col-md-12">
            {{ Form::label('date', __('Interview Date'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::date('date', null, ['class' => 'form-control d_week','required' => 'required']) }}
        </div>
        <div class="form-group col-md-12">
            {{ Form::label('time', __('Interview Time'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::time('time', null, ['class' => 'form-control timepicker','required' => 'required']) }}
        </div>
        <div class="form-group">
            {{ Form::label('comment', __('Comment'), ['class' => 'form-label']) }}
            {{ Form::textarea('comment', null, ['class' => 'form-control','rows'=>3]) }}
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
    <input type="submit" value="{{ __('Update') }}" class="btn  btn-primary">

</div>
{{ Form::close() }}
