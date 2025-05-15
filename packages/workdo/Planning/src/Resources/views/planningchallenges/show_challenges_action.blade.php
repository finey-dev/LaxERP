@if (Laratrust::hasPermission('charters delete') || Laratrust::hasPermission('charters edit') || Laratrust::hasPermission('charters show'))
<td class="Action">

    <div class="action-btn me-2">
        <a href="#"
        data-url="{{ route('charters.receipt', $charters->id) }}"
        data-size="lg" data-ajax-popup="true" class=" mx-3 btn btn-sm align-items-center bg-dark"
        data-bs-toggle="tooltip"
        title="{{ __('Print') }}"><i class="text-white ti ti-printer"></i></a>
    </div>
    @permission('charters show')
        <div class="action-btn me-2">
            <a href="{{ route('planningcharters.show', $charters->id) }}"
                class="mx-3 btn btn-sm align-items-center bg-warning" data-bs-toggle="tooltip"
                title="" data-bs-original-title="{{ __('View') }}">
                <i class="text-white ti ti-eye"></i>
            </a>
        </div>
    @endpermission
    @permission('charters edit')
        <div class="action-btn me-2">
            <a href="{{ route('planningcharters.edit', $charters->id) }}"
                class="mx-3 btn btn-sm align-items-center bg-info" data-bs-toggle="tooltip"
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
            <a class="mx-3 btn btn-sm align-items-center bs-pass-para show_confirm bg-danger"
                data-bs-toggle="tooltip" title="{{ __('Delete') }}"><i
                    class="text-white ti ti-trash"></i></a>
            {!! Form::close() !!}
        </div>
    @endpermission
</td>
@endif
