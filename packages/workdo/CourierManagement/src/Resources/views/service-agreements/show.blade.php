<div class="modal-body">
    <div class="row">
        <div class="col-lg-12 mt-2">
            <table class="table modal-table">
                <tbody>
                    <tr>
                        <th>{{__('Customer Name')}}</th>
                        <td>{{ $service_agreements->customer_name}}</td>
                    </tr>
                    <tr>
                        <th>{{__('Start Date')}}</th>
                        <td>{{ $service_agreements->start_date}}</td>
                    </tr>
                    <tr>
                        <th>{{__('End Date')}}</th>
                        <td>{{ $service_agreements->end_date}}</td>
                    </tr>
                    <tr>
                        <th>{{__('Agreement Details')}}</th>
                        <td style="white-space:normal;">{{ strip_tags($service_agreements->agreement_details) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
