@permission('job template manage')
    <div class="action-btn me-2">
        {!! Form::open(['method' => 'POST', 'route' => ['job-template.store', ['job_id' => $jobs->id]]]) !!}
        <a class="btn btn-sm align-items-center text-white bg-success show_confirm" data-bs-toggle="tooltip"
            data-title="{{ __('Save As Job Template') }}" title="{{ __('Save as template') }}"><i
                class="ti ti-bookmark"></i>
        </a>
        {!! Form::close() !!}
    </div>
@endpermission
@if ($jobs->status != 'in_active' && $jobs->is_post == 1)
    <div class="action-btn me-2">
        <a href="#" id="{{ route('job.requirement', [$jobs->code, !empty($jobs) ? $jobs->createdBy->lang : 'en']) }}"
            class="mx-3 btn btn-sm align-items-center bg-primary" onclick="copyToClipboard(this)" data-bs-toggle="tooltip"
            title="{{ __('Copy') }}" data-original-title="{{ __('Click to copy') }}"><i
                class="ti ti-link text-white"></i></a>
    </div>
@endif
@permission('job show')
    <div class="action-btn me-2">
        <a href="{{ route('job.show', $jobs->id) }}" class="mx-3 btn btn-sm  align-items-center bg-warning" data-bs-toggle="tooltip"
            data-bs-placement="top" title="{{ __('View') }}">
            <i class="ti ti-eye text-white"></i>
        </a>
    </div>
@endpermission

@permission('job edit')
    <div class="action-btn me-2">
        <a href="{{ route('job.edit', $jobs->id) }}" class="mx-3 btn btn-sm  align-items-center  bg-info" data-url=""
            data-title="{{ __('Edit Job') }}" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Edit') }}">
            <i class="ti ti-pencil text-white"></i>
        </a>
    </div>
@endpermission

@permission('job delete')
    <div class="action-btn">
        {!! Form::open(['method' => 'DELETE', 'route' => ['job.destroy', $jobs->id], 'id' => 'delete-form-' . $jobs->id]) !!}
        <a href="#!" class="mx-3 btn btn-sm  align-items-center bs-pass-para show_confirm  bg-danger" data-bs-toggle="tooltip"
            data-bs-placement="top" title="{{ __('Delete') }}" data-confirm="{{ __('Are You Sure?') }}" data-text="{{ __('This action can not be undone. Do you want to continue?') }}">
            <i class="ti ti-trash text-white"></i></a>
        {!! Form::close() !!}
    </div>
@endpermission
