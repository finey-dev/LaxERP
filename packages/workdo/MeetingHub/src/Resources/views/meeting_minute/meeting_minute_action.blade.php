@permission('meetinghub show')
    <div class="action-btn me-2">
        <a class="mx-3 btn btn-sm align-items-center bg-warning"
            href="{{ route('meeting-minutes.show', $meeting_minute->id) }}"
            data-bs-toggle="tooltip" title="{{ __('View') }}">
            <i class="ti ti-eye text-white"></i>
        </a>
    </div>
@endpermission
@permission('meetinghub delete')
    <div class="action-btn ">
        {{ Form::open(['route' => ['meeting-minutes.destroy', $meeting_minute->id], 'class' => 'm-0']) }}
        @method('DELETE')
        <a class="mx-3 btn btn-sm  align-items-center bs-pass-para show_confirm bg-danger"
            data-bs-toggle="tooltip" title=""
            data-bs-original-title="Delete" aria-label="Delete"
            data-confirm="{{ __('Are You Sure?') }}"
            data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
            data-confirm-yes="delete-form-{{ $meeting_minute->id }}"><i
                class="ti ti-trash text-white text-white"></i></a>
        {{ Form::close() }}
    </div>
@endpermission
