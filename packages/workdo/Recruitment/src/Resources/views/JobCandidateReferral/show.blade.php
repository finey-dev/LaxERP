<div class="modal-body">
    <div class="row">
        <div class="col-sm-6">
            <div class="card-body pb-0 pt-2">
                <dl class="row mb-0 align-items-center">
                    <dt class="col-sm-4 h6 text-md">{{ __('Title') }}</dt>
                    <dd class="col-sm-8 text-md">
                        {{ !empty($jobcandidate_referral->title) ? $jobcandidate_referral->title : '-' }}
                    </dd>
                    <dt class="col-sm-4 h6 text-md">{{ __('Start Date') }}</dt>
                    <dd class="col-sm-8 text-md">
                        {{ !empty($jobcandidate_referral->start_date) ? company_date_formate($jobcandidate_referral->start_date) : '-' }}
                    </dd>
                    <dt class="col-sm-4 h6 text-md">{{ __('Country') }}</dt>
                    <dd class="col-sm-8 text-md">{{ !empty($jobcandidate_referral->country) ? $jobcandidate_referral->country : '-' }}
                    </dd>
                    <dt class="col-sm-4 h6 text-md">{{ __('City') }}</dt>
                    <dd class="col-sm-8 text-md">{{ !empty($jobcandidate_referral->city) ? $jobcandidate_referral->city : '-' }}
                    </dd>
                </dl>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="card-body pb-0 pt-2">
                <dl class="row mb-0 align-items-center">
                    <dt class="col-sm-4 h6 text-md">{{ __('Organization') }}</dt>
                    <dd class="col-sm-8 text-md">
                        {{ $jobcandidate_referral->organization ? $jobcandidate_referral->organization : '-' }}</dd>
                    <dt class="col-sm-4 h6 text-md">{{ __('End Date') }}</dt>
                    <dd class="col-sm-8 text-md">
                        {{ $jobcandidate_referral->end_date ? $jobcandidate_referral->end_date : '-' }}</dd>
                    <dt class="col-sm-4 h6 text-md">{{ __('State') }}</dt>
                    <dd class="col-sm-8 text-md">
                        {{ $jobcandidate_referral->state ? $jobcandidate_referral->state : '-' }}</dd>
                    <dt class="col-sm-4 h6 text-md">{{ __('Reference') }}</dt>
                    <dd class="col-sm-8 text-md">
                        {{ !empty($jobcandidate_referral->reference) ? ($jobcandidate_referral->reference == 'yes' ? 'Yes' : 'No') : '-' }}</dd>
                </dl>
            </div>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-sm-6">
            <div class="card-body pb-0 pt-2">
                <dl class="row mb-0 align-items-center">
                    <dt class="col-sm-4 h6 text-md">{{ __('Reference Full Name') }}</dt>
                    <dd class="col-sm-8 text-md">
                        {{ !empty($jobcandidate_referral->full_name) ? $jobcandidate_referral->full_name : '-' }}
                    </dd>
                    <dt class="col-sm-4 h6 text-md">{{ __('Reference Phone') }}</dt>
                    <dd class="col-sm-8 text-md">
                        {{ !empty($jobcandidate_referral->reference_phone) ? $jobcandidate_referral->reference_phone : '-' }}
                    </dd>
                </dl>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="card-body pb-0 pt-2">
                <dl class="row mb-0 align-items-center">
                    <dt class="col-sm-4 h6 text-md">{{ __('Reference Job Position') }}</dt>
                    <dd class="col-sm-8 text-md">
                        {{ $jobcandidate_referral->job_position ? $jobcandidate_referral->job_position : '-' }}
                    </dd>
                    <dt class="col-sm-4 h6 text-md">{{ __('Reference Email') }}</dt>
                    <dd class="col-sm-8 text-md">
                        {{ $jobcandidate_referral->reference_email ? $jobcandidate_referral->reference_email : '-' }}
                    </dd>
                </dl>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-sm-12">
                <div class="card-body pb-0 pt-2">
                    <dl class="row mb-0 align-items-center">
                        <dt class="col-sm-4 h6 text-md">{{ __('Description') }}</dt>
                        <dd class="col-sm-8 text-md">
                            {!! !empty($jobcandidate_referral->description) ? $jobcandidate_referral->description : '-' !!}
                        </dd>
                        <dt class="col-sm-4 h6 text-md">{{ __('Experience Document') }}</dt>
                        <dd class="col-sm-8 text-md">
                            @if (!empty($jobcandidate_referral->experience_proof))
                                <div class="action-btn bg-primary ms-2">
                                    <a class="mx-3 btn btn-sm align-items-center"
                                        href="{{ get_file($jobcandidate_referral->experience_proof) }}" download>
                                        <i class="ti ti-download text-white"></i>
                                    </a>
                                </div>
                                <div class="action-btn bg-secondary ms-2">
                                    <a class="mx-3 btn btn-sm align-items-center"
                                        href="{{ get_file($jobcandidate_referral->experience_proof) }}" target="_blank">
                                        <i class="ti ti-crosshair text-white" data-bs-toggle="tooltip"
                                            data-bs-original-title="{{ __('Preview') }}"></i>
                                    </a>
                                </div>
                            @else
                                <p>-</p>
                            @endif
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>
