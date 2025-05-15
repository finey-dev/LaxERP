@permission('beverage-maintenance edit')
<div class="action-btn me-2">
    <a class="mx-3 btn btn-sm bg-info align-items-center"
        data-url="{{ route('beverage-maintenance.edit', $beverage_maintenance->id) }}"
        data-ajax-popup="true" data-size="lg" data-bs-toggle="tooltip"
        title="" data-title="{{ __('Edit Maintenance') }}"
        data-bs-original-title="{{ __('Edit') }}">
        <i class="ti ti-pencil text-white"></i>
    </a>
</div>
@endpermission

@permission('beverage-maintenance delete')
    <div class="action-btn">
        {!! Form::open([
            'method' => 'DELETE',
            'route' => ['beverage-maintenance.destroy', $beverage_maintenance->id],
            'id' => 'delete-form-' . $beverage_maintenance->id,
        ]) !!}
        <a class="mx-3 btn btn-sm bg-danger align-items-center bs-pass-para show_confirm" data-bs-toggle="tooltip"
            title="{{ __('Delete') }}" data-confirm="{{ __('Are You Sure?') }}" data-text="{{ __('This action can not be undone. Do you want to continue?') }}"><i
                class="ti ti-trash text-white text-white"></i></a>
        {!! Form::close() !!}
    </div>
@endpermission
