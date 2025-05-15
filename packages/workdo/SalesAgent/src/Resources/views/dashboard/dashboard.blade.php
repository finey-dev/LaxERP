@extends('layouts.main')

@section('page-title')
    {{__('Dashboard')}}
@endsection

@section('page-breadcrumb')
    {{ __('Sales Agent')}}
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/plugins/apexcharts.min.js') }}"></script>
    <script>
        var WorkedHoursChart = (function () {
            var $chart = $('#purchase_orders');

            function init($this) {
                var options = {
                    chart: {
                        height: 400,
                        type: 'bar',
                        zoom: {
                            enabled: false
                        },
                        toolbar: {
                            show: false
                        },
                        shadow: {
                            enabled: false,
                        },

                    },
                    plotOptions: {
                bar: {
                    columnWidth: '30%',
                    borderRadius: 10,
                    dataLabels: {
                        position: 'top',
                    },
                }
            },
                    stroke: {
                show: true,
                width: 1,
                colors: ['#fff']
            },
                    series: [{
                        name: 'Orders',
                        data: {!! json_encode($PurchaseOrderData) !!},
                    }],
                    xaxis: {
                        labels: {
                            // format: 'MMM',
                            style: {
                                colors: '#293240',
                                fontSize: '12px',
                                fontFamily: "sans-serif",
                                cssClass: 'apexcharts-xaxis-label',
                            },
                        },
                        axisBorder: {
                            show: false
                        },
                        axisTicks: {
                            show: true,
                            borderType: 'solid',
                            color: '#f2f2f2',
                            height: 50,
                            offsetX: 0,
                            offsetY: 0
                        },
                        title: {
                            text: 'Purchase Orders Status'
                        },
                        categories: {!! json_encode(\Workdo\SalesAgent\Entities\SalesAgentPurchase::$purchaseOrder) !!},
                    },
                    yaxis: {
                        labels: {
                            style: {
                                color: '#f2f2f2',
                                fontSize: '12px',
                                fontFamily: "Open Sans",
                            },
                        },
                        axisBorder: {
                            show: false
                        },
                        axisTicks: {
                            show: true,
                            borderType: 'solid',
                            color: '#f2f2f2',
                            height: 50,
                            offsetX: 0,
                            offsetY: 0
                        }
                    },
                    fill: {
                        type: 'solid',
                        opacity: 1

                    },
                    markers: {
                        size: 4,
                        opacity: 0.7,
                        strokeColor: "#000",
                        strokeWidth: 3,
                        hover: {
                            size: 7,
                        }
                    },
                    grid: {
                        borderColor: '#f2f2f2',
                        strokeDashArray: 5,
                    },
                    dataLabels: {
                        enabled: false
                    }
                }
                // Get data from data attributes
                var dataset = $this.data().dataset,
                    labels = $this.data().labels,
                    color = $this.data().color,
                    height = $this.data().height,
                    type = $this.data().type;

                // Inject synamic properties
                // options.colors = [
                //     PurposeStyle.colors.theme[color]
                // ];
                // options.markers.colors = [
                //     PurposeStyle.colors.theme[color]
                // ];

                // Init chart
                var chart = new ApexCharts($this[0], options);
                // Draw chart
                setTimeout(function () {
                    chart.render();
                }, 300);
            }

            // Events
            if ($chart.length) {
                $chart.each(function () {
                    init($(this));
                });
            }
        })();
    </script>
