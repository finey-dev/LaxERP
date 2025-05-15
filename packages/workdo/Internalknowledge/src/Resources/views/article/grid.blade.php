@extends('layouts.main')
@section('page-title')
    {{ __('Manage Article') }}
@endsection
@section('title')
    {{ __('Article') }}
@endsection
@section('page-breadcrumb')
    {{ __('Article') }}
@endsection
@section('page-action')
    <div>
        <a href="{{ route('article.index') }}" class="btn btn-sm btn-primary btn-icon me-1" data-bs-toggle="tooltip"
            title="{{ __('List View') }}">
            <i class="ti ti-list text-white"></i>
        </a>
        @permission('article create')
            <a class="btn btn-sm btn-primary" data-ajax-popup="true" data-size="md" data-title="{{ __('Create New Article') }}"
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
    <div class="row mb-4 project-wrp d-flex">
        @isset($articles)
            @foreach ($articles as $article)
                <div class="col-xxl-3 col-xl-4 col-sm-6 col-12 ">
                    <div class="project-card">
                        <div class="project-card-inner">
                            <div class="project-card-header d-flex justify-content-between">
                                <p class="badge bg-success mb-0 d-flex align-items-center">{{ $article->type }}
                                </p>
                                <button type="button"
                                    class="btn btn-light dropdown-toggle d-flex align-items-center justify-content-center"
                                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="ti ti-dots-vertical text-black"></i>
                                </button>

                                <div class="dropdown-menu dropdown-menu-end pointer">
                                    <a class="dropdown-item cp_link"
                                        data-link="{{ route('article.shared.link', \Illuminate\Support\Facades\Crypt::encrypt($article->id)) }}"
                                        data-title="{{ __('Copy') }}">
                                        <i class="ti ti-file"></i> <span class="ms-2">{{ __('Copy') }}</span>
                                    </a>
                                    @permission('article duplicate')
                                        <a class="dropdown-item" data-ajax-popup="true" data-size="md"
                                            data-title="{{ __('Duplicate') }}"
                                            data-url="{{ route('article.copy', [$article->id]) }}">
                                            <i class="ti ti-copy"></i> <span class="ms-2">{{ __('Duplicate') }}</span>
                                        </a>
                                    @endpermission
                                    @permission('article show')
                                        <a class="dropdown-item"
                                            href="{{ route('article.shared.link', \Illuminate\Support\Facades\Crypt::encrypt($article->id)) }}"
                                            data-title="{{ __('View') }}">
                                            <i class="ti ti-eye"></i> <span class="ms-2">{{ __('View') }}</span>
                                        </a>
                                    @endpermission
                                    @permission('article edit')
                                        <a class="dropdown-item" data-ajax-popup="true" data-size="md"
                                            data-title="{{ __('Edit Article') }}"
                                            data-url="{{ route('article.edit', [$article->id]) }}">
                                            <i class="ti ti-pencil"></i> <span class="ms-2">{{ __('Edit') }}</span>
                                        </a>
                                    @endpermission
                                    @permission('article delete')
                                        <form id="delete-form-{{ $article->id }}"
                                            action="{{ route('article.destroy', [$article->id]) }}" method="POST">
                                            @csrf
                                            <a href="#"
                                                class="dropdown-item text-danger delete-popup bs-pass-para show_confirm"
                                                data-confirm="{{ __('Are You Sure?') }}"
                                                data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                data-confirm-yes="delete-form-{{ $article->id }}">
                                                <i class="ti ti-trash"></i> <span class="ms-2">{{ __('Delete') }}</span>
                                            </a>
                                            @method('DELETE')
                                        </form>
                                    @endpermission
                                </div>
                            </div>
                            <div class="project-card-content">
                                <div class="project-content-top">
                                    <div class="user-info  d-flex align-items-center">
                                        <h2 class="h5 mb-0">
                                            <a href="{{ route('article.shared.link', \Illuminate\Support\Facades\Crypt::encrypt($article->id)) }}"
                                                title="{{ $article->title }}" class="">{{ $article->title }}
                                            </a>

                                        </h2>
                                    </div>
                                    <p>{{ $article->description }}</p>

                                    <div class="d-flex gap-2 align-items-center justify-content-between">
                                        <p class="mb-0"><b>{{ __('Due Date') }}
                                                : </b>{{ company_date_formate($article->created_at) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endisset
        @auth('web')
            @permission('article create')
                <div class="col-xxl-3 col-xl-4 col-sm-6 col-12 ">
                    <div class="project-card-inner">
                        <a href="#" class="btn-addnew-project " data-ajax-popup="true" data-size="md"
                            data-title="{{ __('Create New Article') }}" data-url="{{ route('article.create') }}">
                            <div class="badge bg-primary proj-add-icon">
                                <i class="ti ti-plus"></i>
                            </div>
                            <h6 class="my-2 text-center">{{ __('New Article') }}</h6>
                            <p class="text-muted text-center">{{ __('Click here to add New Article') }}</p>
                        </a>
                    </div>
                </div>
            @endpermission
        @endauth
    </div>
    {!! $articles->links('vendor.pagination.global-pagination') !!}
@endsection

@push('scripts')
    <script>
        $(document).on("click", ".cp_link", function() {
            var value = $(this).attr('data-link');
            var $temp = $("<input>");
            $("body").append($temp);
            $temp.val(value).select();
            document.execCommand("copy");
            $temp.remove();
            toastrs('success', '{{ __('Link Copy on Clipboard') }}', 'success')
        });
    </script>
@endpush
