@extends('layouts.main')
@section('page-title')
    {{ __('Manage Compliance Type') }}
@endsection
@section('page-breadcrumb')
    {{ __('Compliance Type') }}
@endsection

@section('page-action')
    <div>
        @permission('visitor compliance type create')
            <a class="btn btn-sm btn-primary btn-icon " data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Create') }}"
                data-ajax-popup="true" data-title="{{ __('Create Compliance Type') }}"
                data-url="{{ route('visitors-compliance-type.create') }}"><i class="text-white ti ti-plus"></i></a>
        @endpermission
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-3">
            @include('visitor-management::layouts.visitor_setup')
        </div>

        <div class="col-sm-9">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table mb-0 ">
                            <thead>
                                <tr>
                                    <th>{{ __('Compliance Type') }}</th>
                                    @if (Laratrust::hasPermission('visitor compliance type edit') ||
                                            Laratrust::hasPermission('visitor compliance type delete'))
                                        <th width="200px">{{ __('Action') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($compliance_types as $compliance)
                                    <tr>
                                        <td>{{ $compliance->name }}</td>
                                        @if (Laratrust::hasPermission('visitor compliance type edit') ||
                                                Laratrust::hasPermission('visitor compliance type delete'))
                                            <td class="Action">
                                                <span>
                                                    @permission('visitor compliance type edit')
                                                        <div class="action-btn me-2">
                                                            <a class="mx-3 btn btn-sm bg-info align-items-center"
                                                                data-bs-toggle="tooltip" data-bs-placement="top"
                                                                title="{{ __('Edit') }}" data-ajax-popup="true"
                                                                data-size="md" data-title="{{ __('Edit Compliance Type') }}"
                                                                data-url="{{ route('visitors-compliance-type.edit', $compliance->id) }}"><i
                                                                    class="text-white ti ti-pencil"></i></a>
                                                        </div>
                                                    @endpermission

                                                    @permission('visitor compliance type delete')
                                                        <div class="action-btn">
                                                            {{ Form::open(['route' => ['visitors-compliance-type.destroy', $compliance->id], 'class' => 'm-0']) }}
                                                            @method('DELETE')
                                                            <a href="#"
                                                                class="mx-3 btn btn-sm  bg-danger align-items-center bs-pass-para show_confirm"
                                                                data-bs-toggle="tooltip" title=""
                                                                data-bs-original-title="Delete" aria-label="Delete"
                                                                data-confirm="{{ __('Are You Sure?') }}"
                                                                data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                                data-confirm-yes="delete-form-{{ $compliance->id }}"><i
                                                                    class="text-white ti ti-trash"></i></a>
                                                            {{ Form::close() }}
                                                        </div>
                                                    @endpermission
                                                </span>
                                            </td>
                                        @endif
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
@endsection
