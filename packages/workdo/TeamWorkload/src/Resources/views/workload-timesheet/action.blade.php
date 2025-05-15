@permission('workload holidays edit')
<div class="action-btn me-2">
    <a data-url="{{ route('workload-timesheet.edit',$timesheet->id) }}" data-size="lg" data-ajax-popup="true" data-bs-toggle="tooltip" data-title="{{__('Edit Timesheet')}}" title="{{__('Edit')}}" class="btn btn-sm text-white bg-info">
        <i class="ti ti-pencil"></i>
    </a>
</div>
@endpermission

@permission('workload holidays delete')
<div class="action-btn">
    {!! Form::open(['method' => 'DELETE', 'route' => ['workload-timesheet.destroy', $timesheet->id]]) !!}
    <a href="#!" class="btn btn-sm align-items-center text-white show_confirm bg-danger" data-bs-toggle="tooltip" title='Delete' data-confirm="{{ __('Are You Sure?') }}"
    data-text="{{ __('This action can not be undone. Do you want to continue?') }}">
        <i class="ti ti-trash"></i>
    </a>
    {!! Form::close() !!}
</div>
@endpermission
