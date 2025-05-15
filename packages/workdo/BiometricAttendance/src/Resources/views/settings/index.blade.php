@extends('layouts.main')
@php
    $company_settings = getCompanyAllSetting();
@endphp
@section('page-title')
    {{ __('Biometric Setting') }}
@endsection
@section('page-breadcrumb')
    {{ __('Biometric Setting') }}
@endsection
@section('page-action')
@endsection
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <small class="" style="margin-left: 20px; margin-top: 20px;">
                    <b class="text-danger">{{ __('Note') }}: </b>
                    {{ __('Note that you can use the biometric attendance system only if you are using the ZKTeco machine for biometric attendance.') }}
                </small>
                {{ Form::open(['route' => ['biometric-settings.store'], 'method' => 'post','class'=>'needs-validation','novalidate']) }}
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('zkteco_api_url', __('ZKTeco Api URL'), ['class' => 'form-label']) }}<x-required></x-required>
                                {{ Form::text('zkteco_api_url', !empty($company_settings['zkteco_api_url']) ? $company_settings['zkteco_api_url'] : null, ['class' => 'form-control font-style mb-1', 'required' => 'required', 'placeholder' => __('Enter ZKTeco Api URL')]) }}
                                <small>
                                    <b class="text-dark">{{ __('Example:') }}</b> http://110.78.645.123:8080
                                </small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                {{ Form::label('username', __('Username'), ['class' => 'form-label']) }}<x-required></x-required>
                                {{ Form::text('username', !empty($company_settings['username']) ? $company_settings['username'] : null, ['class' => 'form-control font-style', 'required' => 'required', 'placeholder' => __('Enter Username')]) }}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                {{ Form::label('user_password', __('Password'), ['class' => 'form-label']) }}<x-required></x-required>
                                {{ Form::text('user_password', !empty($company_settings['user_password']) ? $company_settings['user_password'] : null, ['class' => 'form-control', 'placeholder' => __('Enter User Password'), 'required' => 'required']) }}
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('auth_token', __('Auth Token'), ['class' => 'form-label']) }}
                                @if (empty($company_settings['auth_token']))
                                    <small class="text-danger">
                                        {{ __('Please first create auth token.') }}
                                    </small>
                                @endif
                                {{ Form::textarea('', !empty($company_settings['auth_token']) ? $company_settings['auth_token'] : null, ['class' => 'form-control font-style', 'disabled' => 'disabled', 'rows' => 3]) }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <input class="btn btn-print-invoice  btn-primary m-r-10" type="submit"
                        value="{{ __('Generate Token') }}">
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
@endsection
