@permission('fix equipment assets manage')
    <div class="action-btn me-2">
        <a href="{{ route('fix.equipment.assets.show', \Illuminate\Support\Facades\Crypt::encrypt($asset->id)) }}"
            class="btn btn-sm bg-warning align-items-center" data-bs-toggle="tooltip" title="{{ __('View') }}">
            <i class="ti ti-eye text-white"></i>
        </a>
    </div>
@endpermission

@permission('fix equipment assets edit')
    <div class="action-btn me-2">
        <a class="btn btn-sm bg-info align-items-center" data-url="{{ route('fix.equipment.assets.edit', $asset->id) }}"
            data-ajax-popup="true" data-size="lg" data-bs-toggle="tooltip" title="{{ __('Edit') }}" data-title="{{ __('Edit Asset') }}"
            data-bs-original-title="{{ __('Edit') }}">
            <i class="ti ti-pencil text-white"></i>
        </a>
    </div>
@endpermission

@permission('fix equipment assets delete')
    <div class="action-btn">
        {{ Form::open(['route' => ['fix.equipment.assets.delete', $asset->id], 'method' => 'DELETE', 'class' => 'm-0']) }}
        @method('GET')
        <a class="btn btn-sm  bg-danger align-items-center bs-pass-para show_confirm" data-bs-toggle="tooltip"
            title="{{ __('Delete') }}" data-bs-original-title="{{ __('Delete')}}" aria-label="Delete"
            data-confirm="{{ __('Are You Sure?') }}"
            data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
            data-confirm-yes="delete-form-{{ $asset['id'] }}">
            <i class="ti ti-trash text-white"></i>
        </a>
        {{ Form::close() }}
    </div>
@endpermission
