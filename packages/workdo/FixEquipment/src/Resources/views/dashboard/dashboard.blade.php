@extends('layouts.main')
@section('page-title')
    {{ __('Dashboard') }}
@endsection
@section('page-breadcrumb')
    {{ __('Fix Equipment') }}
@endsection
@section('content')
    <div class="row row-gap mb-4 ">
        <div class="col-xl-6 col-12">
            <div class="dashboard-card">
                <img src="{{ asset('assets/images/layer.png')}}" class="dashboard-card-layer" alt="layer">
                <div class="card-inner">
                    <div class="card-content">
                        <h2>{{ $Activeworkspace ?? 'WorkDo'}}</h2>
                        <p>{{__('Manage equipment efficiently with a fix module for repairs, preventive maintenance, and history')}}</p>
                    </div>
                    <div class="card-icon  d-flex align-items-center justify-content-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="111" height="111" viewBox="0 0 111 111" fill="none">
                            <path d="M0.84617 16.9846C1.43711 14.5339 4.5011 13.6645 6.2988 15.4559L18.2595 27.3796H27.4483V18.2243L15.5033 6.32905C13.7141 4.54629 14.5765 1.49667 17.0238 0.899472C22.6721 -0.47868 31.837 -0.272928 38.9228 6.78477C45.1222 12.9616 47.1137 22.1817 44.2001 30.3449L80.7516 66.7635C88.9446 63.8605 98.1983 65.8447 104.398 72.0216C111.303 78.9018 111.71 88.1279 110.33 93.8495C109.739 96.3002 106.675 97.1696 104.878 95.3782L92.9168 83.4545H83.7281V92.6098L95.67 104.502C97.4597 106.285 96.5965 109.335 94.1485 109.932C88.5026 111.307 79.3409 111.108 72.2538 104.049C66.0544 97.8722 64.0629 88.6522 66.9765 80.489L30.425 44.0704C22.232 46.9733 12.9805 44.9891 6.77891 38.8123C-0.126602 31.9323 -0.533331 22.7062 0.84617 16.9846Z" fill="#18BF6B"/>
                            <path d="M55.5866 78.2847L27.8861 105.882C22.3426 111.406 13.7535 112.094 7.45448 107.94L32.4794 83.0079C33.7457 81.7441 33.7457 79.6952 32.4794 78.4313C31.211 77.1696 29.1546 77.1696 27.8861 78.4313L2.86336 103.365C-1.30567 97.0892 -0.617323 88.5313 4.92839 83.0079L32.6266 55.4082L55.5866 78.2847Z" fill="#18BF6B" fill-opacity="0.6"/>
                            <path d="M110.659 9.46064L101.474 27.7605C100.998 28.7116 100.085 29.3673 99.0285 29.5183L84.0299 31.6513L69.3626 46.2653L64.7715 41.6909L79.4367 27.079L81.5796 12.1328C81.7311 11.0804 82.3892 10.1702 83.3438 9.69573L101.71 0.544711C102.959 -0.0764281 104.47 0.167283 105.457 1.15075L110.051 5.72734C111.038 6.71081 111.282 8.21621 110.659 9.46064Z" fill="#18BF6B" fill-opacity="0.6"/>
                            </svg>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-12">
            <div class="row dashboard-wrp">
                <div class="col-sm-6 col-12">
                    <div class="dashboard-project-card">
                        <div class="card-inner  d-flex justify-content-between">
                            <div class="card-content">
                                <div class="theme-avtar bg-white">
                                    <i class="fas fa-archive text-danger"></i>
                                </div>
                                <a href="{{route('fix.equipment.assets.index')}}"><h3 class="mt-3 mb-0 text-danger">{{ __('Total Asset') }}</h3></a>
                            </div>
                            <h3 class="mb-0">{{$total_asset }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-12">
                    <div class="dashboard-project-card">
                        <div class="card-inner  d-flex justify-content-between">
                            <div class="card-content">
                                <div class="theme-avtar bg-white">
                                    <i class="fas fa-ring"></i>
                                </div>
                             <a href="{{route('fix.equipment.accessories.index')}}"><h3 class="mt-3 mb-0">{{ __('Total Accessories') }}</h3></a>
                            </div>
                            <h3 class="mb-0">{{ $total_accessories }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-12">
                    <div class="dashboard-project-card">
                        <div class="card-inner  d-flex justify-content-between">
                            <div class="card-content">
                                <div class="theme-avtar bg-white">
                                    <i class="fas fa-wrench"></i>
                                </div>
                                <a href="{{route('fix.equipment.component.index')}}"><h3 class="mt-3 mb-0">{{ __('Total Component') }}</h3></a>
                            </div>
                            <h3 class="mb-0">{{ $total_component }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-12">
                    <div class="dashboard-project-card">
                        <div class="card-inner d-flex justify-content-between">
                            <div class="card-content">
                                <div class="theme-avtar bg-white">
                                    <i class="fas fa-cogs"></i>
                                </div>
                               <a href="{{route('fix.equipment.consumables.index')}}" ><h3 class="mt-3 mb-0">{{ __('Total Consumables') }}</h3></a>
                            </div>
                            <h3 class="mb-0">{{ $total_consumables }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xxl-7 col-sm-12 mb-4">
            <div class="card h-100 mb-0">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5>{{ __('Assets') }} </h5>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive custom-scrollbar account-info-table">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('Asset Title') }}</th>
                                    <th>{{ __('Asset Category') }}</th>
                                    <th>{{ __('Purchase Date') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($assets as $asset)
                                    <tr>
                                        <td>{{ $asset->title }}</td>
                                        <td>{{ !empty($asset->equipmentCategory->title) ? $asset->equipmentCategory->title : '' }}
                                        </td>
                                        <td>{{ company_date_formate($asset->purchase_date) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-5 col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Equipment Overview') }}</h5>
                </div>
                <div class="card-body p-2">
                    <div id="chart"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/plugins/apexcharts.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            var statusNames = @json($statusNames);
            var assetCounts = @json($assetCounts);
            var statusColors = @json($statusColors);

            if (statusNames.length > 0) {
                var options = {
                    series: [{
                        data: assetCounts
                    }],
                    chart: {
                        height: 350,
                        type: 'bar',
                        events: {
                            click: function(chart, w, e) {

                            }
                        }
                    },
                    colors: statusColors,
                    plotOptions: {
                        bar: {
                            columnWidth: '30%',
                            distributed: true,
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    legend: {
                        show: false
                    },
                    xaxis: {
                        categories: statusNames,
                        labels: {
                            style: {
                                fontSize: '12px'
                            }
                        }
                    }
                };

                var chart = new ApexCharts(document.querySelector("#chart"), options);
                chart.render();
            }
        });
    </script>
@endpush
