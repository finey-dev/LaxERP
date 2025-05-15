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
                        <th>{{__('Check Date')}}</th>
                        <td>{{$data['check_date']}}</td>
                    </tr>
                    <tr>
                        <th>{{__('Check Type')}}</th>
                        <td>{{$data['check_type']}}</td>
                    </tr>
                    <tr>
                        <th>{{__('Result')}}</th>
                        <td>{{$data['result']}}</td>
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
