@extends('layouts.main')
@section('page-title')
    {{ __('Manage Charters') }}
@endsection

@section('page-breadcrumb')
    {{ __('Charters') }}
@endsection
@push('css')
    <link rel="stylesheet" href="{{ asset('packages/workdo/Planning/src/Resources/assets/css/custom.css') }}">
    <style>
        .text-single-line {
            line-height: 1.2em;
            height: 2.2em;
            overflow: hidden;
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2;
        }

        .property {
            border-top: 1px solid #d1d1d19f;
        }

        .text-single-line {
            height: auto;
        }



        .card-address {
            padding-top: 12px !important;
        }

        .card-image {
            flex: 1;
        }

        .time-line-wrapper .count-main-div {
            margin-bottom: 15px;
            height: auto;
        }

        .time-line-wrapper .countDiv,
        .countDiv {
            height: auto;
        }

        .countDiv:before,
        .countDiv:after {
            top: 7px;
            left: 62%;
        }

        .countDiv:before {
            top: -5px;
            left: 62%;
        }

        .time-line-wrapper .position span {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }


        .dropdown-toggle::after {
            display: none;
        }

        .dropdown-btn-wrp button {
            width: 20px;
            height: 20px;
            padding: 0;
            border: 0;
        }

        .dropdown-btn-wrp button.show {
            border: 0;
        }

        .dropdown-btn-wrp {
            width: 20px;
            height: 20px;
        }

    </style>
@endpush
@section('page-action')
    <div>

        <a href="{{ route('planningcharters.index') }}" class="btn btn-sm btn-primary btn-icon me-1" data-bs-toggle="tooltip"
            title="{{ __('List View') }}">
            <i class="ti ti-list text-white"></i>
        </a>

        <a href="{{ route('charters.kanban') }}" data-bs-toggle="tooltip" data-bs-placement="top"
            title="{{ __('Kanban View') }}" class="btn btn-sm btn-primary btn-icon me-1"><i class="ti ti-table"></i>
        </a>

        <a href="{{ route('charters.treeview') }}" data-bs-toggle="tooltip" data-bs-placement="top"
            title="{{ __('Tree View') }}" class="btn btn-sm btn-primary btn-icon me-1"><i class="ti ti-sitemap"></i> </a>

        @permission('charters create')
            <a href="{{ route('charters.create', [0]) }}" data-title="{{ __('Create New Charter') }}" data-bs-toggle="tooltip"
                title="" class="btn btn-sm btn-primary" data-bs-original-title="{{ __('Create') }}">
                <i class="ti ti-plus"></i>
            </a>
        @endpermission
    </div>