@endpush
@section('content')
        <div class="row row-gap mb-4">
            <div @if(\Auth::user()->type == 'company') class="col-xxl-5 col-12 d-xxl-flex" @elseif(\Auth::user()->type == 'salesagent') class="col-xxl-6 col-12" @endif>
                <div class="dashboard-card">
                    <img src="{{ asset('assets/images/layer.png')}}" class="dashboard-card-layer" alt="layer">
                    <div class="card-inner">
                        <div class="card-content">
                            <h2>{{ __($activeworkspace ?? 'WorkDo')}}</h2>
                            <p>{{ __('Boost sales with Sales Agent Add-on for better activity & relationships')}}</p>
                        </div>
                        <div class="card-icon  d-flex align-items-center justify-content-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="100" height="96" viewBox="0 0 100 96" fill="none">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M32.1437 0C41.5971 0 49.272 7.68529 49.272 17.1515C49.272 26.6178 41.5971 34.3031 32.1437 34.3031C22.6902 34.3031 15.0153 26.6178 15.0153 17.1515C15.0153 7.68529 22.6902 0 32.1437 0ZM56.2934 48.3898C60.9076 44.6205 66.8004 42.3567 73.2146 42.3567C87.9966 42.3567 100 54.3764 100 69.1784C100 83.9803 87.9966 96 73.2146 96C58.4326 96 46.4292 83.9803 46.4292 69.1784C46.4292 59.8123 51.247 52.5132 56.2934 48.3898ZM69.6504 82.3138C69.7646 84.1842 71.3182 85.6683 73.2146 85.6683C75.0753 85.6683 76.6074 84.2378 76.7717 82.414C77.0396 82.346 77.3003 82.2673 77.5503 82.1779C80.8502 81.0085 83.2359 78.4658 83.2359 74.042C83.2359 69.7112 80.2752 67.4832 76.1217 66.2709C74.6824 65.8489 73.0968 65.5414 71.7111 65.0764C71.3075 64.9406 70.9254 64.7975 70.5932 64.6044C70.4825 64.54 70.3361 64.5042 70.3361 64.3576C70.3361 63.2669 71.1896 62.8628 72.0968 62.7376C73.936 62.4801 76.1467 63.0308 77.6181 64.0608C79.2324 65.1909 81.4609 64.7939 82.5895 63.1775C83.7181 61.5574 83.3252 59.3259 81.7074 58.1958C80.3217 57.2267 78.5931 56.4756 76.7788 56.025C76.6539 54.1618 75.1039 52.6884 73.2146 52.6884C71.3503 52.6884 69.8146 54.1225 69.6575 55.9499C67.6682 56.4971 65.9826 57.5664 64.829 59.122C63.8362 60.456 63.1933 62.1726 63.1933 64.3576C63.1933 68.6884 66.154 70.9164 70.3075 72.1287C71.7468 72.5507 73.3325 72.8583 74.7182 73.3232C75.1217 73.4591 75.5039 73.6057 75.836 73.7953C75.9467 73.8596 76.0931 73.8954 76.0931 74.042C76.0931 75.122 75.2467 75.5011 74.3503 75.6227C72.486 75.8766 70.2539 75.3116 68.7647 74.3067C67.129 73.2016 64.9076 73.6343 63.804 75.2722C62.704 76.9066 63.1362 79.131 64.7683 80.236C66.1433 81.1658 67.8504 81.8775 69.6504 82.3138ZM46.9042 90.6214C35.3472 90.6357 19.5795 90.6357 10.7404 90.6357C5.02257 90.6357 0.311908 86.144 0.03334 80.4256C0.03334 80.3934 0.0333394 80.3576 0.029768 80.3254C-0.0916591 74.3424 0.0940531 65.0586 1.81546 54.5946C2.72616 49.0657 7.45111 42.1135 12.2653 39.1775C12.4618 39.0595 12.6546 38.9414 12.8475 38.827C14.0725 38.0939 15.6189 38.1618 16.776 39.0058C21.2188 42.2387 26.6866 44.1448 32.5937 44.1448C38.5007 44.1448 43.9685 42.2387 48.4113 39.0058C49.5613 38.169 51.0934 38.0975 52.3184 38.8163C52.5184 38.9343 52.7184 39.0559 52.922 39.1775C53.5827 39.578 54.2398 40.0536 54.8863 40.5865C53.8077 41.2839 52.7684 42.0384 51.7791 42.8502C45.3864 48.0715 39.2864 57.3196 39.2864 69.1784C39.2864 77.3071 42.1436 84.7707 46.9042 90.6214Z" fill="#18BF6B"/>
                                </svg>
                        </div>
                    </div>
                </div>
            </div>
            @if(\Auth::user()->type == 'company')
            <div class="col-xxl-7 col-12">
                <div class="row d-flex dashboard-wrp">
                    <div class="col-xxl-4 col-md-4 col-sm-6 col-12 d-flex flex-wrap">
                        <div class="dashboard-project-card">
                            <div class="card-inner  d-flex justify-content-between">
                                <div class="card-content">
                                    <div class="theme-avtar bg-white">
                                        <i class="fas fa-users text-danger"></i>
                                    </div>
                                   <a href="{{route('management.index')}}"><h3 class="mt-3 mb-0 text-danger">{{ __('Total Agents') }}</h3></a>
                                </div>
                                <h3 class="mb-0">{{ $totalAgents }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-4 col-md-4 col-sm-6 col-12 d-flex flex-wrap">
                        <div class="dashboard-project-card">
                            <div class="card-inner  d-flex justify-content-between">
                                <div class="card-content">
                                    <div class="theme-avtar bg-white">
                                        <i class="fas fa-users "></i>
                                    </div>
                                <h3 class="mt-3 mb-0">{{ __('Active Agents') }}</h3>
                                </div>
                                <h3 class="mb-0">{{ $activeAgents }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-4 col-md-4 col-sm-6 col-12 d-flex flex-wrap">
                        <div class="dashboard-project-card">
                            <div class="card-inner  d-flex justify-content-between">
                                <div class="card-content">
                                    <div class="theme-avtar bg-white">
                                        <i class="fas fa-users "></i>
                                    </div>
                                    <h3 class="mt-3 mb-0">{{ __('Inactive Agents') }}</h3>
                                </div>
                                <h3 class="mb-0">{{ $inactiveAgents }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-4 col-md-4 col-sm-6 col-12 d-flex flex-wrap">
                        <div class="dashboard-project-card">
                            <div class="card-inner  d-flex justify-content-between">
                                <div class="card-content">
                                    <div class="theme-avtar bg-white">
                                        <i class="fas fa-cart-plus"></i>
                                    </div>
                                    <a href="{{route('programs.index')}}"><h3 class="mt-3 mb-0">{{ __('Total Programs') }}</h3></a>
                                </div>
                                <h3 class="mb-0">{{ $totalPrograms }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-4 col-md-4 col-sm-6 col-12 d-flex flex-wrap">
                        <div class="dashboard-project-card">
                            <div class="card-inner  d-flex justify-content-between">
                                <div class="card-content">
                                    <div class="theme-avtar bg-white">
                                        <i class="fas fa-bullhorn"></i>
                                    </div>
                                    <a href="{{route('salesagent.purchase.order.index')}}"><h3 class="mt-3 mb-0">{{ __('Total Orders') }}</h3></a>
                                </div>
                                <h3 class="mb-0">{{ $totalSalesOrders }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-4 col-md-4 col-sm-6 col-12 d-flex flex-wrap">
                        <div class="dashboard-project-card">
                            <div class="card-inner  d-flex justify-content-between">
                                <div class="card-content">
                                    <div class="theme-avtar bg-white">
                                        <i class="fas fa-shipping-fast"></i>
                                    </div>
                                <h3 class="mt-3 mb-0">{{ __('Delivered Orders') }}</h3>
                                </div>
                                <h3 class="mb-0">{{ \Workdo\SalesAgent\Entities\SalesAgent::totalOrderDelivered() }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @elseif(\Auth::user()->type == 'salesagent')
            <div class="col-xxl-6 col-12">
                <div class="row d-flex dashboard-wrp">
                    <div class="col-xxl-6 col-md-4 col-sm-6 col-12 d-flex flex-wrap">
                        <div class="dashboard-project-card">
                            <div class="card-inner  d-flex justify-content-between">
                                <div class="card-content">
                                    <div class="theme-avtar bg-white">
                                        <i class="fas fa-cart-plus text-danger"></i>
                                    </div>
                                   <h3 class="mt-3 mb-0 text-danger">{{ __('Programs Participated') }}</h3>
                                </div>
                                <h3 class="mb-0">{{ \Workdo\SalesAgent\Entities\Program::getProgramsBySalesAgentId()->count() }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-6 col-md-4 col-sm-6 col-12 d-flex flex-wrap">
                        <div class="dashboard-project-card">
                            <div class="card-inner  d-flex justify-content-between">
                                <div class="card-content">
                                    <div class="theme-avtar bg-white">
                                        <i class="fas fa-tag"></i>
                                    </div>
                                <h3 class="mt-3 mb-0">{{ __('Total Items') }}</h3>
                                </div>
                                <h3 class="mb-0">{{ \Workdo\SalesAgent\Entities\SalesAgent::getAllProgramItems() }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-6 col-md-4 col-sm-6 col-12 d-flex flex-wrap">
                        <div class="dashboard-project-card">
                            <div class="card-inner  d-flex justify-content-between">
                                <div class="card-content">
                                    <div class="theme-avtar bg-white">
                                        <i class="fas fa-shipping-fast"></i>
                                    </div>
                                    <h3 class="mt-3 mb-0">{{ __('Total Purchase Orders') }}</h3>
                                </div>
                                <h3 class="mb-0">{{ \Workdo\SalesAgent\Entities\SalesAgent::totalOrder() }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-6 col-md-4 col-sm-6 col-12 d-flex flex-wrap">
                        <div class="dashboard-project-card">
                            <div class="card-inner  d-flex justify-content-between">
                                <div class="card-content">
                                    <div class="theme-avtar bg-white">
                                        <i class="fas fa-dollar-sign"></i>
                                    </div>
                                <h3 class="mt-3 mb-0">{{ __('Total Purchase Orders value') }}</h3>
                                </div>
                                <h3 class="mb-0">{{ currency_format_with_sym(\Workdo\SalesAgent\Entities\SalesAgent::totalOrderValue()) }}</h3>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            @endif
        </div>
        <div class="row">
            @if(\Auth::user()->type == 'company')
                <div class="col-xl-12 col-md-12">
                    <div class="card">
                        <div class="card-body table-border-style">
                            <div class="table-responsive">
                                <table class="table mb-0 pc-dt-simple" id="assets">
                                    <thead>
                                        <tr>
                                            <th>{{ __('name') }}</th>
                                            <th>{{ __('Contact') }}</th>
                                            <th>{{ __('Email') }}</th>
                                            <th>{{ __('Total Orders') }}</th>
                                            <th>{{ __('Total Value') }}</th>
                                            <th>{{ __('Delivered Orders') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($salesAgents as $k => $Agent)
                                    <tr class="font-style">
                                        <td>{{ $Agent['name'] }}</td>
                                        <td>{{ $Agent['contact'] }}</td>
                                        <td>{{ $Agent['email'] }}</td>
                                        <td>{{ \Workdo\SalesAgent\Entities\SalesAgent::totalOrder($Agent->id) }}</td>
                                        <td>{{ \Workdo\SalesAgent\Entities\SalesAgent::totalOrderValue($Agent->id) }}</td>
                                        <td>{{ \Workdo\SalesAgent\Entities\SalesAgent::totalOrderDelivered($Agent->id) }}</td>


                                    </tr>
                                @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif(\Auth::user()->type == 'salesagent')
                <div class="col-xl-12 col-md-12">
                    <div class="card">
                        <div class="card-header ">
                            <h5>{{__('Purchase Orders By Delivery Status')}}</h5>
                        </div>
                        <div class="card-body p-2">
                            <div id="purchase_orders" data-color="primary"  data-height="230"></div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
@endsection




