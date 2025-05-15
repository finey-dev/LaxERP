@extends('layouts.main')

@section('page-title')
    {{ __('Manage Visit Purpose') }}
@endsection

@section('page-breadcrumb')
    {{ __('Visit Purpose') }}
@endsection

@push('css')
    @include('layouts.includes.datatable-css')
@endpush

@section('page-action')
    <div>
        @permission('reason create')
            <a class="btn btn-sm btn-primary" data-ajax-popup="true" data-size="md"
                data-title="{{ __('Create Visit Purpose') }}" data-url="{{ route('visit-reason.create') }}"
                data-bs-toggle="tooltip" data-bs-original-title="{{ __('Create') }}">
                <i class="ti ti-plus"></i>
            </a>
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
                                    <th>{{ __('Visit Purpose') }}</th>
                                    @if (Laratrust::hasPermission('reason edit') || Laratrust::hasPermission('reason delete'))
                                        <th width="200px">{{ __('Action') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($visit_reason as $reason)
                                    <tr>
                                        <td>{{ $reason->reason }}</td>
                                        @if (Laratrust::hasPermission('reason edit') || Laratrust::hasPermission('reason delete'))
                                            <td class="Action">
                                                <span>
                                                    @permission('reason edit')
                                                        <div class="action-btn me-2">
                                                            <a class="mx-3 btn btn-sm bg-info align-items-center"
                                                                data-bs-toggle="tooltip" data-bs-placement="top"
                                                                title="{{ __('Edit') }}" data-ajax-popup="true"
                                                                data-size="md" data-title="{{ __('Edit Visit Purpose') }}"
                                                                data-url="{{ route('visit-reason.edit', $reason->id) }}"><i
                                                                    class="text-white ti ti-pencil"></i></a>
                                                        </div>
                                                    @endpermission

                                                    @permission('reason delete')
                                                        <div class="action-btn">
                                                            {{ Form::open(['route' => ['visit-reason.destroy', $reason->id], 'class' => 'm-0']) }}
                                                            @method('DELETE')
                                                            <a href="#"
                                                                class="mx-3 btn btn-sm align-items-center bs-pass-para show_confirm bg-danger"
                                                                data-bs-toggle="tooltip" title=""
                                                                data-bs-original-title="Delete" aria-label="Delete"
                                                                data-confirm="{{ __('Are You Sure?') }}"
                                                                data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                                data-confirm-yes="delete-form-{{ $reason->id }}"><i
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


