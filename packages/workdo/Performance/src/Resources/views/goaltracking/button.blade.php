@permission('goaltracking edit')
    <div class="action-btn me-2">
        <a href="#" class="btn btn-sm d-inline-flex align-items-center bg-info" data-size="lg"
            data-url="{{ route('goaltracking.edit', $goalTrackings->id) }}" class="dropdown-item" data-ajax-popup="true"
            data-title="{{ __('Edit Goal Tracking') }}" data-bs-toggle="tooltip" data-bs-original-title="{{ __('Edit') }}">
            <span class="text-white"> <i class="ti ti-pencil"></i></span></a>
    </div>
@endpermission

@permission('goaltracking delete')
    <div class="action-btn">
        {{ Form::open([
            'method' => 'DELETE',
            'route' => ['goaltracking.destroy', $goalTrackings->id],
            'id' => 'delete-form-' . $goalTrackings->id,
        ]) }}
        <a href="#" class="btn btn-sm  align-items-center bs-pass-para show_confirm bg-danger"
            data-bs-toggle="tooltip" title="{{ __('Delete') }}" data-confirm="{{ __('Are You Sure?') }}"
            data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
            data-bs-original-title="{{ __('Delete') }}" aria-label="{{ __('Delete') }}"
            data-confirm-yes="delete-form-{{ $goalTrackings->id }}"><i class="ti ti-trash text-white text-white"></i></a>
        {{ Form::close() }}
    </div>
@endpermission
