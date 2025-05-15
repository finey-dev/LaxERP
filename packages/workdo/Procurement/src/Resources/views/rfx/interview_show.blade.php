<div class="modal-body">
    <div class="card-body pb-0 pt-2">
        <h6 class="mb-4">{{ __('Schedule Detail') }}</h6>
        <dl class="row mb-0 align-items-center">
            <dt class="col-sm-4 h6 text-sm">{{ __('RFx') }}</dt>
            <dd class="col-sm-8 text-sm">
                {{ !empty($interviewSchedule->applications) ? (!empty($interviewSchedule->applications->rfxs) ? $interviewSchedule->applications->rfxs->title : '-') : '-' }}
            </dd>
            <dt class="col-sm-4 h6 text-sm">{{ __('Interview On') }}</dt>
            <dd class="col-sm-8 text-sm">
                {{ company_date_formate($interviewSchedule->date,$interviewSchedule->created_by, $interviewSchedule->workspace) . ' ' . company_datetime_formate($interviewSchedule->time,$interviewSchedule->created_by, $interviewSchedule->workspace) }}
            </dd>
            <dt class="col-sm-4 h6 text-sm">{{ __('Assign Employee') }}</dt>
            <dd class="col-sm-8 text-sm">{{ !empty($interviewSchedule->users) ? $interviewSchedule->users->name : '-' }}
            </dd>
        </dl>
    </div>
    <div class="card-body pb-0 pt-2">
        <h6 class="mb-4">{{ __('Applicant Detail') }}</h6>
        <dl class="row mb-0 align-items-center">
            <dt class="col-sm-4 h6 text-sm">{{ __('Name') }}</dt>
            <dd class="col-sm-8 text-sm">
                {{ $interviewSchedule->applications ? $interviewSchedule->applications->name : '-' }}</dd>
            <dt class="col-sm-4 h6 text-sm">{{ __('Email') }}</dt>
            <dd class="col-sm-8 text-sm">
                {{ $interviewSchedule->applications ? $interviewSchedule->applications->email : '-' }}</dd>
            <dt class="col-sm-4 h6 text-sm">{{ __('Phone') }}</dt>
            <dd class="col-sm-8 text-sm">
                {{ $interviewSchedule->applications ? $interviewSchedule->applications->phone : '-' }}</dd>
        </dl>
    </div>
    <div class="card-body pt-2">
        <h6 class="mb-4">{{ __('Applicant Status') }}</h6>
        @foreach ($stages as $stage)
            <div class="custom-control custom-radio ">
                <label class="custom-control-label {{ !empty($interviewSchedule->applications) ? (!empty($interviewSchedule->applications->stage == $stage->id) ? 'badge bg-success rounded p-2' : '') : '' }}" for="stage_{{ $stage->id }}">{{ (!empty($interviewSchedule->applications->stage == $stage->id) ? $stage->title : '') }}</label>
            </div>
        @endforeach
    </div>
</div>
