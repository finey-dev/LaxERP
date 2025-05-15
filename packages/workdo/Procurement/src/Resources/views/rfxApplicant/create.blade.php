@extends('layouts.main')
@section('page-title')
    {{ __('Manage RFx Applicant') }}
@endsection
@section('page-breadcrumb')
    {{ __('RFx Applicant') }}
@endsection

@push('css')
    <link href="{{ asset('assets/js/plugins/summernote-0.8.18-dist/summernote-lite.min.css') }}" rel="stylesheet">
@endpush

@push('scripts')
    <script src="{{ asset('assets/js/plugins/summernote-0.8.18-dist/summernote-lite.min.js') }}"></script>
@endpush

@section('content')    
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="tab-content" id="pills-tabContent">
                        <div class="tab-pane fade show active" id="details-tab" role="tabpanel"
                            aria-labelledby="pills-user-tab-1">
                            {{ Form::open(['route' => 'rfx-applicant.store', 'enctype' => 'multipart/form-data','class'=>'needs-validation','novalidate']) }}
                            <div class="row">
                                <div class="form-group col-md-6">
                                    {{ Form::label('name', __('Name'), ['class' => 'form-label']) }}<x-required></x-required>
                                    <div class="form-icon-user">
                                        {{ Form::text('name', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Name')]) }}
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    {{ Form::label('email', __('Email'), ['class' => 'form-label']) }}<x-required></x-required>
                                    <div class="form-icon-user">
                                        {{ Form::email('email', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Email')]) }}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <x-mobile name="phone" required="true"></x-mobile>
                                </div>

                                <div class="form-group col-md-6">
                                    {!! Form::label('dob', __('Date of Birth'), ['class' => 'form-label']) !!}<x-required></x-required>
                                    {{ Form::date('dob', null, ['class' => 'form-control ', 'required' => 'required', 'autocomplete' => 'off', 'max' => date('Y-m-d')]) }}
                                </div>

                                <div class="form-group col-md-6 gender">
                                    {!! Form::label('gender', __('Gender'), ['class' => 'form-label']) !!}<x-required></x-required>
                                    <div class="d-flex radio-check">
                                        <div class="form-check form-check-inline form-group">
                                            <input type="radio" id="g_male" value="Male" name="gender"
                                                class="form-check-input" required>
                                            <label class="form-check-label" for="g_male">{{ __('Male') }}</label>
                                        </div>
                                        <div class="form-check form-check-inline form-group">
                                            <input type="radio" id="g_female" value="Female" name="gender"
                                                class="form-check-input" required>
                                            <label class="form-check-label" for="g_female">{{ __('Female') }}</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    {{ Form::label('country', __('Country'), ['class' => 'form-label']) }}<x-required></x-required>
                                    <div class="form-icon-user">
                                        {{ Form::text('country', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Country')]) }}
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    {{ Form::label('state', __('State'), ['class' => 'form-label']) }}<x-required></x-required>
                                    <div class="form-icon-user">
                                        {{ Form::text('state', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter State')]) }}
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    {{ Form::label('city', __('City'), ['class' => 'form-label']) }}<x-required></x-required>
                                    <div class="form-icon-user">
                                        {{ Form::text('city', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter City')]) }}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    {{ Form::label('profile', __('Profile'), ['class' => 'form-label']) }}<x-required></x-required>
                                    <div class="choose-file form-group">
                                        <label for="profile" class="form-label d-block">
                                            <input type="file" class="form-control file" name="profile" id="profile"
                                                data-filename="profile"
                                                onchange="document.getElementById('blah').src = window.URL.createObjectURL(this.files[0])" required>
                                            <hr>
                                            <div class="mt-1">
                                                <img src="" id="blah" width="15%" />
                                            </div>
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    {{ Form::label('proposal', __('Proposal'), ['class' => 'form-label']) }}<x-required></x-required>
                                    <div class="choose-file form-group">
                                        <label for="proposal" class="form-label d-block">
                                            <input type="file" class="form-control file" name="proposal" id="proposal"
                                                data-filename="proposal"
                                                onchange="document.getElementById('blah1').src = window.URL.createObjectURL(this.files[0])" required>
                                            <hr>
                                            <div class="mt-1">
                                                <img src="" id="blah1" width="15%" />
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col"></div>
                                <div class="col-6 text-end">
                                    <input type="submit" value="{{ __('Create') }}" class="btn  btn-primary">
                                </div>
                            </div>
                            {{ Form::close() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
