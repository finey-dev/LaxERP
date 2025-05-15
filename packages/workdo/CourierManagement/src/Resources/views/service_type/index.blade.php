@extends('layouts.main')
@php
    $company_settings = getCompanyAllSetting();
@endphp
@section('page-title')
    {{ __('Manage Service Type') }}
@endsection
@section('page-breadcrumb')
    {{ __('Service Type') }}
@endsection

@section('page-action')
    <div>
        @permission('servicetype manage')
            <a class="btn btn-sm btn-primary" data-ajax-popup="true" data-size="md" data-title="{{ __('Create Service Type') }}"
                data-url="{{ route('courier.servicetype.create') }}" data-toggle="tooltip" title="{{ __('Create') }}">
                <i class="ti ti-plus"></i>
            </a>
        @endpermission
    </div>
@endsection
@section('content')
    <div class="row">
        <div class="col-sm-3">
            @include('courier-management::layouts.courier_setup')
        </div>
        <div class="col-sm-9">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table mb-0 ">
                            <thead>
                                <tr>
                                    <th>{{ __('Service Type') }}</th>
                                    <th width="200px">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($servicetypeData as $serviceType)
                                    <tr>
                                        <td>{{ !empty($serviceType->service_type) ? $serviceType->service_type : '' }}</td>
                                        <td class="Action">
                                            <span>
                                                @permission('servicetype edit')
                                                    <div class="action-btn me-2">
                                                        <a class="mx-3 btn btn-sm bg-info align-items-center"
                                                            data-url="{{ route('courier.servicetype.edit', ['servicetypeId' => $serviceType->id]) }}"
                                                            data-ajax-popup="true" data-size="md" data-bs-toggle="tooltip"
                                                            title="" data-title="{{ __('Edit Service Type') }}"
                                                            data-bs-original-title="{{ __('Edit') }}">
                                                            <i class="ti ti-pencil text-white"></i>
                                                        </a>
                                                    </div>
                                                @endpermission

                                                @permission('servicetype delete')
                                                    <div class="action-btn">
                                                        {{ Form::open(['route' => ['courier.servicetype.delete', $serviceType->id], 'class' => 'm-0']) }}
                                                        @method('DELETE')
                                                        <a class="mx-3 btn btn-sm bg-danger align-items-center bs-pass-para show_confirm"
                                                            data-bs-toggle="tooltip" title=""
                                                            data-bs-original-title="Delete" aria-label="Delete"
                                                            data-confirm="{{ __('Are You Sure?') }}"
                                                            data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                            data-confirm-yes="delete-form-{{ $serviceType->id }}"><i
                                                                class="ti ti-trash text-white text-white"></i></a>
                                                        {{ Form::close() }}
                                                    </div>
                                                @endpermission
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    @include('layouts.nodatafound')
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
    </div>
@endsection
