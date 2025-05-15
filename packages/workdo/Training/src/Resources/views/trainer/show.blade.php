<div class="modal-body">
    <div class="col-lg-12">
        <table class="table modal-table">
            <tbody>
                <tr>
                    <td class="text-dark fw-bold">{{ __('Company') }}</td>
                    <td style="display: table-cell;"> {{ !empty($trainer->branches) ? $trainer->branches->name : '' }}
                    </td>
                </tr>
                <tr>
                    <td class="text-dark fw-bold">{{ __('First Name') }}</td>
                    <td style="display: table-cell;">{{ $trainer->firstname }}</td>
                </tr>
                <tr>
                    <td class="text-dark fw-bold">{{ __('Last Name') }}</td>
                    <td style="display: table-cell;">{{ $trainer->lastname }}</td>
                </tr>
                <tr>
                    <td class="text-dark fw-bold">{{ __('Contact Number') }}</td>
                    <td style="display: table-cell;">{{ $trainer->contact }}</td>
                </tr>
                <tr>
                    <td class="text-dark fw-bold">{{ __('Email') }}</td>
                    <td style="display: table-cell;">{{ $trainer->email }}</td>
                </tr>
                <tr>
                    <td class="text-dark fw-bold">{{ __('Expertise') }}</td>
                    <td style="display: table-cell;">{{ $trainer->expertise }}</td>
                </tr>
                <tr>
                    <td class="text-dark fw-bold">{{ __('Address') }}</td>
                    <td style="display: table-cell;">{{ $trainer->address }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

