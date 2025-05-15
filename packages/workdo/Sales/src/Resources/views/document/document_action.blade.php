@permission('salesdocument show')
    <div class="action-btn me-2">
        <a data-size="lg" data-url="{{ route('salesdocument.show', $document->id) }}" data-ajax-popup="true"
            data-bs-toggle="tooltip" title="{{ __('View') }}" data-title="{{ __('Sales Document Details') }}"
            class="mx-3 btn btn-sm align-items-center text-white bg-warning">
            <i class="ti ti-eye"></i>
        </a>
    </div>
@endpermission
@permission('salesdocument edit')
    <div class="action-btn me-2">
        <a href="{{ route('salesdocument.edit', $document->id) }}"
            class="mx-3 btn btn-sm align-items-center text-white bg-info" data-bs-toggle="tooltip"
            title="{{ __('Edit') }}" data-title="{{ __('Edit Document') }}"><i class="ti ti-pencil"></i></a>
    </div>
@endpermission
@permission('salesdocument delete')
    <div class="action-btn">
        {!! Form::open(['method' => 'DELETE', 'route' => ['salesdocument.destroy', $document->id]]) !!}
        <a href="#!" class="mx-3 btn btn-sm   align-items-center text-white show_confirm bg-danger" data-bs-toggle="tooltip"
            title="{{__('Delete')}}" data-confirm="{{ __('Are You Sure?') }}"
            data-text="{{ __('This action can not be undone. Do you want to continue?') }}">
            <i class="ti ti-trash"></i>
        </a>
        {!! Form::close() !!}
    </div>
@endpermission
