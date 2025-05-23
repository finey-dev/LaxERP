@if ($action->is_disable == 1)
    <span>
        @if (!empty($action->customer_id))
            @permission('salesagent show')
                <div class="action-btn me-2">
                    <a href="{{ route('salesagents.show', \Crypt::encrypt($action->id)) }}"
                        class="bg-warning btn btn-sm align-items-center" data-bs-toggle="tooltip" title="{{ __('View') }}">
                        <i class="ti ti-eye text-white text-white"></i>
                    </a>
                </div>
            @endpermission
        @endif
        @permission('salesagent edit')
            <div class="action-btn me-2">
                <a class="bg-info btn btn-sm  align-items-center" data-url="{{ route('salesagents.edit', $action->id) }}"
                    data-ajax-popup="true" data-size="lg" data-bs-toggle="tooltip" title=""
                    data-title="{{ __('Edit Sales Agent') }}" data-bs-original-title="{{ __('Edit') }}">
                    <i class="ti ti-pencil text-white"></i>
                </a>
            </div>
        @endpermission
        @if (!empty($action->customer_id))
            @permission('salesagent delete')
                <div class="action-btn">
                    {{ Form::open(['route' => ['salesagents.destroy', $action->id], 'class' => 'm-0']) }}
                    @method('DELETE')
                    <a class="bg-danger btn btn-sm  align-items-center bs-pass-para show_confirm" data-bs-toggle="tooltip"
                        title="" data-bs-original-title="Delete" aria-label="Delete"
                        data-confirm="{{ __('Are You Sure?') }}"
                        data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                        data-confirm-yes="delete-form-{{ $action->id }}"><i
                            class="ti ti-trash text-white text-white"></i></a>
                    {{ Form::close() }}
                </div>
            @endpermission
        @endif
    </span>
@else
    <div class="text-center">
        <i class="ti ti-lock"></i>
    </div>
@endif
