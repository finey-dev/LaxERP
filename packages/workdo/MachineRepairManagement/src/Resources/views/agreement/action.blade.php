@permission('machine service agreement edit')
    <div class="action-btn me-2">
        <a class="btn btn-sm bg-info align-items-center"
            data-url="{{ route('machine-service-agreement.edit', $machineserviceagreement->id) }}"
            data-ajax-popup="true" data-size="lg" data-bs-toggle="tooltip"
            title="" data-title="{{ __('Edit Service Agreement') }}"
            data-bs-original-title="{{ __('Edit') }}">
            <i class="ti ti-pencil text-white"></i>
        </a>
    </div>
@endpermission

@permission('machine service agreement delete')
    <div class="action-btn">
        {{ Form::open(['route' => ['machine-service-agreement.destroy', $machineserviceagreement->id], 'class' => 'm-0']) }}
        @method('DELETE')
        <a class="btn btn-sm bg-danger align-items-center bs-pass-para show_confirm"
            data-bs-toggle="tooltip" title=""
            data-bs-original-title="Delete" aria-label="Delete"
            data-confirm="{{ __('Are You Sure?') }}"
            data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
            data-confirm-yes="delete-form-{{ $machineserviceagreement->id }}"><i
                class="ti ti-trash text-white text-white"></i></a>
        {{ Form::close() }}
    </div>
@endpermission
