@extends('layouts.main')

@section('page-title')
    {{ __('Workload Setting') }}
@endsection

@section('page-breadcrumb')
    {{ __('Workload Setting') }}
@endsection

@section('page-action')
<div class="d-flex">
        @permission('workload holidays create')
        <a href="{{route('staff-setting.create')}}" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Create')}}"><i class="ti ti-plus"></i></a>
        @endpermission
</div>
@endsection
@push('css')
    <style>
        .employ-name-box .employ-img img {
            max-width: 30px;
            max-height: 30px;
            border-radius: 20px;
            -webkit-border-radius: 20px;
            -moz-border-radius: 20px;
            -ms-border-radius: 20px;
            -o-border-radius: 20px;
        }

        .employ-name-box .employ-name p {
            font-weight: 600;
        }

        .employ-name-box .employ-name span {
            color: #989898;
            font-size: 12px;
            font-weight: 600;
        }
    </style>
@endpush
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    {{ Form::open(['route' => 'staff-setting.workloadstore', 'class' => 'w-100']) }}
                    @csrf
                    <div class="table-responsive">
                        <table class="table modal-table">
                            <thead>
                                <tr>
                                    <th scope="col">{{ __('User') }}</th>
                                    @foreach ($week_days as $name)
                                        <th>
                                            <span class="email-address">{{ $name }}</span>
                                        </th>
                                    @endforeach
                                    <th scope="col">{{ __('Total Hours') }}</th>

                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($staffs as $staff)
                                    @php
                                        $user = \DB::table('workload_staff_settings')
                                            ->join('users', 'users.id', '=', 'workload_staff_settings.user_id')
                                            ->where('users.name', $staff->user->name)
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
                                        $employee = Workdo\Hrm\Entities\Employee::where('user_id', $user->id)->first();
                                        $department = $employee
                                            ? Workdo\Hrm\Entities\Department::find($employee->department_id)
                                            : null;
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="employ-name-box d-flex align-items-center gap-2">
                                                <div class="employ-img">
                                                    <img alt="image" data-bs-placement="top"
                                                        @if ($staff->user->avatar) src="{{ get_file($staff->user->avatar) }}" @else src="{{ get_file('avatar.png') }}" @endif
                                                        class="rounded border-2 border border-primary">
                                                </div>
                                                <div class="employ-name">
                                                    <p class="mb-0">{{ $staff->user->name }}</p>
                                                    <span>
                                                        @if (!empty($department))
                                                            {{ $department->name }}
                                                        @endif
                                                    </span>
                                                </div>
                                            </div>
                                            <input type="hidden" name="user[]" value="{{ $staff->user->id }}">
                                        </td>
                                        @foreach ($week_days as $day)
                                            <td>
                                                <input type="number"
                                                    name="working_hours[{{ $staff->user->id }}][{{ $day }}]"
                                                    class="form-control working-hours"
                                                    id="working_hours_{{ $staff->user->id }}" min="0" max="24"
                                                    value="{{ $staff->workingHours($day) }}">
                                            </td>
                                        @endforeach
                                        <td>
                                            <input type="number" name="total_hours[{{ $staff->user->id }}]"
                                                class="form-control total-hours-{{ $staff->user->id }}"
                                                data-staff-id="{{ $staff->user->id }}" readonly
                                                style="background-color: #e9ecef" value="{{ $staff->total_hours }}">
                                        </td>


                                    </tr>
                                    @empty
                                    @include('layouts.nodatafound')
                                @endforelse

                            </tbody>
                        </table>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <label class="form-label"></label>
                            <div class="col-sm-12 col-md-12 text-end">
                                <button class="btn btn-primary btn-block btn-submit" type="submit"><span>{{ __('Save Changes') }}</span></button>
                            </div>
                        </div>
                    </div>

                    {{ Form::close() }}
                </div>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(".working-hours").on("change", function() {
            calculateTotalHours($(this));
        });

        function calculateTotalHours(input) {
            var staffId = input.closest('tr').find('input[name="user[]"]').val();

            var totalHours = 0;

            input.closest('tr').find('input[name^="working_hours[' + staffId + ']"]').each(function(key, val) {
                var hours = parseFloat($(this).val());
                totalHours += hours;
            });

            $(".total-hours-" + staffId).val(totalHours);
        }
        calculateTotalHours($(".working-hours"));
    </script>
@endpush
