@extends('layouts.main')
@section('page-title')
    {{ __('Manage Booking') }}
@endsection
@section('page-breadcrumb')
    {{ __('Booking') }}
@endsection
@push('css')
    @include('layouts.includes.datatable-css')
@endpush
@section('page-action')
<div>
    @permission('facilitiesbooking create')
        <a class="btn btn-sm btn-primary" data-ajax-popup="true" data-size="lg" data-title="{{ __('Create Booking') }}"
            data-url="{{ route('facility-booking.create') }}" data-bs-toggle="tooltip"
            data-bs-original-title="{{ __('Create') }}">
            <i class="ti ti-plus"></i>
        </a>
    @endpermission
</div>
@endsection
@push('css')
    <link rel="stylesheet" href="{{ asset('packages/workdo/Facilities/src/Resources/assets/css/backend.css') }}">
@endpush
@section('content')
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        {{ $dataTable->table(['width' => '100%']) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $('body').on('click', '#submitForm', function() {
            // Get form data
            var isValid = true;
            var service = $('#service').val();
            var date = $('#date').val();
            var person = $('#person').val();
            var gender = $('#gender').val();
            var booking_id = $('#booking_id').val() || null;

            if (service !== '' && date !== '' && person !== '' && gender !== '') {

                $.ajax({
                    type: 'POST',
                    url: "{{ route('search.facilities.booking') }}",
                    data: {
                        "service": service,
                        "date": date,
                        "person": person,
                        "gender": gender,
                        "booking_id": booking_id,
                        "_token": "{{ csrf_token() }}",
                    },
                    success: function(response) {
                        $('#append_div').empty();
                        $('.error_msg').empty();
                        if (response && response.is_success === true && response.html) {
                            $('#append_div').html(response.html);
                        } else {
                            toastrs('Error', response.message || 'Unknown error', 'error');
                        }
                    },
                    error: function(error) {
                        console.log(error);
                    }
                });
            }
        });

        $('body').on('click', '.time' , function(event) {

            var isValid = true;
            var start_time = $('.start-time-input').val();
            var end_time = $('.end-time-input').val();

            if (start_time == '' || end_time == '') {
                $('.slot_text').removeClass('d-none');
                event.preventDefault();
            }
            else
            {
                $('.slot_text').addClass('d-none');
            }
        });
    </script>

    @include('layouts.includes.datatable-js')
    {{ $dataTable->scripts() }}
@endpush
