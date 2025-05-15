@permission('facilitiesspace edit')
    <div class="action-btn me-2">
        <a class="mx-3 btn bg-info btn-sm align-items-center" data-ajax-popup="true" data-size="md"
            title="{{ __('Edit') }}" data-title="{{ __('Edit Space') }}" data-bs-toggle="tooltip" data-url="{{ route('facilities-space.edit', $facilitiesSpace->id) }}">
            <i class="ti ti-pencil text-white"></i>
        </a>
    </div>
@endpermission
@permission('facilitiesspace delete')
    <div class="action-btn">
        {!! Form::open(['method' => 'DELETE', 'route' => ['facilities-space.destroy', $facilitiesSpace->id], 'id' => 'delete-form-' . $facilitiesSpace->id]) !!}
        <a class="mx-3 btn bg-danger btn-sm align-items-center bs-pass-para show_confirm" data-bs-toggle="tooltip"
            title="{{ __('Delete') }}"
            data-text="{{ __('This action can not be undone. Do you want to continue?') }}"><i
                class="ti ti-trash text-white text-white"></i></a>
        {!! Form::close() !!}
    </div>
@endpermission
