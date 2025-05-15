@if(!empty($customer))
<div class="row">
    <div class="col-md-5 col-sm-6 col-12">
        <h6>{{ __('Request Number')}}</h6>
        <div class="request-to">
            <p>
                <span>
                    {{ \Workdo\MachineRepairManagement\Entities\MachineRepairRequest::machineRepairNumberFormat($repair_request->id) }}
                    {{-- {{ $repair_request->id }} --}}
                </span><br>
            </p>
        </div>
    </div>
    <div class="col-md-5 col-sm-6 col-12">
        <h6>{{ __('Customer Details')}}</h6>
        <div class="customer-to">
            <p>
                <span>{{ $customer['name'] }}</span><br>
                <span>{{ $customer['email'] }}</span><br>
            </p>
        </div>
    </div>
    <div class="col-md-8 col-sm-6 col-12 mt-2">
        <h6>{{ __('Machine Details') }}</h6>
        <dl class="row align-items-center">
            <dt class="col-sm-4" style="font-weight: 600;">{{ __('Name') }}</dt>
            <dd class="col-sm-8  ms-0" style="margin-bottom: 0px;"> : {{ !empty($machine_details->name) ? $machine_details->name : '' }}</dd>
            <dt class="col-sm-4" style="font-weight: 600;">{{ __('Model') }}</dt>
            <dd class="col-sm-8  ms-0" style="margin-bottom: 0px;"> : {{ !empty($machine_details->model) ? $machine_details->model : '' }}</dd>
            <dt class="col-sm-4" style="font-weight: 600;">{{ __('Manufacturer') }}</dt>
            <dd class="col-sm-8  ms-0" style="margin-bottom: 0px;"> : {{ !empty($machine_details->manufacturer) ? $machine_details->manufacturer : '' }}</dd>
        </dl>
    </div>
    <div class="col-md-2 mt-3">
        <a href="#" id="remove" class="text-sm btn btn-danger">{{__(' Remove')}}</a>
    </div>
</div>
@endif
