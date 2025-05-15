@extends('layouts.main')
@section('page-title')
    {{ __('Dashboard') }}
@endsection
@section('page-breadcrumb')
    {{ __('Facilities') }}
@endsection
@push('css')
    <link rel="stylesheet" href="{{ asset('packages/workdo/Facilities/src/Resources/assets/css/main.css') }}">
@endpush

@section('content')
    @if (Auth::user()->type == 'company')
        <div class="row row-gap mb-4">
            <div class="col-xxl-6 col-12">
                <div class="dashboard-card">
                    <img src="{{ asset('assets/images/layer.png') }}" class="dashboard-card-layer" alt="layer">
                    <div class="card-inner">
                        <div class="card-content">
                            <h2>{{ !empty($workspace) ? $workspace->name : '' }}</h2>
                            <p>{{ __('Facilities module offers a centralized platform to manage tasks, optimizing resources and performance') }}
                            </p>
                            <div class="btn-wrp d-flex gap-3">
                                <a href="#" class="btn btn-primary d-flex align-items-center gap-1 cp_link"
                                    data-link="{{ route('facilities.booking', $workspace->slug) }}"
                                    data-bs-whatever="{{ __('Booking Link') }}" data-bs-toggle="tooltip"
                                    data-bs-original-title="{{ __('Create Book') }}"
                                    title="{{ __('Click to copy facilities link') }}">
                                    <i class="ti ti-link"></i>
                                    <span>{{ __('Booking Link') }}</span>
                                </a>
                                {{-- <a href="javascript:" class="btn btn-primary" tabindex="0">
                                <i class="ti ti-share text-white"></i>
                            </a> --}}
                            </div>
                        </div>
                        <div class="card-icon  d-flex align-items-center justify-content-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="68" height="68" viewBox="0 0 68 68"
                                fill="none">
                                <g clip-path="url(#clip0_137_2579)">
                                    <path
                                        d="M38.9502 55.9814H38.7327C37.7713 55.9814 36.8677 56.3551 36.1898 57.0354L34.4498 58.7742L32.711 57.0354C32.0319 56.3551 31.1284 55.9814 30.167 55.9814H29.9495C27.9654 55.9814 26.3516 57.5953 26.3516 59.5782C26.3516 60.6299 26.8097 61.6259 27.608 62.3108L33.1668 67.0761C33.8795 67.6858 35.019 67.6858 35.7317 67.0761L41.2917 62.3108C42.09 61.6248 42.5481 60.6299 42.5481 59.5782C42.5481 57.5953 40.9342 55.9814 38.9502 55.9814Z"
                                        fill="#18BF6B" />
                                    <path
                                        d="M59.3231 12.0191H61.0584V8.228C60.3701 7.82772 59.9015 7.08962 59.9015 6.23467C59.9015 4.9563 60.937 3.92088 62.2153 3.92088C63.4937 3.92088 64.5291 4.9563 64.5291 6.23467C64.5291 7.08846 64.0606 7.82656 63.3722 8.228V12.0191H65.1076L66.8417 9.70536C67.5891 8.71159 67.9986 7.47833 67.9986 6.23467C67.9986 3.04511 65.4037 0.450195 62.2142 0.450195C59.0246 0.450195 56.4297 3.04511 56.4297 6.23467C56.4297 7.47833 56.8404 8.71159 57.5866 9.7042L59.3231 12.0191Z"
                                        fill="#18BF6B" />
                                    <path
                                        d="M63.373 16.6468C64.0105 16.6468 64.5299 16.1285 64.5299 15.4899V14.333H59.9023V15.4899C59.9023 16.1285 60.4218 16.6468 61.0592 16.6468H63.373Z"
                                        fill="#18BF6B" />
                                    <path
                                        d="M6.53557 13.8602C7.26673 14.4698 8.41552 14.4687 9.14437 13.8613C12.4901 11.0743 14.5031 7.06339 14.7553 2.74702C12.3085 2.62901 9.94378 1.91636 7.84054 0.661133C5.73731 1.91636 3.37262 2.62901 0.925781 2.74586C1.17798 7.06339 3.19098 11.0743 6.53557 13.8602Z"
                                        fill="#18BF6B" />
                                    <path
                                        d="M0 55.0808L1.63585 56.7167C5.69655 52.656 12.3013 52.656 16.362 56.7167L17.9978 55.0808C13.0359 50.1189 4.96193 50.1189 0 55.0808Z"
                                        fill="#18BF6B" />
                                    <path
                                        d="M3.27344 58.3542L4.90929 59.9901C6.0014 58.898 7.45446 58.2952 8.99891 58.2952C10.5434 58.2952 11.9964 58.898 13.0897 59.9901L14.7255 58.3542C13.1961 56.8248 11.1623 55.9814 8.99891 55.9814C6.83552 55.9814 4.80285 56.8248 3.27344 58.3542Z"
                                        fill="#18BF6B" />
                                    <path
                                        d="M8.99803 67.5498C10.9148 67.5498 12.4687 65.9959 12.4687 64.0791C12.4687 62.1623 10.9148 60.6084 8.99803 60.6084C7.08122 60.6084 5.52734 62.1623 5.52734 64.0791C5.52734 65.9959 7.08122 67.5498 8.99803 67.5498Z"
                                        fill="#18BF6B" />
                                    <path
                                        d="M57.9371 46.7258C57.1065 46.7258 56.4297 47.4014 56.4297 48.2332C56.4297 48.5317 56.5176 48.8209 56.683 49.0696L58.2055 51.3533H63.909L65.4315 49.0685C65.5969 48.8197 65.6849 48.5305 65.6849 48.232C65.6849 47.4002 65.0092 46.7246 64.1774 46.7246H63.85L61.0573 49.5174L58.2645 46.7258H57.9371Z"
                                        fill="#18BF6B" />
                                    <path
                                        d="M63.9739 53.667H58.1443L55.5529 56.9063C54.6274 58.0632 54.1172 59.5174 54.1172 60.9982C54.1172 64.6112 57.0557 67.5497 60.6687 67.5497H61.4496C65.0626 67.5497 68.0011 64.6112 68.0011 60.9982C68.0011 59.5174 67.4909 58.0632 66.5654 56.9051L63.9739 53.667Z"
                                        fill="#18BF6B" />
                                    <path
                                        d="M28.6647 9.70536H32.1354V13.176C32.1354 13.8146 32.6548 14.3329 33.2923 14.3329H35.6061C36.2435 14.3329 36.763 13.8146 36.763 13.176V9.70536H40.2337C40.8711 9.70536 41.3906 9.18707 41.3906 8.54846V6.23467C41.3906 5.59607 40.8711 5.07778 40.2337 5.07778H36.763V1.60709C36.763 0.968484 36.2435 0.450195 35.6061 0.450195H33.2923C32.6548 0.450195 32.1354 0.968484 32.1354 1.60709V5.07778H28.6647C28.0273 5.07778 27.5078 5.59607 27.5078 6.23467V8.54846C27.5078 9.18707 28.0273 9.70536 28.6647 9.70536Z"
                                        fill="#18BF6B" />
                                    <path
                                        d="M30.9791 42.098C30.9791 40.8219 29.9414 39.7842 28.6654 39.7842C27.3893 39.7842 26.3516 40.8219 26.3516 42.098V46.7256H30.9791V42.098Z"
                                        fill="#18BF6B" />
                                    <path
                                        d="M21.7227 46.7262H24.0364V42.0986C24.0364 39.5465 26.1119 37.471 28.664 37.471C31.2161 37.471 33.2916 39.5465 33.2916 42.0986V46.7262H47.1744V35.1572H21.7227V46.7262ZM36.7623 38.6279H43.7037V43.2555H36.7623V38.6279Z"
                                        fill="#18BF6B" />
                                    <path
                                        d="M19.7617 32.8432H49.1411L35.9293 24.0357C35.0501 23.4503 33.8539 23.4503 32.9735 24.0357L19.7617 32.8432ZM36.7646 28.2156C36.7646 29.494 35.7292 30.5294 34.4508 30.5294C33.1725 30.5294 32.137 29.494 32.137 28.2156C32.137 26.9372 33.1725 25.9018 34.4508 25.9018C35.7292 25.9018 36.7646 26.9372 36.7646 28.2156Z"
                                        fill="#18BF6B" />
                                    <path opacity="0.6"
                                        d="M34.554 55.3983C34.8744 55.0779 35.2319 54.8106 35.6068 54.5758V49.04H33.293V54.5758C33.669 54.8118 34.0276 55.079 34.3481 55.4007L34.4499 55.5025L34.554 55.3983Z"
                                        fill="#18BF6B" />
                                    <path
                                        d="M35.6068 16.6465H33.293V21.4291C33.669 21.3412 34.0554 21.2833 34.4499 21.2833C34.8444 21.2833 35.2308 21.3412 35.6068 21.4291V16.6465Z"
                                        fill="#18BF6B" />
                                    <path opacity="0.6"
                                        d="M49.4913 47.4043L47.8555 49.0401L53.9824 55.1682L55.4366 53.3496L49.4913 47.4043Z"
                                        fill="#18BF6B" />
                                    <path opacity="0.6"
                                        d="M12.5508 13.7379L25.2292 26.4163L27.1924 25.1079L13.9842 11.8984C13.5492 12.5417 13.0679 13.1537 12.5508 13.7379Z"
                                        fill="#18BF6B" />
                                    <path opacity="0.6"
                                        d="M16.0312 50.7824C16.7265 51.1503 17.386 51.5876 18.02 52.0654L21.0452 49.0401L19.4094 47.4043L16.0312 50.7824Z"
                                        fill="#18BF6B" />
                                    <path opacity="0.6"
                                        d="M57.1331 12.9537L55.7367 11.0911L55.7321 11.083L41.707 25.1069L43.6703 26.4153L57.1331 12.9537Z"
                                        fill="#18BF6B" />
                                </g>
                                <defs>
                                    <clipPath id="clip0_137_2579">
                                        <rect width="68" height="68" fill="white" />
                                    </clipPath>
                                </defs>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xxl-6 col-12">
                <div class="row d-flex dashboard-wrp">
                    <div class="col-md-4 col-sm-6 col-12 d-flex flex-wrap">
                        <div class="dashboard-project-card">
                            <div class="card-inner  d-flex justify-content-between">
                                <div class="card-content">
                                    <div class="theme-avtar bg-white">
                                        <i class="ti ti-book text-danger"></i>
                                    </div>
                                    <a href="{{ route('facility-booking.index') }}">
                                        <h3 class="mt-3 mb-0 text-danger">{{ __('Total Booking') }}</h3>
                                    </a>
                                </div>
                                <h3 class="mb-0">{{ $totalBooking }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6 col-12 d-flex flex-wrap">
                        <div class="dashboard-project-card">
                            <div class="card-inner  d-flex justify-content-between">
                                <div class="card-content">
                                    <div class="theme-avtar bg-white">
                                        <i class="ti ti-file-invoice"></i>
                                    </div>
                                    <h3 class="mt-3 mb-0">{{ __('Pending Booking') }}</h3>
                                </div>
                                <h3 class="mb-0">{{ $pendingBooking }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6 col-12 d-flex flex-wrap">
                        <div class="dashboard-project-card">
                            <div class="card-inner  d-flex justify-content-between">
                                <div class="card-content">
                                    <div class="theme-avtar bg-white">
                                        <i class="fas fa-bug"></i>
                                    </div>
                                    <h3 class="mt-3 mb-0">{{ __('Complete Booking') }}</h3>
                                </div>
                                <h3 class="mb-0">{{ $completeBooking }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Calendar') }}</h5>
                </div>
                <div class="card-body">
                    <div id='calendar' class='calendar'></div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mt-1 mb-0">{{ __('Booking') }}</h5>
                </div>
                <div id="callchart" data-color="primary" data-height="230"></div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Current Month Booking') }}</h5>
                </div>
                <div class="card-body">
                    <ul class="event-cards list-group list-group-flush w-100">
                        <div class="row align-items-center justify-content-between">
                            <div class=" align-items-center">
                                @forelse ($current_month_booking as $booking)
                                    <li class="list-group-item card mb-3">
                                        <div class="row align-items-center justify-content-between">
                                            <div class="col-auto mb-3 mb-sm-0">
                                                <div class="d-flex align-items-center">
                                                    <div class="theme-avtar badge bg-primary"
                                                        style="background-color: {{ !empty($booking->getrotarole->color) ? $booking->getrotarole->color : '#8492a6' }} !important">
                                                        <i class="ti ti-building-bank"></i>
                                                    </div>
                                                    <div class="ms-3">
                                                        <h6 class="m-0">
                                                            {{ $booking->client_id != 0 ? (isset($booking->user) ? $booking->user->name : '-') : (isset($booking->name) ? $booking->name : '') }}
                                                            <small class="text-muted text-xs"></small>
                                                        </h6>
                                                        <small class="text-muted">
                                                            {{ date('Y M d', strtotime($booking->date)) }}
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                @empty
                                    <li class="list-group-item card mb-3">
                                        <div class="row align-items-center justify-content-between">
                                            <div class="col-auto mb-3 mb-sm-0">
                                                <div class="d-flex align-items-center">
                                                    {{ __('No Booking Found.') }}
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                @endforelse
                            </div>
                        </div>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="{{ asset('packages/workdo/Facilities/src/Resources/assets/js/main.min.js') }}"></script>
    <script src="{{ asset('packages/workdo/Facilities/src/Resources/assets/js/apexcharts.min.js') }}"></script>
    <script type="text/javascript">
        (function() {
            var etitle;
            var etype;
            var etypeclass;
            var calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                buttonText: {
                    timeGridDay: "{{ __('Day') }}",
                    timeGridWeek: "{{ __('Week') }}",
                    dayGridMonth: "{{ __('Month') }}"
                },
                themeSystem: 'bootstrap',
                slotDuration: '00:10:00',
                navLinks: true,
                droppable: true,
                selectable: true,
                selectMirror: true,
                editable: true,
                dayMaxEvents: true,
                handleWindowResize: true,
                events: {!! json_encode($facilities_book) !!},
            });
            calendar.render();
        })();
        $('.cp_link').on('click', function() {
            var value = $(this).attr('data-link');
            var $temp = $("<input>");
            $("body").append($temp);
            $temp.val(value).select();
            document.execCommand("copy");
            $temp.remove();
            toastrs('Success', '{{ __('Link Copy on Clipboard') }}', 'success')
        });
    </script>
    <script>
        (function() {
            @if (!empty($chartcall['booking_date']))
                var options = {
                    chart: {
                        height: 117,
                        type: 'area',
                        toolbar: {
                            show: false,
                        },
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        width: 2,
                        curve: 'smooth'
                    },
                    series: [{
                        name: "{{ __('Booking') }}",
                        data: {!! json_encode($chartcall['bookings']) !!},
                    }, ],
                    xaxis: {
                        categories: {!! json_encode($chartcall['booking_date']) !!},
                    },
                    colors: ['#6fd943', '#2633cb'],
                    grid: {
                        strokeDashArray: 4,
                    },
                    legend: {
                        show: false,
                    },
                    yaxis: {
                        tickAmount: 3,
                    },
                };
            @endif
            var chart = new ApexCharts(document.querySelector("#callchart"), options);
            chart.render();
        })();
    </script>
@endpush
