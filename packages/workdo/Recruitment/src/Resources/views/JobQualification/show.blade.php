<div class="modal-body">
    <div class="row">
        <div class="col-sm-6">
            <div class="card-body pb-0 pt-2">
                <dl class="row mb-0 align-items-center">
                    <dt class="col-sm-4 h6 text-md">{{ __('Title') }}</dt>
                    <dd class="col-sm-8 text-md">
                        {{ !empty($job_qualification->title) ? $job_qualification->title : '-' }}
                    </dd>
                    <dt class="col-sm-4 h6 text-md">{{ __('Organization') }}</dt>
                    <dd class="col-sm-8 text-md">
                        {{ !empty($job_qualification->organization) ? $job_qualification->organization : '-' }}
                    </dd>
                    <dt class="col-sm-4 h6 text-md">{{ __('Start Date') }}</dt>
                    <dd class="col-sm-8 text-md">
                        {{ !empty($job_qualification->start_date) ? company_date_formate($job_qualification->start_date) : '-' }}
                    </dd>
                    <dt class="col-sm-4 h6 text-md">{{ __('End Date') }}</dt>
                    <dd class="col-sm-8 text-md">{{ !empty($job_qualification->end_date) ? company_date_formate($job_qualification->end_date) : '-' }}
                    </dd>
                </dl>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="card-body pb-0 pt-2">
                <dl class="row mb-0 align-items-center">
                    <dt class="col-sm-4 h6 text-md">{{ __('Country') }}</dt>
                    <dd class="col-sm-8 text-md">
                        {{ !empty($job_qualification->country) ? $job_qualification->country : '-' }}</dd>
                    <dt class="col-sm-4 h6 text-md">{{ __('State') }}</dt>
                    <dd class="col-sm-8 text-md">
                        {{ !empty($job_qualification->state) ? $job_qualification->state : '-' }}</dd>
                    <dt class="col-sm-4 h6 text-md">{{ __('City') }}</dt>
                    <dd class="col-sm-8 text-md">
                        {{ !empty($job_qualification->city) ? $job_qualification->city : '-' }}</dd>
                    <dt class="col-sm-4 h6 text-md">{{ __('Reference') }}</dt>
                    <dd class="col-sm-8 text-md">
                        {{ !empty($job_qualification->reference) ? ($job_qualification->reference == 'yes' ? 'Yes' : 'No') : '-' }} </dd>
                </dl>
            </div>
        </div>
    </div>
</div>
