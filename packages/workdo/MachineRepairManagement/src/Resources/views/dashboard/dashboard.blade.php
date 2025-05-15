 @extends('layouts.main')
@section('page-title')
    {{ __('Dashboard') }}
@endsection
@section('page-breadcrumb')
    {{ __('Machine Repair Management') }}
@endsection
@push('css')
    <link rel="stylesheet" href="{{ asset('packages/workdo/MachineRepairManagement/src/Resources/assets/css/main.css') }}">
@endpush
@section('content')
    @if (session('status'))
        <div class="alert alert-success" role="alert">
            {{ session('status') }}
        </div>
    @endif
    @php
        $workspace = App\Models\WorkSpace::where('id', getActiveWorkSpace())->first();
    @endphp
    <div class="row row-gap mb-4">
        <div class="col-xxl-7 col-12">
            <div class="row row-gap">
                <div class="col-md-12 col-12">
                    <div class="dashboard-card">
                        <img src="{{ asset('assets/images/layer.png')}}" class="dashboard-card-layer" alt="layer">
                        <div class="card-inner">
                            <div class="card-content">
                                <h2>{{ $workspace->name }}</h2>
                                <p>{{__('Machine Repair Management ensures quick repairs, boosts equipment reliability, and improves efficiency.')}}</p>
                            </div>
                            <div class="card-icon  d-flex align-items-center justify-content-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="68" height="78" viewBox="0 0 68 78" fill="none">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M46.2232 4.48046C48.624 5.04706 50.9134 5.98867 53.0178 7.27629L57.434 5.32653L62.6355 10.4913L60.6793 14.8762C61.9761 16.9657 62.9245 19.2391 63.495 21.6301L68 23.3517V30.6574L63.495 32.379C62.9245 34.7628 61.9687 37.0361 60.6793 39.1256L62.6355 43.5105L57.434 48.6753L53.0178 46.733C50.9208 48.0205 48.624 48.9622 46.2232 49.5287L44.4893 54.0018H37.1317L35.3977 49.5287C32.9897 48.9622 30.7001 48.0131 28.5958 46.733L24.1796 48.6753L18.978 43.5105L20.9417 39.1256C19.6449 37.0434 18.6966 34.7628 18.1186 32.379L13.6136 30.6574V23.3517L18.1186 21.6301C18.6966 19.2391 19.6449 16.9657 20.9417 14.8762L18.978 10.4913L24.1796 5.32653L28.5958 7.27629C30.7001 5.98867 32.9897 5.04706 35.3903 4.48046L37.1317 0H44.4893L46.2232 4.48046ZM44.3125 44.537C52.5763 42.9165 58.8093 35.6798 58.8093 26.9995C58.8093 17.1275 50.7475 9.133 40.8054 9.133C30.863 9.133 22.8116 17.1275 22.8116 26.9995C22.8116 35.6833 29.0416 42.9224 37.3085 44.539V32.5151C34.0518 31.1584 31.7598 27.9581 31.7598 24.2317C31.7598 20.7267 33.7785 17.6924 36.7229 16.2084V25.245L40.8106 27.5871L44.8981 25.245V16.2084C47.8424 17.6924 49.8612 20.7267 49.8612 24.2317C49.8612 27.9581 47.5748 31.1584 44.3125 32.5151V44.537ZM3.86588 58.9588L13.3978 75.6826C13.4873 75.8413 13.4332 76.0444 13.2742 76.1335L13.2725 76.1345L10.0334 77.9573C9.8737 78.046 9.66932 77.9923 9.57948 77.8346L9.57898 77.8336L0.0438829 61.1043L0.0433794 61.1035C-0.046465 60.9461 0.0076092 60.7447 0.165299 60.6561L0.166474 60.6554L3.41145 58.8314C3.57032 58.7424 3.77318 58.8002 3.86471 58.957L3.86588 58.9588ZM2.84182 60.6094C2.10594 60.7138 1.59777 61.3923 1.7029 62.123C1.81239 62.8536 2.49588 63.3626 3.23176 63.2538C3.96765 63.1495 4.47565 62.4666 4.37052 61.736C4.26103 61.0053 3.57771 60.5007 2.84182 60.6094ZM13.6206 70.5475L8.2392 61.1057L16.559 56.3231C18.4692 55.226 20.3858 54.3419 22.484 55.0184L22.4845 55.0186C25.2692 55.9152 29.8077 57.8236 34.4462 58.6718C35.415 59.0212 36.0463 59.979 35.7771 60.9724L35.7769 60.9731C35.5099 61.9609 34.6518 62.4233 33.492 62.2992C30.9539 61.7776 28.5966 61.0123 25.9819 60.3144C25.2514 60.1195 24.4989 60.5502 24.3025 61.2756C24.1062 62.0009 24.54 62.7481 25.2705 62.943C27.96 63.6607 33.0359 64.9845 33.0544 64.9875L34.4892 65.2193C34.4909 65.2194 34.4924 65.2198 34.4939 65.2199C36.664 65.5629 38.655 65.2575 40.555 64.1581L52.1158 57.4726C56.8422 54.7386 58.8626 56.1068 58.8093 58.0981C58.7914 58.7649 58.5509 59.4412 58.0243 59.7632L37.045 72.5886L37.0437 72.5894C35.4481 73.5672 33.6991 73.7872 31.8884 73.3076L16.6718 69.2584C16.3207 69.165 15.9465 69.2138 15.6316 69.3944L13.6206 70.5475Z" fill="#18BF6B"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-5 col-12">
            <div class="row d-flex dashboard-wrp">
                <div class="col-md-6 col-sm-6 col-12 d-flex flex-wrap">
                    <div class="dashboard-project-card">
                        <div class="card-inner  d-flex justify-content-between">
                            <div class="card-content">
                                <div class="theme-avtar bg-white">
                                    <i class="ti ti-hand-finger text-danger"></i>
                                </div>
                                <a href="{{ route('machine-repair-request.index') }}"><h3 class="mt-3 mb-0 text-danger">{{ __('Total Repair Request') }}</h3></a>
                            </div>
                            <h3 class="mb-0">{{ $totalRequest ?? '0' }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-sm-6 col-12 d-flex flex-wrap">
                    <div class="dashboard-project-card">
                        <div class="card-inner  d-flex justify-content-between">
                            <div class="card-content">
                                <div class="theme-avtar bg-white">
                                    <i class="ti ti-chart-pie"></i>
                                </div>
                            <h3 class="mt-3 mb-0">{{ __('Total Diagnosis') }}</h3>
                            </div>
                            <h3 class="mb-0">{{ $totalInvoice ?? '0' }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xxl-12">
            <div class="col-xxl-12">
                <div class="row">
                    <div class="col-xxl-7">
                        <div class="card">
                            <div class="card-header">
                                <h5>{{ __("Repair Request") }}</h5>
                            </div>
                            <div class="card-body card-635 ">
                                <div id='calendar' class='calendar'></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-5">
                        <div class="row">
                            <div class="col-xl-12">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="row ">
                                            <div class="col-6">
                                                <h5>{{ __('Diagnosis Report') }}</h5>
                                            </div>
                                            <div class="col-6 text-end">
                                                <h6>{{ __('Last 10 Days') }}</h6>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div id="Diagnosis-chart"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="{{ asset('packages/workdo/MachineRepairManagement/src/Resources/assets/js/main.min.js') }}"></script>
    <script src="{{ asset('packages/workdo/MachineRepairManagement/src/Resources/assets/js/apexcharts.min.js') }}"></script>
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
                events: {!! json_encode($events) !!},
            });
            calendar.render();
        })();


        (function() {
            var options = {
                chart: {
                    height: 350,
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
                        name: '{{ __('Diagnosis') }}',
                        // data: [2117.8,650.8,940,600,800,1150,650,1000,500,1360]
                        data: {!! json_encode($diagnosisArray['value']) !!}

                    },
                ],
                xaxis: {
                    // categories: ["2024-02-09","2024-02-08","2024-02-07","2024-02-06","2024-02-05","2024-02-04","2024-02-03","2024-02-02","2024-02-01","2024-01-31"],
                    categories: {!! json_encode($diagnosisArray['label']) !!},
                    title: {
                        text: '{{ __('Days') }}'
                    }
                },
                colors: ['#FF3A6E', '#6fd943'],

                grid: {
                    strokeDashArray: 4,
                },
                legend: {
                    show: false,
                },
                yaxis: {
                    title: {
                        text: '{{ __('Amount') }}'
                    },
                }
            };
            var chart = new ApexCharts(document.querySelector("#Diagnosis-chart"), options);
            chart.render();
        })();

    </script>
@endpush

