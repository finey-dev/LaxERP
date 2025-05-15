@extends('layouts.main')

@section('page-title')
    {{ __('Manage Booking Orders') }}
@endsection

@section('page-breadcrumb')
    {{ __('Booking Orders') }}
@endsection
@push('css')
    <link rel="stylesheet" href="{{ asset('packages/workdo/Facilities/src/Resources/assets/css/backend.css') }}">
@endpush
@push('scripts')
    <script src="{{ asset('assets/js/plugins/dropzone-amd-module.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/dragula.min.js') }}"></script>
    <link href="{{ asset('assets/css/plugins/dragula.min.css') }}" rel="stylesheet" />

    <script>
        !function (a) {
            "use strict";
            var t = function () {
                this.$body = a("body")
            };
            t.prototype.init = function () {
                a('[data-plugin="dragula"]').each(function () {
                    var t = a(this).data("containers"), n = [];
                    if (t) for (var i = 0; i < t.length; i++) n.push(a("#" + t[i])[0]); else n = [a(this)[0]];
                    var r = a(this).data("handleclass");
                    r ? dragula(n, {
                        moves: function (a, t, n) {
                            return n.classList.contains(r)
                        }
                    }) : dragula(n).on('drop', function (el, target, source, sibling) {


                        var order = [];
                        $("#" + target.id + " > div").each(function () {
                            order[$(this).index()] = $(this).attr('data-id');
                        });

                        var id = $(el).attr('data-id');
                        var stage_id = $(target).attr('data-id');

                        $("#" + source.id).parent().find('.count').text($("#" + source.id + " > div").length);
                        $("#" + target.id).parent().find('.count').text($("#" + target.id + " > div").length);


                        $.ajax({
                            url: '{{route('facilities.booking.stage.order')}}',
                            type: 'POST',
                            data: {booking_id: id, stage_id: stage_id, _token: "{{ csrf_token() }}"},
                            success: function (data) {
                                toastrs('{{__("Success")}}', 'The booking order has been moved successfully.', 'success');
                            },
                            error: function (data) {
                                data = data.responseJSON;
                                toastrs('Error', data.error, 'error')
                            }
                        });
                    });
                })
            }, a.Dragula = new t, a.Dragula.Constructor = t
        }(window.jQuery), function (a) {
            "use strict";

            a.Dragula.init()

        }(window.jQuery);
    </script>
@endpush
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="row kanban-wrapper horizontal-scroll-cards" data-plugin="dragula" data-containers='["booking-0", "booking-1", "booking-2", "booking-3"]'>
                @foreach ($stages as $key => $stage)
                    <div class="col" id="backlog">
                        <div class="card card-list">
                            <div class="card-header">
                                <div class="float-end">
                                    @if (isset($bookings[$key]))
                                        <span class="btn btn-sm btn-primary btn-icon count">{{ count($bookings[$key]) }}</span>
                                    @else
                                        <span class="btn btn-sm btn-primary btn-icon count">0</span>
                                    @endif
                                </div>
                                <h4 class="mb-0">{{ $stage }}</h4>
                            </div>
                            <div class="card-body kanban-box" data-id="{{ $key }}" id="booking-{{ $key }}">
                                @if (array_key_exists($key, $bookings))
                                    @foreach ($bookings[$key] as $booking)
                                        <div class="card grid-card" data-id="{{ $booking['id'] }}">
                                            <div class="card-header d-flex align-items-center justify-content-between gap-2 border-0">
                                                <h5>{{ !empty($booking['name']) ? $booking['name'] : $booking['user_name'] }}</h5>
                                                <div class="card-header-right">
                                                    <div class="btn-group card-option">
                                                        <button type="button" class="btn dropdown-toggle"
                                                            data-bs-toggle="dropdown" aria-haspopup="true"
                                                            aria-expanded="false">
                                                            <i class="feather icon-more-vertical"></i>
                                                        </button>
                                                        <div class="dropdown-menu dropdown-menu-end">
                                                            @permission('facilitiesbooking edit')
                                                                <a data-url="{{ route('facility-booking.edit',$booking['id']) }}"
                                                                    data-ajax-popup="true" data-size="lg" data-title="{{ __('Edit Booking') }}"
                                                                    class="dropdown-item"><i class="ti ti-pencil"></i>
                                                                    <span class="ms-2">{{ __('Edit') }}</span>
                                                                </a>
                                                            @endpermission

                                                            @permission('facilitiesbooking delete')
                                                                {!! Form::open(['method' => 'DELETE', 'route' => ['facility-booking.destroy', $booking['id']], 'id' => 'delete-form-' . $booking['id']]) !!}
                                                                    <a class="bs-pass-para dropdown-item show_confirm text-danger"
                                                                       data-bs-toggle="tooltip" data-bs-placement="top">
                                                                        <i class="ti ti-trash"></i>
                                                                        <span class="ms-2">{{ __('Delete') }}</span>
                                                                    </a>
                                                                {!! Form::close() !!}
                                                            @endpermission
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body pt-0">
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <div>
                                                        <i class="ti ti-clock me-1 text-md"></i>
                                                        {{ !empty($booking['date']) ? $booking['date'] : '' }}
                                                    </div>
                                                    <div>
                                                        <i class="ti ti-calender me-1 text-md"></i>
                                                        {{ !empty($booking['gender']) ? $booking['gender'] : '' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            <span class="empty-container" data-placeholder="Empty"></span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $('body').on('click','#submitForm', function() {
            // Get form data
            var isValid = true;
            var service = $('#service').val();
            var date = $('#date').val();
            var person = $('#person').val();
            var gender = $('#gender').val();
            var booking_id = ($('#booking_id').val()) ?  $('#booking_id').val() : null;

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
                    if (response.is_success == true) {
                        $('#append_div').html(response.html);
                    } else {
                        toastrs('Error', response.message, 'error');
                    }
                },
                error: function(error) {
                    console.log(error);
                }
            });
            } else {
            alert('Please fill in all fields before searching.');
            }
        });
    </script>
@endpush
