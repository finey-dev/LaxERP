@permission('quote create')
    <div class="action-btn me-2">
        {!! Form::open([
            'method' => 'get',
            'route' => ['quote.duplicate', $quote->id],
            'id' => 'duplicate-form-' . $quote->id,
        ]) !!}

        <a href="#" class="mx-3 btn btn-sm align-items-center text-white show_confirm bg-secondary" data-bs-toggle="tooltip"
            data-title="{{ __('Duplicate') }}" title="{{ __('Duplicate') }}"
            data-confirm="{{ __('You want to confirm this action') }}"
            data-text="{{ __('Press Yes to continue or No to go back') }}"
            data-confirm-yes="document.getElementById('duplicate-form-{{ $quote->id }}').submit();">
            <i class="ti ti-copy"></i>
            {!! Form::close() !!}
        </a>
    </div>
@endpermission

@if ($quote->converted_salesorder_id == 0)
    <div class="action-btn me-2">
        {!! Form::open([
            'method' => 'get',
            'route' => ['quote.convert', $quote->id],
            'id' => 'quotes-form-' . $quote->id,
        ]) !!}

        <a href="#" class="mx-3 btn btn-sm align-items-center text-white show_confirm bg-success" data-bs-toggle="tooltip"
            data-title="{{ __('Convert to Sales Order') }}" title="{{ __('Conver to Sale Order') }}"
            data-confirm="{{ __('You want to confirm convert to sales order.') }}"
            data-text="{{ __('Press Yes to continue or No to go back') }}"
            data-confirm-yes="document.getElementById('quotes-form-{{ $quote->id }}').submit();">
            <i class="ti ti-exchange"></i>
            {!! Form::close() !!}
        </a>
    </div>
@else
    <div class="action-btn me-2">
        <a href="{{ route('salesorder.show', $quote->converted_salesorder_id) }}"
            class="mx-3 btn btn-sm align-items-center text-white bg-success" data-bs-toggle="tooltip"
            data-original-title="{{ __('Sales Order Details') }}" title="{{ __('SalesOrders Details') }}">
            <i class="fab fa-stack-exchange"></i>
        </a>
    </div>
@endif
@permission('quote show')
    <div class="action-btn me-2">
        <a href="{{ route('quote.show', $quote->id) }}"
            data-size="md"class="mx-3 btn btn-sm align-items-center text-white bg-warning" data-bs-toggle="tooltip"
            title="{{ __('View') }}" data-title="{{ __('Quote Details') }}">
            <i class="ti ti-eye"></i>
        </a>
    </div>
@endpermission
@permission('quote edit')
    <div class="action-btn me-2">
        <a href="{{ route('quote.edit', $quote->id) }}" class="mx-3 btn btn-sm align-items-center text-white bg-info"
            data-bs-toggle="tooltip" title="{{ __('Edit') }}" data-title="{{ __('Edit Quote') }}"><i
                class="ti ti-pencil"></i></a>
    </div>
@endpermission
@permission('quote delete')
    <div class="action-btn me-2">
        {!! Form::open(['method' => 'DELETE', 'route' => ['quote.destroy', $quote->id]]) !!}
        <a href="#!" class="mx-3 btn btn-sm   align-items-center text-white show_confirm bg-danger" data-bs-toggle="tooltip"
            title="{{__('Delete')}}" data-confirm="{{ __('Are You Sure?') }}"
            data-text="{{ __('This action can not be undone. Do you want to continue?') }}">
            <i class="ti ti-trash"></i>
        </a>
        {!! Form::close() !!}
    </div>
@endpermission
