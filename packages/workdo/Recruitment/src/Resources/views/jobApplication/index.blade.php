@extends('layouts.main')
@section('page-title')
    {{ __('Manage Job Application') }}
@endsection

@section('page-breadcrumb')
    {{ __('Job Application') }}
@endsection
@push('css')
    <link href="{{ asset('packages/workdo/Recruitment/src/Resources/assets/css/bootstrap-tagsinput.css') }}" rel="stylesheet" />
    <link href="{{ asset('packages/workdo/Recruitment/src/Resources/assets/css/custom.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/dragula.min.css') }}">
@endpush
@push('scripts')
    <script src="{{ asset('assets/js/plugins/dragula.min.js') }}"></script>
    <script>
        $(document).on('change', '#jobs', function() {

            var id = $(this).val();

            $.ajax({
                url: "{{ route('get.job.application') }}",
                type: 'POST',
                data: {
                    "id": id,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(data) {
                    var job = JSON.parse(data);
                    var applicant = job.applicant;
                    var visibility = job.visibility;
                    var question = job.custom_question;

                    (applicant.indexOf("gender") != -1) ? $('.gender').removeClass('d-none'): $(
                        '.gender').addClass('d-none');
                    (applicant.indexOf("dob") != -1) ? $('.dob').removeClass('d-none'): $('.dob')
                        .addClass('d-none');
                    (applicant.indexOf("country") != -1) ? $('.country').removeClass('d-none'): $(
                        '.country').addClass('d-none');

                    (visibility.indexOf("profile") != -1) ? $('.profile').removeClass('d-none'): $(
                        '.profile').addClass('d-none');
                    (visibility.indexOf("resume") != -1) ? $('.resume').removeClass('d-none'): $(
                        '.resume').addClass('d-none');
                    (visibility.indexOf("letter") != -1) ? $('.letter').removeClass('d-none'): $(
                        '.letter').addClass('d-none');

                    $('.question').addClass('d-none');

                    if (question.length > 0) {
                        question.forEach(function(id) {
                            $('.question_' + id + '').removeClass('d-none');
                        });
                    }


                }
            });
        });

        $(document).on('change', '#application_type', function() {
            var selectedValue = $(this).val();
            if (selectedValue === 'job_candidate') {
                $('.job_candidate').removeClass('d-none');
                // Call the AJAX function to load job candidate data
                $('#job_candidate').trigger('change');
            } else if (selectedValue === 'new') {
                // Show all fields and clear their values
                $('.form-group').removeClass('d-none');
                $('.name').val('');
                $('[name="email"]').val('');
                $('[name="phone"]').val('');
                $('.dob input').val('');
                $('.gender').val('');
                $('[name="country"]').val('');
                $('[name="state"]').val('');
                $('[name="city"]').val('');
                $('.profile').val('');
                $('.resume').val('');
                // Hide job candidate fields
                $('.job_candidate').addClass('d-none');
            } else {
                // Hide job candidate fields for other values
                $('.job_candidate').addClass('d-none');
            }
        });

        $(document).on('change', '#job_candidate', function() {
            var candidateId = $(this).val();
            $.ajax({
                url: "{{ route('get.job.candidate') }}",
                type: 'POST',
                data: {
                    "id": candidateId,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(data) {
                    console.log(data);
                    if (data && !data.error && Object.keys(data).length > 0) {
                        var job_candidate = data;
                        var name = job_candidate.name;
                        var email = job_candidate.email;
                        var phone = job_candidate.phone;
                        var dob = job_candidate.dob;
                        var gender = job_candidate.gender;
                        var country = job_candidate.country;
                        var state = job_candidate.state;
                        var city = job_candidate.city;
                        var profile = job_candidate.profile;
                        var resume = job_candidate.resume;

                        // Set values and hide the fields
                        $('.name').val(name).closest('.form-group').addClass('d-none');
                        $('[name="email"]').val(email).closest('.form-group').addClass('d-none');
                        $('[name="phone"]').val(phone).closest('.form-group').addClass('d-none');
                        $('.dob input').val(dob).closest('.form-group').addClass('d-none');
                        $('.gender').closest('.form-group').addClass('d-none');
                        $('[name="country"]').val(country).closest('.form-group').addClass('d-none');
                        $('[name="state"]').val(state).closest('.form-group').addClass('d-none');
                        $('[name="city"]').val(city).closest('.form-group').addClass('d-none');
                        if (profile) {
                            $('.profile').val(profile).closest('.form-group').addClass('d-none');
                        }
                        if (resume) {
                            $('.resume').val(resume).closest('.form-group').addClass('d-none');
                        }
                    } else if (data.error) {
                        console.error(data.error);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error:', status, error);
                }
            });
        });

        @permission('jobapplication move')
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
                                url: '{{ route('job.application.order') }}',
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
                                    toastrs('Success', 'Job successfully updated',
                                        'success');
                                },
                                error: function(data) {
                                    data = data.responseJSON;
                                    toastrs('Error', data.error, 'error')
                                }
                            });
                            // $.ajax({
                            //     url: '{{ route('job.application.order') }}',
                            //     type: 'POST',
                            //     data: {
                            //         application_id: id,
                            //         stage_id: stage_id,
                            //         order: order,
                            //         new_status: new_status,
                            //         old_status: old_status,
                            //         "_token": $('meta[name="csrf-token"]').attr('content')
                            //     },
                            //     success: function(data) {
                            //         toastr.success('Job successfully updated');
                            //     },
                            //     error: function(data) {
                            //         var errorMessage = data.responseJSON ? data.responseJSON.error : 'An error occurred';
                            //         toastr.error(errorMessage);
                            //     }
                            // });

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
        @stack('addButtonHook')
        <a href="{{ route('job.application.archived') }}" class="btn btn-sm btn-primary btn-icon me-2"
            data-bs-toggle="tooltip"title="{{ __('Archive Jobs') }}">
            <i class="ti ti-archive"></i>
        </a>
        <a href="{{ route('job.list') }}" class="btn btn-sm btn-primary btn-icon me-2"
            data-bs-toggle="tooltip"title="{{ __('List View') }}">
            <i class="ti ti-list text-white"></i>
        </a>
        @permission('jobapplication create')
            <a data-url="{{ route('job-application.create') }}" data-ajax-popup="true" data-size="lg"
                data-title="{{ __('Create Job Application') }}" data-bs-toggle="tooltip" title=""
                class="btn btn-sm btn-primary" data-bs-original-title="{{ __('Create') }}">
                <i class="ti ti-plus"></i>
            </a>
        @endpermission
    </div>
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class=" mt-2 " id="multiCollapseExample1" style="">
                <div class="card">
                    <div class="card-body">
                        {{ Form::open(['route' => ['job-application.index'], 'method' => 'get', 'id' => 'applicarion_filter']) }}
                        <div class="row align-items-center row-gap justify-content-end">
                            <div class="col-xl-2 col-lg-3 col-sm-6 col-12">
                                <div class="btn-box">
                                    {{ Form::label('start_date', __('Start Date'), ['class' => 'form-label']) }}
                                    {{ Form::date('start_date', isset($_GET['start_date']) ? $_GET['start_date'] : '', ['class' => 'month-btn form-control ']) }}
                                </div>
                            </div>
                            <div class="col-xl-2 col-lg-3 col-sm-6 col-12">
                                <div class="btn-box">
                                    {{ Form::label('end_date', __('End Date'), ['class' => 'form-label']) }}
                                    {{ Form::date('end_date', isset($_GET['end_date']) ? $_GET['end_date'] : '', ['class' => 'month-btn form-control  ', 'autocomplete' => 'off']) }}
                                </div>
                            </div>
                            <div class="col-xl-2 col-lg-3 col-sm-6 col-12">
                                <div class="btn-box">
                                    {{ Form::label('stage', __('Stage'), ['class' => 'form-label']) }}
                                    {{ Form::select('stage', $stage, $filter['stage'], ['class' => 'form-control select ', 'id' => 'stage_id']) }}
                                </div>
                            </div>
                            <div class="col-xl-2 col-lg-3 col-sm-6 col-12">
                                <div class="btn-box">
                                    {{ Form::label('job', __('Job'), ['class' => 'form-label']) }}
                                    {{ Form::select('job', $jobs, $filter['job'], ['class' => 'form-control select ', 'id' => 'job_id']) }}
                                </div>
                            </div>
                            <div class="col-auto float-end mt-xl-4">
                                <div class="d-flex">
                                    <a class="btn btn-sm btn-primary me-2"
                                        onclick="document.getElementById('applicarion_filter').submit(); return false;"
                                        data-bs-toggle="tooltip" title="{{ __('Apply') }}" data-bs-original-title="apply">
                                        <span class="btn-inner--icon"><i class="ti ti-search"></i></span>
                                    </a>
                                    <a href="{{ route('job-application.index') }}" class="btn btn-sm btn-danger"
                                        data-bs-toggle="tooltip" title="{{ __('Reset') }}" data-bs-original-title="Reset">
                                        <span class="btn-inner--icon"><i class="ti ti-trash-off text-white-off "></i></span>
                                    </a>
                                </div>
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-12">
            @php
                $json = [];
                foreach ($stages as $stage) {
                    $json[] = 'kanban-blacklist-' . $stage->id;
                }
            @endphp
            <div class="row kanban-wrapper horizontal-scroll-cards pt-3" data-plugin="dragula"
                data-containers='{!! json_encode($json) !!}'>
                @foreach ($stages as $key => $stage)
                    @php
                        if (\Auth::user()->type == 'staff') {
                            $applications = $stage->applications($filter)->filter(function ($application) {
                                return $application->user_id == \Auth::user()->id;
                            });
                        } else {
                            $applications = $stage->applications($filter);
                        }
                    @endphp
                    <div class="col">
                        <div class="card card-list" id="backlog">
                            <div class="card-header d-flex justify-content-between gap-2">
                                <h4 class="mb-0 text-break">{{ $stage->title }}</h4>
                                <div class="float-end">
                                    <span class="btn btn-sm btn-primary btn-icon count">
                                        {{ count($applications) }}
                                    </span>
                                </div>
                            </div>
                            <div class="card-body kanban-box" id="{{ $json[$key] }}" data-id="{{ $stage->id }}">
                                @foreach ($applications as $application)
                                    <div class="card grid-card" data-id="{{ $application->id }}">
                                        <div class="card-header border-0 p-3 pb-0 d-flex justify-content-between gap-2">
                                            @if (\Auth::user()->type == 'staff')
                                                {{-- <h5> {{ $application->name }}</h5> --}}
                                                <h5><a href="">{{ $application->name }}</a>
                                                </h5>
                                            @else
                                                <h5><a
                                                        href="{{ route('jobsearch.application.show', \Crypt::encrypt($application->id)) }}">{{ $application->name }}</a>
                                                </h5>
                                            @endif

                                            <div class="card-header-right">
                                                @if (\Auth::user()->type == 'staff')
                                                @else
                                                    <div class="btn-group card-option">
                                                        <button type="button" class="btn dropdown-toggle"
                                                            data-bs-toggle="dropdown" aria-haspopup="true"
                                                            aria-expanded="false">
                                                            <i class="feather icon-more-vertical"></i>
                                                        </button>

                                                        <div class="dropdown-menu dropdown-menu-end">

                                                            @permission('jobapplication delete')
                                                                {!! Form::open(['method' => 'DELETE', 'route' => ['job.application.archive', $application->id]]) !!}
                                                                <a class="bs-pass-para dropdown-item show_confirm"
                                                                    data-bs-toggle="tooltip" aria-label="Archive">
                                                                    @if ($application->is_archive == 0)
                                                                        <i class="ti ti-archive"></i><span
                                                                            class="ms-2">{{ __('Archive') }}</span>
                                                                    @endif
                                                                </a>
                                                                {!! Form::close() !!}
                                                            @endpermission
                                                            @permission('jobapplication show')
                                                                <a href="{{ route('jobsearch.application.show', \Crypt::encrypt($application->id)) }}"
                                                                    class="dropdown-item"><i class="ti ti-eye "></i><span
                                                                        class="ms-2">{{ __('View') }}</span></a>
                                                            @endpermission

                                                            @permission('jobapplication delete')
                                                                @if ($application->is_archive == 0)
                                                                    {!! Form::open([
                                                                        'method' => 'DELETE',
                                                                        'route' => ['job-application.destroy', $application->id],
                                                                        'id' => 'delete-form-' . $application->id,
                                                                    ]) !!}
                                                                    <a class="bs-pass-para dropdown-item show_confirm"
                                                                        data-bs-toggle="tooltip" data-bs-placement="top"><i
                                                                            class="ti ti-trash text-danger"></i><span
                                                                            class="ms-2 text-danger">{{ __('Delete') }}</span></a>
                                                                    {!! Form::close() !!}
                                                                @endif
                                                            @endpermission
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="card-body p-3 pt-2">
                                            <div class="gap-2 d-flex align-items-center justify-content-between">
                                                <ul class="list-inline flex-1 mb-0 mt-0">
                                                    <li><span class="static-rating mb-2 static-rating-sm d-block">
                                                        @for ($i = 1; $i <= 5; $i++)
                                                            @if ($i <= $application->rating)
                                                                <i class="star fas fa-star voted"></i>
                                                            @else
                                                                <i class="star fas fa-star"></i>
                                                            @endif
                                                        @endfor
                                                    </span></li>
                                                    <li><p
                                                        class="text-md mb-1">{{ !empty($application->jobs) ? $application->jobs->title : '' }}</p></li>
                                                    <li class="list-inline-item d-inline-flex align-items-center"
                                                        data-bs-toggle="tooltip" title="{{ __('Job Title') }}">
                                                        <i class="ti ti-clock me-2" data-ajax-popup="true"
                                                            data-title="{{ __('Applied at') }}"></i>{{ company_date_formate($application->created_at) }}
                                                    </li>
                                                </ul>

                                                <div class="avatar-group hover-avatar-ungroup">
                                                    <a class="user-group">
                                                        <img src="{{ check_file($application->profile) ? get_file($application->profile) : 'uploads/users-avatar/avatar.png' }}"
                                                            alt="user-image"
                                                            class="rounded-cirlce"
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
