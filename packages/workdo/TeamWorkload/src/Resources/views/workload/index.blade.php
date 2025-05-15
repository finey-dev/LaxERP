@extends('layouts.main')
@section('page-title')
    {{ __('Team Overview') }}
@endsection

@section('page-breadcrumb')
    {{ __('Overview') }}
@endsection


@push('css')
    <link rel="stylesheet" href="{{ asset('packages/workdo/TeamWorkload/src/Resources/assets/css/custom.css') }}">
@endpush
@section('page-action')
    <div class="d-flex">
        @stack('addButtonHook')
        @if (!module_is_active('Timesheet'))
            <a class="btn btn-sm btn-primary" data-ajax-popup="true" data-size="md" data-title="{{ __('Create Timesheet') }}"
                data-url="{{ route('workload-timesheet.create') }}" data-bs-toggle="tooltip"
                data-bs-original-title="{{ __('Create') }}">
                <i class="ti ti-plus"></i>
            </a>
        @endif
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="p-3 card">
                <ul class="nav nav-pills nav-fill information-tab" id="pills-tab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="pills-user-tab-1" data-bs-toggle="pill"
                            data-bs-target="#pills-user-1" type="button">{{ __('Workload') }}</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="pills-user-tab-2" data-bs-toggle="pill" data-bs-target="#pills-user-2"
                            type="button">{{ __('Report') }}</button>
                    </li>
                </ul>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="tab-content" id="pills-tabContent">
                        <div class="tab-pane fade show active" id="pills-user-1" role="tabpanel"
                            aria-labelledby="pills-user-tab-1">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="mt-2 " id="multiCollapseExample1">
                                        <div class="card">
                                            <div class="card-body">
                                                {{ Form::open(['route' => ['workload.index'], 'method' => 'GET', 'id' => 'workload_index']) }}

                                                <div class="row align-items-center justify-content-end">
                                                    <div class="col-xl-10">
                                                        <div class="row">
                                                            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                                                @if (module_is_active('Hrm'))
                                                                    <div class="btn-box">
                                                                        {{ Form::label('department', __('Department'), ['class' => 'form-label']) }}
                                                                        {{ Form::select('department', $department, isset($_GET['department']) ? $_GET['department'] : '', ['class' => 'form-control select']) }}
                                                                    </div>
                                                                @endif
                                                            </div>
                                                            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                                                @if (module_is_active('Hrm'))
                                                                    <div class="btn-box">
                                                                        {{ Form::label('staff', __('Employee'), ['class' => 'form-label']) }}
                                                                        {{ Form::select('staff', $emp, isset($_GET['staff']) ? $_GET['staff'] : '', ['class' => 'form-control select']) }}
                                                                    </div>
                                                                @endif
                                                            </div>
                                                            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                                                <div class="btn-box">
                                                                    {{ Form::label('start_date', __('Start Date'), ['class' => 'form-label']) }}
                                                                    {{ Form::date('start_date', isset($_GET['start_date']) ? $_GET['start_date'] : null, ['class' => 'month-btn form-control start_date', 'placeholder' => 'Select Date']) }}

                                                                </div>
                                                            </div>
                                                            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                                                <div class="btn-box">
                                                                    {{ Form::label('end_date', __('End Date'), ['class' => 'form-label']) }}
                                                                    {{ Form::date('end_date', isset($_GET['end_date']) ? $_GET['end_date'] : null, ['class' => 'month-btn form-control end_date', 'placeholder' => 'Select Date']) }}

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-auto">
                                                        <div class="row">
                                                            <div class="col-auto mt-4">
                                                                <a href="#" class="btn btn-sm btn-primary me-1"
                                                                    onclick="document.getElementById('workload_index').submit(); return false;"
                                                                    data-bs-toggle="tooltip" title="{{ __('Apply') }}"
                                                                    data-original-title="{{ __('apply') }}">
                                                                    <span class="btn-inner--icon"><i
                                                                            class="ti ti-search"></i></span>
                                                                </a>
                                                                <a href="{{ route('workload.index') }}"
                                                                    class="btn btn-sm btn-danger " data-bs-toggle="tooltip"
                                                                    title="{{ __('Reset') }}"
                                                                    data-original-title="{{ __('Reset') }}">
                                                                    <span class="btn-inner--icon"><i
                                                                            class="ti ti-trash-off text-white-off "></i></span>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                {{ Form::close() }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- <div class="card"> --}}


                            <div class="table-border-style">
                                <div class="table-responsive">
                                    <table class="table mb-0 pc-dt-simple">
                                        <thead>

                                            <tr>
                                                <th colspan="3" class="text-center">{{ __('Staff Information') }}</th>
                                                @foreach ($datesInCurrentWeek as $dateInfo)
                                                    <th colspan="2" class="text-center">
                                                        {{ $dateInfo['date'] }}<br>{{ $dateInfo['day'] }}
                                                    </th>
                                                @endforeach
                                            </tr>
                                            <tr>
                                                <th>{{ __('Employee Name') }}</th>
                                                <th class="text-center">{{ __('Role') }}</th>
                                                <th class="text-center">{{ __('Capacity') }}</th>
                                                @foreach ($datesInCurrentWeek as $dateInfo)
                                                    <th class="text-center">E</th>
                                                    <th class="text-center">S</th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($staffs as $staff)
                                                @php

                                                    $user = \DB::table('workload_staff_settings')
                                                        ->join(
                                                            'users',
                                                            'users.id',
                                                            '=',
                                                            'workload_staff_settings.user_id',
                                                        )
                                                        ->where('users.name', $staff->name)
                                                        ->select(
                                                            'users.type',
                                                            'workload_staff_settings.total_hours',
                                                            'users.avatar',
                                                            'users.id',
                                                            'workload_staff_settings.user_id',
                                                            'workload_staff_settings.working',
                                                            'workload_staff_settings.enable_holiday',
                                                        )
                                                        ->first();

                                                    $employee = Workdo\Hrm\Entities\Employee::where(
                                                        'user_id',
                                                        $user->id,
                                                    )->first();
                                                    $department = $employee
                                                        ? Workdo\Hrm\Entities\Department::find($employee->department_id)
                                                        : null;

                                                    $workingHours = json_decode($user->working, true);

                                                    $workingHoursArray = array_column(
                                                        $workingHours,
                                                        'working_hours',
                                                        'working_days',
                                                    );

                                                    $totalHoursPerDate = [];
                                                    $totalMinutesPerDate = [];

                                                    $dateValues = array_column($datesInCurrentWeek, 'date');

                                                    if (module_is_active('Timesheet')) {
                                                        $timesheet_data = Workdo\Timesheet\Entities\Timesheet::where(
                                                            'user_id',
                                                            $user->id,
                                                        )
                                                            ->whereIn('date', $dateValues)
                                                            ->get();
                                                    } else {
                                                        $timesheet_data = Workdo\TeamWorkload\Entities\WorkloadTimesheet::where(
                                                            'user_id',
                                                            $user->id,
                                                        )
                                                            ->whereIn('date', $dateValues)
                                                            ->get();
                                                    }

                                                    foreach ($dateValues as $date) {
                                                        $totalHoursPerDate[$date] = 0;
                                                        $totalMinutesPerDate[$date] = 0;
                                                    }
                                                    foreach ($timesheet_data as $time) {
                                                        $totalHoursPerDate[$time->date] += $time->hours;

                                                        $totalMinutesPerDate[$time->date] += $time->minutes;
                                                    }

                                                    foreach ($dateValues as $date) {
                                                        $totalHoursPerDate[$date] += floor(
                                                            $totalMinutesPerDate[$date] / 60,
                                                        );
                                                        $totalMinutesPerDate[$date] = $totalMinutesPerDate[$date] % 60;
                                                    }
                                                @endphp
                                                <tr>
                                                    <td>
                                                        <div class="employ-name-box d-flex align-items-center gap-2">
                                                            <div class="employ-img">
                                                                <img alt="image" data-bs-placement="top"
                                                                    @if ($user->avatar) src="{{ get_file($user->avatar) }}" @else src="{{ get_file('avatar.png') }}" @endif
                                                                    class="rounded border-2 border border-primary">
                                                            </div>
                                                            <div class="employ-name">
                                                                <p class="mb-0">{{ $staff->name }}</p>
                                                                <span>
                                                                    @if (!empty($department))
                                                                        {{ $department->name }}
                                                                    @endif
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        @if ($user)
                                                            {{ $user->type }}
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        @if ($user)
                                                            {{ $user->total_hours }}
                                                        @endif
                                                    </td>

                                                    @foreach ($datesInCurrentWeek as $dateInfo)
                                                        @php
                                                            $date = $dateInfo['date'];
                                                            $holidaysData = Workdo\TeamWorkload\Entities\Holiday::where(
                                                                'start_date',
                                                                '<=',
                                                                $date,
                                                            )
                                                                ->where('end_date', '>=', $date)
                                                                ->get();

                                                        @endphp


                                                        <td class="text-center"
                                                            style="{{ $user && $user->enable_holiday == 'on' && $holidaysData->isNotEmpty() ? 'background-color: #E0E6EF;' : '' }}">
                                                            @if ($user && $user->enable_holiday == 'on' && $holidaysData->isNotEmpty())
                                                                {{ __('0') }}
                                                            @else
                                                                {{ $workingHoursArray[$dateInfo['day']] ?? '0' }}
                                                            @endif


                                                        </td>

                                                        @php
                                                            $date = $dateInfo['date'];
                                                            $dayKey = $dateInfo['day'];

                                                            $totalHours = $totalHoursPerDate[$date];

                                                            $totalMinutes = $totalMinutesPerDate[$date];
                                                            $totalTime = $totalHours + $totalMinutes / 60;
                                                            $workingHoursForDay = $workingHoursArray[$dayKey] ?? 0;
                                                            $isOverTime = $totalTime > $workingHoursForDay;
                                                        @endphp

                                                        <td class="text-center"
                                                            style="{{ $isOverTime ? 'color: red;' : '' }}">
                                                            @if ($user)
                                                                @if ($totalHours == 0 && $totalMinutes == 0)
                                                                    {{ __('0') }}
                                                                @else
                                                                    {{ $totalHours }}.{{ $totalMinutes }}
                                                                @endif
                                                            @else
                                                                {{ __('0') }}
                                                            @endif
                                                        </td>
                                                    @endforeach

                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <div class="table-cap-total">
                                    <ul class="list-unstyled mt-5 ms-auto">
                                        @php
                                            $totalEstimatedTime = 0;
                                            $totalCapacity = 0;
                                            $totalSpentTime = 0;
                                            $totaltime = [];
                                        @endphp
                                        @foreach ($staffs as $staff)
                                            @php
                                                $user = \DB::table('workload_staff_settings')
                                                    ->join('users', 'users.id', '=', 'workload_staff_settings.user_id')
                                                    ->where('users.name', $staff->name)
                                                    ->select(
                                                        'users.type',
                                                        'workload_staff_settings.total_hours',
                                                        'users.avatar',
                                                        'users.id',
                                                        'workload_staff_settings.user_id',
                                                        'workload_staff_settings.working',
                                                        'workload_staff_settings.enable_holiday',
                                                    )
                                                    ->first();

                                                if ($user) {
                                                    $totalCapacity += $user->total_hours;

                                                    $workingHoursData = Workdo\TeamWorkload\Entities\WorkloadStaffSetting::where(
                                                        'user_id',
                                                        $user->id,
                                                    )->first();
                                                    $workingHours = json_decode($workingHoursData->working, true);
                                                    $workingHoursArray = array_column(
                                                        $workingHours,
                                                        'working_hours',
                                                        'working_days',
                                                    );

                                                    foreach ($datesInCurrentWeek as $dateInfo) {
                                                        $dayKey = $dateInfo['day'];
                                                        $date = $dateInfo['date'];

                                                        $holidaysData = Workdo\TeamWorkload\Entities\Holiday::where(
                                                            'start_date',
                                                            '<=',
                                                            $date,
                                                        )
                                                            ->where('end_date', '>=', $date)
                                                            ->get();

                                                        if (
                                                            $workingHoursData &&
                                                            $user->id == $workingHoursData->user_id
                                                        ) {
                                                            if (
                                                                $user->enable_holiday == 'on' &&
                                                                $holidaysData->isNotEmpty()
                                                            ) {
                                                                $estimatedHours = 0;
                                                            } else {
                                                                $estimatedHours = isset(
                                                                    $workingHoursArray[$dateInfo['day']],
                                                                )
                                                                    ? $workingHoursArray[$dateInfo['day']]
                                                                    : 0;
                                                            }
                                                            $totalEstimatedTime += $estimatedHours;
                                                        }
                                                    }

                                                    $dateValues = array_column($datesInCurrentWeek, 'date');

                                                    if (module_is_active('Timesheet')) {
                                                        $timesheet_data = Workdo\Timesheet\Entities\Timesheet::where(
                                                            'user_id',
                                                            $user->id,
                                                        )
                                                            ->whereIn('date', $dateValues)
                                                            ->get();
                                                    } else {
                                                        $timesheet_data = Workdo\TeamWorkload\Entities\WorkloadTimesheet::where(
                                                            'user_id',
                                                            $user->id,
                                                        )
                                                            ->whereIn('date', $dateValues)
                                                            ->get();
                                                    }
                                                    foreach ($timesheet_data as $time) {
                                                        $totalHoursPerDate = $time->hours * 60;
                                                        $totalMinutesPerDate = $time->minutes;
                                                        $totaltime[] = $totalHoursPerDate + $totalMinutesPerDate;
                                                    }
                                                }
                                            @endphp
                                        @endforeach
                                        @php
                                            $total_min = array_sum($totaltime);
                                            $totalSpentime = number_format($total_min / 60, 2);

                                        @endphp
                                        <li class="d-flex justify-content-between mb-2">
                                            <p class="mb-0">{{ __('Total Capacity:') }}</p>
                                            <span class="text-end">{{ $totalCapacity }}</span>
                                        </li>
                                        <li class="d-flex justify-content-between mb-2">
                                            <p class="mb-0">{{ __('Total Estimated Time:') }}</p>
                                            <span class="text-end">{{ $totalEstimatedTime }}</span>
                                        </li>
                                        <li class="d-flex justify-content-between mb-2">
                                            <p class="mb-0">{{ __('Total Spent Time:') }}</p>
                                            <span class="text-end">{{ $totalSpentime }}</span>

                                        </li>
                                        <li class="d-flex justify-content-between mb-2">
                                            <p class="mb-0">{{ __('Total Available Capacity:') }}</p>
                                            <span class="text-end">{{ $totalCapacity - $totalEstimatedTime }}</span>
                                        </li>
                                    </ul>

                                </div>

                            </div>

                        </div>
                        <div class="tab-pane fade" id="pills-user-2" role="tabpanel" aria-labelledby="pills-user-tab-2">
                            <div class="row">

                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="panel_s">

                                            <div class="panel-body">

                                                <div id="workload-report" height="353"
                                                    style="display: block; height:315px;" data-color="primary"
                                                    data-height="280"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="panel_s">

                                            <div class="panel-body">

                                                <div class="leads-sources-report" id="workload" style="display: block;"
                                                    data-color="primary" data-height="280"></div>
                                            </div>
                                        </div>
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
    <script src="{{ asset('packages/workdo/TeamWorkload/src/Resources/assets/js/apexcharts.min.js') }}"></script>
    <script>
        var totalCapacity = {{ $totalCapacity }};
        var totalEstimatedTime = {{ $totalEstimatedTime }};
        var totalAvaliableTime = {{ $totalCapacity - $totalEstimatedTime }};

        var total = totalCapacity + totalEstimatedTime + totalAvaliableTime;
        var capacityPercentage = (totalCapacity / total) * 100;
        var estimatedTimePercentage = (totalEstimatedTime / total) * 100;
        var avaliablePercentage = (totalAvaliableTime / total) * 100

        var options = {
            chart: {
                width: 500,
                type: 'pie',
            },
            colors: ["#FFA21D", "#FF3A6E", "#8e24aa"],
            labels: ["Total Capacity", "Total Estimated Time", "Total Avaliable Capacity"],
            dataLabels: {
                enabled: false,
            },
            series: [capacityPercentage, estimatedTimePercentage, avaliablePercentage],
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        width: 260,
                    },
                    legend: {
                        position: 'bottom',
                    },
                },
            }, ],
        };

        var chart = new ApexCharts(document.querySelector("#workload-report"), options);
        chart.render();
    </script>

    <script>
        (function() {
            var options = {
                chart: {
                    height: 300,

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
                    name: "{{ __('Total Capacity Hours') }}",
                    data: {!! json_encode($total_hours) !!},
                }, ],
                xaxis: {
                    categories: {!! json_encode($labels) !!},
                    title: {
                        text: '{{ __('Staff') }}'
                    }
                },
                yaxis: {

                },
                colors: ['#6fd944'],
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
            };
            var chart = new ApexCharts(document.querySelector("#workload"), options);
            chart.render();
        })();
    </script>
@endpush
