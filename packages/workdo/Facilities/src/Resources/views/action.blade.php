@permission('facilities booking receipt show')
    <div class="action-btn">
        <a class="mx-3 btn bg-warning btn-sm align-items-center" data-ajax-popup="true" data-size="md"
            title="{{ __('View') }}" data-title="{{ __('Show Receipt') }}" data-bs-toggle="tooltip" data-url="{{ route('booking.receipt.show', $facilitiesreceipt->id) }}">
            <i class="ti ti-eye text-white"></i>
        </a>
    </div>
@endpermission
