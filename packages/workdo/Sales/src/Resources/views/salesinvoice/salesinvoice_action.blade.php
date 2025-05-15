@permission('salesinvoice create')
    <div class="action-btn me-2">
        {!! Form::open([
            'method' => 'get',
            'route' => ['salesinvoice.duplicate', $invoice->id],
            'id' => 'duplicate-form-' . $invoice->id,
        ]) !!}

        <a href="#" class="mx-3 btn btn-sm align-items-center text-white show_confirm bg-secondary" data-bs-toggle="tooltip"
            title="{{ __('Duplicate') }}" data-toggle="tooltip" data-original-title="{{ __('Duplicate') }}"
            data-confirm="{{ __('You want to confirm this action') }}"
            data-text="{{ __('Press Yes to continue or No to go back') }}"
            data-confirm-yes="document.getElementById('duplicate-form-{{ $invoice->id }}').submit();">
            <i class="ti ti-copy"></i>
            {!! Form::close() !!}
        </a>
    </div>
@endpermission
@permission('salesinvoice show')
    <div class="action-btn me-2">
        <a href="{{ route('salesinvoice.show', $invoice->id) }}"  data-bs-toggle="tooltip" title="{{ __('View') }}"
            class="mx-3 btn btn-sm align-items-center text-white bg-warning" data-title="{{ __('View') }}"><i
                class="ti ti-eye"></i></a>
        </a>
    </div>
@endpermission
@permission('salesinvoice edit')
    <div class="action-btn me-2">
        <a href="{{ route('salesinvoice.edit', $invoice->id) }}" data-bs-toggle="tooltip" title="{{ __('Edit') }}"
            class="mx-3 btn btn-sm align-items-center text-white bg-info" data-title="{{ __('Edit') }}"><i
                class="ti ti-pencil"></i></a>
    </div>
@endpermission
@permission('salesinvoice delete')
    <div class="action-btn me-2">
        {!! Form::open(['method' => 'DELETE', 'route' => ['salesinvoice.destroy', $invoice->id]]) !!}
        <a href="#!" class="mx-3 btn btn-sm   align-items-center text-white show_confirm bg-danger" data-bs-toggle="tooltip"
            title="{{__('Delete')}}" data-confirm="{{ __('Are You Sure?') }}"
            data-text="{{ __('This action can not be undone. Do you want to continue?') }}">
            <i class="ti ti-trash"></i>
        </a>
        {!! Form::close() !!}
    </div>
@endpermission
