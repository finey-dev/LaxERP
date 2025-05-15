@extends('layouts.main')
@section('page-title')
    {{ __('Manage Account Industry') }}
@endsection
@section('title')
    <div class="page-header-title">
        <h4 class="m-b-10">{{ __('Account Industry') }}</h4>
    </div>
@endsection
@section('page-breadcrumb')
    {{ __('Constant') }},
   {{ __('Account Industry') }}
@endsection
@section('page-action')
    @permission('accountindustry create')
        <div class="action-btn ms-2">
            <a data-size="md" data-url="{{ route('account_industry.create') }}" data-ajax-popup="true"
                data-bs-toggle="tooltip" data-title="{{ __('Create Account Industry') }}" title="{{ __('Create') }}"
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
                        <table class="table mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col" class="sort" data-sort="name">{{ __('industry') }}</th>
                                    @if (Laratrust::hasPermission('accountindustry edit') || Laratrust::hasPermission('accountindustry delete'))
                                        <th class="text-end">{{ __('Action') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($industrys as $industry)
                                    <tr>
                                        <td class="sorting_1">{{ $industry->name }}</td>
                                        @if (Laratrust::hasPermission('accountindustry edit') || Laratrust::hasPermission('accountindustry delete'))
                                            <td class="action text-end">
                                                @permission('accountindustry edit')
                                                    <div class="action-btn me-2 mt-1">
                                                        <a data-size="md"
                                                            data-url="{{ route('account_industry.edit', $industry->id) }}"
                                                            data-ajax-popup="true" data-bs-toggle="tooltip"
                                                            data-title="{{ __('Edit Account Industry') }}"
                                                            title="{{ __('Edit') }}"
                                                            class="mx-3 btn btn-sm align-items-center text-white bg-info">
                                                            <i class="ti ti-pencil"></i>
                                                        </a>
                                                    </div>
                                                @endpermission
                                                @permission('accountindustry delete')
                                                    <div class="action-btn">
                                                        {!! Form::open(['method' => 'DELETE', 'route' => ['account_industry.destroy', $industry->id]]) !!}
                                                        <a href="#!"
                                                            class="mx-3 btn btn-sm   align-items-center text-white show_confirm bg-danger"
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
