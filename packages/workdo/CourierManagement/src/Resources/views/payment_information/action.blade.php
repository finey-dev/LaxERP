
@permission('courier edit')
<div class="action-btn  me-2">
    <a class="mx-3 btn bg-info btn-sm  align-items-center"
        data-ajax-popup="true" data-size="lg"
        data-title="{{ __('Edit Payment') }}"
        data-url="{{ route('edit.paymentdetails', ['trackingId' => encrypt($courier->tracking_id)]) }}"
        data-toggle="tooltip" title="{{ __('Edit') }}">
        <i class="ti ti-pencil text-white"></i>
    </a>
</div>
@endpermission

@permission('courier delete')
@if (Auth::user()->id == $courier->created_by)
    <div class="action-btn">
        <form method="POST"
            action="{{ route('delete.paymentdetails', ['trackingId' => encrypt($courier->tracking_id)]) }}"
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
