@permission('collection center show')
<div class="action-btn me-2">
    <a class="mx-3 btn btn-sm bg-warning align-items-center" href="{{ route('collection-center.show',$collection_center->id) }}" data-bs-toggle="tooltip" title="{{__('View')}}">
        <i class="ti ti-eye text-white"></i>
    </a>
</div>
@endpermission

@permission('collection center edit')
<div class="action-btn me-2">
    <a class="mx-3 btn btn-sm bg-info align-items-center" data-url="{{ route('collection-center.edit',$collection_center->id) }}" data-ajax-popup="true" data-size="md" data-bs-toggle="tooltip" title="{{__('Edit')}}" data-title="{{__('Edit Collection Center')}}">
        <i class="ti ti-pencil text-white"></i>
    </a>
</div>
@endpermission


@permission('collection center delete')
    <div class="action-btn">
        {!! Form::open([
            'method' => 'DELETE',
            'route' => ['collection-center.destroy', $collection_center->id],
            'id' => 'delete-form-' . $collection_center->id,
        ]) !!}
        <a class="mx-3 btn btn-sm bg-danger align-items-center bs-pass-para show_confirm" data-bs-toggle="tooltip"
            title="{{ __('Delete') }}" data-confirm="{{ __('Are You Sure?') }}" data-text="{{ __('This action can not be undone. Do you want to continue?') }}"><i
                class="ti ti-trash text-white text-white"></i></a>
        {!! Form::close() !!}
    </div>
@endpermission
