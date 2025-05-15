@permission('facilitiesbooking edit')
    <div class="action-btn me-2">
        <a class="mx-3 btn btn-sm bg-info align-items-center" data-ajax-popup="true" data-size="lg"
            title="{{ __('Edit') }}" data-title="{{ __('Edit Booking') }}" data-bs-toggle="tooltip" data-url="{{ route('facility-booking.edit', $bookings->id) }}">
            <i class="ti ti-pencil text-white"></i>
        </a>
    </div>
@endpermission
@permission('facilitiesbooking delete')
    <div class="action-btn">
        {!! Form::open(['method' => 'DELETE', 'route' => ['facility-booking.destroy', $bookings->id], 'id' => 'delete-form-' . $bookings->id]) !!}
        <a class="mx-3 btn btn-sm bg-danger align-items-center bs-pass-para show_confirm" data-bs-toggle="tooltip"
            title="{{ __('Delete') }}"
            data-text="{{ __('This action can not be undone. Do you want to continue?') }}"><i
                class="ti ti-trash text-white text-white"></i></a>
        {!! Form::close() !!}
    </div>
@endpermission
