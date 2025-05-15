<div class="modal-body">
    <div class="table-responsive">
        <table class="table">
            <tr role="row">
                <th>{{ __('Title') }}</th>
                <td>{{ !empty($job_todo->title) ? $job_todo->title : '' }}</td>
            </tr>
            <tr>
                <th>{{ __('Start Date') }}</th>
                <td>{{ !empty($job_todo->start_date) ? company_date_formate($job_todo->start_date) : '' }}</td>
            </tr>
            <tr>
                <th>{{ __('Due Date') }}</th>
                <td>{{ !empty($job_todo->due_date) ? company_date_formate($job_todo->due_date) : '' }}</td>

            </tr>
            <tr>
                <th>{{ __('Assigned By') }}</th>
                <td>{{ !empty($job_todo->assign_by) ? $job_todo->assignedByUser->name : '' }}</td>

            </tr>
            <tr>
                <th>{{ __('Assigned To') }}</th>
                <td class="text-wrap text-break">
                    {{ !empty($job_todo->assigned_to) ? Workdo\Recruitment\Entities\JobTodos::getTeams($job_todo->assigned_to) : '' }}
                </td>
            </tr>
            <tr>
                <th>{{ __('Priority') }}</th>
                <td>{{ !empty($job_todo->priority) ? $job_todo->priority : '' }}</td>
            </tr>
            <tr>
                <th>{{ __('Description ') }}</th>
                <td class="text-wrap text-break">{{ !empty($job_todo->description) ? $job_todo->description : '' }}</td>
            </tr>
        </table>
    </div>
</div>
