<div class="modal-body">
    <div class="row">
        <div class="col-lg-12">
            <table class="table modal-table">
                <tbody>
                    <tr>
                        <th>{{ __('Branch Name') }}</th>
                        <td>{{ !empty($brachData->branch_name) ? $brachData->branch_name : '-' }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('Branch Location') }}</th>
                        <td><div class="text-wrap text-break">{{ !empty($brachData->branch_location) ? $brachData->branch_location : '-' }}</div></td>
                    </tr>
                    <tr>
                        <th>{{ __('City') }}</th>
                        <td>{{ !empty($brachData->city) ? $brachData->city : '-' }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('State') }}</th>
                        <td>{{ !empty($brachData->state) ? $brachData->state : '-' }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('Country') }}</th>
                        <td>{{ !empty($brachData->country) ? $brachData->country : '-' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
