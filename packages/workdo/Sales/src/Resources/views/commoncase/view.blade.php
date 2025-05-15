<div class="modal-body">
    <div class="row">
        <div class="col-lg-12">

            <table class="table modal-table">
                <tr>
                    <th>{{__('Name')}}</th>
                    <td>{{ $commonCase->name }}</td>
                </tr>
                <tr>
                    <th>{{__('Number')}}</th>
                    <td>{{ $commonCase->number}}</td>
                </tr>
                <tr>
                    <th>{{__('Status')}}</th>
                    <td>
                        @if($commonCase->status == 0)
                            <span class="badge bg-success p-2 px-3">{{ __(Workdo\Sales\Entities\CommonCase::$status[$commonCase->status]) }}</span>
                        @elseif($commonCase->status == 1)
                            <span class="badge bg-info p-2 px-3">{{ __(Workdo\Sales\Entities\CommonCase::$status[$commonCase->status]) }}</span>
                        @elseif($commonCase->status == 2)
                            <span class="badge bg-warning p-2 px-3">{{ __(Workdo\Sales\Entities\CommonCase::$status[$commonCase->status]) }}</span>
                        @elseif($commonCase->status == 3)
                            <span class="badge bg-danger p-2 px-3">{{ __(Workdo\Sales\Entities\CommonCase::$status[$commonCase->status]) }}</span>
                        @elseif($commonCase->status == 4)
                            <span class="badge bg-danger p-2 px-3">{{ __(Workdo\Sales\Entities\CommonCase::$status[$commonCase->status]) }}</span>
                        @elseif($commonCase->status == 5)
                            <span class="badge bg-warning p-2 px-3">{{ __(Workdo\Sales\Entities\CommonCase::$status[$commonCase->status]) }}</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>{{__('Account')}}</th>
                    <td>{{ !empty($commonCase->accounts)?$commonCase->accounts->name:'-'  }}</td>
                </tr>
                <tr>
                    <th>{{__('Priority')}}</th>
                    <td>
                        @if($commonCase->priority == 0)
                            <span class="badge bg-primary p-2 px-3">{{ __(Workdo\Sales\Entities\CommonCase::$priority[$commonCase->priority]) }}</span>
                        @elseif($commonCase->priority == 1)
                            <span class="badge bg-info p-2 px-3">{{ __(Workdo\Sales\Entities\CommonCase::$priority[$commonCase->priority]) }}</span>
                        @elseif($commonCase->priority == 2)
                            <span class="badge bg-warning p-2 px-3">{{ __(Workdo\Sales\Entities\CommonCase::$priority[$commonCase->priority]) }}</span>
                        @elseif($commonCase->priority == 3)
                            <span class="badge bg-danger p-2 px-3">{{ __(Workdo\Sales\Entities\CommonCase::$priority[$commonCase->priority]) }}</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>{{__('Contacts')}}</th>
                    <td>{{ !empty($commonCase->contacts->name)?$commonCase->contacts->name:'-' }}</td>
                </tr>
                <tr>
                    <th>{{__('Type')}}</th>
                    <td>{{ !empty($commonCase->types)?$commonCase->types->name:'-' }}</td>
                </tr>
                <tr>
                    <th>{{__('Description')}}</th>
                    <td>{{ $commonCase->description }}</td>
                </tr>
                <tr>
                    <th>{{ __('Assigned User') }}</th>
                    <td>{{ !empty($commonCase->assign_user)?$commonCase->assign_user->name:'-'}}</td>
                </tr>
                <tr>
                    <th>{{__('Created')}}</th>
                    <td>{{company_date_formate($commonCase->created_at)}}</td>
                </tr>
                @if (!empty($customFields) && count($commonCase->customField) > 0)
                @foreach ($customFields as $field)
                    <tr>
                        <th>{{ $field->name }}</th>
                        <td>
                            @if ($field->type == 'attachment')
                                <a href="{{ get_file($commonCase->customField[$field->id]) }}" target="_blank">
                                    <img src="{{ get_file($commonCase->customField[$field->id]) }}"
                                        class="wid-75 rounded me-3">
                                </a>
                            @else
                                {{ !empty($commonCase->customField[$field->id]) ? $commonCase->customField[$field->id] : '-' }}
                            @endif
                        </td>
                    </tr>
                @endforeach
            @endif
            </table>
        </div>
    </div>
</div>


