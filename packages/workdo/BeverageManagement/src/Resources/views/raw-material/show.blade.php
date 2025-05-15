<div class="modal-body">
    <div class="row">
        <div class="col-lg-12 mt-2">
            <table class="table modal-table">
                <tbody>
                    <tr>
                        <th>{{__('Collection Center')}}</th>
                        <td>{{$raw_material->collectionCenter->location_name}}</td>
                    </tr>
                    <tr>
                        <th>{{__('Name')}}</th>
                        <td>{{$data['product']->name}}</td>
                    </tr>
                    <tr>
                        <th>{{__('Price')}}</th>
                        <td>{{ $data['product']->sale_price ? (!empty(company_setting('defult_currancy_symbol')) ? company_setting('defult_currancy_symbol') : '$').$data['product']->sale_price : ''}}</td>
                    </tr>
                    <tr>
                        <th>{{__('Tax')}}</th>
                        <td>
                            @foreach($data['taxes'] as $tax)
                            <span class="badge bg-primary p-2 px-3 span_tax mt-1 me-1">{{ $tax->name }} ({{ $tax->rate }}%)</span>
                            @endforeach
                        </td>
                    </tr>
                    <tr>
                        <th>{{__('Unit')}}</th>
                        <td>{{$data['unit']}}</td>
                    </tr>
                    <tr>
                        <th>{{__('Description')}}</th>
                        <td style="white-space:normal;">{{$data['product']->description}}</td>
                    </tr>
                    <tr>
                        <th>{{__('Image')}}</th>
                        <td><img id="image_show" src="{{$data['product']->image ?? asset('packages/workdo/ProductService/src/Resources/assets/image/img01.jpg')}}" width="15%" class="mb-2"></td>
                    </tr>
                    <tr>
                        <th>{{__('Total Price')}}</th>
                        <td>{{ $raw_material->price ? (!empty(company_setting('defult_currancy_symbol')) ? company_setting('defult_currancy_symbol') : '$').$raw_material->price : ''}}</td>
                    </tr>
                    <tr>
                        <th>{{__('Quantity')}}</th>
                        <td>{{ $raw_material->quantity ?? '-'}}</td>
                    </tr>
                    <tr>
                        <th>{{__('Status')}}</th>
                        <td>
                            @if ($raw_material->status == 1)
                            <span class="badge fix_badge bg-primary p-2 px-3">Active</span>
                            @elseif($raw_material->status == 0)
                            <span class="badge fix_badge bg-danger p-2 px-3">Inactive</span>
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
