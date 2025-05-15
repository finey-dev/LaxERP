@extends('layouts.main')

@section('page-title')
    {{ __('Meeting Minute Show') }}
@endsection

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/dropzone.min.css') }}">
    <link href="{{ asset('assets/js/plugins/summernote-0.8.18-dist/summernote-lite.min.css') }}" rel="stylesheet">
    <style>
        .nav-tabs .nav-link-tabs.active {
            background: none;
        }
    </style>
@endpush
@push('scripts')
    <script src="{{ asset('assets/js/plugins/summernote-0.8.18-dist/summernote-lite.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/dropzone-amd-module.min.js') }}"></script>
    <script>
        var scrollSpy = new bootstrap.ScrollSpy(document.body, {
            target: '#useradd-sidenav',
            offset: 300
        })
    </script>
    <script>
        Dropzone.autoDiscover = true;
        myDropzone = new Dropzone("#my-dropzone", {
            url: "{{ route('meeting.minute.file.upload', [$meeting_minute->id]) }}",
            success: function(file, response) {
                location.reload();
                if (response.is_success) {
                    dropzoneBtn(file, response);
                    toastrs('{{ __('Success') }}', 'Attachment Create Successfully!', 'success');
                } else {
                    myDropzone.removeFile(file);
                    toastrs('{{ __('Error') }}', 'File type must be match with Storage setting.', 'error');
                }
            },
            error: function(file, response) {
                myDropzone.removeFile(file);
                if (response.error) {
                    toastrs('{{ __('Error') }}', response.error, 'error');
                } else {
                    toastrs('{{ __('Error') }}', response.error, 'error');
                }
            }
        });
        myDropzone.on("sending", function(file, xhr, formData) {
            formData.append("_token", $('meta[name="csrf-token"]').attr('content'));
            formData.append("meeting_minute_id", {{ $meeting_minute->id }});

        });

        function dropzoneBtn(file, response) {
            var download = document.createElement('a');
            download.setAttribute('href', response.download);
            download.setAttribute('class', "action-btn btn-primary mx-1 mt-1 btn btn-sm d-inline-flex align-items-center");
            download.setAttribute('data-toggle', "tooltip");
            download.setAttribute('data-original-title', "{{ __('Download') }}");
            download.innerHTML = "<i class='fas fa-download'></i>";

            var del = document.createElement('a');
            del.setAttribute('href', response.delete);
            del.setAttribute('class', "action-btn btn-danger mx-1 mt-1 btn btn-sm d-inline-flex align-items-center");
            del.setAttribute('data-toggle', "tooltip");
            del.setAttribute('data-original-title', "{{ __('Delete') }}");
            del.innerHTML = "<i class='ti ti-trash'></i>";

            del.addEventListener("click", function(e) {
                e.preventDefault();
                e.stopPropagation();
                if (confirm("Are you sure ?")) {
                    var btn = $(this);
                    $.ajax({
                        url: btn.attr('href'),
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        type: 'DELETE',
                        success: function(response) {
                            if (response.is_success) {
                                btn.closest('.dz-image-preview').remove();
                            } else {
                                toastrs('{{ __('Error') }}', response.error, 'error');
                            }
                        },
                        error: function(response) {
                            response = response.responseJSON;
                            if (response.is_success) {
                                toastrs('{{ __('Error') }}', response.error, 'error');
                            } else {
                                toastrs('{{ __('Error') }}', response.error, 'error');
                            }
                        }
                    })
                }
            });

            var html = document.createElement('div');
            html.setAttribute('class', "text-center mt-10");
            html.appendChild(download);
            html.appendChild(del);

            file.previewTemplate.appendChild(html);
        }
        @if ($meeting_minute->files)

            @foreach ($meeting_minute->files as $file)
            @endforeach
        @endif
    </script>
    <script>
        $(document).on('click', '#comment_submit', function(e) {
            var curr = $(this);

            var comment = $.trim($("#form-comment textarea[name='comment']").val());
            $.ajax({
                url: $("#form-comment").attr('data-action'),
                data: {
                    comment: comment,
                    "_token": "{{ csrf_token() }}",
                },
                type: 'POST',
                success: function(data) {

                    toastrs('{{ __('Success') }}', 'Comment Create Successfully!', 'success');


                    setTimeout(function() {
                        location.reload();
                    }, 500)
                    data = JSON.parse(data);
                    var html = "<div class='list-group-item px-0'>" +
                        "                    <div class='row align-items-center'>" +
                        "                        <div class='col-auto'>" +
                        "                            <a href='#' class='avatar avatar-sm rounded-circle ms-2'>" +
                        "                                <img src=" + data.default_img +
                        " alt='' class='avatar-sm rounded-circle'>" +
                        "                            </a>" +
                        "                        </div>" +
                        "                        <div class='col ml-n2'>" +
                        "                            <p class='d-block h6 text-sm font-weight-light mb-0 text-break'>" +
                        data.comment + "</p>" +
                        "                            <small class='d-block'>" + data.current_time +
                        "</small>" +
                        "                        </div>" +
                        "                        <div class='action-btn bg-danger me-4'><div class='col-auto'><a href='#' class='mx-3 btn btn-sm  align-items-center delete-comment' data-url='" +
                        data.deleteUrl + "'><i class='ti ti-trash text-white'></i></a></div></div>" +
                        "                    </div>" +
                        "                </div>";

                    $("#comments").prepend(html);
                    $("#form-comment textarea[name='comment']").val('');
                    load_task(curr.closest('.task-id').attr('id'));
                    toastrs('is_success', 'Comment Added Successfully!');
                },
                error: function(data) {
                    toastrs('error', 'Some Thing Is Wrong!');
                }
            });

        });


        $(document).on("click", ".delete-comment", function() {
            var btn = $(this);

            $.ajax({
                url: $(this).attr('data-url'),
                type: 'DELETE',
                dataType: 'JSON',
                data: {
                    comment: comment,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(data) {
                    load_task(btn.closest('.task-id').attr('id'));
                    toastrs('success', 'Comment Deleted Successfully!');
                    btn.closest('.list-group-item').remove();
                },
                error: function(data) {
                    data = data.responseJSON;
                    if (data.message) {
                        toastrs('error', data.message);
                    } else {
                        toastrs('error', 'Some Thing Is Wrong!');
                    }
                }
            });
        });
    </script>
@endpush
@section('page-breadcrumb')
    {{ __('Meeting Minutes') }},
    {{ __('Show') }}
@endsection
@section('page-action')
@endsection
@section('content')
    <div class="row">
        <div class="col-sm-12 ">
            <div class="row">
                <div class="col-md-3">
                    <div class="card sticky-top" style="top:30px">
                        <div class="list-group list-group-flush" id="useradd-sidenav">
                            <a href="#general"
                                class="list-group-item list-group-item-action border-0 active">{{ __('General') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                            <a href="#meeting-description"
                                class="list-group-item list-group-item-action border-0">{{ __('Meeting Description') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                            <a href="#attachments"
                                class="list-group-item list-group-item-action border-0">{{ __('Attachments') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                            <a href="#comments" class="list-group-item list-group-item-action border-0">{{ __('Comment') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                            <a href="#notes" class="list-group-item list-group-item-action border-0">{{ __('Notes') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                            <a href="#tasks" class="list-group-item list-group-item-action border-0">{{ __('Tasks') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-9">
                    <div id="general">
                        <div class="row">
                            <div class="col-xxl-7">
                                <div class="row">
                                    <div class="col-lg-4 col-6">
                                        <div class="card report_card">
                                            <div class="card-body">
                                                <div class="theme-avtar bg-success badge">
                                                    <i class="ti ti-paperclip  text-white"></i>
                                                </div>
                                                <p class="text-dark h6 mt-4 mb-4">{{ __('Attachments') }}</p>
                                                <h3 class="mb-0">0</h3>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-6">
                                        <div class="card report_card">
                                            <div class="card-body">
                                                <div class="theme-avtar bg-info badge">
                                                    <i class="ti ti-eye  text-white"></i>
                                                </div>
                                                <p class="text-dark h6 mt-4 mb-4">{{ __('Comments') }}</p>
                                                <h3 class="mb-0">0</h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-6">
                                        <div class="card report_card">
                                            <div class="card-body">
                                                <div class="theme-avtar bg-warning badge">
                                                    <i class="ti ti-file-invoice  text-white"></i>
                                                </div>
                                                <p class="text-dark h6 mt-4 mb-4">{{ __('Notes') }}</p>
                                                <h3 class="mb-0">0</h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xxl-5">
                                <div class="card">
                                    <div class="card-body" style="min-height: 190px;">
                                        <div class="row mt-2 mb-0 align-items-center">
                                            <div class="col-sm-4 h6">{{ __('Subject') }}</div>
                                            <div class="col-sm-8"> {{ $meeting->subject }}</div>
                                            <div class="col-sm-4 h6">{{ __('Module') }}</div>
                                            @php
                                                $submodule = Workdo\MeetingHub\Entities\MeetingHubModule::find($meeting->sub_module);
                                            @endphp
                                            <div class="col-sm-8">{{ $submodule->submodule }}</div>
                                            @if ($submodule->submodule == 'Lead')
                                                <div class="col-sm-4 h6">{{ __('Lead') }}</div>
                                                <div class="col-sm-8">{{ $meetingleadname }}</div>
                                            @endif
                                            <div class="col-sm-4 h6">{{ __('User') }}</div>
                                            <div class="col-sm-8">{{ $meetinglogusers }}</div>
                                            <div class="col-sm-4 h6">{{ __('Type') }}</div>
                                            <div class="col-sm-8">{{ $meeting->meetingtype->name }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xxl-12">
                                <div class="card">
                                    <div class="card-body ">
                                        <div class="row mt-2 mb-0 align-items-center">
                                            <div class="col-lg-6 col-md-12">
                                                <div class="row p-0">
                                                    <div class="col-md-3 h6 col-4 mb-3">
                                                        {{ __('Start time') }}
                                                    </div>
                                                    <div class="col-sm-9 col-8 mb-3">
                                                        {{ $meeting_minute->call_start_time }}
                                                    </div>
                                                    <div class="col-md-3 col-4 h6 mb-3">
                                                        {{ __('Duration') }}
                                                    </div>
                                                    <div class="col-sm-9 col-8 mb-3">
                                                        {{ $meeting_minute->duration }}
                                                    </div>
                                                    <div class="col-md-3 h6 col-4 mb-3">
                                                        {{ __('Priority') }}
                                                    </div>
                                                    <div class="col-sm-9 mb-3 col-8 mb-3">
                                                        <select name="priority" id="priority" class="form-control">
                                                            <option value="">{{ __('Select Priority') }}</option>
                                                            <option value="High"
                                                                @if ($meeting_minute->priority == 'High') selected @endif>
                                                                {{ __('High') }}</option>
                                                            <option value="Medium"
                                                                @if ($meeting_minute->priority == 'Medium') selected @endif>
                                                                {{ __('Medium') }}</option>
                                                            <option value="Low"
                                                                @if ($meeting_minute->priority == 'Low') selected @endif>
                                                                {{ __('Low') }}</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3 mt-1 h6 col-4" style="padding-right:0px;">
                                                        {{ __('Completed') }}
                                                    </div>
                                                    <div class="col-sm-9 col-8 ">
                                                        <div class="d-flex radio-check">
                                                            <div class="custom-control custom-radio custom-control-inline">
                                                                <input type="radio" id="completed_yes" value="Yes"
                                                                    name="completed" class="form-check-input"
                                                                    @if ($meeting_minute->completed == 'Yes') checked @endif>
                                                                <label class="form-check-label "
                                                                    for="yes">{{ __('Yes') }}</label>
                                                            </div>
                                                            <div
                                                                class="custom-control custom-radio ms-1 custom-control-inline">
                                                                <input type="radio" id="completed_no" value="No"
                                                                    name="completed" class="form-check-input"
                                                                    @if ($meeting_minute->completed == 'No') checked @endif>
                                                                <label class="form-check-label "
                                                                    for="no">{{ __('No') }}</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-12">
                                                <div class="row p-0">
                                                    <div class="col-md-3 h6 col-4 mb-3">
                                                        {{ __('End time') }}
                                                    </div>
                                                    <div class="col-sm-9 col-8 mb-3">
                                                        {{ $meeting_minute->call_end_time }}
                                                    </div>
                                                    <div class="col-md-3 mt-1 h6 col-4 mb-3">
                                                        {{ __('Note') }}
                                                    </div>
                                                    <div class="col-sm-9 col-8 mb-3">
                                                        {{ $meeting_minute->note }}
                                                    </div>
                                                    <div class="col-md-3 h6 col-4 mb-3">
                                                        {{ __('Status') }}
                                                    </div>
                                                    <div class="col-sm-9 mb-3 col-8 mb-3">
                                                        {{ Form::select('status', $status, isset($_GET['status']) ? $_GET['status'] : '', ['class' => 'form-control']) }}
                                                    </div>
                                                    <div class="col-md-3 mt-1 h6 col-4">
                                                        {{ __('Important') }}
                                                    </div>
                                                    <div class="col-sm-9 col-8 ">
                                                        <div class="d-flex radio-check">
                                                            <div class="custom-control custom-radio custom-control-inline">
                                                                <input type="radio" id="important_yes" value="Yes"
                                                                    name="important" class="form-check-input"
                                                                    checked="checked"
                                                                    @if ($meeting_minute->important == 'Yes') checked @endif>
                                                                <label class="form-check-label "
                                                                    for="yes">{{ __('Yes') }}</label>
                                                            </div>
                                                            <div
                                                                class="custom-control custom-radio ms-1 custom-control-inline">
                                                                <input type="radio" id="important_no" value="No"
                                                                    name="important" class="form-check-input"
                                                                    @if ($meeting_minute->important == 'No') checked @endif>
                                                                <label class="form-check-label "
                                                                    for="no">{{ __('No') }}</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="meeting-description">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5>{{ __('Meeting Description') }}</h5>
                                    </div>
                                    <div class="card-body">
                                        {{ Form::open(['route' => ['meeting.minute.description.store', $meeting_minute->id]]) }}
                                        <div class="form-group mt-3">
                                            <textarea class="tox-target summernote" id="pc_demo1" name="description" rows="8">{!! $meeting_minute->description !!}</textarea>
                                        </div>
                                        @if ($meeting_minute->created_by == creatorId())
                                            <div class="col-md-12 text-end mb-0">
                                                {{ Form::submit(__('Add'), ['class' => 'btn  btn-primary']) }}
                                            </div>
                                        @endif
                                        {!! Form::close() !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="attachments">
                        <div class="row ">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5>{{ __('Attachments') }}</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class=" ">
                                            <div class="col-md-12 dropzone browse-file" id="my-dropzone"></div>
                                        </div>
                                    </div>
                                    <div style="max-height: 500px;overflow-y:auto;">
                                        @foreach ($files as $file)
                                            <div class="px-4 py-3">
                                                <div class="list-group-item ">
                                                    <div class="row align-items-center">
                                                        <div class="col">
                                                            <h6 class="text-sm mb-0">
                                                                <a href="#!">{{ $file->files }}</a>
                                                            </h6>
                                                            <p class="card-text small text-muted">
                                                                {{ number_format(get_size(get_file($file->files)) / 1048576, 2) . ' ' . __('MB') }}

                                                            </p>
                                                        </div>
                                                        <div class="action-btn p-0 w-auto ">
                                                            <a href="{{ get_file($file->files) }}"
                                                                class=" btn btn-sm d-inline-flex align-items-center bg-warning"
                                                                download="" data-bs-toggle="tooltip" title="Download">
                                                                <span class="text-white"><i
                                                                        class="ti ti-download"></i></span>
                                                            </a>
                                                        </div>
                                                        <div class="col-auto p-0 ms-2 action-btn">
                                                            {!! Form::open([
                                                                'method' => 'DELETE',
                                                                'route' => ['meeting.minute.file.delete', $meeting_minute->id, $file->id],
                                                            ]) !!}
                                                            <a href="#!"
                                                                class="btn btn-sm d-inline-flex align-items-center show_confirm bg-danger"
                                                                data-bs-toggle="tooltip" data-bs-placement="top"
                                                                title="{{ __('Delete') }}">
                                                                <span class="text-white"> <i
                                                                        class="ti ti-trash"></i></span>
                                                            </a>
                                                            {!! Form::close() !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="comments">
                        <div class="row pt-2">
                            <div class="col-12">
                                <div class="card">
                                    <div id="comment">
                                        <div class="card-header">
                                            <h5>{{ __('Comments') }}</h5>
                                        </div>
                                        <div class="card-footer">
                                            <div class="col-12 d-flex">
                                                <div class="form-group mb-0 form-send w-100">
                                                    <form method="post" class="card-comment-box" id="form-comment"
                                                        data-action="{{ route('meeting.minute.comment.store', [$meeting_minute->id]) }}">
                                                        <textarea rows="1" class="form-control" name="comment" data-toggle="autosize" placeholder="Add a comment..."
                                                            spellcheck="false" required></textarea>
                                                        <grammarly-extension data-grammarly-shadow-root="true"
                                                            style="position: absolute; top: 0px; left: 0px; pointer-events: none; z-index: 1;"
                                                            class="cGcvT"></grammarly-extension>
                                                        <grammarly-extension data-grammarly-shadow-root="true"
                                                            style="mix-blend-mode: darken; position: absolute; top: 0px; left: 0px; pointer-events: none; z-index: 1;"
                                                            class="cGcvT"></grammarly-extension>
                                                    </form>
                                                </div>
                                                @permission('meetinghub comment create')
                                                    <button id="comment_submit" class="btn btn-send"><i
                                                            class="f-16 text-primary ti ti-brand-telegram">
                                                        </i>
                                                    </button>
                                                @endpermission
                                            </div>
                                        </div>
                                    </div>
                                    <div class="list-group list-group-flush mb-0" id="comments">
                                        @foreach ($comments as $comment)
                                            @php
                                                $user = \App\Models\User::find($comment->user_id);
                                            @endphp
                                            <div class="list-group-item ">
                                                <div class="row align-items-center">
                                                    <div class="col-auto">
                                                        <a href="{{ !empty($user->avatar) ? get_file($user->avatar) : 'avatar.png' }}"
                                                            target="_blank">
                                                            <img src="{{ !empty($user->avatar) ? get_file($user->avatar) : 'avatar.png' }}"
                                                                class="rounded border-2 border border-primary" width="30">
                                                        </a>
                                                    </div>
                                                    <div class="col ml-n2">
                                                        <p class="d-block h6 text-sm font-weight-light mb-0 text-break">
                                                            {{ $comment->comment }}</p>
                                                        <small
                                                            class="d-block">{{ $comment->created_at->diffForHumans() }}</small>
                                                    </div>
                                                    @permission('meetinghub comment delete')
                                                        <div class="col-auto p-0 mx-3 action-btn" style="margin-left: 0.5rem !important">
                                                            {!! Form::open(['method' => 'GET', 'route' => ['meeting.minute.comment.destroy', $comment->id]]) !!}
                                                            <a href="#!"
                                                                class="btn btn-sm d-inline-flex align-items-center show_confirm bg-danger"
                                                                data-bs-toggle="tooltip" data-bs-placement="top"
                                                                title="{{ __('Delete') }}">
                                                                <span class="text-white"> <i class="ti ti-trash"></i></span>
                                                            </a>
                                                            {!! Form::close() !!}
                                                        </div>
                                                    @endpermission
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="notes">
                        <div class="row pt-2">
                            <div class="col-12">
                                <div id="notes">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5>{{ __('Notes') }}</h5>
                                        </div>
                                        <div class="card-body">
                                            {{ Form::open(['route' => ['meeting.minute.note.store', $meeting_minute->id]]) }}
                                            <div class="form-group">
                                                <textarea rows="3" class="form-control" name="note" id="summernote" data-toggle="autosize"
                                                    placeholder="Add a Notes..." spellcheck="false" required></textarea>
                                            </div>
                                            @permission('meetinghub note create')
                                                <div class="col-md-12 text-end mb-0">
                                                    {{ Form::submit(__('Add'), ['class' => 'btn  btn-primary']) }}
                                                </div>
                                            @endpermission
                                            {{ Form::close() }}
                                            <div class="list-group list-group-flush mb-0" id="notes">
                                                @foreach ($notes as $note)
                                                    @php
                                                        $user = \App\Models\User::find($note->user_id);
                                                    @endphp
                                                    <div class="list-group-item ">
                                                        <div class="row align-items-center">
                                                            <div class="col-auto">

                                                                <a href="{{ !empty($user->avatar) ? get_file($user->avatar) : 'avatar.png' }}"
                                                                    target="_blank">
                                                                    <img src="{{ !empty($user->avatar) ? get_file($user->avatar) : 'avatar.png' }}"
                                                                        class="rounded border-2 border border-primary" width="30">
                                                                </a>
                                                            </div>
                                                            <div class="col ml-n2">
                                                                <p
                                                                    class="d-block h6 text-sm font-weight-light mb-0 text-break">
                                                                    {{ $note->note }}</p>
                                                                <small
                                                                    class="d-block">{{ $note->created_at->diffForHumans() }}</small>
                                                            </div>
                                                            @permission('meetinghub note delete')
                                                                <div class="col-auto col-auto p-0 mx-3 action-btn">
                                                                    {!! Form::open(['method' => 'GET', 'route' => ['meeting.minute.note.destroy', $note->id]]) !!}
                                                                    <a href="#!"
                                                                        class="btn btn-sm d-inline-flex align-items-center show_confirm bg-danger"
                                                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                                                        title="{{ __('Delete') }}">
                                                                        <span class="text-white"> <i
                                                                                class="ti ti-trash"></i></span>
                                                                    </a>
                                                                    {!! Form::close() !!}
                                                                </div>
                                                            @endpermission
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="tasks">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h5>{{ __('Tasks') }}</h5>
                                            </div>
                                            <div class="float-end">
                                                @permission('meetingTask create')
                                                    <a class="btn btn-sm btn-primary float-end " data-bs-toggle="tooltip"
                                                        data-bs-placement="top" title="" data-size="md"
                                                        data-url="{{ route('meeting-task.create', $meeting_minute->id) }}"
                                                        data-ajax-popup="true" data-title="{{ __('Add Task') }}"
                                                        data-bs-original-title="{{ __('Add Task') }}">
                                                        <i class="ti ti-plus text-white"></i>
                                                    </a>
                                                @endpermission
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table mb-0 ">
                                                <thead>
                                                    <tr>
                                                        <th>{{ __('Task name') }}</th>
                                                        <th>{{ __('Priority') }}</th>
                                                        <th>{{ __('Date & Time') }}</th>
                                                        <th>{{ __('Status') }}</th>
                                                        @if (Laratrust::hasPermission('meetingTask edit') || Laratrust::hasPermission('meetingTask delete'))
                                                            <th class="text-end">{{ __('Action') }}</th>
                                                        @endif
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse ($tasks as $task)
                                                        <tr>
                                                            <td>{{ $task->name }}</td>
                                                            <td><span
                                                                    class="badge fix_badge @if ($task->priority == 'High') bg-danger @elseif($task->priority == 'Medium') bg-warning @else bg-success @endif  p-2 px-3 ">{{ __($task->priority) }}</span>
                                                            </td>
                                                            <td>{{ company_datetime_formate($task->date . $task->time) }}
                                                            </td>
                                                            <td>
                                                                @if ($task->status == 0)
                                                                    <span
                                                                        class="badge fix_badge p-2 px-3 bg-primary">{{ __(\Workdo\MeetingHub\Entities\MeetingHubMeeting::$statues[$task->status]) }}</span>
                                                                @elseif($task->status == 1)
                                                                    <span
                                                                        class="badge fix_badge p-2 px-3 bg-info">{{ __(\Workdo\MeetingHub\Entities\MeetingHubMeeting::$statues[$task->status]) }}</span>
                                                                @elseif($task->status == 2)
                                                                    <span
                                                                        class="badge fix_badge p-2 px-3 bg-secondary">{{ __(\Workdo\MeetingHub\Entities\MeetingHubMeeting::$statues[$task->status]) }}</span>
                                                                @elseif($task->status == 3)
                                                                    <span
                                                                        class="badge fix_badge p-2 px-3 bg-warning">{{ __(\Workdo\MeetingHub\Entities\MeetingHubMeeting::$statues[$task->status]) }}</span>
                                                                @endif
                                                            </td>
                                                            @if (Laratrust::hasPermission('meetingTask edit') || Laratrust::hasPermission('meetingTask delete'))
                                                                <td class="text-end">
                                                                    <span>
                                                                        @permission('meetingTask edit')
                                                                            <div class="action-btn me-2">
                                                                                <a class="mx-3 btn btn-sm  align-items-center bg-info"
                                                                                    data-url="{{ route('meeting-task.edit', $task->id) }}"
                                                                                    data-ajax-popup="true" data-size="md"
                                                                                    data-bs-toggle="tooltip" title=""
                                                                                    data-title="{{ __('Edit Task') }}"
                                                                                    data-bs-original-title="{{ __('Edit') }}">
                                                                                    <i class="ti ti-pencil text-white"></i>
                                                                                </a>
                                                                            </div>
                                                                        @endpermission
                                                                        @permission('meetingTask delete')
                                                                            <div class="action-btn ">
                                                                                {{ Form::open(['route' => ['meeting-task.destroy', $task->id], 'class' => 'm-0']) }}
                                                                                @method('DELETE')
                                                                                <a class="mx-3 btn btn-sm  align-items-center bs-pass-para show_confirm bg-danger"
                                                                                    data-bs-toggle="tooltip" title=""
                                                                                    data-bs-original-title="Delete"
                                                                                    aria-label="Delete"
                                                                                    data-confirm="{{ __('Are You Sure?') }}"
                                                                                    data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                                                    data-confirm-yes="delete-form-{{ $task->id }}"><i
                                                                                        class="ti ti-trash text-white text-white"></i></a>
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
                    </div>
                </div>
            </div>
        </div>
    @endsection
