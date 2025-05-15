<div class="modal-body">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-responsive">
            <table class="table modal-table">
                <tbody>
                    <tr>
                        <th>{{__('Request ID')}}</th>
                        <td>{{ !empty($repair_request->id) ? \Workdo\MachineRepairManagement\Entities\MachineRepairRequest::machineRepairNumberFormat($repair_request->id) : '' }}</td>
                    </tr>
                    <tr>
                        <th>{{__('Machine Name')}}</th>
                        <td>{{ isset($repair_request->machine->name) ? $repair_request->machine->name : '-' }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('Customer Name') }}</th>
                        <td>
                            {{ !empty($repair_request->customer_name) ? $repair_request->customer_name : '-' }}
                        </td>
                    </tr>
                    <tr>
                        <th>{{ __('Customer Email') }}</th>
                        <td>
                            {{ !empty($repair_request->customer_email) ? $repair_request->customer_email : '-' }}
                        </td>
                    </tr>
                    <tr>
                        <th>{{ __('Staff Name') }}</th>
                        <td>
                            {{ isset($repair_request->staff->name) ? $repair_request->staff->name : '-' }}
                        </td>
                    </tr>
                    <tr>
                        <th>{{ __('Staff Email') }}</th>
                        <td>
                            {{ isset($repair_request->staff->email) ? $repair_request->staff->email : '-' }}
                        </td>
                    </tr>
                    <tr>
                        <th>{{ __('Date of Request') }}</th>
                        <td>{{ !empty($repair_request->date_of_request) ? $repair_request->date_of_request  : '' }}</td>
                    </tr>
                    <tr>
                        <th >{{ __('Priority Level') }}</th>
                        <td>
                            @if($repair_request->priority_level == 'Low')
                                <span class="badge bg-primary p-2 px-3">{{$repair_request->priority_level}}</span>
                            @elseif($repair_request->priority_level == 'Medium')
                                <span class="badge bg-warning p-2 px-3">{{$repair_request->priority_level}}</span>
                            @else
                                <span class="badge bg-danger p-2 px-3">{{$repair_request->priority_level}}</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>{{ __('Status') }}</th>
                        <td>
                            @if($repair_request->status == 'Completed')
                                <span class="badge bg-primary p-2 px-3">{{$repair_request->status}}</span>
                            @elseif($repair_request->status == 'In Progress')
                                <span class="badge bg-warning p-2 px-3">{{$repair_request->status}}</span>
                            @else
                                <span class="badge bg-danger p-2 px-3">{{$repair_request->status}}</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>{{ __('Description of Issue') }}</th>
                        <td>{{ !empty($repair_request->description_of_issue) ? $repair_request->description_of_issue : '' }}</td>
                    </tr>
                </tbody>
            </table>
            </div>
        </div>
    </div>
</div>
