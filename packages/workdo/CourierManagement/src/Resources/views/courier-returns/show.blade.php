<div class="modal-body">
    <div class="row">
        <div class="col-lg-12 mt-2">
            <table class="table modal-table">
                <tbody>
                    <tr>
                        <th>{{__('Package')}}</th>
                        <td>{{ $courier_return->package ? $courier_return->package->package_title : '-' }}</td>
                    </tr>
                    <tr>
                        <th>{{__('Branch')}}</th>
                        <td>{{ $courier_return->customer ? $courier_return->customer->receiver_name : '-' }}</td>
                    </tr>
                    <tr>
                        <th>{{__('Return Date')}}</th>
                        <td>{{ $courier_return->return_date}}</td>
                    </tr>
                    <tr>
                        <th>{{__('Reason')}}</th>
                        <td style="white-space:normal;">{{ $courier_return->return_reason}}</td>
                    </tr>
                    <tr>
                        <th>{{__('Status')}}</th>
                        <td>{{ $courier_return->status == 1 ? 'Processed' : 'Pending'}}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
