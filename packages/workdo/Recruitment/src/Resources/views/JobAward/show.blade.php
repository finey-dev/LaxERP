<div class="modal-body">
    <div class="row">
        <div class="col-sm-6">
            <div class="card-body pb-0 pt-2">
                <dl class="row mb-0 align-items-center">
                    <dt class="col-sm-4 h6 text-md">{{ __('Title') }}</dt>
                    <dd class="col-sm-8 text-md">
                        {{ !empty($job_award->title) ? $job_award->title : '-' }}
                    </dd>
                    <dt class="col-sm-4 h6 text-md">{{ __('Organization') }}</dt>
                    <dd class="col-sm-8 text-md">
                        {{ !empty($job_award->organization) ? $job_award->organization : '-' }}
                    </dd>
                    <dt class="col-sm-4 h6 text-md">{{ __('Start Date') }}</dt>
                    <dd class="col-sm-8 text-md">
                        {{ !empty($job_award->start_date) ? company_date_formate($job_award->start_date) : '-' }}
                    </dd>
                    <dt class="col-sm-4 h6 text-md">{{ __('End Date') }}</dt>
                    <dd class="col-sm-8 text-md">{{ !empty($job_award->end_date) ? company_date_formate($job_award->end_date) : '-' }}
                    </dd>
                </dl>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="card-body pb-0 pt-2">
                <dl class="row mb-0 align-items-center">
                    <dt class="col-sm-4 h6 text-md">{{ __('Country') }}</dt>
                    <dd class="col-sm-8 text-md">
                        {{ !empty($job_award->country) ? $job_award->country : '-' }}</dd>
                    <dt class="col-sm-4 h6 text-md">{{ __('State') }}</dt>
                    <dd class="col-sm-8 text-md">
                        {{ !empty($job_award->state) ? $job_award->state : '-' }}</dd>
                    <dt class="col-sm-4 h6 text-md">{{ __('City') }}</dt>
                    <dd class="col-sm-8 text-md">
                        {{ !empty($job_award->city) ? $job_award->city : '-' }}</dd>
                    <dt class="col-sm-4 h6 text-md">{{ __('Reference') }}</dt>
                    <dd class="col-sm-8 text-md">
                        {{ !empty($job_award->reference) ? ($job_award->reference == 'yes' ? 'Yes' : 'No') : '-' }}</dd>
                </dl>
            </div>
        </div>
    </div>
</div>