@endsection
@section('content')
    <div class="raw mt-3">
        <section class="section">
            <div class="row d-flex grid time-line-wrapper">
                @isset($Charters)
                    @if ($Charters != null)
                        @foreach ($Charters as $Charter)
                            @php
                                $Challenge = \Workdo\Planning\Entities\PlanningChallenge::where(
                                    'created_by',
                                    creatorId(),
                                )
                                    ->where('id', $Charter->challenge)
                                    ->first();
                            @endphp
                            <div class="col-sm-6 col-xl-4 col-xxl-3 All mb-4">
                                <div class="card grid-card h-100 mb-0">
                                    <div class="card-header h-100 border-0 p-3 pb-0">
                                        <div
                                            class="card-header-left d-flex align-items-center justify-content-between mb-3 gap-2">
                                            <h5>
                                                <a
                                                    href="{{ route('planningcharters.show', $Charter->id) }}" style="text-decoration: none;">{{ $Charter->charter_name }}</a>
                                            </h5>
                                            <div class="card-header-right">
                                                <div class="btn-group card-option">
                                                    <button type="button" class="btn dropdown-toggle" data-bs-toggle="dropdown"
                                                        aria-haspopup="true" aria-expanded="false">
                                                        <i class="feather icon-more-vertical"></i>
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-end">
                                                        <a href="#"
                                                            data-url="{{ route('charters.receipt', $Charter->id) }}"
                                                            data-size="lg" data-bs-whatever="{{ __('Print') }}"
                                                            data-ajax-popup="true" class="dropdown-item"
                                                            data-bs-toggle="tooltip"><i class="ti ti-printer me-1"></i>
                                                            {{ __('Print') }}</a>
                                                        @permission('charters show')
                                                            <a href="{{ route('planningcharters.show', $Charter->id) }}"
                                                                class="dropdown-item" data-bs-toggle="tooltip"
                                                                data-bs-whatever="{{ __('Print') }}">
                                                                <i class="ti ti-eye me-1"></i>
                                                                {{ __('View') }}
                                                            </a>
                                                        @endpermission
                                                        @permission('charters edit')
                                                            <a href="{{ route('planningcharters.edit', $Charter->id) }}"
                                                                data-size="md" class="dropdown-item"
                                                                data-bs-whatever="{{ __('Edit') }}" data-bs-toggle="tooltip"><i
                                                                    class="ti ti-pencil me-1"></i>
                                                                {{ __('Edit') }}</a>
                                                        @endpermission
                                                        @permission('charters delete')
                                                            {{ Form::open(['route' => ['planningcharters.destroy', $Charter->id], 'class' => 'm-0']) }}
                                                            @method('DELETE')
                                                            <a href="#!"
                                                                class="dropdown-item bs-pass-para show_confirm text-danger"
                                                                aria-label="Delete" data-confirm="{{ __('Are You Sure?') }}"
                                                                data-text="{{ __('This action permission not be undone. Do you want to continue?') }}"
                                                                data-confirm-yes="delete-form-{{ $Charter->id }}">
                                                                <i class="ti ti-trash me-1"></i>
                                                                <span>{{ __('Delete') }}</span>
                                                            </a>
                                                            {{ Form::close() }}
                                                        @endpermission
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body p-3 pt-0">
                                        <div class="grid-image">
                                            <a href="{{ route('planningcharters.show', [$Charter->id]) }}">
                                                <img src="{{ isset($Charter->thumbnail_image) && check_file($Charter->thumbnail_image) ? get_file($Charter->thumbnail_image) : get_file('packages/workdo/Planning/src/Resources/assets/img/thumbnail-not-found.png') }}"
                                                    alt="Thumbnail" id="thumbnail"
                                                    class="card-img rounded border-2 border border-primary"
                                                    style="height: 200px; object-fit:cover;">
                                            </a>
                                        </div>
                                        <div class="count-main-div">
                                            <div id="Accounts_I_like_Publish_{{ !empty($Charter) ? $Charter->id : '' }}"
                                                data-date="{{ !empty($Challenge) ? $Challenge->end_date : '' }}"
                                                class="set_countdown mt-5"
                                                data-publish="{{ !empty($Challenge->id) ? $Challenge->id : '' }}">
                                            </div>
                                        </div>
                                        <div class="bottom-center-wrp row g-2 align-items-center justify-content-between" style="min-height:38px;">
                                            <div class="col-auto"><span
                                                    class="badge bg-success">{{ !empty($Charter->statuses) ? $Charter->statuses->name : '-' }}</span>
                                            </div>
                                            <div class="col-auto">
                                                @php
                                                    $user_id = explode(',', $Charter->user_id);
                                                    $users = App\Models\User::whereIn('id', $user_id)->get();
                                                @endphp
                                                <div class="user-group">
                                                    @foreach ($users as $user)
                                                        <img alt="image" data-bs-toggle="tooltip" data-bs-placement="top"
                                                            title="{{ $user->name }}"
                                                            @if ($user->avatar) src="{{ get_file($user->avatar) }}" @else src="{{ get_file('avatar.png') }}" @endif
                                                            class="rounded-circle" width="25" height="25">
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                </div>
                                        <div class="property">
                                            <div class="card-unit p-3">
                                                <div class="row">
                                                    <div class="col-6">
                                                        <h6 class="">{{ $Charter->countAttachment() }}</h6>
                                                        <p class="text-muted text-sm mb-0">{{ __('Attachments') }}</p>
                                                    </div>
                                                    <div class="col-6 text-end">
                                                        <h6 class="">{{ $Charter->countCharterComments() }}</h6>
                                                        <p class="text-muted text-sm mb-0">{{ __('Comments') }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                @endisset
                @auth('web')
                    @permission('charters create')
                        <div class="col-sm-6 col-xl-4 col-xxl-3 mb-4 All">
                            <a href="{{ route('charters.create', [0]) }}" class="btn-addnew-project border-primary"
                                data-title="{{ __('Create New Charter') }}" style="text-decoration: none;">
                                <div class="badge bg-primary proj-add-icon">
                                    <i class="ti ti-plus"></i>
                                </div>
                                <h6 class="my-2 text-center">{{ __('New Charter') }}</h6>
                                <p class="text-muted text-center">{{ __('Click here to Create New Charter') }}</p>
                            </a>
                        </div>
                    @endpermission
                @endauth

            </div>
        </section>
        {!! $Charters->links('vendor.pagination.global-pagination') !!}
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('packages/workdo/Planning/src/Resources/assets/js/moment.js') }}"></script>
    <script src="{{ asset('packages/workdo/Planning/src/Resources/assets/js/moment-timezone.min.js') }}"></script>

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
@endpush
