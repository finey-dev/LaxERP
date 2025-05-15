<div class="modal-body">
    <div class="row">
        <div class="col-lg-12">

            <table class="table modal-table">
                <tbody>
                    <tr>
                        <th>{{__('Name')}}</th>
                        <td>{{ $opportunities->name }}</td>
                    </tr>
                    <tr>
                        <th>{{__('Account')}}</th>
                        <td>{{ !empty($opportunities->accounts)?$opportunities->accounts->name:'-'  }}</td>
                    </tr>
                    <tr>
                        <th>{{__('Stage')}}</th>
                        <td>{{ !empty($opportunities->stages)?$opportunities->stages->name:'-'}}</td>
                    </tr>
                    <tr>
                        <th>{{__('Amount')}}</th>
                        <td>{{currency_format_with_sym( $opportunities->amount)}}</td>
                    </tr>
                    <tr>
                        <th>{{__('Probability')}}</th>
                        <td>{{ $opportunities->probability }}</td>
                    </tr>
                    <tr>
                        <th>{{__('Close Date')}}</th>
                        <td>{{company_date_formate($opportunities->close_date)}}</td>
                    </tr>
                    <tr>
                        <th>{{__('Contacts')}}</th>
                        <td>{{ !empty($opportunities->contacts)?$opportunities->contacts->name:'-'}}</td>
                    </tr>
                    @if(module_is_active('Lead'))
                    <tr>
                        <th>{{__('Lead Source')}}</th>
                        <td>{{ !empty($opportunities->leadsource)?$opportunities->leadsource->name:'-'}}</td>
                    </tr>
                    @endif
                    <tr>
                        <th>{{__('Description')}}</th>
                        <td>{{ $opportunities->description }}</td>
                    </tr>
                    <tr>
                        <th>{{__('Assigned User')}}</th>
                        <td>{{ !empty($opportunities->assign_user)?$opportunities->assign_user->name:'-'}}</td>
                    </tr>
                    <tr>
                        <th>{{__('Created')}}</th>
                        <td>{{company_date_formate($opportunities->created_at )}}</td>
                    </tr>
                    @if (!empty($customFields) && count($opportunities->customField) > 0)
                        @foreach ($customFields as $field)
                            <tr>
                                <th>{{ $field->name }}</th>
                                <td>
                                    @if ($field->type == 'attachment')
                                        <a href="{{ get_file($opportunities->customField[$field->id]) }}" target="_blank">
                                            <img src="{{ get_file($opportunities->customField[$field->id]) }}"
                                                class="wid-75 rounded me-3">
                                        </a>
                                    @else
                                        {{ !empty($opportunities->customField[$field->id]) ? $opportunities->customField[$field->id] : '-' }}
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
