@permission('machine show')
    <div class="action-btn me-2">
        <a class="btn btn-sm bg-warning align-items-center"
            data-url="{{ route('machine-repair.show', \Crypt::encrypt($machine->id)) }}" data-ajax-popup="true" data-size="lg"
            data-bs-toggle="tooltip" title="{{ __('View') }}" data-title="{{ __('View Machine') }}"
            data-bs-original-title="{{ __('View') }}">
            <i class="ti ti-eye text-white"></i>
        </a>
    </div>
@endpermission
@permission('machine edit')
    <div class="action-btn me-2">
        <a data-size="lg" data-url="{{ route('machine-repair.edit', $machine->id) }}"
            class="btn btn-sm bg-info align-items-center text-white " data-ajax-popup="true" data-bs-toggle="tooltip"
            data-title="{{ __('Edit Machine') }}" title="{{ __('Edit') }}"><i class="ti ti-pencil"></i></a>
    </div>
@endpermission
@permission('machine delete')
    <div class="action-btn">
        {!! Form::open(['method' => 'DELETE', 'route' => ['machine-repair.destroy', $machine->id]]) !!}
        <a href="#!" class="btn btn-sm bg-danger align-items-center text-white show_confirm" data-bs-toggle="tooltip"
            title='Delete'>
            <i class="ti ti-trash"></i>
        </a>
        {!! Form::close() !!}
    </div>
@endpermission
