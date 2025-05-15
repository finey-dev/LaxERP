    @extends('layouts.main')
    @section('page-title')
        {{ __('Manage Indicator') }}
    @endsection

    @section('page-breadcrumb')
        {{ __('Indicator') }}
    @endsection
    @push('css')
        @include('layouts.includes.datatable-css')
        <link rel="stylesheet" href="{{ asset('packages/workdo/Performance/src/Resources/assets/css/custom.css') }}">
    @endpush
    @section('page-action')
        <div>
            @permission('indicator create')
                <a data-url="{{ route('indicator.create') }}" data-ajax-popup="true" data-size="lg"
                    data-title="{{ __('Create Indicator') }}" data-bs-toggle="tooltip" title="" class="btn btn-sm btn-primary"
                    data-bs-original-title="{{ __('Create') }}">
                    <i class="ti ti-plus"></i>
                </a>
            @endpermission
        </div>
    @endsection
    @php
        $company_settings = getCompanyAllSetting();
    @endphp
    @section('content')
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body table-border-style">
                        <div class="table-responsive">
                            {{ $dataTable->table(['width' => '100%']) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @push('scripts')
        @include('layouts.includes.datatable-js')
        {{ $dataTable->scripts() }}
    @endpush
