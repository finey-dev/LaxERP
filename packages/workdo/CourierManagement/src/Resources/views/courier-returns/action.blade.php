@permission('courier returns show')
<div class="action-btn me-2">
    <a class="mx-3 btn btn-sm bg-warning align-items-center" data-url="{{ route('courier-returns.show',$courier_return->id) }}" data-ajax-popup="true" data-size="md" data-bs-toggle="tooltip" title="" data-title="{{ __('Courier Return Detail') }}" data-bs-original-title="{{ __('View') }}">
        <i class="ti ti-eye text-white"></i>
    </a>
</div>
@endpermission
@permission('courier returns edit')
<div class="action-btn me-2">
    <a class="mx-3 btn btn-sm  align-items-center bg-info"
        data-url="{{ route('courier-returns.edit', $courier_return->id) }}"
        data-ajax-popup="true" data-size="md" data-bs-toggle="tooltip"
        title="" data-title="{{ __('Edit Courier Return') }}"
        data-bs-original-title="{{ __('Edit') }}">
        <i class="ti ti-pencil text-white"></i>
    </a>
</div>
@endpermission


@permission('courier returns delete')
    <div class="action-btn">
        {!! Form::open([
            'method' => 'DELETE',
            'route' => ['courier-returns.destroy', $courier_return->id],
            'id' => 'delete-form-' . $courier_return->id,
        ]) !!}
        <a class="mx-3 btn btn-sm bg-danger align-items-center bs-pass-para show_confirm" data-bs-toggle="tooltip"
            title="{{ __('Delete') }}" data-confirm="{{ __('Are You Sure?') }}" data-text="{{ __('This action can not be undone. Do you want to continue?') }}"><i
                class="ti ti-trash text-white text-white"></i></a>
        {!! Form::close() !!}
    </div>
@endpermission
