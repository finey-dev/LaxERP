<div class="modal-body">
    <div class="row">
        <div class="col-lg-12 mt-2">
            <table class="table modal-table">
                <tbody>
                    <tr>
                        <th>{{__('Maintenance Date')}}</th>
                        <td>{{$data['maintenance_date']}}</td>
                    </tr>
                    <tr>
                        <th>{{__('Maintenance Type')}}</th>
                        <td>{{$data['maintenance_type']}}</td>
                    </tr>
                    <tr>
                        <th>{{__('Status')}}</th>
                        <td>{{$data['status']}}</td>
                    </tr>
                    <tr>
                        <th>{{__('Comments')}}</th>
                        <td style="white-space:normal;">{{$data['comments']}}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
