<div class="modal-body">
    <div class="row">
        <div class="col-lg-12">

            <table class="table modal-table">
                <tbody>
                    <tr>
                        <th>{{__('Name')}}</th>
                        <td>{{ $salesdocument->name }}</td>
                    </tr>
                    <tr>
                        <th>{{__('Status')}}</th>
                        <td>
                            @if($salesdocument->status == 0)
                            <span class="badge bg-success p-2 px-3">{{ __(Workdo\Sales\Entities\SalesDocument::$status[$salesdocument->status]) }}
                            @elseif($salesdocument->status == 1)
                                <span class="badge bg-info p-2 px-3">{{ __(Workdo\Sales\Entities\SalesDocument::$status[$salesdocument->status]) }}
                            @elseif($salesdocument->status == 2)
                                <span class="badge bg-warning p-2 px-3">{{ __(Workdo\Sales\Entities\SalesDocument::$status[$salesdocument->status]) }}
                            @elseif($salesdocument->status == 3)
                                <span class="badge bg-danger p-2 px-3">{{ __(Workdo\Sales\Entities\SalesDocument::$status[$salesdocument->status]) }}
                            @endif
                                </td>
                    </tr>
                    <tr>
                        <th>{{__('Type')}}</th>
                        <td>{{ $salesdocument->types->name}}</td>
                    </tr>
                    <tr>
                        <th>{{__('Account')}}</th>
                        <td>{{ !empty($salesdocument->accounts)?$salesdocument->accounts->name:'-'}}</td>
                    </tr>
                    <tr>
                        <th >{{__('Opportunities')}}</th>
                        <td>{{ !empty($salesdocument->opportunitys)?$salesdocument->opportunitys->name:'-'}}</td>
                    </tr>
                    <tr>
                        <th>{{__('Publish Date')}}</th>
                        <td>{{company_date_formate($salesdocument->publish_date)}}</td>
                    </tr>
                    <tr>
                        <th>{{__('Expiration Date')}}</th>
                        <td>{{company_date_formate($salesdocument->expiration_date)}}</td>
                    </tr>
                    <tr>
                        <th>{{__('Description')}}</th>
                        <td>{{ $salesdocument->description }}</td>
                    </tr>
                    <tr>
                        <th>{{__('File')}}</th>
                        <td>
                            @if (!empty($salesdocument->attachment) && check_file($salesdocument->attachment))
                                <a href="{{ get_file($salesdocument->attachment) }}" download=""><i class="ti ti-download"></i></a>
                            @else
                                {{__('No File')}}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>{{__('Assigned User')}}</th>
                        <td>{{ !empty($salesdocument->assign_user)?$salesdocument->assign_user->name:'-'}}</td>
                    </tr>
                    <tr>
                        <th>{{__('Created')}}</th>
                        <td>{{company_date_formate($salesdocument->created_at)}}</td>
                    </tr>
                </tbody>
            </table>

        </div>
    </div>
    <div class="w-100 text-end pr-2">
        @permission('salesdocument edit')
        <div class="action-btn">
            <a href="{{ route('salesdocument.edit',$salesdocument->id) }}" class="mx-3 btn btn-sm align-items-center text-white bg-info "data-bs-toggle="tooltip" data-title="{{__('Document Edit')}}"  title="{{__('Edit')}}"><i class="ti ti-pencil"></i>
            </a>
        </div>

        @endpermission
    </div>
</div>


