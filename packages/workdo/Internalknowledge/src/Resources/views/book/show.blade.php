@extends('layouts.main')
@section('page-title')
    {{ __('Manage Article') }}
@endsection
@section('page-breadcrumb')
    {{ __('Article') }}
@endsection
@section('page-action')
    <div class="d-flex">
        <a href="{{ route('article.grid') }}" class="btn btn-sm btn-primary me-2"
            data-bs-toggle="tooltip"title="{{ __('Grid View') }}">
            <i class="ti ti-layout-grid text-white"></i>
        </a>
        @permission('article create')
            <a class="btn btn-sm btn-primary" data-ajax-popup="true" data-size="lg" data-title="{{ __('Create New Article') }}"
                data-url="{{ route('article.create') }}" data-bs-toggle="tooltip" data-bs-original-title="{{ __('Create') }}">
                <i class="ti ti-plus"></i>
            </a>
        @endpermission
    </div>
@endsection
@push('css')
    <link href="{{ asset('assets/js/plugins/summernote-0.8.18-dist/summernote-lite.min.css') }}" rel="stylesheet">
@endpush
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table mb-0 pc-dt-simple" id="assets">
                            <thead>
                                <tr>
                                    <th> {{ __('Book') }}</th>
                                    <th> {{ __('Title') }}</th>
                                    <th> {{ __('Description') }}</th>
                                    <th> {{ __('Type') }}</th>
                                    @if (Laratrust::hasPermission('article duplicate') ||
                                            Laratrust::hasPermission('article edit') ||
                                            Laratrust::hasPermission('article delete'))
                                        <th width="10%"> {{ __('Action') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($articles as $article)
                                    <tr class="font-style">
                                        <td>{{ $article->book_name->title }}</td>
                                        <td>{{ $article->title }}</td>
                                        <td>{{ substr($article->description, 0, 50) }}
                                            @if (strlen($article->description) > 50)
                                                ...
                                            @endif
                                        </td>
                                        <td>
                                            @if ($article->type == 'mindmap')
                                                <span class="badge bg-primary p-2 px-3"
                                                    style="width: 90px;">{{ __($article->type) }}</span>
                                            @elseif($article->type == 'document')
                                                <span class="badge bg-success p-2 px-3"
                                                    style="width: 90px;">{{ __($article->type) }}</span>
                                            @endif
                                        </td>
                                        @if (Laratrust::hasPermission('article duplicate') ||
                                                Laratrust::hasPermission('article edit') ||
                                                Laratrust::hasPermission('article delete'))
                                            <td class="Action">
                                                <span>
                                                    <div class="action-btn me-2">
                                                        <a href="#"
                                                            class="mx-3 btn btn-sm d-inline-flex align-items-center text-white cp_link bg-primary"
                                                            data-link="{{ route('article.shared.link', \Illuminate\Support\Facades\Crypt::encrypt($article->id)) }}"
                                                            data-bs-toggle="tooltip" title="{{ __('Copy') }}"
                                                            data-original-title="{{ __('Copy') }}">
                                                            <span class="btn-inner--icon text-white"><i
                                                                    class="ti ti-file"></i></span>
                                                        </a>
                                                    </div>
                                                    @permission('article duplicate')
                                                        <div class="action-btn me-2">
                                                            <a data-size="md"
                                                                data-url="{{ route('article.copy', [$article->id]) }}"
                                                                data-ajax-popup="true" data-title="{{ __('Duplicate') }}"
                                                                class="mx-3 btn btn-sm align-items-center bg-secondary"
                                                                data-bs-toggle="tooltip" data-bs-placement="top"
                                                                title="{{ __('Duplicate') }}"><i
                                                                    class="ti ti-copy text-white"></i></a>
                                                        </div>
                                                    @endpermission
                                                    @permission('article edit')
                                                        <div class="action-btn me-2">
                                                            <a class="mx-3 btn btn-sm align-items-center bg-info"
                                                                data-url="{{ route('article.edit', $article->id) }}"
                                                                data-ajax-popup="true" data-size="lg" data-bs-toggle="tooltip"
                                                                title="{{ __('Edit') }}"
                                                                data-title="{{ __('Edit Article') }}">
                                                                <i class="ti ti-pencil text-white"></i>
                                                            </a>
                                                        </div>
                                                    @endpermission
                                                    @permission('article delete')
                                                        <div class="action-btn">
                                                            {{ Form::open(['route' => ['article.destroy', $article->id], 'class' => 'm-0']) }}
                                                            @method('DELETE')
                                                            <a class="mx-3 btn btn-sm  align-items-center bs-pass-para show_confirm bg-danger"
                                                                data-bs-toggle="tooltip" title=""
                                                                data-bs-original-title="Delete" aria-label="Delete"
                                                                data-confirm="{{ __('Are You Sure?') }}"
                                                                data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                                data-confirm-yes="delete-form-{{ $article->id }}">
                                                                <i class="ti ti-trash text-white text-white"></i>
                                                            </a>
                                                            {{ Form::close() }}
                                                        </div>
                                                    @endpermission
                                                </span>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            $('.cp_link').on('click', function() {
                var value = $(this).attr('data-link');
                var $temp = $("<input>");
                $("body").append($temp);
                $temp.val(value).select();
                document.execCommand("copy");
                $temp.remove();
                toastrs('success', '{{ __('Link Copy on Clipboard') }}', 'success')
            });
        });
    </script>
@endpush
