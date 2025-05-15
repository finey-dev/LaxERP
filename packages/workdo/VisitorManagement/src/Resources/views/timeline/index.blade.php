@extends('layouts.main')

@section('page-title')
    {{ __('Manage Visitors Time-Line') }}
@endsection
@section('page-breadcrumb')
    {{ __('Visitors Time-Line') }}
@endsection

@push('css')
    <link rel="stylesheet" href="{{ asset('packages/workdo/VisitorManagement/src/Resources/assets/css/custom.css') }}">
@endpush
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="my-custom-timeline">
                        <div class="custom-timeline-inner">
                            <h4>{{ __('Visitors Today') }}</h4>
                            <div class="row">
                                <div class="col-6">
                                    <div class="d-flex justify-content-center">
                                        <h6>{{ __('Visitor Arrival') }}</h6>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex justify-content-center">
                                        <h6>{{ __('Visitor Departure') }}</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="timeline">
                                @foreach ($visitorLogs as $key => $visitorLog)
                                    <div class="tl-container left">
                                        <span class="support-time"></span>
                                        <div class="tl-box">
                                            <span class="tl-btn licence-btn">
                                                {{ $visitorLog->check_in }}
                                            </span>
                                            <ul>
                                                <li>
                                                    <span>
                                                        <b>{{ __('Name') }}:-</b>
                                                    </span>
                                                    <span>
                                                        {{ $visitorLog->visitor->first_name . ' ' . $visitorLog->visitor->last_name }}
                                                    </span>
                                                </li>
                                                <li>
                                                    <span>
                                                        <b>{{ __('Reason') }}:-</b>
                                                    </span>
                                                    <span>
                                                        {{ $visitorLog->visitReasons[0]->reason }}
                                                    </span>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    @if (!empty($visitorLog->check_out))
                                        <div class="tl-container right">
                                            <span class="support-time"></span>
                                            <div class="tl-box">
                                                <span class="tl-btn licence-btn">
                                                    {{ $visitorLog->check_out }}
                                                </span>
                                                <ul>
                                                    <li>
                                                        <span>
                                                            <b>{{ __('Name') }}:-</b>
                                                        </span>
                                                        <span>
                                                            {{ $visitorLog->visitor->first_name . ' ' . $visitorLog->visitor->last_name }}
                                                        </span>
                                                    </li>
                                                    <li>
                                                        <span>
                                                            <b>{{ __('Reason') }}:-</b>
                                                        </span>
                                                        <span>{{ $visitorLog->visitReasons[0]->reason }}</span>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
