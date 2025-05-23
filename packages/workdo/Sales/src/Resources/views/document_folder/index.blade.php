@extends('layouts.main')
@section('page-title')
    {{ __('Manage Document Folders') }}
@endsection
@section('title')
    <div class="page-header-title">
        <h4 class="m-b-10">{{ __('Document Folders') }}</h4>
    </div>
@endsection
@section('page-breadcrumb')
   {{ __('Constant') }},
   {{ __('Document Folders') }}
@endsection
@section('page-action')
    @permission('documentfolder create')
        <div class="action-btn ms-2">
            <a data-size="md" data-url="{{ route('salesdocument_folder.create') }}" data-ajax-popup="true"
                data-bs-toggle="tooltip" data-title="{{ __('Create Document Folder') }}" title="{{ __('Create') }}"
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
                        <table class="table mb-0 ">
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col" class="sort" data-sort="name">{{ __('Folder Name') }}</th>
                                    <th>{{__('Parent')}}</th>
                                    @if (Laratrust::hasPermission('documentfolder edit') || Laratrust::hasPermission('documentfolder delete'))
                                        <th class="text-end">{{ __('Action') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($folders as $folder)
                                    <tr>
                                        <td class="sorting_1">{{ $folder->name }}</td>
                                        <td class="sorting_1">{{ !empty($folder->parents->name)?$folder->parents->name:'-' }}</td>
                                        @if (Laratrust::hasPermission('documentfolder edit') || Laratrust::hasPermission('documentfolder delete'))
                                            <td class="action text-end">
                                                @permission('documentfolder edit')
                                                    <div class="action-btn me-2 mt-1">
                                                        <a data-size="md"
                                                            data-url="{{ route('salesdocument_folder.edit', $folder->id) }}"
                                                            data-ajax-popup="true" data-bs-toggle="tooltip"
                                                            title="{{ __('Edit') }}" data-title="{{ __('Edit Document Folder') }}"
                                                            class="mx-3 btn btn-sm align-items-center text-white bg-info">
                                                            <i class="ti ti-pencil"></i>
                                                        </a>
                                                    </div>
                                                @endpermission
                                                @permission('documentfolder delete')
                                                    <div class="action-btn">
                                                        {!! Form::open(['method' => 'DELETE', 'route' => ['salesdocument_folder.destroy', $folder->id]]) !!}
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
@push('scrip-page')
    <script>
        $(document).delegate("li .li_title", "click", function(e) {
            $(this).closest("li").find("ul:first").slideToggle(300);
            $(this).closest("li").find(".location_picture_row:first").slideToggle(300);
            if ($(this).find("i").attr('class') == 'glyph-icon simple-icon-arrow-down') {
                $(this).find("i").removeClass("simple-icon-arrow-down").addClass("simple-icon-arrow-right");
            } else {
                $(this).find("i").removeClass("simple-icon-arrow-right").addClass("simple-icon-arrow-down");
            }
        });
    </script>
@endpush
