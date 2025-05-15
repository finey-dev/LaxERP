@extends('layouts.main')
@section('page-title')
    {{ __('Manage Account Type') }}
@endsection
@section('title')
    {{ __('Account Type') }}
@endsection
@section('page-breadcrumb')
   {{ __('Constant') }},
    {{ __('Account Type') }}
@endsection
@section('page-action')
    @permission('salesaccounttype create')
        <div class="action-btn ms-2">
            <a data-size="md" data-url="{{ route('account_type.create') }}" data-ajax-popup="true"
                data-bs-toggle="tooltip" data-title="{{ __('Create Account Type') }}" title="{{ __('Create') }}"
                class="btn btn-sm btn-primary btn-icon m-1">
                <i class="ti ti-plus"></i>
            </a>
        </div>
    @endpermission
@endsection
@section('filter')
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-3">
            @include('sales::layouts.system_setup')
        </div>
        <div class="col-sm-9">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive overflow_hidden">
                        <table class="table mb-0" >
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col" class="sort" data-sort="name">{{ __('type') }}</th>
                                    @if (Laratrust::hasPermission('salesaccounttype edit') || Laratrust::hasPermission('salesaccounttype delete'))
                                        <th class="text-end">{{ __('Action') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($types as $type)
                                    <tr>
                                        <td class="sorting_1">{{ $type->name }}</td>
                                        @if (Laratrust::hasPermission('salesaccounttype edit') || Laratrust::hasPermission('salesaccounttype delete'))
                                            <td class="action text-end">
                                                @permission('salesaccounttype edit')
                                                    <div class="action-btn me-2 mt-1">
                                                        <a data-size="md"
                                                            data-url="{{ route('account_type.edit', $type->id) }}"
                                                            data-ajax-popup="true" data-bs-toggle="tooltip"
                                                            data-title="{{ __('Edit Account type') }}"
                                                            title="{{ __('Edit') }}"
                                                            class="mx-3 btn btn-sm align-items-center text-white bg-info">
                                                            <i class="ti ti-pencil"></i>
                                                        </a>
                                                    </div>
                                                @endpermission
                                                @permission('salesaccounttype delete')
                                                    <div class="action-btn">
                                                        {!! Form::open(['method' => 'DELETE', 'route' => ['account_type.destroy', $type->id]]) !!}
                                                        <a href="#!"
                                                            class="mx-3 btn btn-sm align-items-center text-white show_confirm bg-danger"
                                                            data-bs-toggle="tooltip" title="{{__('Delete')}}" data-confirm="{{ __('Are You Sure?') }}"
                                                            data-text="{{ __('This action can not be undone. Do you want to continue?') }}">
                                                            <i class="ti ti-trash"></i>
                                                        </a>
                                                        {!! Form::close() !!}
                                                    </div>
                                                @endpermission
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

