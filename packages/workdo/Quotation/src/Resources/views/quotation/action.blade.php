@if (module_is_active('Pos'))
    @if ($quotation->quotation == 'pos')
        @if ($quotation->is_converted == 0)
            @permission('quotation convert')
                <div class="action-btn me-2">
                    <a href="{{ route('pos.index', ['quotation_id' => $quotation->id]) }}"
                        class="mx-3 btn btn-sm align-items-center bg-warning" data-bs-toggle="tooltip" title="{{ __('Convert to POS') }}"
                        data-original-title="{{ __('Detail') }}">
                        <i class="ti ti-exchange text-white"></i>
                    </a>
                </div>
            @endpermission
        @else
            <div class="action-btn me-2">
                <a href="{{ route('pos.show', \Crypt::encrypt($quotation->converted_pos_id)) }}"
                    class="mx-3 btn btn-sm align-items-center bg-warning" data-bs-toggle="tooltip"
                    title="{{ __('Already convert to POS') }}" data-original-title="{{ __('Detail') }}">
                    <i class="ti ti-eye text-white"></i>
                </a>
            </div>
        @endif
    @endif
@endif

@if ($quotation->quotation == 'invoice')
    @if ($quotation->is_converted == 0)
        @permission('quotation convert')
            <div class="action-btn me-2">
                {{ Form::open(['route' => ['quotation.convert.invoice', \Crypt::encrypt($quotation->id)], 'class' => 'm-0']) }}
                <a href="#" class="mx-3 btn btn-sm  align-items-center bs-pass-para show_confirm bg-success" data-bs-toggle="tooltip"
                    title="" data-bs-original-title="{{ __('Convert to Invoice') }}" aria-label="Delete"
                    data-confirm="{{ __('Are You Sure?') }}"
                    data-text="{{ __('You want to confirm convert to invoice. Press Yes to continue or Cancel to go back.') }}"
                    data-confirm-yes="delete-form-{{ $quotation->id }}"><i class="ti ti-exchange text-white "></i></a>
                {{ Form::close() }}
            </div>
        @endpermission
    @else
        <div class="action-btn me-2">
            <a href="{{ route('invoice.show', \Crypt::encrypt($quotation->is_converted) ) }}"
                class="mx-3 btn btn-sm align-items-center bg-success" data-bs-toggle="tooltip"
                title="{{ __('Already convert to Invoice') }}" data-original-title="{{ __('Detail') }}">
                <i class="ti ti-file text-white"></i>
            </a>
        </div>
    @endif
@endif

@permission('quotation edit')
    <div class="action-btn me-2">
        <a href="{{ route('quotation.edit', \Crypt::encrypt($quotation->id)) }}" class="mx-3 btn btn-sm align-items-center bg-info"
            data-bs-toggle="tooltip" title="{{ __('Edit') }}" data-original-title="Edit">
            <i class="ti ti-pencil text-white"></i>
        </a>
    </div>
@endpermission
@permission('quotation delete')
    <div class="action-btn">
        {{ Form::open(['route' => ['quotation.destroy', $quotation->id], 'class' => 'm-0']) }}
        @method('DELETE')
        <a href="#" class="mx-3 btn btn-sm  align-items-center bs-pass-para show_confirm bg-danger" data-bs-toggle="tooltip"
            title="" data-bs-original-title="{{ __('Delete') }}" aria-label="Delete"
            data-confirm="{{ __('Are You Sure?') }}"
            data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
            data-confirm-yes="delete-form-{{ $quotation->id }}"><i class="ti ti-trash text-white"></i></a>
        {{ Form::close() }}
    </div>
@endpermission
