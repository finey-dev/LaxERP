    @if (Laratrust::hasPermission(['salesagent programs show', 'programs show']))
        <div class="action-btn me-2">
            <a href="{{ route('programs.show', \Crypt::encrypt($action->id)) }}"
                class="bg-warning btn btn-sm align-items-center" data-bs-toggle="tooltip" title="{{ __('View') }}">
                <i class="ti ti-eye text-white text-white"></i>
            </a>
        </div>
        @if (Laratrust::hasPermission('salesagent programs show') &&
                !in_array(\Auth::user()->id, explode(',', $action->sales_agents_applicable)) &&
                !in_array(\Auth::user()->id, explode(',', $action->requests_to_join)))
            <div class="action-btn me-2">
                <a href="{{ route('salesagent.program.send.request', [$action->id]) }}"
                    class="bg-primary btn btn-sm align-items-center" data-bs-toggle="tooltip" title="{{ __('Send Request') }}">
                    <i class="ti ti-arrow-forward-up text-white text-white"></i>
                </a>
            </div>
        @endif
    @endif
    @permission('programs edit')
        <div class="action-btn me-2">
            <a href="{{ route('programs.edit', $action->id) }}" class="bg-info btn btn-sm  align-items-center" data-size="lg"
                data-bs-toggle="tooltip" title="" data-title="{{ __('Edit Sales Agent') }}"
                data-bs-original-title="{{ __('Edit') }}">
                <i class="ti ti-pencil text-white"></i>
            </a>
        </div>
    @endpermission
    @if (!empty($action->id))
        @permission('programs delete')
            <div class="action-btn">
                {{ Form::open(['route' => ['programs.destroy', $action->id], 'class' => 'm-0']) }}
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
