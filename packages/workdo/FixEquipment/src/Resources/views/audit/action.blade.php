@permission('equipment audit manage')
    <div class="action-btn me-2">
        <a class="btn btn-sm  bg-primary align-items-center" data-url="{{ route('fix.equipment.audit.status', $audit->id) }}"
            data-ajax-popup="true" data-size="md" data-bs-toggle="tooltip" title=""
            data-title="{{ __('Audit Status') }}" data-bs-original-title="{{ __('Status') }}">
            <i class="ti ti-caret-right text-white"></i>
        </a>
    </div>
@endpermission
@permission('equipment audit manage')
    <div class="action-btn me-2">
        <a href="{{ route('fix.equipment.audit.show', \Illuminate\Support\Facades\Crypt::encrypt($audit->id)) }}"
            data-bs-toggle="tooltip" data-bs-original-title="{{ __('Show')}}" class="btn btn-sm  bg-warning align-items-center">
            <span class="btn-inner--icon text-white"><i class="ti ti-eye"></i></span>
        </a>
    </div>
@endpermission
@if ($audit->audit_status == 'Pending')
    @permission('equipment audit edit')
        <div class="action-btn me-2">
            <a class="btn btn-sm  bg-info align-items-center" data-bs-toggle="tooltip" data-bs-original-title="{{ __('Edit')}}"
                href="{{ route('fix.equipment.audit.edit', \Illuminate\Support\Facades\Crypt::encrypt($audit->id)) }}">
                <i class="ti ti-pencil text-white"></i>
            </a>
        </div>
    @endpermission
@endif
@permission('equipment audit delete')
    <div class="action-btn">
        {{ Form::open(['route' => ['fix.equipment.audit.delete', $audit->id], 'class' => 'm-0']) }}
        @method('GET')
        <a class="btn btn-sm  bg-danger align-items-center bs-pass-para show_confirm" data-bs-toggle="tooltip" title=""
            data-bs-original-title="{{ __('Delete')}}" aria-label="Delete" data-confirm="{{ __('Are You Sure?') }}"
            data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
            data-confirm-yes="delete-form-{{ $audit->id }}"><i class="ti ti-trash text-white text-white"></i></a>
        {{ Form::close() }}
    </div>
@endpermission
