@if (module_is_active('ContractTemplate'))
    @permission('contract template create')
        <div class="action-btn me-2">
            <a data-size="md"
                data-url="{{ route('contract-template.create', ['contract_id' => $contract->id, 'type' => 'template']) }}"
                class="btn btn-sm align-items-center text-white bg-success" data-ajax-popup="true" data-bs-toggle="tooltip"
                data-title="{{ __('Save As Contract Template') }}" title="{{ __('Save as template') }}"><i
                    class="ti ti-bookmark"></i></a>
            </a>
        </div>
    @endpermission
@endif
@permission('contract create')
    @if (\Auth::user()->type == 'company')
        <div class="action-btn me-2">
            <a data-size="lg" data-url="{{ route('contracts.copy', $contract->id) }}"data-ajax-popup="true"
                data-title="{{ __('Duplicate') }}" class="mx-3 btn btn-sm align-items-center bg-primary"
                data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Duplicate') }}"><i
                    class="ti ti-copy text-white"></i></a>
        </div>
    @endif
@endpermission
@permission('contract show')
    <div class="action-btn me-2">
        <a class="mx-3 btn btn-sm  align-items-center bg-warning" href="{{ route('contract.show', $contract->id) }}"
            data-size="md" data-bs-toggle="tooltip" title="{{ __('View') }}">
            <i class="ti ti-eye text-white"></i>
        </a>
    </div>
@endpermission
@permission('contract edit')
    <div class="action-btn me-2">
        <a class="mx-3 btn btn-sm  align-items-center bg-info" data-url="{{ route('contract.edit', $contract->id) }}"
                data-ajax-popup="true" data-size="lg" data-bs-toggle="tooltip" title=""
                data-title="{{ __('Edit Contract') }}" data-bs-original-title="{{ __('Edit') }}">
                <i class="ti ti-pencil text-white"></i>
        </a>
    </div>
@endpermission
@permission('contract delete')
    <div class="action-btn ">
        {!! Form::open([
            'method' => 'DELETE',
            'route' => ['contract.destroy', $contract->id],
            'id' => 'delete-form-' . $contract->id,
        ]) !!}
        <a class="mx-3 btn btn-sm  align-items-center bs-pass-para show_confirm bg-danger" data-bs-toggle="tooltip"
            title="{{ __('Delete') }}"
            data-text="{{ __('This action can not be undone. Do you want to continue?') }}"><i
                class="ti ti-trash text-white text-white"></i></a>
        {!! Form::close() !!}
    </div>
@endpermission
