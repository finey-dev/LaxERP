<div class="action-btn me-2">
    <a class="mx-3 btn btn-sm align-items-center bg-success"
        data-url="{{ route('send.business.mail', $businessProcessMapping->id) }}"
        data-ajax-popup="true" data-size="md" data-bs-toggle="tooltip"
        title="{{ __('Add Email') }}" data-title="{{ __('Add Email') }}">
        <i class="ti ti-mail text-white"></i>
    </a>
</div>
<div class="action-btn me-2">
    <a class="mx-3 btn btn-sm align-items-center bg-secondary"
        href="{{ route('business.preview', $businessProcessMapping->id) }}"
        target="_blank">
        <i class="ti ti-crosshair text-white" data-bs-toggle="tooltip"
            data-bs-original-title="{{ __('Preview') }}"></i>
    </a>
</div>
<div class="action-btn me-2">
    <a href="{{ route('store.flowchart', $businessProcessMapping->id) }}"
        class="mx-3 btn btn-sm align-items-center bg-warning"><i
            class="ti ti-vector text-white" data-bs-toggle="tooltip"
            title="{{ __('Edit Flowchart') }}"></i>
    </a>
</div>
<div class="action-btn me-2">
    <a href="#"
        class="mx-3 btn btn-sm d-inline-flex align-items-center text-white cp_link bg-primary"
        data-link="{{ route('business.shared.link', \Illuminate\Support\Facades\Crypt::encrypt($businessProcessMapping->id)) }}"
        data-bs-toggle="tooltip" title="{{ __('Copy') }}"
        data-original-title="{{ __('Copy') }}">
        <span class="btn-inner--icon text-white"><i
                class="ti ti-file"></i></span>
    </a>
</div>
@permission('businessprocessmapping edit')
    <div class="action-btn me-2">
        <a class="mx-3 btn btn-sm align-items-center bg-info"
            data-url="{{ route('business-process-mapping.edit', $businessProcessMapping->id) }}"
            data-ajax-popup="true" data-size="md" data-bs-toggle="tooltip"
            title="{{ __('Edit') }}"
            data-title="{{ __('Edit Business Process Mapping') }}">
            <i class="ti ti-pencil text-white"></i>
        </a>
    </div>
@endpermission
@permission('businessprocessmapping delete')
    <div class="action-btn">
        {{ Form::open(['route' => ['business-process-mapping.destroy', $businessProcessMapping->id], 'class' => 'm-0']) }}
        @method('DELETE')
        <a class="mx-3 btn btn-sm  align-items-center bs-pass-para show_confirm bg-danger"
            data-bs-toggle="tooltip" title=""
            data-bs-original-title="Delete" aria-label="Delete"
            data-confirm="{{ __('Are You Sure?') }}"
            data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
            data-confirm-yes="delete-form-">
            <i class="ti ti-trash text-white text-white"></i>
        </a>
        {{ Form::close() }}
    </div>
@endpermission
