

@permission('courier agents show')
<div class="action-btn me-2">
    <a class="mx-3 btn btn-sm  align-items-center bg-warning" data-url="{{ route('courier-agents.show',$courier_agent->id) }}" data-ajax-popup="true" data-size="lg" data-bs-toggle="tooltip" title="" data-title="{{ __('Courier Agent Detail') }}" data-bs-original-title="{{ __('View') }}">
        <i class="ti ti-eye text-white"></i>
    </a>
</div>
@endpermission
@permission('courier agents edit')
<div class="action-btn me-2">
    <a class="mx-3 btn btn-sm  align-items-center bg-info"
        data-url="{{ route('courier-agents.edit', $courier_agent->id) }}"
        data-ajax-popup="true" data-size="lg" data-bs-toggle="tooltip"
        title="" data-title="{{ __('Edit Courier Agent') }}"
        data-bs-original-title="{{ __('Edit') }}">
        <i class="ti ti-pencil text-white"></i>
    </a>
</div>
@endpermission

@permission('courier agents delete')
    <div class="action-btn">
        {!! Form::open([
            'method' => 'DELETE',
            'route' => ['courier-agents.destroy', $courier_agent->id],
            'id' => 'delete-form-' . $courier_agent->id,
        ]) !!}
        <a class="mx-3 btn btn-sm bg-danger align-items-center bs-pass-para show_confirm" data-bs-toggle="tooltip"
            title="{{ __('Delete') }}" data-confirm="{{ __('Are You Sure?') }}" data-text="{{ __('This action can not be undone. Do you want to continue?') }}"><i
                class="ti ti-trash text-white text-white"></i></a>
        {!! Form::close() !!}
    </div>
@endpermission
