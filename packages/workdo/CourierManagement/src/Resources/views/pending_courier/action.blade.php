@if ($courier->is_approve === null)
        @permission('courier pending request approve')
        <div class="action-btn me-2">
            <a href="{{ route('approve.courier.request', ['trackingId' => $courier->tracking_id]) }}"
                class="btn-success btn btn-sm align-items-center "
                data-toggle="tooltip" title="" data-bs-original-title="Accept">
                <i class="ti ti-check"></i>
            </a>
        </div>
        @endpermission

        @permission('courier pending request reject')
        <div class="action-btn me-2">
            <a href="{{ route('reject.courier.request', ['trackingId' => $courier->tracking_id]) }}"
                class="btn-warning btn btn-sm align-items-center "
                data-toggle="tooltip" title="" data-bs-original-title="Reject">
                <i class="ti ti-x"></i>
            </a>
        </div>
        @endpermission
    @endif

    @permission('courier delete')
        @if (Auth::user()->id == $courier->created_by)
            <div class="action-btn">
                {{ Form::open(['route' => ['delete.courier.pending.request', 'trackingId' => encrypt($courier->tracking_id)], 'id' => "user-form-{$courier->id}", 'class' => 'm-0']) }}
                @method('DELETE')
                <a href="#" class="bg-danger btn btn-sm align-items-center bs-pass-para show_confirm"
                   data-bs-toggle="tooltip" title="" data-bs-original-title="Delete"
                   aria-label="Delete" data-confirm="{{ __('Are You Sure?') }}"
                   data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                   data-confirm-yes="user-form-{{ $courier->id }}">
                    <i class="ti ti-trash text-white"></i>
                </a>
                {{ Form::close() }}
            </div>
        @endif
    @endpermission
