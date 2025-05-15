<div class="action-btn me-2">
    <a class="mx-3 btn btn-sm align-items-center bg-success"
        data-bs-toggle="tooltip" title="{{ __('Meeting Minute') }}"
        data-ajax-popup="true"
        data-url="{{ route('meetinghub.meeting.minute', $meeting->id) }}"
        data-size="lg" data-title="{{ __('Create Meeting Minute') }}">
        <i class="ti ti-circle-plus"></i>
    </a>
</div>
@permission('meetinghub edit')
    <div class="action-btn me-2">
        <a href="#" class="mx-3 btn btn-sm align-items-center bg-info"
            data-url="{{ route('meetings.edit', $meeting->id) }}"
            class="dropdown-item" data-ajax-popup="true" data-bs-toggle="tooltip"
            data-size="lg" data-bs-original-title="{{ __('Edit') }}"
            data-title="{{ __('Edit Meeting') }}"> <span class="text-white"> <i
                    class="ti ti-pencil"></i></span></a>
    </div>
@endpermission
@permission('meetinghub delete')
    <div class="action-btn ">
        {{ Form::open(['route' => ['meetings.destroy', $meeting->id], 'class' => 'm-0']) }}
        @method('DELETE')
        <a href="#"
            class="mx-3 btn btn-sm  align-items-center bs-pass-para show_confirm bg-danger"
            data-bs-toggle="tooltip" title="" data-bs-original-title="Delete"
            aria-label="Delete"
            data-confirm-yes="delete-form-{{ $meeting->id }}"><i
                class="ti ti-trash text-white text-white"></i></a>
        {{ Form::close() }}
    </div>
@endpermission
