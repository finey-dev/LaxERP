@extends('layouts.main')
@section('page-title')
    {{ __('Convert To Employee') }}
@endsection


@push('css')
    <link rel="stylesheet" href="{{ asset('packages/workdo/Recruitment/src/Resources/assets/css/custom.css') }}">
@endpush
@section('page-breadcrumb')
    {{ __('Job OnBoard') }},
    {{ __('Convert To Employee') }}
@endsection
@section('page-action')
    <div>
        <a href="{{ route('job.on.board') }}" class="btn-submit btn btn-sm btn-primary " data-toggle="tooltip"
            title="{{ __('Back') }}">
            <i class=" ti ti-arrow-back-up"></i> </a>
    </div>
@endsection
@php
    $company_settings = getCompanyAllSetting();
@endphp
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="mb-4 col-md-12 d-flex align-items-center justify-content-between justify-content-md-end">
                <div class="col-md-6">
                    <ul class="nav nav-pills nav-fill cust-nav information-tab" id="pills-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="personal-details" data-bs-toggle="pill"
                                data-bs-target="#personal-details-tab" type="button">{{ __('Personal Details') }}</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="company" data-bs-toggle="pill" data-bs-target="#company-tab"
                                type="button">{{ __('Company Details') }}</button>
                        </li>

                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            {{ Form::open(['route' => ['job.on.board.convert', $jobOnBoard->id], 'method' => 'post', 'enctype' => 'multipart/form-data', 'class' => 'needs-validation', 'novalidate']) }}
            <div class="card">
                <div class="card-body">
                    <div class="tab-content" id="pills-tabContent">
                        <div class="tab-pane fade show active" id="personal-details-tab" role="tabpanel"
                            aria-labelledby="pills-user-tab-1">
                            <div class="row">
                                <div class="col-sm-6">
                                    <h5>{{ __('Personal Details') }}</h5>
                                    <hr>
                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            {{ Form::label('name', __('Name'), ['class' => 'form-label']) }}<x-required></x-required>
                                            <div class="form-icon-user">
                                                {{ Form::text('name', !empty($jobOnBoard->applications) ? $jobOnBoard->applications->name : '', ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Employee Name')]) }}
                                            </div>
                                            <p class="text-danger d-none" id="{{ 'name_validation' }}">
                                                {{ __('This field is required.') }}</p>
                                        </div>
                                        <x-mobile divClass="col-md-6" name="phone" label="{{ __('Phone') }}"
                                            value="{{ !empty($jobOnBoard->applications) ? $jobOnBoard->applications->phone : '' }}"
                                            placeholder="{{ __('Enter employee phone') }}" id="phone" required>
                                        </x-mobile>
                                        <div class="form-group col-md-6">
                                            {!! Form::label('dob', __('Date of Birth'), ['class' => 'form-label']) !!}<x-required></x-required>
                                            {{ Form::date('dob', date('Y-m-d'), ['class' => 'form-control ', 'required' => 'required', 'autocomplete' => 'off', 'placeholder' => __('Select Date of Birth'), 'max' => date('Y-m-d')]) }}
                                        </div>
                                        <div class="form-group col-md-6">
                                            {!! Form::label('gender', __('Gender'), ['class' => 'form-label']) !!}<x-required></x-required>
                                            <div class="d-flex radio-check">
                                                <div class="custom-control custom-radio custom-control-inline">
                                                    <input type="radio" id="g_male" value="Male" name="gender"
                                                        class="form-check-input"
                                                        {{ !empty($jobOnBoard->applications) && $jobOnBoard->applications->gender == 'Male' ? 'checked' : '' }}>
                                                    <label class="form-check-label "
                                                        for="g_male">{{ __('Male') }}</label>
                                                </div>
                                                <div class="custom-control custom-radio ms-1 custom-control-inline">
                                                    <input type="radio" id="g_female" value="Female" name="gender"
                                                        class="form-check-input"
                                                        {{ !empty($jobOnBoard->applications) && $jobOnBoard->applications->gender == 'Female' ? 'checked' : '' }}>
                                                    <label class="form-check-label "
                                                        for="g_female">{{ __('Female') }}</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            {!! Form::label('email', __('Email'), ['class' => 'form-label']) !!}<x-required></x-required>
                                            {!! Form::email('email', !empty($jobOnBoard->applications) ? $jobOnBoard->applications->email : '', [
                                                'class' => 'form-control',
                                                'required' => 'required',
                                                'placeholder' => __('Enter employee email'),
                                            ]) !!}
                                            <p class="text-danger d-none" id="{{ 'email_validation' }}">
                                                {{ __('This field is required.') }}</p>
                                        </div>
                                        <div class="form-group col-md-6">
                                            {!! Form::label('password', __('Password'), ['class' => 'form-label']) !!}<x-required></x-required>
                                            {!! Form::password('password', [
                                                'class' => 'form-control',
                                                'required' => 'required',
                                                'placeholder' => __('Enter employee new password'),
                                            ]) !!}
                                            <p class="text-danger d-none" id="{{ 'password_validation' }}">
                                                {{ __('This field is required.') }}</p>
                                        </div>
                                        <div class="form-group col-md-6">
                                            {{ Form::label('passport_country', __('Passport country'), ['class' => 'form-label']) }}<x-required></x-required>
                                            <div class="form-icon-user">
                                                {{ Form::text('passport_country', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Passport Country')]) }}
                                            </div>
                                            <p class="text-danger d-none" id="{{ 'passport_country_validation' }}">
                                                {{ __('This field is required.') }}</p>
                                        </div>
                                        <div class="form-group col-md-6">
                                            {{ Form::label('passport', __('Passport'), ['class' => 'form-label']) }}<x-required></x-required>
                                            <div class="form-icon-user">
                                                {{ Form::text('passport', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Passport')]) }}
                                            </div>
                                            <p class="text-danger d-none" id="{{ 'passport_validation' }}">
                                                {{ __('This field is required.') }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <h5>{{ __('Location Details') }}</h5>
                                    <hr>
                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            {{ Form::label('location_type', __('Location Type'), ['class' => 'form-label']) }}<x-required></x-required>
                                            {{ Form::select('location_type', $location_type, null, ['class' => 'form-control select', 'required' => 'required']) }}
                                            <p class="text-danger d-none" id="{{ 'location_type_validation' }}">
                                                {{ __('This field is required.') }}</p>
                                        </div>

                                        <div class="form-group col-md-6">
                                            {{ Form::label('country', __('Country'), ['class' => 'form-label']) }}<x-required></x-required>
                                            <div class="form-icon-user">
                                                {{ Form::text('country', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Country')]) }}
                                            </div>
                                            <p class="text-danger d-none" id="{{ 'country_validation' }}">
                                                {{ __('This field is required.') }}</p>
                                        </div>
                                        <div class="form-group col-md-6">
                                            {{ Form::label('state', __('State'), ['class' => 'form-label', 'required' => 'required']) }}<x-required></x-required>
                                            <div class="form-icon-user">
                                                {{ Form::text('state', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter State')]) }}
                                            </div>
                                            <p class="text-danger d-none" id="{{ 'state_validation' }}">
                                                {{ __('This field is required.') }}</p>
                                        </div>
                                        <div class="form-group col-md-6">
                                            {{ Form::label('city', __('City'), ['class' => 'form-label']) }}<x-required></x-required>
                                            <div class="form-icon-user">
                                                {{ Form::text('city', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter City')]) }}
                                            </div>
                                            <p class="text-danger d-none" id="{{ 'city_validation' }}">
                                                {{ __('This field is required.') }}</p>
                                        </div>
                                        <div class="form-group col-md-6">
                                            {{ Form::label('zipcode', __('Zip code'), ['class' => 'form-label']) }}<x-required></x-required>
                                            <div class="form-icon-user">
                                                {{ Form::text('zipcode', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Zip code')]) }}
                                            </div>
                                            <p class="text-danger d-none" id="{{ 'zipcode_validation' }}">
                                                {{ __('This field is required.') }}</p>
                                        </div>
                                        <div class="form-group col-md-6">
                                            {!! Form::label('address', __('Address'), ['class' => 'form-label']) !!}<x-required></x-required>
                                            {!! Form::textarea('address', old('address'), [
                                                'class' => 'form-control',
                                                'rows' => 2,
                                                'placeholder' => __('Enter employee address'),
                                                'required' => 'required',
                                            ]) !!}
                                            <p class="text-danger d-none" id="{{ 'address_validation' }}">
                                                {{ __('This field is required.') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <h5>{{ __('Document') }}</h5>
                                    <hr>
                                    <div class="card-body employee-detail-create-body p-0">
                                        @foreach ($documents as $key => $document)
                                            <div class="row">
                                                <div class="form-group col-12 d-flex">
                                                    <div class="float-left col-9">
                                                        {{ Form::label('document', $document->name, ['class' => 'float-left pt-1 form-label']) }}
                                                        @if ($document->is_required == 1)
                                                            <x-required></x-required>
                                                        @endif
                                                        <input type="hidden" name="emp_doc_id[{{ $document->id }}]"
                                                            value="{{ $document->id }}">
                                                        <div class="choose-file form-group">
                                                            <label for="document[{{ $document->id }}]"
                                                                class="form-label d-block">
                                                                <input type="file"
                                                                    class="form-control file @error('document') is-invalid @enderror doc_data"
                                                                    name="document[{{ $document->id }}]"
                                                                    id="document[{{ $document->id }}]"
                                                                    data-filename="{{ $document->id . '_filename' }}"
                                                                    @if ($document->is_required == 1) data-key="{{ $key }}" required @endif
                                                                    onchange="document.getElementById('{{ 'blah' . $key }}').src = window.URL.createObjectURL(this.files[0])">
                                                                <hr>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="float-right col-3">
                                                        <img id="{{ 'blah' . $key }}" style="margin-left: 15px;"
                                                            src="{{ asset('packages/workdo/Hrm/src/Resources/assets/image/default.png') }}"
                                                            alt="your image" width="120" height="100"
                                                            class="rounded" />
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <h5>{{ __('Bank Account Detail') }}</h5>
                                    <hr>
                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            {!! Form::label('account_holder_name', __('Account Holder Name'), ['class' => 'form-label']) !!}
                                            {!! Form::text('account_holder_name', old('account_holder_name'), [
                                                'class' => 'form-control',
                                                'placeholder' => __('Enter Account Holder Name'),
                                            ]) !!}

                                        </div>
                                        <div class="form-group col-md-6">
                                            {!! Form::label('account_number', __('Account Number'), ['class' => 'form-label']) !!}
                                            {!! Form::number('account_number', old('account_number'), [
                                                'class' => 'form-control',
                                                'placeholder' => __('Enter Account Number'),
                                            ]) !!}

                                        </div>
                                        <div class="form-group col-md-6">
                                            {!! Form::label('bank_name', __('Bank Name'), ['class' => 'form-label']) !!}
                                            {!! Form::text('bank_name', old('bank_name'), [
                                                'class' => 'form-control',
                                                'placeholder' => __('Enter Bank Name'),
                                            ]) !!}

                                        </div>
                                        <div class="form-group col-md-6">
                                            {!! Form::label('bank_identifier_code', __('Bank Identifier Code'), ['class' => 'form-label']) !!}
                                            {!! Form::text('bank_identifier_code', old('bank_identifier_code'), [
                                                'class' => 'form-control',
                                                'placeholder' => __('Enter Bank Identifier Code'),
                                            ]) !!}
                                        </div>
                                        <div class="form-group col-md-6">
                                            {!! Form::label('branch_location', __('Branch Location'), ['class' => 'form-label']) !!}
                                            {!! Form::text('branch_location', old('branch_location'), [
                                                'class' => 'form-control',
                                                'placeholder' => __('Enter Branch Location'),
                                            ]) !!}
                                        </div>
                                        <div class="form-group col-md-6">
                                            {!! Form::label('tax_payer_id', __('Tax Payer Id'), ['class' => 'form-label']) !!}
                                            {!! Form::text('tax_payer_id', old('tax_payer_id'), [
                                                'class' => 'form-control',
                                                'placeholder' => __('Enter Tax Payer Id'),
                                            ]) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col"></div>
                                <div class="col-6 text-end">
                                    <button class="btn btn-primary d-inline-flex align-items-center"
                                        onClick="changetab('#company-tab')" type="button">{{ __('Next') }}<i
                                            class="ti ti-chevron-right ms-2"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="company-tab" role="tabpanel" aria-labelledby="pills-user-tab-2">
                            <div class="row">
                                <div class="col-sm-6">
                                    <h5>{{ __('Company Detail') }}</h5>
                                    <hr>
                                    <div class="row">
                                        <div class="form-group">
                                            {!! Form::label('employee_id', __('Employee ID'), ['class' => 'form-label']) !!}
                                            {!! Form::text('employee_id', $employeesId, ['class' => 'form-control', 'disabled' => 'disabled']) !!}
                                        </div>
                                        <div class="form-group col-md-6">
                                            {{ Form::label('branch_id', !empty($company_settings['hrm_branch_name']) ? $company_settings['hrm_branch_name'] : __('Branch'), ['class' => 'form-label']) }}<x-required></x-required>
                                            {{ Form::select('branch_id', $branches, null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Select ' . (!empty($company_settings['hrm_branch_name']) ? $company_settings['hrm_branch_name'] : __('Branch')))]) }}
                                            @if (empty($branches->count()))
                                                <div class="text-xs">
                                                    {{ __('Please add Branch. ') }}<a
                                                        href="{{ route('branch.index') }}"><b>{{ __('Add Branch') }}</b></a>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="form-group col-md-6">
                                            {{ Form::label('department_id', !empty($company_settings['hrm_department_name']) ? $company_settings['hrm_department_name'] : __('Department'), ['class' => 'form-label']) }}<x-required></x-required>
                                            {{ Form::select('department_id', [], null, ['class' => 'form-control', 'id' => 'department_id', 'required' => 'required', 'placeholder' => __('Select ' . (!empty($company_settings['hrm_department_name']) ? $company_settings['hrm_department_name'] : __('Department')))]) }}
                                        </div>
                                        <div class="form-group col-md-6">
                                            {{ Form::label('designation_id', !empty($company_settings['hrm_designation_name']) ? $company_settings['hrm_designation_name'] : __('Designation'), ['class' => 'form-label']) }}<x-required></x-required>
                                            {{ Form::select('designation_id', [], null, ['class' => 'form-control', 'id' => 'designation_id', 'required' => 'required', 'placeholder' => __('Select ' . (!empty($company_settings['hrm_designation_name']) ? $company_settings['hrm_designation_name'] : __('Designation')))]) }}
                                        </div>
                                        <div class="form-group col-md-6">
                                            {!! Form::label('company_doj', __('Company Date Of Joining'), ['class' => 'form-label']) !!}<x-required></x-required>
                                            {{ Form::date('company_doj', date('Y-m-d'), ['class' => 'form-control ', 'required' => 'required', 'autocomplete' => 'off', 'placeholder' => __('Select company date of joining')]) }}
                                        </div>
                                        <div class="form-group col-md-6">
                                            {{ Form::label('roles', __('Role'), ['class' => 'form-label']) }}<x-required></x-required>
                                            {{ Form::select('roles', $roles, null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Select Role')]) }}
                                        </div>
                                        @stack('biometric_emp_id')
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <h5>{{ __('Hours and Rates Detail') }}</h5>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6>{{ __('Hours') }}</h6>
                                            <hr>
                                        </div>
                                        <div class="col-md-6">
                                            <h6>{{ __('Rates') }}</h6>
                                            <hr>
                                        </div>
                                        <div class="form-group col-md-6">
                                            {{ Form::label('hours_per_day', __('Hours Per day'), ['class' => 'form-label']) }}
                                            <div class="form-icon-user">
                                                {{ Form::number('hours_per_day', null, ['class' => 'form-control', 'step' => '0.01', 'placeholder' => __('Enter Hours Per Day')]) }}
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            {{ Form::label('annual_salary', __('Annual salary'), ['class' => 'form-label']) }}
                                            <div class="form-icon-user">
                                                {{ Form::number('annual_salary', null, ['class' => 'form-control', 'placeholder' => __('Enter Annual Salary')]) }}
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            {{ Form::label('days_per_week', __('Days Per week'), ['class' => 'form-label']) }}
                                            <div class="form-icon-user">
                                                {{ Form::number('days_per_week', null, ['class' => 'form-control', 'placeholder' => __('Enter Days Per Week')]) }}
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            {{ Form::label('fixed_salary', __('Fixed Salary'), ['class' => 'form-label']) }}
                                            <div class="form-icon-user">
                                                {{ Form::number('fixed_salary', null, ['class' => 'form-control', 'placeholder' => __('Enter Fixed Salary')]) }}
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            {{ Form::label('hours_per_month', __('Hours Per month'), ['class' => 'form-label']) }}
                                            <div class="form-icon-user">
                                                {{ Form::number('hours_per_month', null, ['class' => 'form-control', 'step' => '0.01', 'placeholder' => __('Enter Hours Per Month')]) }}
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            {{ Form::label('rate_per_day', __('Rate per day'), ['class' => 'form-label']) }}
                                            <div class="form-icon-user">
                                                {{ Form::number('rate_per_day', null, ['class' => 'form-control', 'placeholder' => __('Enter Rate Per Day')]) }}
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            {{ Form::label('days_per_month', __('Days per month'), ['class' => 'form-label']) }}
                                            <div class="form-icon-user">
                                                {{ Form::number('days_per_month', null, ['class' => 'form-control', 'placeholder' => __('Enter Days Per Month')]) }}
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            {{ Form::label('rate_per_hour', __('Rate per hour'), ['class' => 'form-label']) }}
                                            <div class="form-icon-user">
                                                {{ Form::number('rate_per_hour', null, ['class' => 'form-control', 'placeholder' => __('Enter Rate Per Hour')]) }}
                                            </div>
                                        </div>
                                        <div class="form-group col-md-12">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input"
                                                    id="payment_requires_work_advice" name="payment_requires_work_advice">
                                                <label class="form-check-label"
                                                    for="payment_requires_work_advice">{{ __('This employee must not be paid unless hours or days worked are advised') }}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 row">
                                <div class="col-6">
                                    <button class="btn btn-outline-secondary d-inline-flex align-items-center"
                                        onClick="changetab('#personal-details-tab')" type="button"><i
                                            class="ti ti-chevron-left me-2"></i>{{ __('Previous') }}</button>
                                </div>
                                <div class="col-6 text-end" id="savebutton">
                                    <a class="btn btn-secondary btn-submit"
                                        href="{{ route('job.on.board') }}">{{ __('Cancel') }}</a>
                                    <button class="btn btn-primary btn-submit ms-2" type="submit"
                                        id="submit">{{ __('Create') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @if (!empty($user) == null)
                {{ Form::close() }}
            @endif
        </div>
    </div>
@endsection
@push('scripts')
    <script src="{{ asset('js/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/dropzone-amd-module.min.js') }}"></script>
    <script>
        function changetab(tabname) {
            var someTabTriggerEl = document.querySelector('button[data-bs-target="' + tabname + '"]');
            var actTab = new bootstrap.Tab(someTabTriggerEl);
            actTab.show();
        }
    </script>

    <script type="text/javascript">
        $(document).on('change', '#branch_id', function() {
            var branch_id = $(this).val();
            getDepartment(branch_id);
        });

        function getDepartment(branch_id) {
            var data = {
                "branch_id": branch_id,
                "_token": "{{ csrf_token() }}",
            }

            $.ajax({
                url: '{{ route('employee.getdepartments') }}',
                method: 'POST',
                data: data,
                success: function(data) {
                    $('#department_id').empty();
                    $('#department_id').append(
                        '<option value="" disabled>{{ __('Select Department') }}</option>');

                    $.each(data, function(key, value) {
                        $('#department_id').append('<option value="' + key + '">' + value +
                            '</option>');
                    });
                    $('#department_id').val('');
                }
            });
        }

        $(document).on('change', 'select[name=department_id]', function() {
            var department_id = $(this).val();
            getDesignation(department_id);
        });

        function getDesignation(did) {
            $.ajax({
                url: '{{ route('employee.getdesignations') }}',
                type: 'POST',
                data: {
                    "department_id": did,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(data) {
                    $('#designation_id').empty();
                    $('#designation_id').append(
                        '<option value="">{{ __('Select Designation') }}</option>');
                    $.each(data, function(key, value) {
                        $('#designation_id').append('<option value="' + key + '">' + value +
                            '</option>');
                    });
                }
            });
        }
    </script>
@endpush
