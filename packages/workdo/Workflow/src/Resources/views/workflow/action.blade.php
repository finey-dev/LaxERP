<td class="Action">
    @permission('workflow edit')
        <div class="action-btn me-2">
            <a  class="bg-info btn btn-sm align-items-center" href="{{ route('workflow.edit',$workflow->id) }}" data-bs-toggle="tooltip" title="{{__('Edit')}}">
                <i class="ti ti-pencil text-white"></i>
            </a>
        </div>
    @endpermission
    @permission('workflow delete')
        <div class="action-btn ">
            {!! Form::open(['method' => 'DELETE', 'route' => ['workflow.destroy', $workflow->id],'id'=>'delete-form-'.$workflow->id]) !!}
            <a  class="bg-danger btn btn-sm  align-items-center bs-pass-para show_confirm" data-bs-toggle="tooltip" title="{{__('Delete')}}"><i class="ti ti-trash text-white text-white"></i></a>
            {!! Form::close() !!}
        </div>
    @endpermission
</td>
