<span>
    <div class="action-btn me-2">
        <a href="#" class="bg-primary btn btn-sm  align-items-center cp_link"
            data-link="{{ route('pay.invoice', \Illuminate\Support\Facades\Crypt::encrypt($action->id)) }}"
            data-bs-toggle="tooltip" title="{{ __('Copy') }}"
            data-original-title="{{ __('Click to copy invoice link') }}">
            <i class="ti ti-file text-white"></i>
        </a>
    </div>
    @if (module_is_active('EInvoice'))
        @permission('download invoice')
            @include('einvoice::download.generate_invoice', ['invoice_id' => $action->id])
        @endpermission
    @endif
    <div class="action-btn">
        <a href="#" class="bg-info btn btn-sm  align-items-center"
            data-url="{{ route('delivery-form.pdf', \Crypt::encrypt($action->id)) }}" data-ajax-popup="true"
            data-size="lg" data-bs-toggle="tooltip" title="{{ __('Invoice Delivery Form') }}"
            data-title="{{ __('Invoice Delivery Form') }}">
            <i class="ti ti-clipboard-list text-white"></i>
        </a>
    </div>
    @permission('invoice duplicate')
        <div class="action-btn me-2">
            {!! Form::open([
                'method' => 'get',
                'route' => ['invoice.duplicate', $action->id],
                'id' => 'duplicate-form-' . $action->id,
            ]) !!}
            <a href="#" class="bg-secondary btn btn-sm  align-items-center bs-pass-para show_confirm" data-bs-toggle="tooltip"
                title="" data-bs-original-title="{{ __('Duplicate') }}" aria-label="Delete"
                data-text="{{ __('You want to confirm duplicate this invoice. Press Yes to continue or Cancel to go back') }}"
                data-confirm-yes="duplicate-form-{{ $action->id }}">
                <i class="ti ti-copy  text-white"></i>
            </a>
            {{ Form::close() }}
        </div>
    @endpermission
    @permission('invoice show')
        <div class="action-btn me-2">
            <a href="{{ route('invoice.show', \Crypt::encrypt($action->id)) }}" class="bg-warning btn btn-sm align-items-center"
                data-bs-toggle="tooltip" title="{{ __('View') }}">
                <i class="ti ti-eye  text-white"></i>
            </a>
        </div>
    @endpermission
    @if (module_is_active('ProductService') && $action->invoice_module == 'taskly'
            ? module_is_active('Taskly')
            : module_is_active('Account'))
        @permission('invoice edit')
            <div class="action-btn me-2">
                <a href="{{ route('invoice.edit', \Crypt::encrypt($action->id)) }}"
                    class="bg-info btn btn-sm  align-items-center" data-bs-toggle="tooltip"
                    data-bs-original-title="{{ __('Edit') }}">
                    <i class="ti ti-pencil text-white"></i>
                </a>
            </div>
        @endpermission
    @endif
    @permission('invoice delete')
        <div class="action-btn">
            {{ Form::open(['route' => ['invoice.destroy', $action->id], 'class' => 'm-0']) }}
            @method('DELETE')
            <a href="#" class="bg-danger btn btn-sm  align-items-center bs-pass-para show_confirm" data-bs-toggle="tooltip"
                title="" data-bs-original-title="Delete" aria-label="Delete" data-confirm="{{ __('Are You Sure?') }}"
                data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                data-confirm-yes="delete-form-{{ $action->id }}">
                <i class="ti ti-trash text-white text-white"></i>
            </a>
            {{ Form::close() }}
        </div>
    @endpermission
</span>
