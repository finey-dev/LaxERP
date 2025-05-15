
@if (module_is_active('SalesForce'))
    @include('sales-force::layouts.addHook', [
        'object' => $salesAccount,
        'type' => 'Account',
        'resources_type' => 'list',
        ])
@endif
@permission('salesaccount show')
    <div class="action-btn me-2">
        <a data-size="md" data-url="{{ route('salesaccount.show', $salesAccount->id) }}" data-ajax-popup="true"
            data-bs-toggle="tooltip" title="{{ __('View') }}" data-title="{{ __('Sales Account Details') }}"
            class="mx-3 btn btn-sm align-items-center text-white bg-warning">
            <i class="ti ti-eye"></i>
        </a>
    </div>
@endpermission
@permission('salesaccount edit')
    <div class="action-btn me-2">
        <a href="{{ route('salesaccount.edit', $salesAccount->id) }}"
            data-size="md"class="mx-3 btn btn-sm align-items-center text-white bg-info"
            data-bs-toggle="tooltip"data-title="{{ __('Account Edit') }}" title="{{ __('Edit') }}"><i
                class="ti ti-pencil"></i></a>

    </div>
@endpermission
@permission('salesaccount delete')
    <div class="action-btn">
        {!! Form::open(['method' => 'DELETE', 'route' => ['salesaccount.destroy', $salesAccount->id]]) !!}
        <a href="#!" class="mx-3 btn btn-sm align-items-center text-white show_confirm bg-danger" data-bs-toggle="tooltip"
            title="{{__('Delete')}}" data-confirm="{{ __('Are You Sure?') }}"
            data-text="{{ __('This action can not be undone. Do you want to continue?') }}">
            <i class="ti ti-trash"></i>
        </a>
        {!! Form::close() !!}
    </div>
@endpermission
