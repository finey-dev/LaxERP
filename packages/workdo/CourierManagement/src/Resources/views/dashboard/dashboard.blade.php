@extends('layouts.main')
<style>
    .shareqrcode img {
        width: 85%;
        height: 85%;
    }

    /* Social Sharing  */
    .sharingButtonsContainer {
        position: absolute;
        top: 85%;
        right: 20px;
        transform: translateY(-50%);
        z-index: 9999;
    }

    .sharingButtonsContainer a {
        background-color: #ddd;
        display: flex;
        justify-content: center;
        min-width: 13px;
        border-radius: 20px;
        width: 35px;
        height: 35px;
        align-items: center;
    }

    .share-btn {
        background-color: #47dbcd;
        border: 1px solid #47dbcd;
    }

    .sharingButtonsContainer .Demo1 {
        margin-bottom: 0px !important;
    }

    @media screen and (max-width:1200px) {
        .sharingButtonsContainer {
            right: 25px;
        }
    }

    .socialJS {
        display: flex;
        gap: 0 10px;
    }
</style>
@section('page-title')
    {{ __('Dashboard') }}
@endsection
@section('page-breadcrumb')
    {{ __('Courier Management') }}
@endsection
@section('content')
    <div class="row row-gap mb-4">
        <div class="{{ Auth::user()->type == 'company' ? 'col-xxl-6 col-12' : 'col-12' }}">
            <div class="row row-gap">
                <div class="col-md-12 col-12">
                    <div class="dashboard-card">
                        <img src="{{ asset('assets/images/layer.png') }}" class="dashboard-card-layer" alt="layer">
                        <div class="card-inner">
                            <div class="card-content">
                                <h2>{{ Auth::user()->ActiveWorkspaceName() }}</h2>
                                <p>{{ __('Have a nice day! Did you know that you can quickly Track your Courier by using this link.') }}

                                <div class="btn-wrp d-flex flex-wrap gap-3">
                                    <a href="javascript:" class="btn btn-primary d-flex align-items-center gap-1 cp_link"
                                        tabindex="0" data-bs-whatever="{{ __('Copy Link') }}" data-bs-toggle="tooltip"
                                        data-bs-original-title="{{ __('Copy Link') }}"
                                        title="{{ __('Click to copy link') }}" id="cp_link"
                                        data-link="{{ route('find.courier', $workspace->slug) }}" onclick="copy_link(this)">
                                        <i class="ti ti-link text-white"></i>
                                        <span> {{ __('Track Your Courier') }}</span></a>

                                    <a href="javascript:"
                                        class="btn btn-primary d-flex align-items-center gap-1 copy_form_link"
                                        tabindex="0"
                                        data-link="{{ route('create.public.courier.request', ['workspaceSlug' => $workspace->slug]) }}"
                                        data-bs-whatever="{{ __('Service Request Link') }}" data-bs-toggle="tooltip"
                                        data-bs-original-title="{{ __('Create Courier') }}"
                                        title="{{ __('Click to copy link') }}">
                                        <i class="ti ti-link text-white"></i>
                                        <span> {{ __('Create Courier') }}</span></a>
                                    {{-- <a href="javascript:" class="btn btn-primary" tabindex="0">
                                        <i class="ti ti-share text-white"></i>
                                    </a> --}}
                                </div>
                            </div>
                            <div class="card-icon  d-flex align-items-center justify-content-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="74" height="66" viewBox="0 0 74 66"
                                    fill="none">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M12.5577 39.6959L10.5524 36.6143L0.0078125 43.4748L14.6662 66.0004L25.2086 59.1399L12.5577 39.6959Z"
                                        fill="#18BF6B" />
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M39.5464 42.5711C39.0999 42.569 38.7376 42.2067 38.7397 41.7601C38.7418 41.3136 39.102 40.9534 39.5485 40.9534H39.6138C40.4985 40.9534 41.3579 40.9134 42.1689 40.8354C44.5617 40.6058 46.5733 40.0519 48.1468 39.1903C49.7856 38.293 50.4891 37.3093 50.7924 36.6437C51.1842 35.7801 51.1315 34.9312 50.645 34.253C49.8403 33.1324 47.9825 32.6037 45.6739 32.8375C41.8003 33.2293 38.6723 31.9191 35.915 30.7627C34.1119 30.0065 32.5532 29.3535 31.1672 29.3535C31.1651 29.3535 31.163 29.3535 31.1609 29.3535C30.7186 29.3535 30.2783 29.3956 29.8486 29.4778C29.8465 29.4778 29.8444 29.4778 29.8402 29.4799C27.2683 29.9791 25.1472 31.9444 22.9039 34.0255C20.5974 36.1656 18.0086 38.5626 14.5078 39.7275L24.0877 54.4511C26.1751 52.7449 27.9781 51.6833 29.7159 51.1314C31.4221 50.5901 33.0272 50.5417 34.9208 50.9777C41.4485 52.4795 46.0299 51.4369 49.7097 50.5985C49.952 50.5438 50.1879 50.489 50.4238 50.4363C53.061 49.8444 58.5481 47.2515 63.4729 44.2709C68.9874 40.9323 72.6988 37.7854 73.1601 36.0581C73.3329 35.4136 73.434 34.5394 72.9347 34.0444C72.3113 33.4231 70.8557 33.3641 68.9431 33.8844C68.6567 33.9623 68.3639 34.0508 68.0711 34.1498C67.9236 34.2003 67.3128 34.4657 66.6072 34.7732C61.9563 36.8038 49.6107 42.1962 42.3016 42.4468C41.4443 42.529 40.5406 42.569 39.6138 42.569C39.5907 42.5711 39.5696 42.5711 39.5464 42.5711Z"
                                        fill="#18BF6B" />
                                    <path opacity="0.6" fill-rule="evenodd" clip-rule="evenodd"
                                        d="M42.2656 13.7488L48.7154 8.65559C49.0082 8.42389 49.4231 8.42389 49.718 8.65559L56.1678 13.7488V0.208984H42.2677L42.2656 13.7488Z"
                                        fill="#18BF6B" />
                                    <path opacity="0.6" fill-rule="evenodd" clip-rule="evenodd"
                                        d="M57.7762 0.209137V15.4172C57.7762 15.7269 57.5993 16.0091 57.3191 16.1461C57.2075 16.2008 57.0874 16.2261 56.9674 16.2261C56.7883 16.2261 56.6114 16.1671 56.466 16.0513L49.2074 10.3198L41.9488 16.0513C41.7066 16.2429 41.3738 16.2788 41.0957 16.1439C40.8177 16.0091 40.6386 15.7269 40.6386 15.4151V0.207031H30.4922V27.7628C30.7155 27.7459 30.9409 27.7354 31.1662 27.7354C32.8766 27.7375 34.6544 28.481 36.5375 29.2709C39.1199 30.3536 42.0457 31.5795 45.5086 31.2278C48.4428 30.9308 50.7935 31.6891 51.9563 33.3089C52.7841 34.4611 52.8957 35.9187 52.2638 37.311C51.9879 37.9177 51.5813 38.4948 51.0589 39.0256C57.0242 37.191 63.2718 34.4632 65.9574 33.2899C66.9137 32.8729 67.3455 32.6833 67.5519 32.6159C67.6741 32.5738 67.7963 32.5358 67.9185 32.4958V0.209137H57.7762Z"
                                        fill="#18BF6B" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        @if (Auth::user()->type == 'company')
            <div class="col-xxl-6 col-12">
                <div class="row d-flex dashboard-wrp">
                    <div class="col-md-4 col-sm-6 col-12 d-flex flex-wrap">
                        <div class="dashboard-project-card">
                            <div class="card-inner  d-flex justify-content-between">
                                <div class="card-content">
                                    <div class="theme-avtar bg-white">
                                        <i class="ti ti-package text-danger"></i>
                                    </div>
                                    <a href="{{ route('courier') }}">
                                        <h3 class="mt-3 mb-0 text-danger">{{ __('Courier') }}</h3>
                                    </a>
                                </div>
                                <h3 class="mb-0">{{ count($totalCourier) }}</h3>
                            </div>
                        </div>
                    </div>


                    <div class="col-md-4 col-sm-6 col-12 d-flex flex-wrap">
                        <div class="dashboard-project-card">
                            <div class="card-inner  d-flex justify-content-between">
                                <div class="card-content">
                                    <div class="theme-avtar bg-white">
                                        <i class="ti ti-currency-dollar"></i>
                                    </div>
                                    <a href="{{ route('courier.all.payment') }}">
                                        <h3 class="mt-3 mb-0">{{ __('Income') }}</h3>
                                    </a>
                                </div>
                                <h3 class="mb-0">{{ $totalIncome }}</h3>
                            </div>
                        </div>
                    </div>



                    <div class="col-md-4 col-sm-6 col-12 d-flex flex-wrap">
                        <div class="dashboard-project-card">
                            <div class="card-inner  d-flex justify-content-between">
                                <div class="card-content">
                                    <div class="theme-avtar bg-white">
                                        <i class="ti ti-thumb-up"></i>
                                    </div>
                                    <a href="{{ route('courier') }}">
                                        <h3 class="mt-3 mb-0">{{ __('Delivered Courier') }}</h3>
                                    </a>
                                </div>
                                <h3 class="mb-0">{{ $totalDeliveredCourier }}</h3>
                            </div>
                        </div>
                    </div>



                </div>
            </div>
        @endif
    </div>
    <div class="row">
        <div class="col-xxl-6 mb-4">
            <div class="card h-100 mb-0">
                <div class="card-header">
                    <h5>{{ __('Couriers') }}
                        <span class="float-end text-muted">{{ __('Current Year') . ' - ' . $currentYear }}</span>
                    </h5>
                </div>
                <div class="card-body table-border-style">
                    <div class="table-responsive custom-scrollbar account-info-table">
                        <table class="table mb-0" id="assets">
                            <thead>
                                <tr>
                                    <th>{{ __('Ticket Id') }}</th>
                                    <th>{{ __('Sender Name') }}</th>
                                    <th>{{ __('Created By') }}</th>
                                    <th>{{ __('Tracking Status') }}</th>
                                    <th>{{ __('Payment Type') }}</th>
                                    <th>{{ __('Payment Status') }}</th>
                                    <th>{{ __('Created') }}</th>
                                </tr>
                            </thead>
                            <tbody>

                                @forelse ($getCurrentMonthData as $currentMonthData)
                                    <tr>

                                        <td>
                                            <a class="btn btn-outline-primary">
                                                {{ $currentMonthData->tracking_id }}
                                            </a>
                                        </td>
                                        <td>{{ $currentMonthData->sender_name }}</td>
                                        <td class="text-primary">{{ $currentMonthData->createdBy->name }}</td>

                                        @php
                                            $statusColor = isset(
                                                $currentMonthData->packageInformarmation->getTrackingStatus
                                                    ->status_color,
                                            )
                                                ? $currentMonthData->packageInformarmation->getTrackingStatus
                                                    ->status_color
                                                : 'ffa833';
                                        @endphp
                                        <td>
                                            <span class="badge fix_badge p-2 px-3"
                                                style="background-color: {{ '#' . $statusColor }}; color: white;">
                                                {{ isset($currentMonthData->packageInformarmation->getTrackingStatus->status_name) ? $currentMonthData->packageInformarmation->getTrackingStatus->status_name : 'pending' }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span
                                                class="white-space">{{ isset($currentMonthData->payment_type) ? $currentMonthData->payment_type : '---' }}</span>
                                        </td>

                                        <td><span
                                                class="badge fix_badge @if ($currentMonthData->payment_status == 'pending') bg-warning @elseif($currentMonthData->payment_status == 'success') bg-success @else bg-success @endif  p-2 px-3 ">{{ $currentMonthData->payment_status }}</span>

                                        </td>

                                        <td>{{ $currentMonthData->created_at->diffforHumans() }}</td>

                                    </tr>
                                @empty
                                    @include('layouts.nodatafound')
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xxl-6 mb-4">
            <div class="card h-100 mb-0">
                <div class="card-header">
                    <h5>{{ __('Courier & Income') }}
                        <span class="float-end text-muted">{{ __('Current Year') . ' - ' . $currentYear }}</span>
                    </h5>
                </div>
                <div class="card-body">
                    <div id="incExpBarChart"></div>
                    <div class="text-center text-danger">
                        {{ __('Chart Data Will Calculate After Completed Courier Payments.') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="{{ asset('assets/js/plugins/apexcharts.min.js') }}"></script>
    <script>
        function copy_link(link) {
            var value = $(link).attr('data-link');
            var temp = $("<input>");
            console.log(temp);
            $("body").append(temp);
            temp.val(value).select();
            document.execCommand("copy");
            temp.remove();
            toastrs('Success', '{{ __('Link Copy on Clipboard') }}', 'success')
        }

        $('.copy_form_link').on('click', function() {
            var value = $(this).attr('data-link');
            var $temp = $("<input>");
            $("body").append($temp);
            $temp.val(value).select();
            document.execCommand("copy");
            $temp.remove();
            toastrs('Success', '{{ __('Link Copy on Clipboard') }}', 'success')
        });

        (function() {
            var options = {
                chart: {
                    height: 265,
                    type: 'bar',
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
                    name: "{{ __('Total Courier') }}",
                    data: {!! json_encode($data['incExpBarChartData']['totalCourier']) !!},

                }, {
                    name: "{{ __('Total Income') }}",
                    data: {!! json_encode($data['incExpBarChartData']['income']) !!},

                }],
                xaxis: {
                    categories: {!! json_encode($data['incExpBarChartData']['month']) !!},
                },
                colors: ['#3ec9d6', '#008000'],
                fill: {
                    type: 'solid',
                },
                grid: {
                    strokeDashArray: 4,
                },
                legend: {
                    show: true,
                    position: 'top',
                    horizontalAlign: 'right',
                },
                markers: {
                    size: 4,
                    colors: ['#3ec9d6', '#FF3A6E'],
                    opacity: 0.9,
                    strokeWidth: 2,
                    hover: {
                        size: 7,
                    }
                }
            };
            var chart = new ApexCharts(document.querySelector("#incExpBarChart"), options);
            chart.render();
        })();
    </script>
@endpush
