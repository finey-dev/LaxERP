@extends('layouts.main')
@section('page-title')
    {{ __('Manage Files') }}
@endsection
@section('page-breadcrumb')
    {{ __('Files') }}
@endsection
@section('page-action')
    <div class="d-flex">
        <a href="{{ route('files.index') }}" class="btn btn-sm btn-primary me-2" data-bs-toggle="tooltip"
            title="{{ __('List View') }}">
            <i class="ti ti-list text-white"></i>
        </a>
        @if (isset($status) && $status == 1)
            @permission('files create')
                <a class="btn btn-sm btn-primary" data-ajax-popup="true" data-size="md" data-title="{{ __('Create File') }}"
                    data-url="{{ route('files.create') }}" data-bs-toggle="tooltip"
                    data-bs-original-title="{{ __('Create') }}">
                    <i class="ti ti-plus"></i>
                </a>
            @endpermission
        @endif
    </div>
@endsection
@push('css')
    <link href="{{ asset('assets/js/plugins/summernote-0.8.18-dist/summernote-lite.min.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="raw mt-3">
        @if (isset($status) && $status == 1)
            <div class="mt-2" id="multiCollapseExample1">
                <div class="card">
                    <div class="card-body">
                        {{ Form::open(['route' => ['file.grid'], 'method' => 'GET', 'id' => 'file-form']) }}
                        <div class="row row-gap align-items-center justify-content-end">
                            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                <div class="btn-box">
                                    {{ Form::label('file_status', __('Status'), ['class' => 'text-type']) }}
                                    {{ Form::select('file_status', ['' => 'Select status'] + $file_status, isset($_GET['file_status']) ? $_GET['file_status'] : '', ['class' => 'form-control']) }}
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                <div class="btn-box">
                                    {{ Form::label('filesharing_type', __('Share Mode'), ['class' => 'text-type']) }}
                                    {{ Form::select('filesharing_type', ['' => 'Select mode'] + $filesharing_type, isset($_GET['filesharing_type']) ? $_GET['filesharing_type'] : '', ['class' => 'form-control']) }}
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                <div class="btn-box">
                                    {{ Form::label('user', __('User'), ['class' => 'text-type']) }}
                                    {{ Form::select('user', $users, isset($_GET['user']) ? $_GET['user'] : '', ['class' => 'form-control', 'placeholder' => __('Select User')]) }}
                                </div>
                            </div>
                            <div class="col-lg-auto col-md-6 float-end mt-md-4 d-flex">
                                <a href="#" class="btn btn-sm btn-primary me-2"
                                    onclick="document.getElementById('file-form').submit(); return false;"
                                    data-bs-toggle="tooltip" title="{{ __('Apply') }}" id="applyfilter"
                                    data-original-title="{{ __('apply') }}">
                                    <span class="btn-inner--icon"><i class="ti ti-search"></i></span>
                                </a>
                                <a href="{{ route('file.grid') }}" class="btn btn-sm btn-danger" data-toggle="tooltip"
                                    id="clearfilter" data-original-title="{{ __('Reset') }}" data-bs-toggle="tooltip" title="{{ __('Reset') }}">
                                    <span class="btn-inner--icon"><i class="ti ti-trash-off text-white-off"></i></span>
                                </a>
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        @endif
        @if (isset($status) && $status == 1)
            <section class="section">
                <div class="row  d-flex grid">
                    @foreach ($files as $file)
                        @php
                            $user_id = explode(',', $file->user_id);
                            $users = App\Models\User::whereIn('id', $user_id)->get();
                            $filename = basename($file->file_path);
                            $extension = pathinfo($filename, PATHINFO_EXTENSION);
                        @endphp
                        <div class="col-xxl-3  col-xl-4 col-sm-6  All mb-4">
                            <div class="card grid-card manage-card h-100 mb-0">
                                <div class="card-header  p-3 border-0 pb-0">
                                    <div class="d-flex align-items-center justify-content-between mb-3  gap-2">
                                        <a href="#" class="text-white">
                                            @if ($file->file_status == 'Available')
                                                <span class="badge bg-primary p-2 px-3"
                                                    style="width: 90px;">{{ __($file->file_status) }}</span>
                                            @elseif($file->file_status == 'Not Available')
                                                <span class="badge bg-danger p-2 px-3"
                                                    style="width: 90px;">{{ __($file->file_status) }}</span>
                                            @endif
                                        </a>
                                        <div class="card-header-right">
                                            <div class="btn-group card-option">
                                                <button type="button" class="btn dropdown-toggle" data-bs-toggle="dropdown"
                                                    aria-haspopup="true" aria-expanded="false">
                                                    <i class="feather icon-more-vertical"></i>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    @permission('files download')
                                                        @if (check_file($file->file_path))
                                                            <a class="dropdown-item"
                                                                href="{{ get_file($file->file_path) }}" download>
                                                                <i class="ti ti-download"></i> <span
                                                                    class="ms-1">{{ __('Download') }}</span>
                                                            </a>
                                                            <a class="dropdown-item"
                                                                href="{{ get_file($file->file_path) }}" target="_blank">
                                                                <i class="ti ti-crosshair"></i> <span
                                                                    class="ms-1">{{ __('Preview') }}</span>
                                                            </a>
                                                        @endif
                                                    @endpermission
                                                    <a class="dropdown-item"
                                                        id="{{ route('file.shared.link', \Illuminate\Support\Facades\Crypt::encrypt($file->id)) }}"
                                                        onclick="copyToClipboard(this)" data-bs-toggle="tooltip"
                                                        data-title="{{ __('Click To Copy Link') }}"
                                                        title="{{ __('Copy Link') }}">
                                                        <i class="ti ti-file"></i> <span
                                                            class="ms-1">{{ __('Copy Link') }}</span>
                                                    </a>
                                                    @if (Laratrust::hasPermission('files edit') || Laratrust::hasPermission('files delete'))
                                                        @permission('files edit')
                                                            <a class="dropdown-item" data-ajax-popup="true" data-size="md"
                                                                data-title="{{ __('Edit File') }}"
                                                                data-url="{{ route('files.edit', [$file->id]) }}">
                                                                <i class="ti ti-pencil"></i> <span
                                                                    class="ms-1">{{ __('Edit') }}</span>
                                                            </a>
                                                        @endpermission
                                                        @permission('files delete')
                                                            <form id="delete-form-{{ $file->id }}"
                                                                action="{{ route('files.destroy', [$file->id]) }}"
                                                                method="POST">
                                                                @csrf
                                                                <a href="#"
                                                                    class="dropdown-item text-danger delete-popup bs-pass-para show_confirm"
                                                                    data-confirm="{{ __('Are You Sure?') }}"
                                                                    data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                                    data-confirm-yes="delete-form-{{ $file->id }}">
                                                                    <i class="ti ti-trash"></i> <span
                                                                        class="ms-1">{{ __('Delete') }}</span>
                                                                </a>
                                                                @method('DELETE')
                                                            </form>
                                                        @endpermission
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="manage-content d-flex h-100 flex-column p-3  pt-0">
                                    <div class="manage-card-image">
                                        @if($extension == 'pdf')
                                            <img src="{{ asset('images/pdf.png') }}"
                                            alt="PDF File" id="file_path" class="card-img"
                                            style="height: 200px; object-fit: cover;">
                                        @elseif($extension == 'mp3')
                                            <img src="{{ asset('images/mp3.png') }}"
                                            alt="PDF File" id="file_path" class="card-img"
                                            style="height: 200px; object-fit: cover;">
                                        @elseif($extension == 'mp4')
                                            <img src="{{ asset('images/mp4.png') }}"
                                            alt="PDF File" id="file_path" class="card-img"
                                            style="height: 200px; object-fit: cover;">
                                        @else
                                            <img src="{{ isset($file->file_path) && !empty($file->file_path) && check_file($file->file_path) ? get_file($file->file_path) : asset('packages/workdo/FileSharing/src/Resources/assets/upload/thumbnail-not-found.png') }}"
                                            alt="file_path" id="file_path" class="card-img"
                                            style=" height: 200px; object-fit: cover;">
                                        @endif
                                    </div>
                                    <div class="card mb-0 h-100 mt-3">
                                        <div class="card-body p-3">
                                            <div class="pb-2 border-1 border-bottom mb-2">
                                                <p class="text-muted text-sm mb-1">{{ __('File Name') }}</p>
                                                <h6 class="mb-0">{{ $filename }}</h6>
                                            </div>
                                            <p class="text-muted text-sm mb-1">{{ __('Users') }}</p>
                                            <h6 class="mb-0 user-group">
                                                @foreach ($users as $user)
                                                    <img alt="image" data-bs-toggle="tooltip" data-bs-placement="top"
                                                        title="{{ $user->name }}"
                                                        @if (!empty($user->avatar) && file_exists(get_file($user->avatar))) src="{{ get_file($user->avatar) }}"
                                                          @else
                                                              src="{{ asset('packages/workdo/FileSharing/src/Resources/assets/img/avatar.png') }}" @endif
                                                        class="rounded-circle" width="25" height="25">
                                                @endforeach
                                            </h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    <div class="col-xxl-3 col-sm-4 col-sm-6 mb-4">
                        <a data-url="{{ route('files.create') }}" class="btn-addnew-project border-primary"
                            data-ajax-popup="true" data-size="md" data-title="{{ __('Create File') }}">
                            <div class="badge bg-primary proj-add-icon">
                                <i class="ti ti-plus"></i>
                            </div>
                            <h6 class="my-2 text-center">{{ __('New Files') }}</h6>
                            <p class="text-muted text-center">{{ __('Click here to add New Files') }}</p>
                        </a>
                    </div>
                </div>
            </section>
            {!! $files->links('vendor.pagination.global-pagination') !!}
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        function copyToClipboard(element) {
            var copyText = element.id;
            document.addEventListener('copy', function(e) {
                e.clipboardData.setData('text/plain', copyText);
                e.preventDefault();
            }, true);
            document.execCommand('copy');
            toastrs('success', 'Link Copy on Clipboard', 'success');
        }
    </script>
@endpush
