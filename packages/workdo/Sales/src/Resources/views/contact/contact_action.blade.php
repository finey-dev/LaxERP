
@if (module_is_active('SalesForce'))
    @include('sales-force::layouts.addHook', [
        'object' => $contact,
        'type' => 'Contact',
        'resources_type' => 'list',

    ])
@endif
@permission('contact show')
    <div class="action-btn me-2">
        <a data-size="md" data-url="{{ route('contact.show', $contact->id) }}" data-bs-toggle="tooltip"
            title="{{ __('View') }}" data-ajax-popup="true" data-title="{{ __('Contact Details') }}"
            class="mx-3 btn btn-sm align-items-center text-white bg-warning">
            <i class="ti ti-eye"></i>
        </a>
    </div>
@endpermission
@permission('contact edit')
    <div class="action-btn me-2">
        <a href="{{ route('contact.edit', $contact->id) }}"data-size="md"
            class="btn btn-sm align-items-center text-white bg-info"
            data-bs-toggle="tooltip"data-title="{{ __('Contact Edit') }}" title="{{ __('Edit') }}"><i
                class="ti ti-pencil"></i></a>
    </div>
@endpermission
@permission('contact delete')
    <div class="action-btn">
        {!! Form::open(['method' => 'DELETE', 'route' => ['contact.destroy', $contact->id]]) !!}
        <a href="#!" class="btn btn-sm   align-items-center text-white show_confirm bg-danger" data-bs-toggle="tooltip"
            title="{{__('Delete')}}" data-confirm="{{ __('Are You Sure?') }}"
            data-text="{{ __('This action can not be undone. Do you want to continue?') }}">
            <i class="ti ti-trash"></i>
        </a>
        {!! Form::close() !!}
    </div>
@endpermission
