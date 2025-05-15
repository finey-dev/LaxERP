@permission('accessories edit')
    <div class="action-btn me-2">
        <a class="btn btn-sm  bg-info align-items-center" data-url="{{ route('fix.equipment.accessories.edit', $accessory->id) }}"
            data-ajax-popup="true" data-size="md" data-bs-toggle="tooltip" title="{{ __('Edit') }}"  data-title="{{ __('Edit Accessory') }}"
            data-bs-original-title="{{ __('Edit') }}">
            <i class="ti ti-pencil text-white"></i>
        </a>
    </div>
@endpermission

@permission('accessories delete')
    <div class="action-btn">
        {{ Form::open(['route' => ['fix.equipment.accessories.delete', $accessory->id], 'class' => 'm-0']) }}
        @method('GET')
        <a class="btn btn-sm  bg-danger align-items-center bs-pass-para show_confirm" data-bs-toggle="tooltip"
            title="{{ __('Delete') }}" data-bs-original-title="{{ __('Delete')}}" aria-label="Delete"
            data-confirm="{{ __('Are You Sure?') }}"
            data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
            data-confirm-yes="delete-form-{{ $accessory['id'] }}">
            <i class="ti ti-trash text-white"></i>
        </a>
        {{ Form::close() }}
    </div>
@endpermission
