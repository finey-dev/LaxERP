@permission('assets extra create')
<div class="action-btn  me-2">
    <a  class="btn btn-sm bg-success align-items-center" data-size="lg" data-url="{{ route('extra.create',$asset->id) }}" data-ajax-popup="true" data-title="{{__('Create Extra Asset')}}" data-size="lg" data-bs-toggle="tooltip" title="{{__('Extra')}}" data-original-title="{{__('Extra')}}">
        <i class="ti ti-frame"></i>
    </a>
</div>
@endpermission
@permission('assets defective manage')
<div class="action-btn me-2">
    <a  class="btn btn-sm bg-secondary align-items-center" data-size="lg" data-url="{{ route('defective.create',$asset->id) }}" data-ajax-popup="true" data-title="{{__('Create Defective Asset')}}" data-bs-toggle="tooltip" title="{{__('Defective')}}" data-original-title="{{__('Defective')}}">
        <i class="ti ti-bookmark-off"></i>
    </a>
</div>
@endpermission
@permission('assets distribution create')
<div class="action-btn me-2">
    <a  class="btn btn-sm bg-warning align-items-center" data-size="lg" data-url="{{ route('distribution.create',$asset->id) }}" data-ajax-popup="true" data-title="{{__('Create Distribution Asset')}}" data-size="lg" data-bs-toggle="tooltip" title="{{__('Distribution')}}" data-original-title="{{__('Distribution')}}">
        <i class="ti ti-arrows-maximize"></i>
    </a>
</div>
@endpermission
@permission('assets edit')
    <div class="action-btn me-2">
        <a  class="btn btn-sm bg-info align-items-center" data-size="lg" data-url="{{ route('asset.edit',$asset->id) }}" data-ajax-popup="true" data-title="{{__('Edit Asset')}}" data-size="lg" data-bs-toggle="tooltip" title="{{__('Edit')}}" data-original-title="{{__('Edit')}}">
            <i class="ti ti-pencil text-white"></i>
        </a>
    </div>
@endpermission
@permission('assets delete')
    <div class="action-btn" data-bs-whatever="{{ __('Delete Asset') }}" data-bs-toggle="tooltip" title="" data-bs-original-title="{{ __('Delete') }}">
        {!! Form::open(['method' => 'DELETE', 'route' => ['asset.destroy', $asset->id],'id'=>'delete-form-'.$asset->id]) !!}
        <a  class="btn btn-sm bg-danger align-items-center bs-pass-para show_confirm" data-confirm="{{__('Are You Sure?')}}" data-text="{{__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$asset->id}}').submit();">
            <i class="ti ti-trash text-white"></i>
        </a>
    {!! Form::close() !!}
    </div>
@endpermission
