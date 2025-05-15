@permission('courier manage')
<div class="action-btn me-2">
    <a href="#" class="mx-3 btn bg-success btn-sm align-item-center cp_link"
        id="cp_link" data-link="{{ route('find.courier', $workspace->slug) }}"
        data-bs-toggle="tooltip" title="{{ __('Copy') }}"
        data-original-title="{{ __('Click to copy Courier link') }}"
        onclick="copy_link(this)">
        <span class="btn-inner--icon text-white"><i class="ti ti-link"></i></span>
    </a>
</div>
@endpermission

@permission('courier payment')
@if ($courier->is_payment_done == 0)
    <div class="action-btn me-2">
        <a class="mx-3 btn bg-secondary btn-sm  align-items-center"
            data-ajax-popup="true" data-size="lg"
            data-title="{{ __('Create Payment') }}"
            data-url="{{ route('courier.paymnent', ['trackingId' => encrypt($courier->tracking_id)]) }}"
            data-toggle="tooltip" title="{{ __('Add Payment') }}">
            <i class="ti ti-currency-dollar text-white"></i>
        </a>
    </div>
@endif
@endpermission

@permission('courier manage')
<div class="action-btn  me-2">
    <a href="{{ route('courier.show', ['trackingId' => encrypt($courier->tracking_id)]) }}"
        class="mx-3 btn bg-warning btn-sm d-inline-flex align-items-center"
        data-bs-toggle="tooltip" title="{{ __('View') }}"> <span
            class="text-white"> <i class="ti ti-eye text-white"></i></span></a>
</div>
@endpermission

@permission('courier edit')
<div class="action-btn  me-2">
    <a href="{{ route('courier.edit', ['trackingId' => encrypt($courier->tracking_id)]) }}"
        class="mx-3 btn bg-info btn-sm d-inline-flex align-items-center"
        data-bs-toggle="tooltip" title="{{ __('Edit') }}"> <span
            class="text-white"> <i class="ti ti-pencil text-white"></i></span></a>
</div>
@endpermission

@permission('courier delete')
@if (Auth::user()->id == $courier->created_by)
    <div class="action-btn">
        <form method="POST"
            action="{{ route('courier.delete', ['trackingId' => encrypt($courier->tracking_id)]) }}"
            id="user-form-{{ $courier->id }}">
            @csrf
            @method('DELETE')
            <input name="_method" type="hidden" value="DELETE">
            <button type="button"
                class="mx-3 btn bg-danger btn-sm d-inline-flex align-items-center show_confirm"
                data-bs-toggle="tooltip" title='Delete' data-confirm="{{ __('Are You Sure?') }}"
                data-text="{{ __('This action can not be undone. Do you want to continue?') }}">
                <span class="text-white"> <i class="ti ti-trash"></i></span>
            </button>
        </form>
    </div>
@endif
@endpermission
