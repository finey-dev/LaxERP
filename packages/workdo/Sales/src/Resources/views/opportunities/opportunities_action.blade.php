
@if (module_is_active('SalesForce'))
    @include('sales-force::layouts.addHook', [
        'object' => $opportunities,
        'type' => 'Opportunity',
        'resources_type' => 'list',

    ])
@endif
@permission('opportunities show')
    <div class="action-btn me-2">
        <a data-size="md" data-url="{{ route('opportunities.show', $opportunities->id) }}"
            data-bs-toggle="tooltip"title="{{ __('View') }}" data-ajax-popup="true"
            data-title="{{ __('Opportunities Details') }}"
            class="mx-3 btn btn-sm align-items-center text-white bg-warning">
            <i class="ti ti-eye"></i>
        </a>
    </div>
@endpermission
@permission('opportunities edit')
    <div class="action-btn me-2">
        <a href="{{ route('opportunities.edit', $opportunities->id) }}"
            class="mx-3 btn btn-sm align-items-center text-white bg-info" data-bs-toggle="tooltip"
            title="{{ __('Edit') }}" data-title="{{ __('Opportunities Edit') }}"><i class="ti ti-pencil"></i></a>
    </div>
@endpermission
@permission('opportunities delete')
    <div class="action-btn">
        {!! Form::open(['method' => 'DELETE', 'route' => ['opportunities.destroy', $opportunities->id]]) !!}
        <a href="#!" class="mx-3 btn btn-sm   align-items-center text-white show_confirm bg-danger" data-bs-toggle="tooltip"
            title="{{__('Delete')}}" data-confirm="{{ __('Are You Sure?') }}"
            data-text="{{ __('This action can not be undone. Do you want to continue?') }}">
            <i class="ti ti-trash"></i>
        </a>
        {!! Form::close() !!}
    </div>
@endpermission
