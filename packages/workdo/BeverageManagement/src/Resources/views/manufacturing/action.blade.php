@permission('move stock')
@if($manufacturing->status == 1)
<div class="action-btn  me-2">
    <a data-size="lg" data-url="{{ route('collection-center.qty.transfer',[$manufacturing->id,'Manufactured']) }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{__('Move Stock')}}" data-title="{{__('Move Stock')}}" class="mx-3 btn btn-sm bg-secondary align-items-center">
        <i class="ti ti-arrows-right-left"></i>
    </a>

</div>
@endif
@endpermission
@permission('manufacturing status')
@if($manufacturing->status == 0)
<div class="action-btn me-2">
    {!! Form::open(['method' => 'GET', 'route' => ['manufacture.status', $manufacturing->id]]) !!}
    <a href="#" class="mx-3 btn bg-secondary btn-sm align-items-center bs-pass-para show_confirm" data-bs-toggle="tooltip" title="{{__('Change Status To Completed')}}" data-title="{{__('Change Status To Completed')}}" data-confirm="{{ __('Are You Sure?') }}" data-text="{{ __('Do You Want To Change Manufacturing Status To Completed') }}">
        <i class="ti ti-arrow-down-right text-white"></i>
    </a>
    {!! Form::close() !!}
</div>
@endif
@endpermission

@permission('manufacturing show')
<div class="action-btn me-2">
    <a class="mx-3 btn bg-warning btn-sm align-items-center" href="{{ route('manufacturing.show',$manufacturing->id) }}" data-bs-toggle="tooltip" title="{{__('View')}}">
        <i class="ti ti-eye text-white"></i>
    </a>
</div>
@endpermission

@permission('manufacturing edit')
@if($manufacturing->status == 0)
<div class="action-btn me-2">
    <a class="mx-3 btn bg-info btn-sm align-items-center" href="{{ route('manufacturing.edit',$manufacturing->id) }}" data-bs-toggle="tooltip" title="{{__('Edit')}}">
        <i class="ti ti-pencil text-white"></i>
    </a>
</div>
@endif
@endpermission

@permission('manufacturing delete')
<div class="action-btn">
    {!! Form::open(['method' => 'DELETE', 'route' => ['manufacturing.destroy', $manufacturing->id],'id'=>'delete-form-'.$manufacturing->id, 'class' => 'mb-1 mt-1']) !!}
    <a href="#" class="mx-3 bg-danger mt-1 mb-1 btn btn-sm align-items-center bs-pass-para show_confirm" data-bs-toggle="tooltip" title="{{__('Delete')}}" data-confirm="{{ __('Are You Sure?') }}" data-text="{{ __('This action can not be undone. Do you want to continue?') }}">
        <i class="ti ti-trash text-white"></i>
    </a>
    {!! Form::close() !!}
</div>
@endpermission
