@extends('layouts.main')
@section('page-title')
    {{ __('Manage Book') }}
@endsection
@section('title')
    {{ __('Book') }}
@endsection
@section('page-breadcrumb')
    {{ __('Book') }}
@endsection
@section('page-action')
    <div>
        <a href="{{ route('book.index') }}" class="btn btn-sm btn-primary btn-icon me-1" data-bs-toggle="tooltip"
            title="{{ __('List View') }}">
            <i class="ti ti-list text-white"></i>
        </a>
        @permission('book create')
            <a class="btn btn-sm btn-primary" data-ajax-popup="true" data-size="md" data-title="{{ __('Create Book') }}"
                data-url="{{ route('book.create') }}" data-bs-toggle="tooltip" data-bs-original-title="{{ __('Create') }}">
                <i class="ti ti-plus"></i>
            </a>
        @endpermission
    </div>
@endsection
@section('content')
    <div class="row mb-4 project-wrp d-flex">
        @isset($books)
            @foreach ($books as $book)
                @php
                    $articleCount = Workdo\Internalknowledge\Entities\Article::where('book', $book->id)->count();
                @endphp
                <div class="col-xxl-3 col-xl-4 col-sm-6 col-12 ">
                    <div class="project-card">
                        <div class="project-card-inner">
                            <div class="project-card-header d-flex justify-content-end">
                                <button type="button"
                                    class="btn btn-light dropdown-toggle d-flex align-items-center justify-content-center"
                                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="ti ti-dots-vertical text-black"></i>
                                </button>

                                <div class="dropdown-menu dropdown-menu-end pointer">
                                    <a class="dropdown-item cp_link"
                                        data-link="{{ route('book.shared.link', \Illuminate\Support\Facades\Crypt::encrypt($book->id)) }}"
                                        data-title="{{ __('Copy') }}">
                                        <i class="ti ti-file"></i> <span class="ms-2">{{ __('Copy') }}</span>
                                    </a>
                                    @permission('book show')
                                        <a href="{{ route('book.show', $book->id) }}" class="dropdown-item"
                                            data-title="{{ __('Show Book') }}">
                                            <i class="ti ti-eye"></i> <span class="ms-2">{{ __('View') }}</span>
                                        </a>
                                    @endpermission
                                    @permission('book edit')
                                        <a class="dropdown-item" data-ajax-popup="true" data-size="lg"
                                            data-title="{{ __('Edit Book') }}" data-url="{{ route('book.edit', [$book->id]) }}">
                                            <i class="ti ti-pencil"></i> <span class="ms-2">{{ __('Edit') }}</span>
                                        </a>
                                    @endpermission
                                    @permission('book delete')
                                        <form id="delete-form-{{ $book->id }}"
                                            action="{{ route('book.destroy', [$book->id]) }}" method="POST">
                                            @csrf
                                            <a href="#"
                                                class="dropdown-item text-danger delete-popup bs-pass-para show_confirm"
                                                data-confirm="{{ __('Are You Sure?') }}"
                                                data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                data-confirm-yes="delete-form-{{ $book->id }}">
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
                                            <a href="@permission('book manage') {{ route('book.show', [$book->id]) }} @endpermission"
                                                title="{{ $book->title }}" class="">{{ $book->title }}</a>

                                        </h2>
                                    </div>
                                    <p>{{ $book->description }}</p>

                                    <div class="d-flex gap-2 align-items-center justify-content-between">
                                        <p class="mb-0"><b>{{ __('Due Date') }}
                                                : </b>{{ company_date_formate($book->created_at) }}</p>
                                        <div class="view-btn d-flex gap-2 align-items-center">
                                            @permission('book manage')
                                                <a class="btn btn-warning" data-bs-toggle="tooltip"
                                                    href="{{ route('book.show', [$book->id]) }}"
                                                    data-bs-original-title="{{ __('View') }}">
                                                    <i class="ti ti-eye text-white"></i>
                                                </a>
                                            @endpermission
                                        </div>
                                    </div>
                                </div>
                                <div class="project-content-bottom flex-wrap d-flex align-items-center justify-content-between gap-2">
                                    <div class="d-flex align-items-center gap-2 user-image">
                                        @php
                                            $userIds = explode(',', $book->user_id);
                                            $users = \App\Models\User::whereIn('id', $userIds)->get();
                                        @endphp
                                        @foreach ($users as $user)
                                            <img alt="image" data-bs-toggle="tooltip" data-bs-placement="top"
                                                title="{{ $user->name }}"
                                                @if (!empty($user->avatar) && file_exists(get_file($user->avatar))) src="{{ get_file($user->avatar) }}"
                                        @else
                                        src="{{ asset('packages/workdo/Internalknowledge/src/Resources/assets/image/avatar.png') }}" @endif
                                                class="rounded-circle" width="25" height="25">
                                        @endforeach

                                        <span class="">{{ __('Members') }}</span>
                                    </div>
                                    <div class="comment d-flex align-items-center gap-2">
                                        <h6 class="mb-0">{{ $articleCount }}</h6>
                                            <span class="">{{ __('ARTICLES') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endisset
        @auth('web')
            @permission('book create')
            <div class="col-xxl-3 col-xl-4 col-sm-6 col-12 ">
                <div class="project-card-inner">
                    <a href="#" class="btn-addnew-project " data-ajax-popup="true" data-size="md"
                        data-title="{{ __('Create New Book') }}" data-url="{{ route('book.create') }}"
                        >
                        <div class="badge bg-primary proj-add-icon">
                            <i class="ti ti-plus"></i>
                        </div>
                        <h6 class="my-2 text-center">{{ __('New Book') }}</h6>
                        <p class="text-muted text-center">{{ __('Click here to add New Book') }}</p>
                    </a>
                </div>
            </div>
        @endpermission
        @endauth
    </div>
    {!! $books->links('vendor.pagination.global-pagination') !!}

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
