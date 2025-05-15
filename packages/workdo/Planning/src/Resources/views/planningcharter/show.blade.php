@extends('layouts.main')
@section('page-title')
    {{ __('Charters Details') }}
@endsection

@push('css')
    <link rel="stylesheet" href="{{ asset('packages/workdo/Planning/src/Resources/assets/css/custom.css') }}">
    <link rel="stylesheet" href="{{ asset('packages/workdo/Planning/src/Resources/assets/summernote/summernote-bs4.css') }}">
    <link href="{{ asset('assets/js/plugins/summernote-0.8.18-dist/summernote-lite.min.css') }}" rel="stylesheet">
@endpush

@section('page-breadcrumb')
    {{ __('Charters Details') }}
@endsection

@section('page-action')
    <div class="mr-4">
        @if (URL::previous() == URL::current())
            <a href="{{ route('planningcharters.index') }}" class="btn-submit btn btn-sm btn-primary " data-toggle="tooltip"
                title="{{ __('Back') }}">
                <i class=" ti ti-arrow-back-up"></i> </a>
        @else
            <a href="{{ url(URL::previous()) }}" class="btn-submit btn btn-sm btn-primary " data-toggle="tooltip"
                title="{{ __('Back') }}">
                <i class=" ti ti-arrow-back-up"></i> </a>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            /* ... Other code ... */

            $('#stars li').on('click', function() {
                // Retrieve Creativity ID from data attribute
                var CreativityId = $('#stars').data('creativity-id');

                $('#stars li').on('mouseover', function() {
                    var onStar = parseInt($(this).data('value'), 10); // The star currently mouse on

                    // Now highlight all the stars that's not after the current hovered star
                    $(this).parent().children('li.star').each(function(e) {
                        if (e < onStar) {
                            $(this).addClass('hover');
                        } else {
                            $(this).removeClass('hover');
                        }
                    });

                }).on('mouseout', function() {
                    $(this).parent().children('li.star').each(function(e) {
                        $(this).removeClass('hover');
                    });
                });
                $('#stars li').on('click', function() {
                    var onStar = parseInt($(this).data('value'), 10); // The star currently selected
                    var stars = $(this).parent().children('li.star');

                    for (i = 0; i < stars.length; i++) {
                        $(stars[i]).removeClass('selected');
                    }

                    for (i = 0; i < onStar; i++) {
                        $(stars[i]).addClass('selected');
                    }
                    var ratingValue = parseInt($('#stars li.selected').last().data('value'), 10);
                    $.ajax({
                        url: '{{ route('charters.rating', $Charters->id) }}',
                        type: 'POST',
                        data: {
                            rating: ratingValue,
                            "_token": $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(data) {},
                        error: function(data) {
                            data = data.responseJSON;
                            toastrs('Error', data.error, 'error')
                        }
                    });

                });
            });
        });
    </script>
