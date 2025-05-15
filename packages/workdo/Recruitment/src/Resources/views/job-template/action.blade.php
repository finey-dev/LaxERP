@permission('job manage')
    <div class="action-btn me-2">
        {!! Form::open(['method' => 'POST', 'route' => ['job-template.convertToJob', ['job_template_id' => $job_template->id]]]) !!}
        <a class="btn btn-sm align-items-center text-white bg-success show_confirm" data-bs-toggle="tooltip"
            data-title="{{ __('Convert to job') }}" title="{{ __('Convert to job') }}"><i
                class="ti ti-replace"></i>
        </a>
        {!! Form::close() !!}
    </div>
@endpermission
@permission('job template show')
    <div class="action-btn me-2">
        <a href="{{ route('job-template.show', $job_template->id) }}" class="mx-3 btn btn-sm  align-items-center bg-warning" data-bs-toggle="tooltip"
            data-bs-placement="top" title="{{ __('View') }}">
            <i class="ti ti-eye text-white"></i>
        </a>
    </div>
@endpermission

@permission('job template edit')
    <div class="action-btn me-2">
        <a href="{{ route('job-template.edit', $job_template->id) }}" class="mx-3 btn btn-sm  align-items-center  bg-info" data-url=""
            data-title="{{ __('Edit Job Template') }}" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Edit') }}">
            <i class="ti ti-pencil text-white"></i>
        </a>
    </div>
@endpermission

@permission('job template delete')
    <div class="action-btn">
        {!! Form::open(['method' => 'DELETE', 'route' => ['job-template.destroy', $job_template->id], 'id' => 'delete-form-' . $job_template->id]) !!}
        <a href="#!" class="mx-3 btn btn-sm  align-items-center bs-pass-para show_confirm  bg-danger" data-bs-toggle="tooltip"
            data-bs-placement="top" title="{{ __('Delete') }}" data-confirm="{{ __('Are You Sure?') }}" data-text="{{ __('This action can not be undone. Do you want to continue?') }}">
            <i class="ti ti-trash text-white"></i></a>
        {!! Form::close() !!}
    </div>
@endpermission
