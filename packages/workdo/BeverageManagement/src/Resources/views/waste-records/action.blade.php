@permission('waste-records show')
<div class="action-btn me-2">
    <a class="mx-3 btn btn-sm bg-warning align-items-center" data-url="{{ route('waste-records.show',$waste_records->id) }}" data-ajax-popup="true" data-size="lg" data-bs-toggle="tooltip" title="" data-title="{{ __('Show Detail') }}" data-bs-original-title="{{ __('View') }}">
        <i class="ti ti-eye text-white"></i>
    </a>
</div>
@endpermission

@permission('waste-records edit')
<div class="action-btn me-2">
    <a class="mx-3 btn btn-sm bg-info align-items-center"
        data-url="{{ route('waste-records.edit', $waste_records->id) }}"
        data-ajax-popup="true" data-size="lg" data-bs-toggle="tooltip"
        title="" data-title="{{ __('Edit Waste Records') }}"
        data-bs-original-title="{{ __('Edit') }}">
        <i class="ti ti-pencil text-white"></i>
    </a>
</div>
@endpermission

@permission('waste-records delete')
    <div class="action-btn">
        {!! Form::open([
            'method' => 'DELETE',
            'route' => ['waste-records.destroy', $waste_records->id],
            'id' => 'delete-form-' . $waste_records->id,
        ]) !!}
        <a class="mx-3 btn btn-sm bg-danger align-items-center bs-pass-para show_confirm" data-bs-toggle="tooltip"
            title="{{ __('Delete') }}" data-confirm="{{ __('Are You Sure?') }}" data-text="{{ __('This action can not be undone. Do you want to continue?') }}"><i
                class="ti ti-trash text-white text-white"></i></a>
        {!! Form::close() !!}
    </div>
@endpermission