@endpush
@section('content')
    <div class="row">
        <div class="col-sm-12 ">
            <div class="row">
                <div class="col-md-3">
                    <div class="card sticky-top" style="top:30px">
                        <div class="list-group list-group-flush" id="useradd-sidenav">
                            <a href="#general" class="list-group-item list-group-item-action border-0">{{ __('General') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                            <a href="#description"
                                class="list-group-item list-group-item-action border-0">{{ __('Description') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                            <a href="#attachments"
                                class="list-group-item list-group-item-action border-0">{{ __('Attachments') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                            <a href="#organisational_effects"
                                class="list-group-item list-group-item-action border-0">{{ __('Organisational Effects') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                            <a href="#goal_description"
                                class="list-group-item list-group-item-action border-0">{{ __('Goal Description') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                            <a href="#notes" class="list-group-item list-group-item-action border-0">{{ __('Notes') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                            <a href="#comment" class="list-group-item list-group-item-action border-0">{{ __('Comment') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="row time-line-wrapper ">
                        <div id="general" class="col-md-6">
                            @php
                                $Challenge = \Workdo\Planning\Entities\PlanningChallenge::where(
                                    'created_by',
                                    creatorId(),
                                )
                                    ->where('id', $Charters->challenge)
                                    ->first();
                            @endphp


                            <div class="card table-card">
                                <div class="card-header">
                                    <div class="d-flex card-counter-div justify-content-between align-items-center">
                                        <div>
                                            <h5>{{ __('Video') }}</h5>
                                        </div>
                                        <div class="count-main-Div">
                                            <div id="Accounts_I_like_Publish_{{ !empty($Charters) ? $Charters->id : '' }}"
                                                data-date="{{ !empty($Challenge) ? $Challenge->end_date : '' }}"
                                                class="set_countdown mt-5"
                                                data-publish="{{ !empty($Challenge->id) ? $Challenge->id : '' }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="card-body">

                                            @if ($Charters->video_file)
                                                <video width="100%" controls>
                                                    <source id="videoresource" src="{{ get_file($Charters->video_file) }}"
                                                        type="video/mp4">
                                                </video>
                                            @else
                                                <video width="100%" controls>
                                                    <source id="videoresource"
                                                        src="{{ asset('packages/workdo/Planning/src/Resources/assets/img/no-video.jpg') }}"
                                                        type="image">
                                                </video>
                                            @endif
                                        </div>
                                    </div>
                                    <div id="stars" class='rating-stars text-right'
                                        data-creativity-id="{{ $Charters->id }}">
                                        <ul id='stars'>
                                            <li class='star {{ in_array($Charters->rating, [1, 2, 3, 4, 5]) == true ? 'selected' : '' }}'
                                                data-bs-toggle="tooltip" data-bs-original-title="Poor" data-value='1'>
                                                <i class='fa fa-star fa-fw'></i>
                                            </li>
                                            <li class='star {{ in_array($Charters->rating, [2, 3, 4, 5]) == true ? 'selected' : '' }}'
                                                data-bs-toggle="tooltip" data-bs-original-title='Fair' data-value='2'>
                                                <i class='fa fa-star fa-fw'></i>
                                            </li>
                                            <li class='star {{ in_array($Charters->rating, [3, 4, 5]) == true ? 'selected' : '' }}'
                                                data-bs-toggle="tooltip" data-bs-original-title='Good' data-value='3'>
                                                <i class='fa fa-star fa-fw'></i>
                                            </li>
                                            <li class='star {{ in_array($Charters->rating, [4, 5]) == true ? 'selected' : '' }}'
                                                data-bs-toggle="tooltip" data-bs-original-title='Excellent' data-value='4'>
                                                <i class='fa fa-star fa-fw'></i>
                                            </li>
                                            <li class='star {{ in_array($Charters->rating, [5]) == true ? 'selected' : '' }}'
                                                data-bs-toggle="tooltip" data-bs-original-title='WOW!!!' data-value='5'>
                                                <i class='fa fa-star fa-fw'></i>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="">
                                <div class="card ">

                                    <div class="card-body pt-0" style="min-height: 190px;">
                                        <div class="row mt-2 mb-0 align-items-center">
                                            <div class="col-sm-4 h6 text-m">{{ __('Charter Name') }}</div>
                                            <div class="col-sm-8 text-m"> {{ $Charters->charter_name }}</div>
                                            <div class="col-sm-4 h6 text-m">{{ __('Status') }}</div>
                                            <div class="col-sm-8 text-m">
                                                {{ !empty($Charters->statuses) ? $Charters->statuses->name : '-' }}</div>
                                            <div class="col-sm-4 h6 text-m">{{ __('Stage') }}</div>
                                            <div class="col-sm-8 text-m">
                                                {{ !empty($Charters->stages) ? $Charters->stages->name : '-' }}</div>
                                            <div class="col-sm-4 h6 text-m">{{ __('Rating Points') }}</div>
                                            <div class="col-sm-8 text-m"> {{ $Charters->rating }}</div>
                                            <div class="col-sm-4 h6 text-m">{{ __('Due Date') }}</div>
                                            <div class="col-sm-8 text-m">
                                                {{ company_date_formate($Charters['created_at']) }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="description">
                            <div class="row">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5>{{ __('Description') }}</h5>
                                        </div>
                                        <div class="card-body">
                                            {{ Form::open(['route' => ['charters.description.store', $Charters->id]]) }}
                                            <textarea name="description"
                                                class="form-control summernote {{ !empty($errors->first('description')) ? 'is-invalid' : '' }}"
                                                id="description_ck">{!! $Charters->dsescription !!}</textarea>
                                            @permission('charters decription create')
                                                <div class="col-md-12 text-end mb-0">
                                                    {{ Form::submit(__('Add'), ['class' => 'btn  btn-primary']) }}
                                                </div>
                                            @endpermission
                                            {{ Form::close() }}
                                            @if ($errors->has('description'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('description') }}
                                                </div>
                                            @endif
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

                                        @php
                                            $attachments = json_decode($Charters->charter_attachments);

                                        @endphp
                                        @if (!is_null($attachments) && count($attachments) > 0)
                                            @foreach ($attachments as $index => $attachment)
                                                <div class="px-4 py-3">
                                                    <div class="list-group-item ">
                                                        <div class="row align-items-center">
                                                            <div class="col">
                                                                <h6 class="text-sm mb-0">
                                                                    <li class="list-group-item px-0">
                                                                        {{ $attachment->name }}</li>
                                                                </h6>
                                                            </div>
                                                            <div class="action-btn p-0 w-auto me-2">
                                                                <a class="btn btn-sm align-items-center bg-secondary"
                                                                    href="{{ get_file($attachment->path) }}"
                                                                    target="_blank">
                                                                    <i class="ti ti-crosshair text-white"
                                                                        data-bs-toggle="tooltip"
                                                                        data-bs-original-title="{{ __('Preview') }}"></i>
                                                                </a>
                                                            </div>
                                                            <div class="action-btn me-2 p-0 w-auto">
                                                                <a class="btn btn-sm align-items-center bg-warning"
                                                                    href="{{ get_file($attachment->path) }}" download>
                                                                    <i class="ti ti-download text-white"></i>
                                                                </a>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="organisational_effects">
                            <div class="row">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5>{{ __('Organisational Effects') }}</h5>
                                        </div>
                                        <div class="card-body">
                                            {{ Form::open(['route' => ['charters.organisational.store', $Charters->id]]) }}
                                            <textarea name="organisational_effects"
                                                class="form-control summernote {{ !empty($errors->first('organisational_effects')) ? 'is-invalid' : '' }}"
                                                id="description_ck1">{!! $Charters->organisational_effects !!}</textarea>
                                            @permission('charters organisational effects create')
                                                <div class="col-md-12 text-end mb-0">
                                                    {{ Form::submit(__('Add'), ['class' => 'btn  btn-primary']) }}
                                                </div>
                                            @endpermission
                                            {{ Form::close() }}
                                            @if ($errors->has('organisational_effects'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('organisational_effects') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="goal_description">
                            <div class="row">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5>{{ __('Goal Description') }}</h5>
                                        </div>
                                        <div class="card-body">
                                            {{ Form::open(['route' => ['charters.goal.store', $Charters->id]]) }}
                                            <textarea name="goal_description"
                                                class="form-control summernote {{ !empty($errors->first('goal_description')) ? 'is-invalid' : '' }}"
                                                id="description_ck2">{!! $Charters->goal_description !!}</textarea>
                                            @permission('charters goal description create')
                                                <div class="col-md-12 text-end mb-0">
                                                    {{ Form::submit(__('Add'), ['class' => 'btn  btn-primary']) }}
                                                </div>
                                            @endpermission
                                            {{ Form::close() }}
                                            @if ($errors->has('goal_description'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('goal_description') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="notes">
                            <div class="row">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5>{{ __('Notes') }}</h5>
                                        </div>
                                        <div class="card-body">
                                            {{ Form::open(['route' => ['charters.notes.store', $Charters->id]]) }}
                                            <textarea name="notes" class="form-control summernote {{ !empty($errors->first('notes')) ? 'is-invalid' : '' }}"
                                                id="description_ck3">{!! $Charters->notes !!}</textarea>
                                            @permission('charters notes create')
                                                <div class="col-md-12 text-end mb-0">
                                                    {{ Form::submit(__('Add'), ['class' => 'btn  btn-primary']) }}
                                                </div>
                                            @endpermission
                                            {{ Form::close() }}
                                            @if ($errors->has('notes'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('notes') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!--Comments-->
                        <div id="comment">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">{{ __('Comments') }}</h5>
                                </div>
                                <div class="card-body">
                                    @foreach ($comments as $comment)
                                        <div class="media comment-card mb-2 rounded border-1 border border-primary">
                                            <a class="pr-2" href="#">
                                                <img src="{{ check_file($comment->commentUser->avatar) ? get_file($comment->commentUser->avatar) : get_file('uploads/users-avatar/avatar.png') }}"
                                                    class="rounded border-2 border border-primary" alt="" width="40" height="40">
                                            </a>
                                            <div class="media-body ms-2">
                                                <h6>
                                                    {{ !empty($comment->commentUser->name) ? $comment->commentUser->name : '' }}
                                                    <small
                                                        class="text-muted float-right">{{ $comment->created_at->diffForHumans() }}</small>
                                                </h6>
                                                <div class="d-flex gap-2 align-items-center">
                                                    <p class="text-sm mb-0">
                                                        {{ $comment->comment }}
                                                    </p>
                                                    <div class="d-flex">
                                                        @if (!empty($comment->file))
                                                            <div class="d-flex me-2">
                                                                <a href="{{ get_file('uploads/Planning') . '/' . $comment->file }}"
                                                                    download=""
                                                                    class="m-0 p-1 btn btn-sm d-inline-flex align-items-center"
                                                                    data-bs-toggle="tooltip"
                                                                    title="{{ __('Download') }}">
                                                                    <i class="ti ti-download text-primary"></i> </a>
                                                                </a>
                                                            </div>
                                                            <div class="d-flex me-2">
                                                                <a href="{{ get_file('uploads/Planning') . '/' . $comment->file }}"
                                                                    target=_blank
                                                                    class="btn btn-sm p-1 d-inline-flex align-items-center text-white "
                                                                    data-bs-toggle="tooltip" title="{{ __('Preview') }}">
                                                                    <i class="ti ti-crosshair text-primary"></i>
                                                                </a>
                                                            </div>
                                                        @endif
                                                        @permission('charters comment replay')
                                                            <div class="d-flex me-2">
                                                                    <a href="#"
                                                                        data-url="{{ route('charters.comment.reply', [$Charters->id, $comment->id]) }}"
                                                                         class="btn btn-sm p-1 d-inline-flex align-items-center text-white "
                                                                        data-ajax-popup="true" data-bs-toggle="tooltip"
                                                                        data-title="{{ __('Create Comment Reply') }}"
                                                                        title="{{ __('Reply') }}">
                                                                        <i class="ti ti-send text-primary"></i>
                                                                    </a>
                                                            </div>
                                                        @endpermission
                                                    </div>
                                                </div>
                                                @foreach ($comment->subComment as $subComment)
                                                    @include('planning::planningcharter.comment', [
                                                        'subComment' => $subComment,
                                                    ])
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach

                                    <div class="border rounded mt-4">
                                        {{ Form::open(['route' => ['charters.comment.store', $Charters->id], 'enctype' => 'multipart/form-data', 'class' => 'd-flex align-items-center gap-3 form-wrp needs-validation', 'novalidate']) }}
                                        <textarea rows="3" class="form-control resize-none project_comment border-4" name="comment"
                                            placeholder="Your comment..." required style="flex: 1;"></textarea>

                                        <div class="btn-wrp p-2 gap-2 d-flex  justify-content-between align-items-center">
                                            <div class="choose-file">
                                                <input class="custom-input-file custom-input-file-link  commentFile d-none"
                                                    onchange="showfilename()" type="file" name="file"
                                                    id="file" multiple="">
                                                <label for="file">
                                                    <button type="button" onclick="selectFile('commentFile')"
                                                        class="btn btn-primary"><svg xmlns="http://www.w3.org/2000/svg"
                                                            width="24" height="24" viewBox="0 0 24 24"
                                                            fill="none" stroke="currentColor" stroke-width="2"
                                                            stroke-linecap="round" stroke-linejoin="round"
                                                            class="feather feather-upload me-2">
                                                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                                            <polyline points="17 8 12 3 7 8"></polyline>
                                                            <line x1="12" y1="3" x2="12"
                                                                y2="15"></line>
                                                        </svg>
                                                        {{ __('Choose a file...') }}</button>
                                                </label><br>
                                            </div>
                                            <button type="submit" class="btn btn-primary">{{ __('Post') }}</button>
                                        </div>
                                        {{ Form::close() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection
    @push('scripts')
        <script src="{{ asset('assets/js/plugins/dropzone-amd-module.min.js') }}"></script>
        <script src="{{ asset('packages/workdo/Planning/src/Resources/assets/summernote/summernote-bs4.js') }}"></script>
        <script src="{{ asset('js/custom.js') }}"></script>
        {{-- <script src="{{ asset('assets/js/plugins/summernote-0.8.18-dist/summernote-lite.min.js') }}"></script> --}}
        <script src="{{ asset('packages/workdo/Planning/src/Resources/assets/js/moment.js') }}"></script>
        <script src="{{ asset('packages/workdo/Planning/src/Resources/assets/js/moment-timezone.min.js') }}"></script>

        <script src="{{ asset('packages/workdo/Planning/src/Resources/assets/js/main.js') }}"></script>
        <script>
            function showfilename() {
                var uploaded_file_name = document.getElementById('file');
                $('.uploaded_file_name').text(uploaded_file_name.files.item(0).name);
            };
        </script>

        <script>
            $(".set_countdown").each(function(i) {
                console.log();
                var id = $(this).attr('id');
                var date = $(this).attr('data-date');
                var publish = $(this).attr('data-publish');
                // if (publish == 0 || publish == 1) {
                Publish(id, date);
                // }
            });


            function Publish(id = null, date = null) {
                if (date != '') {

                    (function($) {

                        // Number of seconds in every time division
                        var days = 24 * 60 * 60,
                            hours = 60 * 60,
                            minutes = 60;

                        // Creating the plugin
                        $.fn.countdown = function(prop) {
                            var options = $.extend({
                                callback: function() {},
                                timestamp: 0
                            }, prop);
                            var left, d, h, m, s, positions;

                            // Initialize the plugin
                            init(this, options);

                            positions = this.find('.position');

                            (function tick() {

                                // Time left
                                left = Math.floor((options.timestamp - (new Date())) / 1000);

                                if (left < 0) {
                                    left = 0;
                                }

                                // Number of days left
                                d = Math.floor(left / days);
                                updateDuo(0, 1, d);
                                left -= d * days;

                                // Number of hours left
                                h = Math.floor(left / hours);
                                updateDuo(2, 3, h);
                                left -= h * hours;

                                // Number of minutes left
                                m = Math.floor(left / minutes);
                                updateDuo(4, 5, m);
                                left -= m * minutes;

                                // Number of seconds left
                                s = left;
                                updateDuo(6, 7, s);

                                // Calling an optional user supplied callback
                                options.callback(d, h, m, s);

                                // Scheduling another call of this function in 1s
                                setTimeout(tick, 1000);
                            })();

                            // This function updates two digit positions at once
                            function updateDuo(minor, major, value) {
                                switchDigit(positions.eq(minor), Math.floor(value / 10) % 10);
                                switchDigit(positions.eq(major), value % 10);
                            }

                            return this;
                        };

                        function init(elem, options) {
                            elem.addClass('countdownHolder');

                            // Creating the markup inside the container
                            $.each(['Days', 'Hours', 'Minutes', 'Seconds'], function(i) {
                                $('<span class="count' + this + '">').html(
                                    '<span class="position">\
                                                                                        <span class="digit static">0</span>\
                                                                                    </span>\
                                                                                    <span class="position">\
                                                                                        <span class="digit static">0</span>\
                                                                                    </span>'
                                ).appendTo(elem);

                                if (this != "Seconds") {
                                    elem.append('<span class="countDiv countDiv' + i + '"></span>');
                                }
                            });

                        }

                        // Creates an animated transition between the two numbers
                        function switchDigit(position, number) {

                            var digit = position.find('.digit')

                            if (digit.is(':animated')) {
                                return false;
                            }

                            if (position.data('digit') == number) {
                                // We are already showing this number
                                return false;
                            }

                            position.data('digit', number);

                            var replacement = $('<span>', {
                                'class': 'digit',
                                css: {
                                    top: '-2.1em',
                                    opacity: 0
                                },
                                html: number
                            });

                            // The .static class is added when the animation
                            // completes. This makes it run smoother.

                            digit
                                .before(replacement)
                                .removeClass('static')
                                .animate({
                                    top: '2.5em',
                                    opacity: 0
                                }, 'fast', function() {
                                    digit.remove();
                                })

                            replacement
                                .delay(100)
                                .animate({
                                    top: 0,
                                    opacity: 1
                                }, 'fast', function() {
                                    replacement.addClass('static');
                                });
                        }
                    })(jQuery);

                    // other one

                    $(function() {
                        var note = $('#Note_' + id),
                            ts = new Date(date),
                            newYear = true;
                        if ((new Date()) >= ts) {
                            // The new year is here! Count towards something else.
                            // Notice the *1000 at the end - time must be in milliseconds
                            ts = (new Date()).getTime() + 10 * 24 * 60 * 60 * 1000;

                            newYear = false;
                        }
                        $('#' + id).countdown({
                            timestamp: ts,
                            callback: function(days, hours, minutes, seconds) {
                                var message = "";

                                message += days + " day" + (days == 1 ? '' : 's') + ", ";
                                message += hours + " hour" + (hours == 1 ? '' : 's') + ", ";
                                message += minutes + " minute" + (minutes == 1 ? '' : 's') + " and ";
                                message += seconds + " second" + (seconds == 1 ? '' : 's') + " <br />";

                                if (newYear) {
                                    message += "left until the publishing of this card!";
                                } else {
                                    message += "left to 10 days from now!";
                                }

                                note.html(message);
                            }

                        });

                    });
                }
            }
        </script>

        <script>
            var scrollSpy = new bootstrap.ScrollSpy(document.body, {
                target: '#useradd-sidenav',
                offset: 300
            })
        </script>
    @endpush
