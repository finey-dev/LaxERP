@extends('layouts.main')
@section('page-title')
    {{ __('Staff Setting') }}
@endsection

@section('page-breadcrumb')
    {{ __('Staff Settings') }}
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="post" class="needs-validation" novalidate action="{{ route('staff-setting.store') }}">
                        @csrf
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label class="form-label">{{ __('Users') }}</label><x-required></x-required>
                                {{ Form::select('user', $staffs, null, ['id' => 'user', 'class' => 'form-control choices', 'placeholder' => 'Select User', 'required'=>'required']) }}
                            </div>
                            <div class="form-group col-md-6">
                                <label class="form-label">{{ __('Working Hours Per Day') }}</label><x-required></x-required>
                                <input type="number" name="working_hours" id="working_hours" class="form-control working_hours" required="required" placeholder="10">
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-6">
                            <h6>{{ __('Working days of the week:') }}<x-required></x-required></h6>
                                @foreach ($week_days as $key => $day)
                                    <div class="col-xs-12 col-sm-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="{{ $day }}" name="working_days[{{ $day }}]" id="working_days_{{ $key }}">
                                            <label class="form-check-label" for="working_days_{{ $key }}">
                                                {{ $day }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="form-group col-md-6">
                                <div class="d-flex align-items-center">
                                    <h6 class="mb-0 me-3">{{ __('Total capacity of week:') }} <span id="totalCapacity"></span></h6>
                                    <div class="form-switch">
                                        <input type="hidden" name="enable_holiday" value="off">
                                        <input type="checkbox" class="form-check-input" name="enable_holiday" id="enable_holiday">
                                        <label class="form-check-label" for="enable_holiday"></label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-12">
                                <label class="form-label"></label>
                                <div class="col-sm-12 col-md-12 text-end">
                                    <input type="button" value="{{ __('Cancel') }}" onclick="location.href = '{{ route('staff-setting.index') }}';" class="btn btn-secondary me-1">
                                    <button class="btn btn-primary btn-block btn-submit" type="submit"><span>{{ __('Add') }}</span></button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('scripts')
    <script>
        $(document).ready(function() {
            choices();
        });
    </script>
    <script>
        $(document).ready(function() {
            $('.working_hours, input[name^="working_days"]').on('change', function() {
                var workingHours = $('.working_hours').val();
                var workingDays = $('input[name^="working_days"]:checked').length;
                var totalCapacity = workingHours * workingDays;
                $('#totalCapacity').text(totalCapacity);
            });
        });
    </script>
@endpush


