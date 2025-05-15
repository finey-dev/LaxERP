@permission('visitor edit')
    <div class="action-btn  me-2">
        <a class="mx-3 btn btn-sm  align-items-center bg-info" data-url="{{ route('visitors.edit', $visitor->id) }}"
            data-ajax-popup="true" data-size="md" data-bs-toggle="tooltip" title=""
            data-title="{{ __('Edit Visitor') }}" data-bs-original-title="{{ __('Edit') }}">
            <i class="ti ti-pencil text-white"></i>
        </a>
    </div>
@endpermission

@permission('visitor delete')
    <div class="action-btn">
        {{ Form::open(['route' => ['visitors.destroy', $visitor->id], 'method' => 'POST', 'class' => 'm-0']) }}
        @method('DELETE')
        <a class="mx-3 btn btn-sm  align-items-center bs-pass-para show_confirm bg-danger" data-bs-toggle="tooltip"
            title="" data-bs-original-title="{{ __('Delete') }}" aria-label="Delete"
            data-confirm="{{ __('Are You Sure?') }}"
            data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
            data-confirm-yes="delete-form-{{ $visitor->id }}"><i class="ti ti-trash text-white text-white"></i>
        </a>
        {{ Form::close() }}
    </div>
@endpermission
