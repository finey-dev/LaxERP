@if($assetDefective->status != 'Fail')
    <span>
        <div class="action-btn ">
            <a href="#" class="btn btn-sm bg-warning d-inline-flex align-items-center" title="{{__('Action')}}" data-bs-toggle="tooltip" data-bs-placement="top" data-url="{{ route('assets.withdraw.status',$assetDefective->id) }}" data-ajax-popup="true" data-title="{{__('Update Status')}}" data-size="md"><span class="text-white"><i class="ti ti-caret-right text-white"></i></span></a>
        </div>
    </span>
@endif
