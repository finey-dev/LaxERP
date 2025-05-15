@permission('add stock')
@if($packaging->status == 1)
<div class="action-btn me-2">
    <a data-size="lg" data-url="{{ route('add-stock.show.form',[$packaging->id,'Packaging']) }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{__('Add Stock')}}" data-title="{{__('Add Stock')}}" class="mx-3 btn bg-success btn-sm align-items-center">
        <i class="ti ti-plus"></i>
    </a>
</div>
@endif
@endpermission
@permission('move stock')
<div class="action-btn me-2">
    <a data-size="lg" data-url="{{ route('collection-center.qty.transfer',[$packaging->id,'Packaging']) }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{__('Move Stock')}}" data-title="{{__('Move Stock')}}" class="mx-3 bg-secondary btn btn-sm align-items-center">
        <i class="ti ti-arrows-right-left"></i>
    </a>

</div>
@endpermission

@permission('packaging status')
@if($packaging->status == 0)
<div class="action-btn me-2">
    {!! Form::open(['method' => 'GET', 'route' => ['package.status', $packaging->id]]) !!}
    <a href="#" class="mx-3 btn bg-secondary btn-sm align-items-center bs-pass-para show_confirm" data-bs-toggle="tooltip" title="{{__('Change Status To Completed')}}" data-title="{{__('Change Status To Completed')}}" data-confirm="{{ __('Are You Sure?') }}" data-text="{{ __('Do You Want To Change Packaging Status To Completed') }}">
        <i class="ti ti-arrow-down-right text-white"></i>
    </a>
    {!! Form::close() !!}
</div>
@endif
@endpermission

@permission('packaging show')
<div class="action-btn me-2">
    <a class="mx-3 btn btn-sm bg-warning align-items-center" href="{{ route('packaging.show',$packaging->id) }}" data-bs-toggle="tooltip" title="{{__('View')}}">
        <i class="ti ti-eye text-white"></i>
    </a>
</div>
@endpermission

@permission('packaging edit')
@if($packaging->status == 0)
<div class="action-btn me-2">
    <a class="mx-3 btn btn-sm bg-info align-items-center" href="{{ route('packaging.edit',$packaging->id) }}" data-bs-toggle="tooltip" title="{{__('Edit')}}">
        <i class="ti ti-pencil text-white"></i>
    </a>
</div>
@endif
@endpermission

@permission('packaging delete')
<div class="action-btn">
    {!! Form::open(['method' => 'DELETE', 'route' => ['packaging.destroy', $packaging->id],'id'=>'delete-form-'.$packaging->id, 'class' => 'mb-1 mt-1']) !!}
    <a href="#" class="mx-3 mt-1 mb-1 bg-danger btn btn-sm align-items-center bs-pass-para show_confirm" data-bs-toggle="tooltip" title="{{__('Delete')}}" data-confirm="{{ __('Are You Sure?') }}" data-text="{{ __('This action can not be undone. Do you want to continue?') }}">
        <i class="ti ti-trash text-white"></i>
    </a>
    {!! Form::close() !!}
</div>
@endpermission
