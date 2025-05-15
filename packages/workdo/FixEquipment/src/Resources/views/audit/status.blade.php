{{ Form::model($audit, ['route' => ['fix.equipment.audit.status.update', $audit->id], 'method' => 'POST']) }}
<div class="modal-body">
    <div class="table-responsive">
        <table class="table modal-table">
            <tr>
            <tr>
                <th>{{ __('Audit Title') }}</th>
                <td>{{ $audit->audit_title }}</td>
            </tr>
            <tr>
                <th>{{ __('Audit Date') }}</th>
                <td>{{ company_date_formate($audit->audit_date) }}</td>
            </tr>
            <th>{{ __('status') }}</th>
            <td>
                @if ($audit->audit_status == 'Approved')
                    <div class="col-auto"><span class="bg-success badge p-2 px-3 text-white">{{ __('Approved') }}</span>
                    </div>
                @elseif($audit->audit_status == 'Pending')
                    <div class="col-auto"><span class="bg-warning badge p-2 px-3 text-white">{{ __('Pending') }}</span>
                    </div>
                @else
                    <div class="col-auto"><span class="bg-danger badge p-2 px-3 text-white">{{ __('Rejected') }}</span>
                    </div>
                @endif
            </td>
            </tr>

        </table>
    </div>
</div>
@if ($audit->audit_status == 'Pending')
    <div class="modal-footer">
        <a href=""></a>
        <input type="submit" value="{{ 'Reject' }}" class="btn btn-danger" name="status">
        <input type="submit" value="{{ 'Approved' }}" class="btn btn-success" name="status">
    </div>
@endif
{{ Form::close() }}
