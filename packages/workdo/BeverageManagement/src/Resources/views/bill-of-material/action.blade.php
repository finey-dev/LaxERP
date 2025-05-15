@permission('move stock')
<div class="action-btn me-2">
    <a data-size="lg" data-url="{{ route('collection-center.qty.transfer',[$bill_of_material->id,'Bill of Material']) }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{__('Move Stock')}}" data-title="{{__('Move Stock')}}" class="mx-3 btn btn-sm bg-secondary align-items-center">
        <i class="ti ti-arrows-right-left"></i>
    </a>

</div>
@endpermission

@permission('bill of material show')
<div class="action-btn me-2">
    <a class="mx-3 btn btn-sm bg-warning align-items-center" href="{{ route('bill-of-material.show',$bill_of_material->id) }}" data-bs-toggle="tooltip" title="{{__('View')}}">
        <i class="ti ti-eye text-white"></i>
    </a>
</div>
@endpermission

@permission('bill of material edit')
@if($bill_of_material->status == 0)
<div class="action-btn me-2">
    <a class="mx-3 btn bg-info btn-sm align-items-center" href="{{ route('bill-of-material.edit',$bill_of_material->id) }}" data-bs-toggle="tooltip" title="{{__('Edit')}}">
        <i class="ti ti-pencil text-white"></i>
    </a>
</div>
@endif
@endpermission

@permission('bill of material delete')
<div class="action-btn">
    {!! Form::open(['method' => 'DELETE', 'route' => ['bill-of-material.destroy', $bill_of_material->id],'id'=>'delete-form-'.$bill_of_material->id, 'class' => 'mb-1 mt-1']) !!}
    <a href="#" class="mx-3 btn bg-danger btn-sm align-items-center bs-pass-para show_confirm" data-bs-toggle="tooltip" title="{{__('Delete')}}" data-confirm="{{ __('Are You Sure?') }}" data-text="{{ __('This action can not be undone. Do you want to continue?') }}">
        <i class="ti ti-trash text-white"></i>
    </a>
    {!! Form::close() !!}
</div>
@endpermission
