<div class="modal-body">
    <div class="row">
        <div class="col-lg-12 mt-2">
            <table class="table modal-table">
                <tbody>
                    <tr>
                        <th>{{__('Customer Name')}}</th>
                        <td>{{ $courier_contract->customer_name}}</td>
                    </tr>
                    <tr>
                        <th>{{__('Service')}}</th>
                        <td>{{ $courier_contract->servicetype ? $courier_contract->servicetype->service_type : '-' }}</td>
                    </tr>
                    <tr>
                        <th>{{__('Start Date')}}</th>
                        <td>{{ $courier_contract->start_date}}</td>
                    </tr>
                    <tr>
                        <th>{{__('End Date')}}</th>
                        <td>{{ $courier_contract->end_date}}</td>
                    </tr>
                    <tr>
                        <th>{{__('Contract Details')}}</th>
                        <td style="white-space:normal;">{{ strip_tags($courier_contract->contract_details)}}</td>
                    </tr>
                    <tr>
                        <th>{{__('Status')}}</th>
                        <td>{{ $courier_contract->status == '1' ? 'Active' : 'Expired'}}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
