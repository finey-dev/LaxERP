@if (Laratrust::hasPermission('charters delete') || Laratrust::hasPermission('charters edit') || Laratrust::hasPermission('charters show'))
<td class="Action">
    <div class="action-btn me-2">
        <a href="#"
        data-url="{{ route('charters.receipt', $charters->id) }}"
        data-size="lg" data-ajax-popup="true" class="bg-dark btn btn-sm align-items-center"
        data-bs-toggle="tooltip" data-title="{{ __('Print Charters') }}"
        title="{{ __('Print') }}"><i class="text-white ti ti-printer"></i></a>
    </div>

    @permission('charters show')
        <div class="action-btn me-2">
            <a href="{{ route('planningcharters.show', $charters->id) }}"
                class="bg-warning btn btn-sm align-items-center" data-bs-toggle="tooltip"
                title="" data-bs-original-title="{{ __('View') }}">
                <i class="text-white ti ti-eye"></i>
            </a>
        </div>
    @endpermission
    @permission('charters edit')
        <div class="action-btn me-2">
            <a href="{{ route('planningcharters.edit', $charters->id) }}"
                class="bg-info btn btn-sm align-items-center" data-bs-toggle="tooltip"
                title="" data-bs-original-title="{{ __('Edit') }}">
                <i class="text-white ti ti-pencil"></i>
            </a>
        </div>
    @endpermission
    @permission('charters delete')
        <div class="action-btn">
            {!! Form::open([
                'method' => 'DELETE',
                'route' => ['planningcharters.destroy', $charters->id],
                'id' => 'delete-form-' . $charters->id,
            ]) !!}
            <a class="bg-danger btn btn-sm align-items-center bs-pass-para show_confirm"
                data-bs-toggle="tooltip" title="{{ __('Delete') }}" data-confirm="{{ __('Are You Sure?') }}"
                data-text="{{ __('This action can not be undone. Do you want to continue?') }}"><i
                    class="text-white ti ti-trash"></i></a>
            {!! Form::close() !!}
        </div>
    @endpermission
</td>
@endif
