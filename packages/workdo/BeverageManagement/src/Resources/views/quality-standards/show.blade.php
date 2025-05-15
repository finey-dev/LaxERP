<div class="modal-body">
    <div class="row">
        <div class="col-lg-12 mt-2">
            <table class="table modal-table">
                <tbody>
                    <tr>
                        <th>{{__('Product Name')}}</th>
                        <td>{{$data['product']->name}}</td>
                    </tr>
                    <tr>
                        <th>{{__('Standard Type')}}</th>
                        <td>{{$data['standardtype']}}</td>
                    </tr>
                    <tr>
                        <th>{{__('Value')}}</th>
                        <td style="white-space:normal;">{{$data['value']}}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
