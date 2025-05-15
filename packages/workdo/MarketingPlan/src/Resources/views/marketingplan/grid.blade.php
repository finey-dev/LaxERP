@extends('layouts.main')
@section('page-title')
    {{ __('Manage Marketing Plan') }}
@endsection

@section('page-breadcrumb')
    {{ __('Marketing Plan') }}
@endsection
@push('css')
    <link rel="stylesheet" href="{{ asset('packages/workdo/MarketingPlan/src/Resources/assets/css/custom.css') }}">
    <style>

    </style>
@endpush
@section('page-action')
    <div class="d-flex">

        <a href="{{ route('marketing-plan.index') }}" class="btn btn-sm btn-primary btn-icon me-2" data-bs-toggle="tooltip"
            title="{{ __('List View') }}">
            <i class="text-white ti ti-list"></i>
        </a>

        <a href="{{ route('marketing-plan.kanban') }}" data-bs-toggle="tooltip" data-bs-placement="top"
            title="{{ __('Kanban View') }}" class="btn btn-sm btn-primary btn-icon me-2"><i class="ti ti-table"></i>
        </a>

        <a href="{{ route('marketing-plan.treeview') }}" data-bs-toggle="tooltip" data-bs-placement="top"
            title="{{ __('Tree View') }}" class="btn btn-sm btn-primary btn-icon me-2"><i class="ti ti-sitemap"></i>
        </a>

        @permission('marketing plan create')
            <a href="{{ route('marketing-plan.create', [0]) }}" data-title="{{ __('Create Marking Plan') }}"
                data-bs-toggle="tooltip" title="" class="btn btn-sm btn-primary"
                data-bs-original-title="{{ __('Create') }}">
                <i class="ti ti-plus"></i>
            </a>
        @endpermission

    </div>
@endsection
@section('content')
    <div class="mt-3 raw">
        <section class="section">
            <div class="row  d-flex grid time-line-wrapper">
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

                            <div class="d-flex col-sm-6 col-xl-4 col-xxl-3 All">
                                <div class="card  w-100">
                                    <div class="card-header w-100 h-100 border-0 p-3 pb-0 mb-3">
                                        <div class="h-100 d-flex gap-2 justify-content-between">
                                            <h5><a
                                                    href="{{ route('marketing-plan.show', $Charter->id) }}" style="text-decoration: none;">{{ $Charter->name }}</a>
                                            </h5>

                                            <div class="dropdown-btn-wrp text-end">
                                                <button type="button" class="btn dropdown-toggle" data-bs-toggle="dropdown"
                                                    aria-haspopup="true" aria-expanded="false">
                                                    <i class="feather icon-more-vertical"></i>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-end">

                                                    <a href="#"
                                                        data-url="{{ route('marketing-plan.receipt', $Charter->id) }}"
                                                        data-size="lg" data-bs-whatever="{{ __('Print') }}"
                                                        data-ajax-popup="true" class="dropdown-item" data-bs-toggle="tooltip" data-title="{{__('Print Marketing Plan')}}"><i
                                                            class="ti ti-printer me-1"></i>
                                                        {{ __('Print') }}</a>


                                                    @permission('marketing plan show')
                                                        <a href="{{ route('marketing-plan.show', $Charter->id) }}"
                                                            class="dropdown-item" data-bs-toggle="tooltip"
                                                            data-bs-whatever="{{ __('Print') }}">
                                                            <i class="ti ti-eye me-1"></i>
                                                            {{ __('View') }}
                                                        </a>
                                                    @endpermission


                                                    @permission('marketing plan edit')
                                                        <a href="{{ route('marketing-plan.edit', $Charter->id) }}" data-size="md"
                                                            class="dropdown-item" data-bs-whatever="{{ __('Edit') }}"
                                                            data-bs-toggle="tooltip"><i class="ti ti-pencil me-1"></i>
                                                            {{ __('Edit') }}</a>
                                                    @endpermission

                                                    @permission('marketing plan delete')
                                                        {{ Form::open(['route' => ['marketing-plan.destroy', $Charter->id], 'class' => 'm-0']) }}
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


                                    <div class="card-body pt-0 p-3">
                                    <div class="card-image align-items-center">
                                            <a href="{{ isset($Charter->thumbnail_image) && check_file($Charter->thumbnail_image) ? get_file($Charter->thumbnail_image) : asset('packages/workdo/MarketingPlan/src/Resources/assets/img/thumbnail-not-found.png') }}">
                                                <img src="{{ isset($Charter->thumbnail_image) && check_file($Charter->thumbnail_image) ? get_file($Charter->thumbnail_image) : asset('packages/workdo/MarketingPlan/src/Resources/assets/img/thumbnail-not-found.png') }}"
                                                    alt="Thumbnail" id="thumbnail"
                                                    class="card-img rounded border-2 border border-primary"
                                                    style="height: 200px; object-fit:cover;">
                                            </a>
                                        </div>
                                        <div class="count-main-div">
                                            <div id="Accounts_I_like_Publish_{{ !empty($Charter) ? $Charter->id : '' }}"
                                                data-date="{{ !empty($Challenge) ? $Challenge->end_date : '' }}"
                                                class="mt-5 set_countdown"
                                                data-publish="{{ !empty($Challenge->id) ? $Challenge->id : '' }}">
                                            </div>
                                        </div>


                                        <div class="bottom-center-wrp row g-2 align-items-center justify-content-between" style="min-height:38px">
                                            <div class="col-auto"><span
                                                    class="badge bg-success">{{ !empty($Charter->Status) ? $Charter->Status->name : '-' }}</span>
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
                                                            @if ($user->avatar) src="{{ get_file($user->avatar) }}" @else src="{{ get_file('uploads/users-avatar/avatar.png') }}" @endif
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
                                                        <h6 class="mb-0">{{ $Charter->countBusinessAttachment() }}</h6>
                                                        <p class="mb-0 text-sm text-muted">{{ __('Attachments') }}</p>
                                                    </div>
                                                    <div class="col-6 text-end">
                                                        <h6 class="mb-0">{{ $Charter->countBusinessComments() }}
                                                        </h6>
                                                        <p class="mb-0 text-sm text-muted">{{ __('Comments') }}</p>
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
                    @permission('marketing plan create')
                        <div class="col-sm-6 col-xxl-3 col-xl-4 mb-4 All">
                            <a href="{{ route('marketing-plan.create', [0]) }}" class="btn-addnew-project border-primary"
                                data-title="{{ __('Create New Marketing Plan') }}" style="text-decoration: none;">
                                <div class="badge bg-primary proj-add-icon">
                                    <i class="ti ti-plus"></i>
                                </div>
                                <h6 class="my-2 text-center">{{ __('New Marketing Plan') }}</h6>
                                <p class="text-center text-muted">{{ __('Click here to add New') }} </p>
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
    <script src="{{ asset('packages/workdo/MarketingPlan/src/Resources/assets/js/moment.js') }}"></script>
    <script src="{{ asset('packages/workdo/MarketingPlan/src/Resources/assets/js/moment-timezone.min.js') }}"></script>
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
                                                                                            <span class="static digit">0</span>\
                                                                                        </span>\
                                                                                        <span class="position">\
                                                                                            <span class="static digit">0</span>\
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
