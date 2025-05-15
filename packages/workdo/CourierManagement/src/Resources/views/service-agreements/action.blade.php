@permission('service agreements show')
<div class="action-btn me-2">
    <a class="mx-3 btn btn-sm  align-items-center bg-warning" data-url="{{ route('service-agreements.show',$service_agreements->id) }}" data-ajax-popup="true" data-size="lg" data-bs-toggle="tooltip" title="" data-title="{{ __('Service Agreements Detail') }}" data-bs-original-title="{{ __('View') }}">
        <i class="ti ti-eye text-white"></i>
    </a>
</div>
@endpermission
@permission('service agreements edit')
<div class="action-btn  me-2">
    <a class="mx-3 btn btn-sm  align-items-center bg-info"
        data-url="{{ route('service-agreements.edit', $service_agreements->id) }}"
        data-ajax-popup="true" data-size="lg" data-bs-toggle="tooltip"
        title="" data-title="{{ __('Edit Service Agreement') }}"
        data-bs-original-title="{{ __('Edit') }}">
        <i class="ti ti-pencil text-white"></i>
    </a>
</div>
@endpermission


@permission('service agreements delete')
    <div class="action-btn me-2">
        {!! Form::open([
            'method' => 'DELETE',
            'route' => ['service-agreements.destroy', $service_agreements->id],
            'id' => 'delete-form-' . $service_agreements->id,
        ]) !!}
        <a class="mx-3 btn btn-sm bg-danger align-items-center bs-pass-para show_confirm" data-bs-toggle="tooltip"
            title="{{ __('Delete') }}" data-confirm="{{ __('Are You Sure?') }}" data-text="{{ __('This action can not be undone. Do you want to continue?') }}"><i
                class="ti ti-trash text-white text-white"></i></a>
        {!! Form::close() !!}
    </div>
@endpermission
