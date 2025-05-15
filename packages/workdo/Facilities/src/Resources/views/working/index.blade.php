@extends('layouts.main')
@section('page-title')
    {{ __('Manage Working Hours') }}
@endsection

@section('page-breadcrumb')
    {{ __('Working Hours') }}
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="post" class="needs-validation" action="{{ route('facilities-working.store') }}">
                        @csrf
                        <div class="row">
                            <div class="form-group col-md-6">
                                {{ Form::label('opening_time', __('Opening Time'), ['class' => 'col-form-label']) }} <x-required></x-required>
                                {{ Form::time('opening_time', isset($work->opening_time) ? $work->opening_time : '', ['class' => 'form-control ', 'id' => 'clock_in', 'required' => 'required']) }}
                            </div>
                            <div class="form-group col-md-6">
                                {{ Form::label('closing_time', __('Closing Time'), ['class' => 'col-form-label']) }} <x-required></x-required>
                                {{ Form::time('closing_time', isset($work->closing_time) ? $work->closing_time : '', ['class' => 'form-control ', 'id' => 'clock_in', 'required' => 'required']) }}
                            </div>
                        </div>

                        <div class="row">

                            <div class="form-group col-md-6">
                                <h6>{{ __('Working days of the week: ') }}<x-required></x-required></h6>
                                @foreach ($week_days as $key => $day)
                                    @php
                                        $week = isset($work->day_of_week) ? explode(',', $work->day_of_week) : [];
                                        $isChecked = in_array($day, $week);
                                    @endphp
                                    <div class="col-xs-12 col-sm-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="{{ $day }}"
                                                name="working_days[{{ $day }}]"
                                                id="working_days_{{ $key }}"
                                                @if ($isChecked) checked @endif>
                                            <label class="form-check-label" for="working_days_{{ $key }}">
                                                {{ $day }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach

                            </div>
                            <div class="col-md-6">
                                @if (module_is_active('Hrm'))
                                    <div class="row">
                                        <div class="d-flex">
                                            <h6 class="me-2">{{ __('Holidays: ') }} <span id="totalCapacity"></span></h6>
                                            <div class="form-group">
                                                <div class="form-switch">
                                                    <input type="hidden" name="holiday_setting" value="off">
                                                    <input type="checkbox" class="form-check-input" name="holiday_setting" id="holiday_setting" @if(isset($work->holiday_setting) && $work->holiday_setting == 'on') checked @endif>
                                                    <label class="form-check-label" for="holiday_setting"></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label class="form-label"></label>
                                <div class="col-sm-12 col-md-12 text-end">
                                    <button class="btn btn-primary btn-block btn-submit" type="submit"><span>{{ __('Save Change') }}</span></button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
