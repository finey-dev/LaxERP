@extends('layouts.main')
@section('page-title')
    {{ __('Manage RFx Application') }}
@endsection

@section('page-breadcrumb')
    {{ __('RFx Application') }}
@endsection
@push('css')
    <link href="{{ asset('packages/workdo/Procurement/src/Resources/assets/css/bootstrap-tagsinput.css') }}"
        rel="stylesheet" />
    <link href="{{ asset('packages/workdo/Procurement/src/Resources/assets/css/custom.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/dragula.min.css') }}">
@endpush
@push('scripts')
    <script src="{{ asset('assets/js/plugins/dragula.min.js') }}"></script>
    <script>
        $(document).on('change', '#rfxs', function() {
            var id = $(this).val();

            $.ajax({
                url: "{{ route('get.rfx.application') }}",
                type: 'POST',
                data: {
                    "id": id,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(data) {
                    var rfx = JSON.parse(data);
                    var applicant = rfx.applicant;
                    var visibility = rfx.visibility;
                    var question = rfx.custom_question;

                    (applicant.indexOf("gender") != -1) ? $('.gender').removeClass('d-none'): $(
                        '.gender').addClass('d-none');
                    (applicant.indexOf("dob") != -1) ? $('.dob').removeClass('d-none'): $('.dob')
                        .addClass('d-none');
                    (applicant.indexOf("country") != -1) ? $('.country').removeClass('d-none'): $(
                        '.country').addClass('d-none');

                    (visibility.indexOf("profile") != -1) ? $('.profile').removeClass('d-none'): $(
                        '.profile').addClass('d-none');
                    (visibility.indexOf("proposal") != -1) ? $('.proposal').removeClass('d-none'): $(
                        '.resume').addClass('d-none');
                    (visibility.indexOf("letter") != -1) ? $('.letter').removeClass('d-none'): $(
                        '.letter').addClass('d-none');

                    $('.question').addClass('d-none');

                    if (question.length > 0) {
                        question.forEach(function(id) {
                            $('.question_' + id + '').removeClass('d-none');
                            $('.question_' + id + ' input').prop('required',
                            false); // Set required attribute to false for all inputs
                        });
                    }
                }
            });
        });

        @permission('rfxapplication move')
            ! function(a) {
                "use strict";

                var t = function() {
                    this.$body = a("body")
                };
                t.prototype.init = function() {
                    a('[data-plugin="dragula"]').each(function() {

                        var t = a(this).data("containers"),

                            n = [];
                        if (t)
                            for (var i = 0; i < t.length; i++) n.push(a("#" + t[i])[0]);
                        else n = [a(this)[0]];
                        var r = a(this).data("handleclass");
                        r ? dragula(n, {
                            moves: function(a, t, n) {
                                return n.classList.contains(r)
                            }
                        }) : dragula(n).on('drop', function(el, target, source, sibling) {
                            var order = [];
                            $("#" + target.id + " > div").each(function() {
                                order[$(this).index()] = $(this).attr('data-id');
                            });

                            var id = $(el).attr('data-id');

                            var old_status = $("#" + source.id).data('status');
                            var new_status = $("#" + target.id).data('status');
                            var stage_id = $(target).attr('data-id');


                            $("#" + source.id).parent().find('.count').text($("#" + source.id +
                                " > div").length);
                            $("#" + target.id).parent().find('.count').text($("#" + target.id +
                                " > div").length);
                            $.ajax({
                                url: '{{ route('rfx.application.order') }}',
                                type: 'POST',
                                data: {
                                    application_id: id,
                                    stage_id: stage_id,
                                    order: order,
                                    new_status: new_status,
                                    old_status: old_status,
                                    "_token": $('meta[name="csrf-token"]').attr('content')
                                },
                                success: function(data) {
                                    toastrs('Success', __(
                                        'RFx has been moved successfully'),
                                        'success');
                                },
                                error: function(data) {
                                    data = data.responseJSON;
                                    toastrs('Error', data.error, 'error')
                                }
                            });
                        });
                    })
                }, a.Dragula = new t, a.Dragula.Constructor = t
            }(window.jQuery),
            function(a) {
                "use strict";

                a.Dragula.init()

            }(window.jQuery);
        @endpermission
    </script>
@endpush
@section('page-action')
    <div class="d-flex">
        <a href="{{ route('rfx.application.archived') }}" class="btn btn-sm btn-primary me-2"
            data-bs-toggle="tooltip"title="{{ __('Archive RFxs') }}">
            <i class="ti ti-archive"></i>
        </a>
        <a href="{{ route('rfx.list') }}" class="btn btn-sm btn-primary me-2"
            data-bs-toggle="tooltip"title="{{ __('List View') }}">
            <i class="ti ti-list text-white"></i>
        </a>
        @permission('rfxapplication create')
            <a data-url="{{ route('rfx-application.create') }}" data-ajax-popup="true" data-size="lg"
                data-title="{{ __('Create RFx Application') }}" data-bs-toggle="tooltip" title=""
                class="btn btn-sm btn-primary" data-bs-original-title="{{ __('Create') }}">
                <i class="ti ti-plus"></i>
            </a>
        @endpermission
    </div>
@endsection
@section('content')
    <div class="row">
        <div class="col-sm-12 col-lg-12 col-xl-12 col-md-12">
            <div class=" mt-2 " id="multiCollapseExample1" style="">
                <div class="card">
                    <div class="card-body">
                        {{ Form::open(['route' => ['rfx-application.index'], 'method' => 'get', 'id' => 'applicarion_filter']) }}
                        <div class="row row-gap align-items-center justify-content-end">
                            <div class="col-xl-2 col-lg-3 col-md-4 col-12">
                                <div class="btn-box">
                                    {{ Form::label('start_date', __('Start Date'), ['class' => 'form-label']) }}
                                    {{ Form::date('start_date', isset($_GET['start_date']) ? $_GET['start_date'] : '', ['class' => 'month-btn form-control ']) }}
                                </div>
                            </div>
                            <div class="col-xl-2 col-lg-3 col-md-4 col-12">
                                <div class="btn-box">
                                    {{ Form::label('end_date', __('End Date'), ['class' => 'form-label']) }}
                                    {{ Form::date('end_date', isset($_GET['end_date']) ? $_GET['end_date'] : '', ['class' => 'month-btn form-control  ', 'autocomplete' => 'off']) }}
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-3 col-md-4 col-12">
                                <div class="btn-box">
                                    {{ Form::label('rfx', __('RFx'), ['class' => 'form-label']) }}
                                    {{ Form::select('rfx', $rfxs, $filter['rfx'], ['class' => 'form-control select ', 'id' => 'rfx_id']) }}
                                </div>
                            </div>
                            <div class="col-auto float-end mt-lg-4">
                                <a class="btn btn-sm btn-primary me-1"
                                    onclick="document.getElementById('applicarion_filter').submit(); return false;"
                                    data-bs-toggle="tooltip" title="" data-bs-original-title="apply">
                                    <span class="btn-inner--icon"><i class="ti ti-search"></i></span>
                                </a>
                                <a href="{{ route('rfx-application.index') }}" class="btn btn-sm btn-danger"
                                    data-bs-toggle="tooltip" title="" data-bs-original-title="Reset">
                                    <span class="btn-inner--icon"><i class="ti ti-trash-off text-white-off "></i></span>
                                </a>
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            @php
                $json = [];
                foreach ($stages as $stage) {
                    $json[] = 'kanban-blacklist-' . $stage->id;
                }
            @endphp
            <div class="row kanban-wrapper procurement-card horizontal-scroll-cards" data-plugin="dragula"
                data-containers='{!! json_encode($json) !!}'>
                @foreach ($stages as $key => $stage)
                    @php
                        $applications = $stage->applications($filter);
                    @endphp
                    <div class="col">
                        <div class="card mt-4">
                            <div class="card-header">
                                <div class="float-end">
                                    <span class="btn btn-sm btn-primary btn-icon count">
                                        {{ count($applications) }}
                                    </span>
                                </div>
                                <h4 class="mb-0">{{ $stage->title }}</h4>
                            </div>
                            <div class="card-body kanban-box" id="{{ $json[$key] }}" data-id="{{ $stage->id }}">
                                @foreach ($applications as $application)
                                    <div class="card grid-card" data-id="{{ $application->id }}">
                                        <div class="card-header border-0 p-3 pb-0 d-flex justify-content-between gap-2">
                                            <h5 class="mb-0">
                                                <a href="{{ route('rfx-application.show', \Crypt::encrypt($application->id)) }}">{{ $application->name }}</a>
                                            </h5>
                                            <div class="card-header-right">
                                                <div class="btn-group card-option">
                                                    <button type="button" class="btn dropdown-toggle"
                                                        data-bs-toggle="dropdown" aria-haspopup="true"
                                                        aria-expanded="false">
                                                        <i class="feather icon-more-vertical"></i>
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-end">


                                                        @permission('rfxapplication delete')
                                                            {!! Form::open(['method' => 'DELETE', 'route' => ['rfx.application.archive', $application->id]]) !!}
                                                            <a class="bs-pass-para dropdown-item show_confirm"
                                                                data-bs-toggle="tooltip" aria-label="Archive">
                                                                @if ($application->is_archive == 0)
                                                                    <i class="ti ti-archive"></i><span
                                                                        class="ms-2">{{ __('Archive') }}</span>
                                                                @endif
                                                            </a>
                                                            {!! Form::close() !!}
                                                        @endpermission

                                                        @permission('rfxapplication show')
                                                            <a href="{{ route('rfx-application.show', \Crypt::encrypt($application->id)) }}"
                                                                class="dropdown-item"><i class="ti ti-eye "></i><span
                                                                    class="ms-2">{{ __('View') }}</span></a>
                                                        @endpermission

                                                        @permission('rfxapplication delete')
                                                            @if ($application->is_archive == 0)
                                                                {!! Form::open([
                                                                    'method' => 'DELETE',
                                                                    'route' => ['rfx-application.destroy', $application->id],
                                                                    'id' => 'delete-form-' . $application->id,
                                                                ]) !!}
                                                                <a class="bs-pass-para dropdown-item show_confirm text-danger"
                                                                    data-bs-toggle="tooltip" data-bs-placement="top"><i
                                                                        class="ti ti-trash"></i><span
                                                                        class="ms-2">{{ __('Delete') }}</span></a>
                                                                {!! Form::close() !!}
                                                            @endif
                                                        @endpermission

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body  p-3 pt-2">
                                            <div class="gap-2 d-flex align-items-center justify-content-between">
                                                <ul class="list-inline flex-1 mb-0 mt-0">
                                                    <li>
                                                        <span class="static-rating mb-2 static-rating-sm d-block border-0 pb-0">
                                                            @for ($i = 1; $i <= 5; $i++)
                                                                @if ($i <= $application->rating)
                                                                    <i class="star fas fa-star voted"></i>
                                                                @else
                                                                    <i class="star fas fa-star"></i>
                                                                @endif
                                                            @endfor
                                                        </span>
                                                    </li>
                                                    <li>
                                                        <p class="text-md mb-1" data-bs-toggle="tooltip"
                                                            title="{{ __('RFx Title') }}">
                                                            {{ !empty($application->rfxs) ? $application->rfxs->title : '' }}
                                                        </p>
                                                    </li>
                                                    <li class="list-inline-item d-inline-flex align-items-center">
                                                        <i class="ti ti-clock me-2" data-ajax-popup="true"
                                                            data-title="{{ __('Applied at') }}"></i>{{ company_date_formate($application->created_at) }}
                                                    </li>
                                                </ul>
                                                <div class="avatar-group hover-avatar-ungroup">
                                                    <a class="user-group">
                                                        <img src="{{ check_file($application->profile) ? get_file($application->profile) : 'uploads/users-avatar/avatar.png' }}"
                                                            alt="user-image" class="user-image-hr-prj ui-w-30"
                                                            width="50px" height="50px">
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <span class="empty-container" data-placeholder="Empty"></span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
