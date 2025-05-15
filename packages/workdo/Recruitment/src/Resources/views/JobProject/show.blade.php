<div class="modal-body">
    <div class="row">
        <div class="col-sm-6">
            <div class="card-body pb-0 pt-2">
                <dl class="row mb-0 align-items-center">
                    <dt class="col-sm-4 h6 text-md">{{ __('Title') }}</dt>
                    <dd class="col-sm-8 text-md">
                        {{ !empty($job_project->title) ? $job_project->title : '-' }}
                    </dd>
                    <dt class="col-sm-4 h6 text-md">{{ __('Organization') }}</dt>
                    <dd class="col-sm-8 text-md">
                        {{ !empty($job_project->organization) ? $job_project->organization : '-' }}
                    </dd>
                    <dt class="col-sm-4 h6 text-md">{{ __('Start Date') }}</dt>
                    <dd class="col-sm-8 text-md">
                        {{ !empty($job_project->start_date) ? company_date_formate($job_project->start_date) : '-' }}
                    </dd>
                    <dt class="col-sm-4 h6 text-md">{{ __('End Date') }}</dt>
                    <dd class="col-sm-8 text-md">{{ !empty($job_project->end_date) ? company_date_formate($job_project->end_date) : '-' }}
                    </dd>
                </dl>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="card-body pb-0 pt-2">
                <dl class="row mb-0 align-items-center">
                    <dt class="col-sm-4 h6 text-md">{{ __('Country') }}</dt>
                    <dd class="col-sm-8 text-md">
                        {{ !empty($job_project->country) ? $job_project->country : '-' }}</dd>
                    <dt class="col-sm-4 h6 text-md">{{ __('State') }}</dt>
                    <dd class="col-sm-8 text-md">
                        {{ !empty($job_project->state) ? $job_project->state : '-' }}</dd>
                    <dt class="col-sm-4 h6 text-md">{{ __('City') }}</dt>
                    <dd class="col-sm-8 text-md">
                        {{ !empty($job_project->city) ? $job_project->city : '-' }}</dd>
                    <dt class="col-sm-4 h6 text-md">{{ __('Reference') }}</dt>
                    <dd class="col-sm-8 text-md">
                        {{ !empty($job_project->reference) ? ($job_project->reference == 'yes' ? 'Yes' : 'No') : '-' }} </dd>
                </dl>
            </div>
        </div>
    </div>
</div>
