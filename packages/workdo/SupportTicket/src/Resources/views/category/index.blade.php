@extends('layouts.main')
@section('page-title')
    {{ __('Manage Support Category') }}
@endsection

@section('page-breadcrumb')
    {{ __('Support Category') }}
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-3">
            @include('support-ticket::layouts.system_setup')
        </div>
        <div class="col-md-9">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-11">
                            <h5 class="">
                                {{ __('Support Category') }}
                            </h5>
                        </div>
                        <div class="col-1  text-end">
                            @permission('ticketcategory create')
                                <a data-url="{{ route('ticket-category.create') }}" data-ajax-popup="true"
                                    data-bs-toggle="tooltip" title="{{ __('Create') }}" title="{{ __('Create') }}"
                                    data-title="{{ __('Create Support Category') }}" class="btn btn-sm btn-primary">
                                    <i class="ti ti-plus"></i>
                                </a>
                            @endpermission
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-hover">

                        <thead class="thead-light">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">{{ __('Name') }}</th>
                                <th scope="col">{{ __('Color') }}</th>
                                @if (Laratrust::hasPermission('ticketcategory edit') || Laratrust::hasPermission('ticketcategory delete'))
                                    <th class="text-end">{{ __('Action') }}</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($categories as $index => $category)
                                <tr>
                                    <th scope="row">{{ ++$index }}</th>
                                    <td>{{ $category->name }}</td>
                                    <td><span class="badge"
                                            style="background: {{ $category->color }}">&nbsp;&nbsp;&nbsp;</span></td>
                                    @if (Laratrust::hasPermission('ticketcategory edit') || Laratrust::hasPermission('ticketcategory delete'))
                                        <td class="text-end">
                                            @permission('ticketcategory edit')
                                                <div class="action-btn me-2">
                                                    <a class="mx-3 btn btn-sm align-items-center bg-info"
                                                        data-url="{{ route('ticket-category.edit', $category->id) }}"
                                                        data-ajax-popup="true"
                                                        data-title="{{ __('Edit Support Category') }}"
                                                        data-bs-toggle="tooltip" title="{{ __('Edit') }}"
                                                        data-original-title="{{ __('Edit') }}">
                                                        <i class="ti ti-pencil text-white"></i>
                                                    </a>
                                                </div>
                                            @endpermission
                                            @permission('ticketcategory delete')
                                                <div class="action-btn">
                                                    <form method="POST"
                                                        action="{{ route('ticket-category.destroy', $category->id) }}"
                                                        id="user-form-{{ $category->id }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <input name="_method" type="hidden" value="DELETE">
                                                        <button type="button"
                                                            class="mx-3 btn btn-sm d-inline-flex align-items-center show_confirm bg-danger"
                                                            data-bs-toggle="tooltip" title="{{__('Delete')}}">
                                                            <span class="text-white"> <i class="ti ti-trash"></i></span>
                                                        </button>
                                                    </form>
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
@endsection
