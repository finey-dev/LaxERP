@extends('layouts.main')
@php
    $company_settings = getCompanyAllSetting();
@endphp
@section('page-title')
    {{ __('Manage Branch') }}
@endsection
@section('page-breadcrumb')
    {{ __('Branch') }}
@endsection

@section('page-action')
    <div>
        @permission('courier branch create')
            <a class="btn btn-sm btn-primary" data-ajax-popup="true" data-size="md" data-title="{{ __('Create Branch') }}"
                data-url="{{ route('courier.branch.create') }}" data-toggle="tooltip" title="{{ __('Create') }}">
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
                                    <th>{{ __('Branch') }}</th>
                                    <th>{{ __('City') }}</th>
                                    <th>{{ __('Country') }}</th>
                                    <th width="200px">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($branchData as $branch)
                                    <tr>
                                        <td>{{ !empty($branch->branch_name) ? $branch->branch_name : '' }}</td>
                                        <td>{{ !empty($branch->city) ? $branch->city : '' }}</td>
                                        <td>{{ !empty($branch->country) ? $branch->country : '' }}</td>

                                        <td class="Action">
                                            <span>
                                                @permission('courier branch show')
                                                    <div class="action-btn me-2">
                                                        <a class="mx-3 bg-warning btn btn-sm  align-items-center"
                                                            data-url="{{ route('courier.branch.show', ['branchId' => $branch->id]) }}"
                                                            data-ajax-popup="true" data-size="md" data-bs-toggle="tooltip"
                                                            title="" data-title="{{ __('Branch Details') }}"
                                                            data-bs-original-title="{{ __('view') }}">
                                                            <i class="ti ti-eye text"></i>
                                                        </a>
                                                    </div>
                                                @endpermission

                                                @permission('courier branch edit')
                                                    <div class="action-btn me-2">
                                                        <a class="mx-3 btn bg-info btn-sm  align-items-center"
                                                            data-url="{{ route('courier.branch.edit', ['branchId' => $branch->id]) }}"
                                                            data-ajax-popup="true" data-size="md" data-bs-toggle="tooltip"
                                                            title="" data-title="{{ __('Edit Branch') }}"
                                                            data-bs-original-title="{{ __('Edit') }}">
                                                            <i class="ti ti-pencil text-white"></i>
                                                        </a>
                                                    </div>
                                                @endpermission

                                                @permission('courier branch delete')
                                                    <div class="action-btn">
                                                        {{ Form::open(['route' => ['courier.branch.delete', $branch->id], 'class' => 'm-0']) }}
                                                        @method('DELETE')
                                                        <a class="mx-3 btn btn-sm  bg-danger align-items-center bs-pass-para show_confirm"
                                                            data-bs-toggle="tooltip" title=""
                                                            data-bs-original-title="Delete" aria-label="Delete"
                                                            data-confirm="{{ __('Are You Sure?') }}"
                                                            data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                            data-confirm-yes="delete-form-{{ $branch->id }}"><i
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
