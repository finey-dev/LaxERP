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
        <a href="{{ route('job-application.index') }}" class="btn btn-sm btn-primary btn-icon me-2  "
            data-bs-toggle="tooltip"title="{{ __('Grid View') }}">
            <i class="ti ti-layout-grid"></i>
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
        <div class=" mt-2 " id="multiCollapseExample1" style="">
            <div class="card">
                <div class="card-body">
                    {{ Form::open(['route' => ['job.list'], 'method' => 'get', 'id' => 'applicarion_filter']) }}
                    <div class="d-flex align-items-center justify-content-end">
                        <div class="col-xl-2 col-lg-3 col-sm-12 col-12 mx-2">
                            <div class="btn-box">
                                {{ Form::label('start_date', __('Start Date'), ['class' => 'form-label']) }}
                                {{ Form::date('start_date', isset($_GET['start_date']) ? $_GET['start_date'] : ''   , ['class' => 'month-btn form-control ']) }}
                            </div>
                        </div>
                        <div class="col-xl-2 col-lg-3 col-sm-12 col-12 mx-2">
                            <div class="btn-box">
                                {{ Form::label('end_date', __('End Date'), ['class' => 'form-label']) }}
                                {{ Form::date('end_date', isset($_GET['end_date']) ? $_GET['end_date'] : '', ['class' => 'month-btn form-control  ', 'autocomplete' => 'off']) }}
                            </div>
                        </div>
                        <div class="col-xl-2 col-lg-3 col-sm-12 col-12 mx-2">
                            <div class="btn-box">
                                {{ Form::label('stage', __('Stage'), ['class' => 'form-label']) }}
                                {{ Form::select('stage', $stage, $filter['stage'], ['class' => 'form-control select ', 'id' => 'stage_id']) }}
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-3 col-md-6 col-sm-12 col-12 mx-2">
                            <div class="btn-box">
                                {{ Form::label('job', __('Job'), ['class' => 'form-label']) }}
                                {{ Form::select('job', $jobs, $filter['job'], ['class' => 'form-control select ', 'id' => 'job_id']) }}
                            </div>
                        </div>
                        <div class="col-auto float-end ms-2 mt-4">
                            <div class="d-flex">
                                <a class="btn btn-sm btn-primary me-2 "
                                    onclick="document.getElementById('applicarion_filter').submit(); return false;"
                                    data-bs-toggle="tooltip" title="{{ __('Apply') }}" data-bs-original-title="apply">
                                    <span class="btn-inner--icon"><i class="ti ti-search"></i></span>
                                </a>
                                <a href="{{ route('job.list') }}" class="btn btn-sm btn-danger" data-bs-toggle="tooltip"
                                    title="{{ __('Reset') }}" data-bs-original-title="Reset">
                                    <span class="btn-inner--icon"><i class="ti ti-trash-off text-white-off "></i></span>
                                </a>
                            </div>
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table mb-0 pc-dt-simple" id="assets">
                            <thead>
                                <tr>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Job') }}</th>
                                    <th>{{ __('Stage') }}</th>
                                    <th>{{ __('CV / Resume') }}</th>
                                    <th>{{ __('Rating') }}</th>
                                    <th>{{ __('Created At') }}</th>
                                    @if (Laratrust::hasPermission('job edit') ||
                                            Laratrust::hasPermission('job delete') ||
                                            Laratrust::hasPermission('job show'))
                                        <th width="200px">{{ __('Action') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($stages as $key => $stage)
                                    @php
                                        $applications = $stage->applications($filter);
                                    @endphp
                                    @foreach ($applications as $application)
                                        <tr>
                                            <td>
                                                <a class=" text-primary"
                                                    href="{{ route('job-application.show', \Crypt::encrypt($application->id)) }}">{{ !empty($application->name) ? $application->name : '' }}</a>
                                            </td>
                                            <td>{{ !empty($application->jobs) ? $application->jobs->title : '' }}</td>
                                            <td>{{ !empty($application->stages) ? $application->stages->title : '' }}</td>
                                            <td>
                                                @if (!empty($application->resume))
                                                    <span class="text-sm action-btn bg-primary ms-2">
                                                        <a class=" btn btn-sm align-items-center"
                                                            href="{{ get_file($application->resume) }}"
                                                            data-bs-toggle="tooltip"
                                                            data-bs-original-title="{{ __('download') }}" download=""><i
                                                                class="ti ti-download text-white"></i></a>
                                                    </span>

                                                    <div class="action-btn bg-secondary ms-2 ">
                                                        <a class=" mx-3 btn btn-sm align-items-center"
                                                            href="{{ get_file($application->resume) }}" target="_blank">
                                                            <i class="ti ti-crosshair text-white" data-bs-toggle="tooltip"
                                                                data-bs-original-title="{{ __('Preview') }}"></i>
                                                        </a>
                                                    </div>
                                                @else
                                                    <div class="mx-4">-</div>
                                                @endif
                                            </td>
                                            <td><span class="static-rating static-rating-sm d-block">
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        @if ($i <= $application->rating)
                                                            <i class="star fas fa-star voted"></i>
                                                        @else
                                                            <i class="star fas fa-star"></i>
                                                        @endif
                                                    @endfor
                                                </span></td>
                                            <td>{{ company_date_formate($application->created_at) }}</td>
                                            <td class="Action">
                                                @if (Laratrust::hasPermission('jobapplication delete') || Laratrust::hasPermission('jobapplication show'))
                                                    <span>
                                                        @permission('jobapplication delete')
                                                            <div class="action-btn me-2">
                                                                {!! Form::open(['method' => 'DELETE', 'route' => ['job.application.archive', $application->id]]) !!}
                                                                    <a href="#" class="btn btn-sm bg-primary bs-pass-para show_confirm align-items-center" data-bs-toggle="tooltip" aria-label="Archive" title="{{ __('Archive') }}">
                                                                        @if ($application->is_archive == 0)
                                                                            <i class="ti ti-archive text-white"></i>
                                                                        @endif
                                                                    </a>
                                                                {!! Form::close() !!}
                                                            </div>
                                                        @endpermission
                                                        @permission('jobapplication show')
                                                            <div class="action-btn me-2">
                                                                <a href="{{ route('job-application.show', \Crypt::encrypt($application->id)) }}"
                                                                    class="btn btn-sm align-items-center bg-warning "
                                                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                                                    title="{{ __('View') }}">
                                                                    <i class="ti ti-eye text-white"></i>
                                                                </a>
                                                            </div>
                                                        @endpermission
                                                        @permission('jobapplication delete')
                                                            <div class="action-btn">
                                                                {!! Form::open([
                                                                    'method' => 'DELETE',
                                                                    'route' => ['job-application.destroy', $application->id],
                                                                    'id' => 'delete-form-' . $application->id,
                                                                ]) !!}
                                                                <a href="#!"
                                                                    class="btn btn-sm align-items-center bs-pass-para show_confirm bg-danger"
                                                                    data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                                    title="{{ __('Delete') }}" data-text="{{ __('This action can not be undone. Do you want to continue?') }}">
                                                                    <i class="ti ti-trash text-white"></i></a>
                                                                {!! Form::close() !!}
                                                            </div>
                                                        @endpermission
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
