@permission('job candidate edit')
    <div class="action-btn  me-2 edit_btn">
        <a href="{{ route('job-candidates.edit', Crypt::encrypt($job_candidates->id)) }}"
            class="mx-3 btn btn-sm  align-items-center bg-info" data-bs-toggle="tooltip" data-bs-placement="top"
            data-title="{{ __('Edit Job Candidate') }}" title="{{ __('Edit') }}">
            <i class="ti ti-pencil text-white"></i>
        </a>
    </div>
@endpermission
@permission('job candidate delete')
    <div class="action-btn delete_btn">
        {{ Form::open(['route' => ['job-candidates.destroy', $job_candidates->id], 'class' => 'm-0']) }}
        @method('DELETE')
        <a class="mx-3 btn btn-sm  align-items-center bs-pass-para show_confirm bg-danger" data-bs-toggle="tooltip"
            data-bs-original-title="Delete" aria-label="Delete" data-confirm="{{ __('Are You Sure?') }}" title="{{ __('Delete') }}"
            data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
            data-confirm-yes="delete-form-{{ $job_candidates->id }}"><i class="ti ti-trash text-white text-white"></i></a>
        {{ Form::close() }}
    </div>
@endpermission
