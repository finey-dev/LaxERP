<div class="modal-body">
    <div class="row">
        <div class="col-sm-6">
            <div class="card-body pb-0 pt-2">
                <dl class="row mb-0 align-items-center">
                    <dt class="col-sm-4 h6 text-md">{{ __('Type') }}</dt>
                    <dd class="col-sm-8 text-md">
                        {{ !empty($job_skill->type) ? $job_skill->type : '-' }}
                    </dd>
                    <dt class="col-sm-4 h6 text-md">{{ __('Title') }}</dt>
                    <dd class="col-sm-8 text-md">
                        {{ !empty($job_skill->title) ? $job_skill->title : '-' }}
                    </dd>
                    <dt class="col-sm-4 h6 text-md">{{ __('Organization') }}</dt>
                    <dd class="col-sm-8 text-md">
                        {{ !empty($job_skill->organization) ? $job_skill->organization : '-' }}
                    </dd>
                    <dt class="col-sm-4 h6 text-md">{{ __('Start Date') }}</dt>
                    <dd class="col-sm-8 text-md">
                        {{ !empty($job_skill->start_date) ? company_date_formate($job_skill->start_date) : '-' }}
                    </dd>
                    <dt class="col-sm-4 h6 text-md">{{ __('End Date') }}</dt>
                    <dd class="col-sm-8 text-md">
                        {{ !empty($job_skill->end_date) ? company_date_formate($job_skill->end_date) : '-' }}
                    </dd>
                </dl>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="card-body pb-0 pt-2">
                <dl class="row mb-0 align-items-center">
                    <dt class="col-sm-4 h6 text-md">{{ __('Country') }}</dt>
                    <dd class="col-sm-8 text-md">
                        {{ !empty($job_skill->country) ? $job_skill->country : '-' }}</dd>
                    <dt class="col-sm-4 h6 text-md">{{ __('State') }}</dt>
                    <dd class="col-sm-8 text-md">
                        {{ !empty($job_skill->state) ? $job_skill->state : '-' }}</dd>
                    <dt class="col-sm-4 h6 text-md">{{ __('City') }}</dt>
                    <dd class="col-sm-8 text-md">
                        {{ !empty($job_skill->city) ? $job_skill->city : '-' }}</dd>
                    <dt class="col-sm-4 h6 text-md">{{ __('Reference') }}</dt>
                    <dd class="col-sm-8 text-md">
                        {{ !empty($job_skill->reference) ? ($job_skill->reference == 'yes' ? 'Yes' : 'No') : '-' }}
                    </dd>
                </dl>
            </div>
        </div>
    </div>
</div>
