@extends('layouts.main')
@section('page-title')
    {{ __('Manage Document Type') }}
@endsection
@section('page-breadcrumb')
    {{ __('Document Type') }}
@endsection

@section('page-action')
    <div>
        @permission('visitor document type create')
            <a class="btn btn-sm btn-primary btn-icon " data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Create') }}"
                data-ajax-popup="true" data-title="{{ __('Create Document Type') }}"
                data-url="{{ route('visitors-document-type.create') }}"><i class="text-white ti ti-plus"></i></a>
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
                                    <th>{{ __('Document Type') }}</th>
                                    @if (Laratrust::hasPermission('visitor document type edit') || Laratrust::hasPermission('visitor document type delete'))
                                        <th width="200px">{{ __('Action') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($document_types as $document)
                                    <tr>
                                        <td>{{ $document->name }}</td>
                                        @if (Laratrust::hasPermission('visitor document type edit') || Laratrust::hasPermission('visitor document type delete'))
                                            <td class="Action">
                                                <span>
                                                    @permission('visitor document type edit')
                                                        <div class="action-btn me-2">
                                                            <a class="mx-3 btn btn-sm bg-info align-items-center"
                                                                data-bs-toggle="tooltip" data-bs-placement="top"
                                                                title="{{ __('Edit') }}" data-ajax-popup="true"
                                                                data-size="md" data-title="{{ __('Edit Document Type') }}"
                                                                data-url="{{ route('visitors-document-type.edit', $document->id) }}"><i
                                                                    class="text-white ti ti-pencil"></i></a>
                                                        </div>
                                                    @endpermission

                                                    @permission('visitor document type delete')
                                                        <div class="action-btn">
                                                            {{ Form::open(['route' => ['visitors-document-type.destroy', $document->id], 'class' => 'm-0']) }}
                                                            @method('DELETE')
                                                            <a href="#"
                                                                class="mx-3 btn btn-sm align-items-center bs-pass-para show_confirm bg-danger"
                                                                data-bs-toggle="tooltip" title=""
                                                                data-bs-original-title="Delete" aria-label="Delete"
                                                                data-confirm="{{ __('Are You Sure?') }}"
                                                                data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                                data-confirm-yes="delete-form-{{ $document->id }}"><i
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
