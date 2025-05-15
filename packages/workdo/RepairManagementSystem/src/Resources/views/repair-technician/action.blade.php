
@permission('repair technician edit')
    <div class="action-btn me-2">
        <a class="btn btn-sm bg-info align-items-center" data-url="{{ route('repair-technician.edit', $repair_technician->id) }}"
            data-ajax-popup="true" data-size="md" data-bs-toggle="tooltip" data-title="{{__('Edit Repair Technician')}}"
            data-bs-original-title="{{ __('Edit') }}">
            <i class="ti ti-pencil text-white"></i>
        </a>
    </div>
@endpermission
@permission('repair technician delete')
<div class="action-btn">
    {!! Form::open(['method' => 'DELETE', 'route' => ['repair-technician.destroy', $repair_technician->id],'id'=>'delete-form-'.$repair_technician->id]) !!}

    <a type="submit" class="btn btn-sm bg-danger align-items-center show_confirm" data-toggle="tooltip" title="{{ __('Delete') }}" data-bs-original-title="{{ _('Delete') }}" data-confirm="{{ __('Are You Sure?') }}" data-text="{{ __('This action can not be undone. and this recored all data delete. Do you want to continue?') }}">
        <i class="ti ti-trash text-white"></i>
    </a>
    {!! Form::close() !!}
</div>
@endpermission
