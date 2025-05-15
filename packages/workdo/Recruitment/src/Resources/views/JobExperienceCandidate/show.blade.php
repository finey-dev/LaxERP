<div class="modal-body">
    <div class="row">
        <div class="col-sm-6">
            <div class="card-body pb-0 pt-2">
                <dl class="row mb-0 align-items-center">
                    <dt class="col-sm-4 h6 text-md">{{ __('Title') }}</dt>
                    <dd class="col-sm-8 text-md">
                        {{ !empty($job_experience_candidate->title) ? $job_experience_candidate->title : '-' }}
                    </dd>
                    <dt class="col-sm-4 h6 text-md">{{ __('Organization') }}</dt>
                    <dd class="col-sm-8 text-md">
                        {{ !empty($job_experience_candidate->organization) ? $job_experience_candidate->organization : '-' }}
                    </dd>
                    <dt class="col-sm-4 h6 text-md">{{ __('Start Date') }}</dt>
                    <dd class="col-sm-8 text-md">
                        {{ !empty($job_experience_candidate->start_date) ? company_date_formate($job_experience_candidate->start_date) : '-' }}
                    </dd>
                    <dt class="col-sm-4 h6 text-md">{{ __('End Date') }}</dt>
                    <dd class="col-sm-8 text-md">{{ !empty($job_experience_candidate->end_date) ? company_date_formate($job_experience_candidate->end_date) : '-' }}
                    </dd>
                </dl>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="card-body pb-0 pt-2">
                <dl class="row mb-0 align-items-center">
                    <dt class="col-sm-4 h6 text-md">{{ __('Country') }}</dt>
                    <dd class="col-sm-8 text-md">
                        {{ !empty($job_experience_candidate->country) ? $job_experience_candidate->country : '-' }}</dd>
                    <dt class="col-sm-4 h6 text-md">{{ __('State') }}</dt>
                    <dd class="col-sm-8 text-md">
                        {{ !empty($job_experience_candidate->state) ? $job_experience_candidate->state : '-' }}</dd>
                    <dt class="col-sm-4 h6 text-md">{{ __('City') }}</dt>
                    <dd class="col-sm-8 text-md">
                        {{ !empty($job_experience_candidate->city) ? $job_experience_candidate->city : '-' }}</dd>
                    <dt class="col-sm-4 h6 text-md">{{ __('Reference') }}</dt>
                    <dd class="col-sm-8 text-md">
                        {{ !empty($job_experience_candidate->reference) ? ($job_experience_candidate->reference == 'yes' ? 'Yes' : 'No') : '-' }} </dd>
                </dl>
            </div>
        </div>
    </div>
</div>
