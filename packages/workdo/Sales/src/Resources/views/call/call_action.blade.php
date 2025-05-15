@permission('call show')
    <div class="action-btn me-2">
        <a data-size="md" data-url="{{ route('call.show', $call->id) }}" data-ajax-popup="true" data-bs-toggle="tooltip"
            data-title="{{ __('Call Details') }}"
            title="{{ __('View') }}"class="mx-3 btn btn-sm align-items-center text-white bg-warning">
            <i class="ti ti-eye"></i>
        </a>
    </div>
@endpermission
@permission('call edit')
    <div class="action-btn me-2">
        <a href="{{ route('call.edit', $call->id) }}" class="mx-3 btn btn-sm align-items-center text-white bg-info"
            data-bs-toggle="tooltip" data-title="{{ __('Edit Call') }}"title="{{ __('Edit') }}"><i
                class="ti ti-pencil"></i></a>
    </div>
@endpermission
@permission('call delete')
    <div class="action-btn">
        {!! Form::open(['method' => 'DELETE', 'route' => ['call.destroy', $call->id]]) !!}
        <a href="#!" class="mx-3 btn btn-sm   align-items-center text-white show_confirm bg-danger" data-bs-toggle="tooltip"
            title="{{__('Delete')}}" data-confirm="{{ __('Are You Sure?') }}"
            data-text="{{ __('This action can not be undone. Do you want to continue?') }}">
            <i class="ti ti-trash"></i>
        </a>
        {!! Form::close() !!}
    </div>
@endpermission
