<div class="modal-body">
    <div class="row">
        <div class="col-lg-12 mt-2">
            <table class="table modal-table">
                <tbody>
                    <tr>
                        <th>{{__('Name')}}</th>
                        <td>{{ $courier_agents->name}}</td>
                    </tr>
                    <tr>
                        <th>{{__('Phone')}}</th>
                        <td>{{ $courier_agents->phone}}</td>
                    </tr>
                    <tr>
                        <th>{{__('E-mail')}}</th>
                        <td>{{ $courier_agents->email}}</td>
                    </tr>
                    <tr>
                        <th>{{__('Address')}}</th>
                        <td>{{ $courier_agents->address}}</td>
                    </tr>
                    <tr>
                        <th>{{__('Branch')}}</th>
                        <td>{{ $courier_agents->branch ? $courier_agents->branch->branch_name : '-' }}</td>
                    </tr>
                    <tr>
                        <th>{{__('Status')}}</th>
                        <td>{{ $courier_agents->status == 1 ? 'Active' : 'Inactive' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
