@permission('salesorder create')
    <div class="action-btn me-2">
        {!! Form::open([
            'method' => 'get',
            'route' => ['salesorder.duplicate', $salesorder->id],
            'id' => 'duplicate-form-' . $salesorder->id,
        ]) !!}

        <a href="#" class="mx-3 btn btn-sm align-items-center text-white show_confirm bg-secondary" data-bs-toggle="tooltip"
            title="{{ __('Duplicate') }}" data-confirm="{{ __('You want to confirm this action') }}"
            data-text="{{ __('Press Yes to continue or No to go back') }}"
            data-confirm-yes="document.getElementById('duplicate-form-{{ $salesorder->id }}').submit();">
            <i class="ti ti-copy"></i>
            {!! Form::close() !!}
        </a>
    </div>
@endpermission
@permission('salesorder show')
    <div class="action-btn me-2">
        <a href="{{ route('salesorder.show', $salesorder->id) }}" data-size="md"
            class="mx-3 btn btn-sm align-items-center text-white bg-warning" data-bs-toggle="tooltip"
            title="{{ __('View') }}" data-title="{{ __('SalesOrders Details') }}">
            <i class="ti ti-eye"></i>
        </a>
    </div>
@endpermission
@if($salesorder->convert_invoice == '0')
@permission('salesinvoice create')
    <div class="action-btn me-2">
        <a href="{{ route('salesorder.invoice', $salesorder->id) }}" data-bs-toggle="tooltip" title="{{ __('Convert To Invoice') }}"
            class="mx-3 btn btn-sm align-items-center text-white bg-secondary" data-title="{{ __('Convert To Invoice') }}"><i
                class="ti ti-exchange"></i></a>
            </div>
@endpermission
@endif
@permission('salesorder edit')
    <div class="action-btn me-2">
        <a href="{{ route('salesorder.edit', $salesorder->id) }}"
            class="mx-3 btn btn-sm align-items-center text-white bg-info" data-bs-toggle="tooltip"
            title="{{ __('Edit') }}" data-title="{{ __('Edit SalesOrders') }}"><i class="ti ti-pencil"></i></a>
    </div>
@endpermission

@permission('salesorder delete')
    <div class="action-btn">
        {!! Form::open(['method' => 'DELETE', 'route' => ['salesorder.destroy', $salesorder->id]]) !!}
        <a href="#!" class="mx-3 btn btn-sm   align-items-center text-white show_confirm bg-danger" data-bs-toggle="tooltip"
            title="{{__('Delete')}}" data-confirm="{{ __('Are You Sure?') }}"
            data-text="{{ __('This action can not be undone. Do you want to continue?') }}">
            <i class="ti ti-trash"></i>
        </a>
        {!! Form::close() !!}
    </div>
@endpermission
