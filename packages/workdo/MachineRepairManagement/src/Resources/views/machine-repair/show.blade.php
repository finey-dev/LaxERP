<div class="modal-body">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-responsive">
                <table class="table modal-table">
                    <tbody>
                        <tr>
                            <th>{{ __('Name') }}</th>
                            <td>{{ !empty($machine->name) ? $machine->name : '' }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Manufacturer') }}</th>
                            <td>
                                {{ !empty($machine->manufacturer) ? $machine->manufacturer : '' }}
                            </td>
                        </tr>
                        <tr>
                            <th>{{ __('Model') }}</th>
                            <td>{{ !empty($machine->model) ? $machine->model : '' }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Installation Date') }}</th>
                            <td>{{ !empty($machine->installation_date) ? company_date_formate($machine->installation_date) : '' }}
                            </td>
                        </tr>
                        <tr>
                            <th>{{ __('Last Maintenance Date') }}</th>
                            <td>{{ !empty($machine->last_maintenance_date) ? company_date_formate($machine->last_maintenance_date) : '' }}
                            </td>
                        </tr>
                        <tr>
                            <th>{{ __('Status') }}</th>
                            <td>
                                @if (isset($machine->status) && $machine->status == 'Active')
                                    <dd class="p-2 px-3 badge bg-primary ms-0">
                                        {{ !empty($machine->status) ? $machine->status : '' }}</dd>
                                @else
                                    <dd class="p-2 px-3 badge bg-danger ms-0">
                                        {{ !empty($machine->status) ? $machine->status : '' }}</dd>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>{{ __('Description') }}</th>
                            <td>{{ !empty($machine->description) ? $machine->description : '' }}</td>
                        </tr>
                        @if (!empty($customFields) && count($machine->customField) > 0)
                            @foreach ($customFields as $field)
                                <tr>
                                    <th>{{ $field->name }}</th>
                                    <td>
                                        @if ($field->type == 'attachment')
                                            <a href="{{ get_file($machine->customField[$field->id]) }}"
                                                target="_blank">
                                                <img src="{{ get_file($machine->customField[$field->id]) }}"
                                                    class="wid-75 rounded me-3">
                                            </a>
                                        @else
                                            {{ !empty($machine->customField[$field->id]) ? $machine->customField[$field->id] : '-' }}
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
</div>
