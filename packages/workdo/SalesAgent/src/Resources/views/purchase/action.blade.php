@permission('salesagent purchase show')
    <div class="action-btn me-2">
        <a href="{{ route('salesagents.purchase.order.show', \Crypt::encrypt($action->id)) }}"
            class="bg-warning btn btn-sm align-items-center" data-bs-toggle="tooltip" title="{{ __('View') }}">
            <i class="ti ti-eye text-white text-white"></i>
        </a>
    </div>
@endpermission
@permission('salesagent purchase delete')
    <div class="action-btn">
        {{ Form::open(['route' => ['salesagents.purchase.order.destroy', $action->id], 'class' => 'm-0']) }}
        @method('DELETE')
        <a class="bg-danger btn btn-sm  align-items-center bs-pass-para show_confirm" data-bs-toggle="tooltip" title=""
            data-bs-original-title="Delete" aria-label="Delete" data-confirm="{{ __('Are You Sure?') }}"
            data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
            data-confirm-yes="delete-form-{{ $action->id }}"><i class="ti ti-trash text-white text-white"></i></a>
        {{ Form::close() }}
    </div>
@endpermission
