@permission('add stock')
<div class="action-btn me-2">
    <a data-size="lg" data-url="{{ route('add-stock.create',$raw_meterial->id) }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{__('Add Stock')}}" data-title="{{__('Add Stock')}}" class="mx-3 btn btn-sm bg-success align-items-center">
        <i class="ti ti-plus"></i>
    </a>
</div>
@endpermission
@permission('move stock')
<div class="action-btn me-2">
    <a data-size="lg" data-url="{{ route('collection-center.qty.transfer',[$raw_meterial->id,'Raw Material']) }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{__('Move Stock')}}" data-title="{{__('Move Stock')}}" class="mx-3 btn btn-sm bg-secondary align-items-center">
        <i class="ti ti-arrows-right-left"></i>
    </a>
</div>
@endpermission

@permission('raw material show')
<div class="action-btn me-2">
    <a class="mx-3 btn bg-warning btn-sm  align-items-center" data-url="{{ route('raw-material.show',$raw_meterial->id) }}" data-ajax-popup="true" data-size="lg" data-bs-toggle="tooltip" title="" data-title="{{ __('Show Detail') }}" data-bs-original-title="{{ __('View') }}">
        <i class="ti ti-eye text-white"></i>
    </a>
</div>
@endpermission

@permission('raw material edit')
<div class="action-btn me-2">
    <a class="mx-3 btn bg-info btn-sm align-items-center" href="{{ route('raw-material.edit',$raw_meterial->id) }}" data-bs-toggle="tooltip" title="{{__('Edit')}}">
        <i class="ti ti-pencil text-white"></i>
    </a>
</div>
@endpermission


@permission('raw material delete')
<div class="action-btn">
    {!! Form::open([
    'method' => 'DELETE',
    'route' => ['raw-material.destroy', $raw_meterial->id],
    'id' => 'delete-form-' . $raw_meterial->id,
    ]) !!}
    <a class="mx-3 btn btn-sm bg-danger align-items-center bs-pass-para show_confirm" data-bs-toggle="tooltip" title="{{ __('Delete') }}" data-confirm="{{ __('Are You Sure?') }}" data-text="{{ __('This action can not be undone. Do you want to continue?') }}"><i class="ti ti-trash text-white text-white"></i></a>
    {!! Form::close() !!}
</div>
@endpermission
