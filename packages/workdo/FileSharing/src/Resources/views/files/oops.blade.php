
@extends('layouts.main')
@section('page-title')
    {{ __('Manage Files') }}
@endsection
@section('page-breadcrumb')
    {{ __('Files') }}
@endsection
@section('content')
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body table-border-style">
                <h5></h5>
                <div class="text-center">
                    <i class="fas fa-folder-open text-primary fs-40"></i>
                    <h2>{{ __('Oops...') }}</h2>
                    <h6>{{ __('Please complete your verification from here:') }} <a
                            href="{{ route('file-verification.index') }}">{{ __('Verification Page') }}</a></h6>
                </div>
            </div>
        </div>
    </div>
    @endsection
