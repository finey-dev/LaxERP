<div class="modal-body">
    <div class="row">
        <div class="col-sm-6">
            <div class="card-body pb-0 pt-2">
                <dl class="row mb-0 align-items-center">
                    <dt class="col-sm-4 h6 text-md">{{ __('Title') }}</dt>
                    <dd class="col-sm-8 text-md">
                        {{ !empty($job_experience->title) ? $job_experience->title : '-' }}
                    </dd>
                    <dt class="col-sm-4 h6 text-md">{{ __('Start Date') }}</dt>
                    <dd class="col-sm-8 text-md">
                        {{ !empty($job_experience->start_date) ? company_date_formate($job_experience->start_date) : '-' }}
                    </dd>
                    <dt class="col-sm-4 h6 text-md">{{ __('Country') }}</dt>
                    <dd class="col-sm-8 text-md">{{ !empty($job_experience->country) ? $job_experience->country : '-' }}
                    </dd>
                    <dt class="col-sm-4 h6 text-md">{{ __('City') }}</dt>
                    <dd class="col-sm-8 text-md">{{ !empty($job_experience->city) ? $job_experience->city : '-' }}
                    </dd>
                </dl>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="card-body pb-0 pt-2">
                <dl class="row mb-0 align-items-center">
                    <dt class="col-sm-4 h6 text-md">{{ __('Organization') }}</dt>
                    <dd class="col-sm-8 text-md">
                        {{ $job_experience->organization ? $job_experience->organization : '-' }}</dd>
                    <dt class="col-sm-4 h6 text-md">{{ __('End Date') }}</dt>
                    <dd class="col-sm-8 text-md">
                        {{ $job_experience->end_date ? $job_experience->end_date : '-' }}</dd>
                    <dt class="col-sm-4 h6 text-md">{{ __('State') }}</dt>
                    <dd class="col-sm-8 text-md">
                        {{ $job_experience->state ? $job_experience->state : '-' }}</dd>
                    <dt class="col-sm-4 h6 text-md">{{ __('Zip Code') }}</dt>
                    <dd class="col-sm-8 text-md">
                        {{ $job_experience->zipcode ? $job_experience->zipcode : '-' }}</dd>
                </dl>
            </div>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-sm-12">
            <div class="card-body pb-0 pt-2">
                <dl class="row mb-0 align-items-center">
                    <dt class="col-sm-4 h6 text-md">{{ __('Address') }}</dt>
                    <dd class="col-sm-8 text-md">
                        {{ !empty($job_experience->address) ? $job_experience->address : '-' }}
                    </dd>
                    <dt class="col-sm-4 h6 text-md">{{ __('Phone') }}</dt>
                    <dd class="col-sm-8 text-md">
                        {{ !empty($job_experience->phone) ? $job_experience->phone : '-' }}
                    </dd>
                    <dt class="col-sm-4 h6 text-md">{{ __('Email') }}</dt>
                    <dd class="col-sm-8 text-md">
                        {{ !empty($job_experience->email) ? $job_experience->email : '-' }}
                    </dd>
                    <dt class="col-sm-4 h6 text-md">{{ __('Website') }}</dt>
                    <dd class="col-sm-8 text-md">
                        {{ !empty($job_experience->website) ? $job_experience->website : '-' }}
                    </dd>
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
                        {{ !empty($job_experience->full_name) ? $job_experience->full_name : '-' }}
                    </dd>
                    <dt class="col-sm-4 h6 text-md">{{ __('Reference Phone') }}</dt>
                    <dd class="col-sm-8 text-md">
                        {{ !empty($job_experience->reference_phone) ? $job_experience->reference_phone : '-' }}
                    </dd>
                </dl>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="card-body pb-0 pt-2">
                <dl class="row mb-0 align-items-center">
                    <dt class="col-sm-4 h6 text-md">{{ __('Reference Job Position') }}</dt>
                    <dd class="col-sm-8 text-md">
                        {{ $job_experience->job_position ? $job_experience->job_position : '-' }}
                    </dd>
                    <dt class="col-sm-4 h6 text-md">{{ __('Reference Email') }}</dt>
                    <dd class="col-sm-8 text-md">
                        {{ $job_experience->reference_email ? $job_experience->reference_email : '-' }}
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
                            {{ !empty($job_experience->description) ? $job_experience->description : '-' }}
                        </dd>
                        <dt class="col-sm-4 h6 text-md">{{ __('Experience Document') }}</dt>
                        <dd class="col-sm-8 text-md">
                            @if (!empty($job_experience->experience_proof))
                                <div class="action-btn bg-primary ms-2">
                                    <a class="mx-3 btn btn-sm align-items-center"
                                        href="{{ get_file($job_experience->experience_proof) }}" download>
                                        <i class="ti ti-download text-white"></i>
                                    </a>
                                </div>
                                <div class="action-btn bg-secondary ms-2">
                                    <a class="mx-3 btn btn-sm align-items-center"
                                        href="{{ get_file($job_experience->experience_proof) }}" target="_blank">
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
