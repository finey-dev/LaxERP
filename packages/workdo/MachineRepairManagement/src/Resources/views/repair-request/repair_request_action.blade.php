@permission('repair request show')
    <div class="action-btn me-2">
        <a class="btn btn-sm  bg-warning align-items-center"
            data-url="{{ route('machine-repair-request.show', \Crypt::encrypt($repair_request->id)) }}" data-ajax-popup="true"
            data-size="lg" data-bs-toggle="tooltip" title="{{ __('View') }}"
            data-title="{{ __('View Machine Repair Request') }}" data-bs-original-title="{{ __('View') }}">
            <i class="ti ti-eye text-white"></i>
        </a>
    </div>
@endpermission
@permission('repair request edit')
    <div class="action-btn me-2">
        <a data-size="lg" data-url="{{ route('machine-repair-request.edit', $repair_request->id) }}"
            class="btn btn-sm  bg-info align-items-center text-white " data-ajax-popup="true" data-bs-toggle="tooltip"
            data-title="{{ __('Machine Repair Request Edit') }}" title="{{ __('Edit') }}"><i
                class="ti ti-pencil"></i></a>
    </div>
@endpermission
@permission('repair request delete')
    <div class="action-btn">
        {!! Form::open(['method' => 'DELETE', 'route' => ['machine-repair-request.destroy', $repair_request->id]]) !!}
        <a href="#!" class="btn btn-sm bg-danger align-items-center text-white show_confirm" data-bs-toggle="tooltip"
            title='Delete'>
            <i class="ti ti-trash"></i>
        </a>
        {!! Form::close() !!}
    </div>
@endpermission
