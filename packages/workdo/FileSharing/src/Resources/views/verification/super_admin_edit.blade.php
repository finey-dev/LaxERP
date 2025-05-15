{{ Form::model($verification, ['route' => ['file-verification.update', $verification->id], 'method' => 'PUT']) }}
<div class="modal-body">
    <div class="table-responsive">
        <table class="table">
            <tr>
                <th>{{ __('Appplied On') }}</th>
                <td>{{ isset($verification->applied_date) ? company_datetime_formate($verification->applied_date) : '-' }}
                </td>
            </tr>
            <tr>
                <th>{{ __('Action On') }}</th>
                <td>{{ isset($verification->action_date) ? company_datetime_formate($verification->action_date) : '-' }}
                </td>
            </tr>
            <tr>
                <th>{{ __('Status') }}</th>
                <td>
                    @if ($verification->status == 0)
                        <span
                            class="badge bg-warning p-2 px-3 text-white">{{ __(Workdo\FileSharing\Entities\FileSharingVerification::$statues[$verification->status]) }}</span>
                    @elseif($verification->status == 1)
                        <span
                            class="badge bg-success p-2 px-3 text-white">{{ __(Workdo\FileSharing\Entities\FileSharingVerification::$statues[$verification->status]) }}</span>
                    @elseif($verification->status == 2)
                        <span
                            class="badge bg-danger p-2 px-3 text-white">{{ __(Workdo\FileSharing\Entities\FileSharingVerification::$statues[$verification->status]) }}</span>
                    @endif
                </td>
            </tr>
            <tr>
                <th>{{ __('Attachment') }}</th>
                <td>
                    @if (!empty($verification->attachment) && check_file($verification->attachment))
                        <div class="action-btn me-2">
                            <a class="mx-3 btn btn-sm align-items-center bg-primary"
                                href="{{ get_file($verification->attachment) }}" download>
                                <i class="ti ti-download text-white"></i>
                            </a>
                        </div>
                    @else
                        {{ __('Not Found') }}
                    @endif
                </td>
            </tr>
        </table>
    </div>
</div>
@if ($verification->status == 0)
    <div class="modal-footer">
        <button type="submit" class="btn btn-danger" name="status" value=2>{{ 'Reject' }}</button>
        <button type="submit" class="btn btn-success" name="status" value=1>{{ 'Approved' }}</button>
    </div>
@else
    <div class="modal-footer">
        <input type="button" value="{{ __('Cancel') }}" class="btn  btn-light" data-bs-dismiss="modal">
    </div>
@endif
{{ Form::close() }}
