@permission('visitor log edit')
    <div class="action-btn me-2">
        <a class="mx-3 btn btn-sm align-items-center bg-warning" data-url="{{ route('visitor-log.show', $visitLog->id) }}"
            data-ajax-popup="true" data-size="lg" data-bs-toggle="tooltip" title="" data-title="{{ __('Visitor Departure Time') }}"
            data-bs-original-title="{{ __('Departure Time') }}">
            <i class="ti ti-clock text-white"></i>
        </a>
    </div>
@endpermission
@permission('visitor log edit')
    <div class="action-btn me-2">
        <a class="mx-3 btn btn-sm align-items-center bg-info" data-url="{{ route('visitor-log.edit', $visitLog->id) }}"
            data-ajax-popup="true" data-size="lg" data-bs-toggle="tooltip" title="" data-title="{{ __('Edit Visitor Log') }}"
            data-bs-original-title="{{ __('Edit') }}">
            <i class="ti ti-pencil text-white"></i>
        </a>
    </div>
@endpermission

@permission('visitor log delete')
    <div class="action-btn">
        {{ Form::open(['route' => ['visitor-log.destroy', $visitLog->id], 'method' => 'POST', 'class' => 'm-0']) }}
        @method('DELETE')
        <a class="mx-3 btn btn-sm align-items-center bs-pass-para show_confirm bg-danger" data-bs-toggle="tooltip"
            title="" data-bs-original-title="{{ __('Delete') }}" aria-label="Delete"
            data-confirm="{{ __('Are You Sure?') }}"
            data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
            data-confirm-yes="delete-form-{{ $visitLog->id }}"><i class="ti ti-trash text-white text-white"></i>
        </a>
        {{ Form::close() }}
    </div>
@endpermission
