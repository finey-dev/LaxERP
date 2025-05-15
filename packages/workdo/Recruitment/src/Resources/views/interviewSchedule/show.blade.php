<div class="modal-body">
    <div class="card-body pb-0 pt-2">
        <h6 class="mb-2">{{ __('Schedule Detail') }}</h6>
        <dl class="row mb-0 align-items-center">
            <dt class="col-sm-4 h6 text-sm">{{ __('Job') }}</dt>
            <dd class="col-sm-8 text-sm">
                {{ !empty($interviewSchedule->applications) ? (!empty($interviewSchedule->applications->jobs) ? $interviewSchedule->applications->jobs->title : '-') : '-' }}
            </dd>
            <dt class="col-sm-4 h6 text-sm">{{ __('Interview On') }}</dt>
            <dd class="col-sm-8 text-sm">
                {{ company_date_formate($interviewSchedule->date) . ' ' . company_datetime_formate($interviewSchedule->time) }}
            </dd>
            <dt class="col-sm-4 h6 text-sm">{{ __('Assign Employee') }}</dt>
            <dd class="col-sm-8 text-sm">{{ !empty($interviewSchedule->users) ? $interviewSchedule->users->name : '-' }}
            </dd>
            <dt class="col-sm-4 h6 text-sm">{{ __('Meeting Type') }}</dt>
            <dd class="col-sm-8 text-sm">
                {{ !empty($interviewSchedule->meeting_type) ? $interviewSchedule->meeting_type : '-' }}
            </dd>
            <dt class="col-sm-4 h6 text-sm">{{ __('Start Url / Join Url') }}</dt>
            <dd class="col-sm-8 text-sm">
                <div class="urls">
                    @if (!empty($interviewSchedule->start_url))
                        <a href="{{ $interviewSchedule->start_url }}" class="btn btn-primary btn-sm text-sm"
                            data-bs-whatever="{{ __('Start Url') }}" data-bs-toggle="tooltip"
                            data-bs-original-title="{{ __('Start Url') }}" target="_blank">
                            <i class="ti ti-external-link"></i>
                            {{ __('Start Url') }}
                        </a>
                    @endif
                    @if (!empty($interviewSchedule->join_url))
                        <a href="{{ $interviewSchedule->join_url }}" class="btn btn-primary btn-sm text-sm"
                            data-bs-whatever="{{ __('Join Url') }}" data-bs-toggle="tooltip"
                            data-bs-original-title="{{ __('Join Url') }}" target="_blank">
                            <i class="ti ti-external-link"></i>
                            {{ __('Join Url') }}
                        </a>
                    @endif
                </div>
            </dd>
        </dl>
    </div>
    <div class="card-body pb-0 pt-2">
        <h6 class="mb-2 mt-2">{{ __('Candidate Detail') }}</h6>
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
        <h6 class="mb-2 mt-2">{{ __('Candidate Status') }}</h6>
        @foreach ($stages as $stage)
            <div class="custom-control custom-radio ">
                <input type="radio" id="stage_{{ $stage->id }}" name="stage"
                    data-scheduleid="{{ $interviewSchedule->candidate }}" value="{{ $stage->id }}"
                    class="form-check-input stages"
                    {{ !empty($interviewSchedule->applications) ? (!empty($interviewSchedule->applications->stage == $stage->id) ? 'checked' : '') : '' }}>
                <label class="custom-control-label" for="stage_{{ $stage->id }}">{{ $stage->title }}</label>
            </div>
        @endforeach
    </div>
    @if (
        $interviewSchedule->applications->stages->title != __('Rejected') &&
            $interviewSchedule->applications->stages->title != __('rejected'))
        <div class="modal-footer">
            <a data-url="{{ route('job.on.board.create', $interviewSchedule->candidate) }}" data-ajax-popup="true"
                class="btn btn-primary text-white"> {{ __('Add to Job OnBoard') }}</a>
        </div>
    @endif
</div>
