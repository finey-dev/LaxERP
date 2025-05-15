@if ($repair_request_order->status == 0 || $repair_request_order->status == 5)
<div class="action-btn me-2">
    <a href="{{route('repair.request.status.steps.change',[$repair_request_order->id,1])}}" data-toggle="tooltip" title="{{ __('Start Repair') }}" class="text-light btn btn-sm bg-success align-items-center">
        <i class="ti ti-sort-ascending"></i>
    </a>
</div>
@elseif($repair_request_order->status == 1 || $repair_request_order->status == 5)
<div class="action-btn me-2">
    <a href="{{route('repair.request.status.steps.change',[$repair_request_order->id,2])}}" data-toggle="tooltip" title="{{ __('End Repair') }}" class="text-light btn btn-sm bg-success  align-items-center">
        <i class="ti ti-sort-descending"></i>
    </a>
</div>
@elseif($repair_request_order->status == 2 || $repair_request_order->status == 5)
<div class="action-btn me-2">
    <a href="{{route('repair.request.status.steps.change',[$repair_request_order->id,3])}}" data-toggle="tooltip" title="{{ __('Start Testing') }}" class="text-light btn btn-sm bg-primary align-items-center">
        <i class="ti ti-sort-ascending"></i>
    </a>
</div>
@elseif($repair_request_order->status == 3 || $repair_request_order->status == 5)
<div class="action-btn me-2">
    <a href="{{route('repair.request.status.steps.change',[$repair_request_order->id,4])}}" data-toggle="tooltip" title="{{ __('End Testing') }}" class="text-light btn btn-sm bg-primary align-items-center">
        <i class="ti ti-sort-descending"></i>
    </a>
</div>
@endif
@php
$repair_data = \Workdo\RepairManagementSystem\Entities\RepairPart::where('repair_id', $repair_request_order->id)->get();
@endphp

@if(count($repair_data) > 0 && ($repair_request_order->status == 1 || $repair_request_order->status == 5))
<div class="action-btn me-2">
    @permission('repair part edit')
    <a class="btn btn-sm bg-primary align-items-center" href="{{ route('repair.parts.edit', [\Crypt::encrypt($repair_request_order->id)]) }}" data-toggle="tooltip" title="{{ __('Product Parts Edit') }}">
        <i class="ti ti-tool" style="color: white;"></i>
    </a>
    @endpermission
</div>
@elseif($repair_request_order->status == 1 || $repair_request_order->status == 5)
<div class="action-btn me-2">
    @permission('repair part create')
    <a class="btn btn-sm bg-primary align-items-center" href="{{ route('repair.parts.create', [\Crypt::encrypt($repair_request_order->id)]) }}" data-toggle="tooltip" title="{{ __('Product Parts Add') }}">
        <i class="ti ti-tool" style="color: white;"></i>
    </a>
    @endpermission
</div>
@endif
@if(($repair_request_order->status == 1) || ($repair_request_order->status == 2) || ($repair_request_order->status == 3) || ((!$repair_request_order->status == 0) && (!$repair_request_order->status == 4)))
<div class="action-btn me-2">
    <a href="{{route('repair.request.status.steps.change',[$repair_request_order->id,5])}}" data-toggle="tooltip" title="{{ __('Irrepairable') }}" class="text-light btn btn-sm bg-secondary align-items-center">
        <i class="ti ti-brand-sentry"></i>
    </a>

</div>
@endif
@if(($repair_request_order->status == 1) || ($repair_request_order->status == 2) || ($repair_request_order->status == 3) || !$repair_request_order->status == 4)
<div class="action-btn me-2">
    <a href="{{route('repair.request.status.steps.change',[$repair_request_order->id,6])}}" data-toggle="tooltip" title="{{ __('Repair Order Cancel') }}" class="text-light btn btn-sm bg-danger align-items-center">
        <i class="ti ti-x"></i>
    </a>
</div>
@endif
@permission('repair movement history show')
@if(!$repair_request_order->status == 0)
<div class="action-btn me-2">
    <a href="{{route('repair.movement.hostory.index',[$repair_request_order->id])}}" data-toggle="tooltip" title="{{ __('Movement History List') }}" class="text-light btn btn-sm bg-info align-items-center">
        <i class="ti ti-list"></i>
    </a>
</div>
@endif
@endpermission
@permission('repair invoice payment create')
@if($repair_request_order->status == 4)
<div class="action-btn me-2">
    <a class="btn btn-sm bg-primary align-items-center" data-url="{{route('repair.request.invoice',[$repair_request_order->id])}}" data-ajax-popup="true" data-size="md" data-toggle="tooltip" data-bs-original-title="{{__('Create Invoice')}}" data-title="{{__('Create Invoice')}}">
        <i class="ti ti-file-invoice"></i>
    </a>
</div>
@endif
@endpermission
@permission('repair order request edit')
@if(!$repair_request_order->status == 4)
<div class="action-btn me-2">
    <a data-url="{{ route('repair.request.edit',$repair_request_order->id) }}" data-size="md" data-ajax-popup="true" data-bs-original-title="{{__('Edit')}}" class="btn btn-sm bg-info align-items-center" data-toggle="tooltip" data-title="{{__('Edit Repair Order Request')}}">
        <i class="ti ti-pencil text-white"></i>
    </a>
</div>
@endif
@endpermission
@permission('repair order request delete')
<div class="action-btn">
    {!! Form::open(['method' => 'DELETE', 'route' => ['repair.request.destroy', $repair_request_order->id],'id'=>'delete-form-'.$repair_request_order->id]) !!}

    <a type="submit" class="btn btn-sm bg-danger align-items-center show_confirm" data-toggle="tooltip" title="{{ __('Delete') }}" data-bs-original-title="{{ _('Delete') }}" data-confirm="{{ __('Are You Sure?') }}" data-text="{{ __('This action can not be undone. and this recored all data delete. Do you want to continue?') }}">
        <i class="ti ti-trash text-white"></i>
    </a>
    {!! Form::close() !!}
</div>
@endpermission
