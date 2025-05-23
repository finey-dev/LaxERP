<div class="modal-body">
    <div class="row">
        <div class="col-lg-12">
            <table class="table modal-table">
                <tbody>
                    <tr>
                        <th>{{__('Name')}}</th>
                        <td>{{ $meeting->name }}</td>
                    </tr>
                    <tr>
                        <th>{{__('Status')}}</th>
                        <td>
                            @if($meeting->status == 0)
                                <span class="badge bg-success p-2 px-3">{{ __(Workdo\Sales\Entities\Meeting::$status[$meeting->status]) }}
                            @elseif($meeting->status == 1)
                                <span class="badge bg-info p-2 px-3">{{ __(Workdo\Sales\Entities\Meeting::$status[$meeting->status]) }}
                            @elseif($meeting->status == 2)
                                <span class="badge bg-warning p-2 px-3">{{ __(Workdo\Sales\Entities\Meeting::$status[$meeting->status]) }}
                            @elseif($meeting->status == 3)
                                <span class="badge bg-danger p-2 px-3">{{ __(Workdo\Sales\Entities\Meeting::$status[$meeting->status]) }}
                            @elseif($meeting->status == 4)
                                <span class="badge bg-danger p-2 px-3">{{ __(Workdo\Sales\Entities\Meeting::$status[$meeting->status]) }}
                            @elseif($meeting->status == 5)
                                <span class="badge bg-warning p-2 px-3">{{ __(Workdo\Sales\Entities\Meeting::$status[$meeting->status]) }}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>{{__('Start Date')}}</th>
                        <td>{{company_date_formate($meeting->start_date)}}</td>
                    </tr>
                    <tr>
                        <th>{{__('End Date')}}</th>
                        <td>{{company_date_formate($meeting->end_date)}}</td>
                    </tr>
                    <tr>
                        <th>{{__('Parent')}}</th>
                        <td>{{ $meeting->parent }}</td>
                    </tr>
                    <tr>
                        <th>{{__('Parent User')}}</th>
                        <td>{{ $meeting->getparent($meeting->parent,$meeting->parent_id)  }}</td>
                    </tr>
                    <tr>
                        <th>{{__('Description')}}</th>
                        <td>{{ $meeting->description }}</td>
                    </tr>
                    <tr>
                        <th>{{__('Attendees User')}}</th>
                        <td>{{ !empty($meeting->attendees_users->name)?$meeting->attendees_users->name:'--' }}</td>
                    </tr>
                    <tr>
                        <th>{{__('Attendees Contact')}}</th>
                        <td>{{ !empty($meeting->attendees_contacts->name)?$meeting->attendees_contacts->name:'--' }}</td>
                    </tr>
                    <tr>
                        <th>{{__('Attendees Lead')}}</th>
                        <td>{{ !empty($meeting->attendees_leads()->name)?$meeting->attendees_leads()->name:'--' }}</td>
                    </tr>
                    <tr>
                        <th>{{__('Assigned User')}}</th>
                        <td>{{ !empty($meeting->assign_user)?$meeting->assign_user->name:''}}</td>
                    </tr>
                    <tr>
                        <th>{{__('Created')}}</th>
                        <td>{{company_date_formate($meeting->created_at)}}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="w-100 text-end pr-2">
        @permission('meeting edit')
        <div class="action-btn">
            <a href="{{ route('meeting.edit',$meeting->id) }}" class="mx-3 btn btn-sm align-items-center text-white bg-info" data-bs-toggle="tooltip"data-title="{{__('Edit Call')}}" title="{{__('Edit')}}"><i class="ti ti-pencil"></i>
            </a>
        </div>

        @endpermission
    </div>
</div>

