@extends('layouts.main')
@section('page-title')
    {{ __('Job Application Details') }}
@endsection
@push('css')
    <link href="{{ asset('packages/workdo/Recruitment/src/Resources/assets/css/bootstrap-tagsinput.css') }}"
        rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('packages/workdo/Recruitment/src/Resources/assets/css/custom.css') }}">
@endpush

@section('page-breadcrumb')
    {{ __('Job Application') }}
@endsection

@push('scripts')
    <script src="{{ asset('packages/workdo/Recruitment/src/Resources/assets/js/bootstrap-tagsinput.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/dropzone-amd-module.min.js') }}"></script>

    <script>
        Dropzone.autoDiscover = false;
        myDropzone = new Dropzone("#dropzonewidget", {
            url: "{{ route('jobapplication.file.upload', [$jobApplication->id]) }}",
            success: function(file, response) {
                if (response.is_success) {
                    // dropzoneBtn(file, response);
                    location.reload();
                    myDropzone.removeFile(file);
                    toastrs('{{ __('Success') }}', 'File Successfully Uploaded', 'success');
                } else {
                    location.reload();
                    myDropzone.removeFile(response.error);
                    toastrs('Error', response.error, 'error');
                }
            },
            error: function(file, response) {
                myDropzone.removeFile(file);
                location.reload();
                if (response.error) {
                    toastrs('Error', response.error, 'error');
                } else {
                    toastrs('Error', response, 'error');
                }
            }
        });
        myDropzone.on("sending", function(file, xhr, formData) {
            formData.append("_token", $('meta[name="csrf-token"]').attr('content'));
            formData.append("jobapplication_id", {{ $jobApplication->id }});
        });
    </script>

    <script>
        var e = $('[data-toggle="tags"]');
        e.length && e.each(function() {
            $(this).tagsinput({
                tagClass: "badge badge-primary"
            })
        });

        $(document).ready(function() {

            /* 1. Visualizing things on Hover - See next part for action on click */
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


            /* 2. Action to perform on click */
            $('#stars li').on('click', function() {

                var onStar = parseInt($(this).data('value'), 10); // The star currently selected
                var stars = $(this).parent().children('li.star');

                for (i = 0; i < stars.length; i++) {
                    $(stars[i]).removeClass('selected');
                }

                for (i = 0; i < onStar; i++) {
                    $(stars[i]).addClass('selected');
                }

                // JUST RESPONSE (Not needed)
                var ratingValue = parseInt($('#stars li.selected').last().data('value'), 10);
                $.ajax({
                    url: '{{ route('job.application.rating', $jobApplication->id) }}',
                    type: 'POST',
                    data: {
                        rating: ratingValue,
                        "_token": $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(data) {
                        toastrs('Success', 'The candidate rating successfully added',
                            'success');
                    },
                    error: function(data) {
                        data = data.responseJSON;
                        toastrs('Error', data.error, 'error')
                    }
                });

            });

        });
        $(document).on('change', '.stages', function() {
            var id = $(this).val();
            var schedule_id = $(this).attr('data-scheduleid');

            $.ajax({
                url: "{{ route('job.application.stage.change') }}",
                type: 'POST',
                data: {
                    "stage": id,
                    "schedule_id": schedule_id,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(data) {
                    toastrs('Success', 'The candidate stage successfully changed', 'success');
                    setTimeout(function() {
                        window.location.reload();
                    }, 1000);
                }
            });
        });
    </script>
@endpush
@section('content')
    <div class="row justify-content-end align-items-center mb-3">
        <div class="col-md-8">
            <ul class="nav nav-pills nav-fill cust-nav information-tab" id="pills-tab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="details-tab" data-bs-toggle="pill" data-bs-target="#details"
                        type="button">{{ __('Details') }}</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="interview-tab" data-bs-toggle="pill" data-bs-target="#interview"
                        type="button">{{ __('Interview') }}</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="rating-tab" data-bs-toggle="pill" data-bs-target="#rating"
                        type="button">{{ __('Rating') }}</button>
                </li>
                @if (module_is_active('FileSharing'))
                    @permission('jobapplication attachment manage')
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="attachment-tab" data-bs-toggle="pill" data-bs-target="#attachment"
                                type="button">{{ __('Attachment') }}</button>
                        </li>
                    @endpermission
                @endif
                @permission('jobapplication note manage')
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="notes-tab" data-bs-toggle="pill" data-bs-target="#notes"
                            type="button">{{ __('Notes') }}</button>
                    </li>
                @endpermission
                @permission('jobapplication todo manage')
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="to-do-tab" data-bs-toggle="pill" data-bs-target="#to-do"
                            type="button">{{ __('To Do') }}</button>
                    </li>
                @endpermission
                @if (module_is_active('ActivityLog'))
                    @permission('jobapplication activity manage')
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="activity-log-tab" data-bs-toggle="pill" data-bs-target="#activity-log"
                                type="button">{{ __('Activity Log') }}</button>
                        </li>
                    @endpermission
                @endif
            </ul>
        </div>
    </div>
    <div class="row">
        <div class="job-card">
            <div class="col-lg-12">
                <div class="tab-content" id="pills-tabContent">
                    <div class="tab-pane fade show active" id="details" role="tabpanel"
                        aria-labelledby="pills-user-tab-1">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="row">
                                            <div class="col-6">
                                                <h5>{{ __('Basic Details') }}</h5>
                                            </div>
                                            <div class="col text-end">
                                                <ul class="list-inline mb-0">
                                                    @permission('jobapplication delete')
                                                        <li class="list-inline-item mb-1">
                                                            {!! Form::open(['method' => 'DELETE', 'route' => ['job.application.archive', $jobApplication->id]]) !!}
                                                            <a class="bs-pass-para show_confirm" data-bs-toggle="tooltip"
                                                                title="" data-bs-original-title="Archive"
                                                                aria-label="Delete">
                                                                @if ($jobApplication->is_archive == 0)
                                                                    <span
                                                                        class="badge bg-info p-2 px-3 ">{{ __('Archive') }}</span>
                                                                @else
                                                                    <span
                                                                        class="badge bg-warning p-2 px-3 ">{{ __('UnArchive') }}</span>
                                                                @endif
                                                            </a>
                                                            {!! Form::close() !!}

                                                        </li>
                                                        @if ($jobApplication->is_archive == 0)
                                                            <li class="list-inline-item">
                                                                {!! Form::open([
                                                                    'method' => 'DELETE',
                                                                    'route' => ['job-application.destroy', $jobApplication->id],
                                                                    'id' => 'delete-form-' . $jobApplication->id,
                                                                ]) !!}
                                                                <a class="bs-pass-para show_confirm" data-bs-toggle="tooltip"
                                                                    title="" data-bs-original-title="Delete"
                                                                    aria-label="Delete" data-confirm="{{ __('Are You Sure?') }}" data-text="{{__('This action can not be undone. Do you want to continue?')}}"><span
                                                                        class="badge bg-danger p-2 px-3 ">{{ __('Delete') }}</span></a>
                                                                {!! Form::close() !!}
                                                            </li>
                                                        @endif
                                                    @endpermission
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card-body job-app">
                                        <h5 class="h4">
                                            <div class="d-flex align-items-center" data-toggle="tooltip"
                                                data-placement="right" data-title="2 hrs ago" data-original-title=""
                                                title="">
                                                <div>
                                                    <a href="{{ check_file($jobApplication->profile) ? get_file($jobApplication->profile) : get_file('uploads/users-avatar/avatar.png') }}" target="_blank" class=" avatar-sm">
                                                        <img src="{{ check_file($jobApplication->profile) ? get_file($jobApplication->profile) : get_file('uploads/users-avatar/avatar.png') }}"
                                                            class="rounded border-2 border border-primary" width="50px"
                                                            height="50px">
                                                    </a>
                                                </div>
                                                <div class="flex-fill ms-3">
                                                    <div class="h6 text-sm mb-0"> {{ $jobApplication->name }}</div>
                                                    <p class="text-sm lh-140 mb-0">
                                                        {{ $jobApplication->email }}
                                                    </p>
                                                </div>
                                            </div>
                                        </h5>
                                        <div class="py-2 my-4 border-top ">
                                            <div class="row  my-3">
                                                @foreach ($stages as $stage)
                                                <div class="col-md-6">
                                                    <div class="form-check form-check-inline form-group">
                                                        <input type="radio" id="stage_{{ $stage->id }}"
                                                            name="stage" data-scheduleid="{{ $jobApplication->id }}"
                                                            value="{{ $stage->id }}" class="form-check-input stages"
                                                            {{ $jobApplication->stage == $stage->id ? 'checked' : '' }}>
                                                        <label class="form check-label"
                                                            for="stage_{{ $stage->id }}">{{ $stage->title }}</label>
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="row">
                                            <div class="col-6">
                                                <h5>{{ __('Basic Information') }}</h5>
                                            </div>
                                            <div class="col text-end">
                                                @if ($jobOnBoards == null)
                                                    <div class="col-12 text-end">
                                                        <a data-url="{{ route('job.on.board.create', $jobApplication->id) }}"
                                                            data-ajax-popup="true"
                                                            class="btn-sm btn btn-primary text-white"
                                                            data-title="{{ __('Create Job OnBoard') }}"
                                                            data-bs-original-title="{{ __('Create Job OnBoard') }}">
                                                            <i class="ti ti-plus "></i>{{ __('Add to Job OnBoard') }}</a>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card-body job-app">
                                        <dl class="row">

                                            <dt class="col-sm-3"><span class="h6 text-md mb-0">{{ __('Phone') }}</span>
                                            </dt>
                                            <dd class="col-sm-9"><span
                                                    class="text-md">{{ $jobApplication->phone }}</span></dd>
                                            @if (!empty($jobApplication->dob))
                                                <dt class="col-sm-3"><span
                                                        class="h6 text-md mb-0">{{ __('DOB') }}</span></dt>
                                                <dd class="col-sm-9"><span
                                                        class="text-md">{{ company_date_formate($jobApplication->dob) }}</span>
                                                </dd>
                                            @endif
                                            @if (!empty($jobApplication->gender))
                                                <dt class="col-sm-3"><span
                                                        class="h6 text-md mb-0">{{ __('Gender') }}</span></dt>
                                                <dd class="col-sm-9"><span
                                                        class="text-md">{{ $jobApplication->gender }}</span></dd>
                                            @endif
                                            @if (!empty($jobApplication->country))
                                                <dt class="col-sm-3"><span
                                                        class="h6 text-md mb-0">{{ __('Country') }}</span></dt>
                                                <dd class="col-sm-9"><span
                                                        class="text-md">{{ $jobApplication->country }}</span>
                                                </dd>
                                            @endif
                                            @if (!empty($jobApplication->state))
                                                <dt class="col-sm-3"><span
                                                        class="h6 text-md mb-0">{{ __('State') }}</span></dt>
                                                <dd class="col-sm-9"><span
                                                        class="text-md">{{ $jobApplication->state }}</span></dd>
                                            @endif
                                            @if (!empty($jobApplication->city))
                                                <dt class="col-sm-3"><span
                                                        class="h6 text-md mb-0">{{ __('City') }}</span></dt>
                                                <dd class="col-sm-9"><span
                                                        class="text-md">{{ $jobApplication->city }}</span></dd>
                                            @endif

                                            <dt class="col-sm-3"><span
                                                    class="h6 text-md mb-0">{{ __('Applied For') }}</span></dt>
                                            <dd class="col-sm-9"><span
                                                    class="text-md">{{ !empty($jobApplication->jobs) ? $jobApplication->jobs->title : '-' }}</span>
                                            </dd>

                                            <dt class="col-sm-3"><span
                                                    class="h6 text-md mb-0">{{ __('Applied at') }}</span></dt>
                                            <dd class="col-sm-9"><span
                                                    class="text-md">{{ company_date_formate($jobApplication->created_at) }}</span>
                                            </dd>
                                            <dt class="col-sm-3"><span
                                                    class="h6 text-md mb-0">{{ __('CV / Resume') }}</span></dt>
                                                    <dd class="col-sm-9">
                                                        @if (!empty($jobApplication->resume))
                                                            <span class="text-md action-btn bg-primary me-2">
                                                                <a class=" btn btn-sm align-items-center"
                                                                    href="{{ get_file($jobApplication->resume) }}"
                                                                    data-bs-toggle="tooltip"
                                                                    data-bs-original-title="{{ __('download') }}"
                                                                    download=""><i class="ti ti-download text-white"></i></a>
                                                            </span>

                                                            <div class="action-btn bg-secondary">
                                                                <a class=" mx-3 btn btn-sm align-items-center"
                                                                    href="{{ get_file($jobApplication->resume) }}"
                                                                    target="_blank">
                                                                    <i class="ti ti-crosshair text-white" data-bs-toggle="tooltip"
                                                                        data-bs-original-title="{{ __('Preview') }}"></i>
                                                                </a>
                                                            </div>
                                                        @else
                                                            <div class="mx-4">-</div>
                                                        @endif
                                                    </dd>
                                            <dt class="col-sm-3"><span
                                                    class="h6 text-md mb-0">{{ __('Cover Letter') }}</span></dt>
                                            <dd class="col-sm-9">
                                                @if (!empty($jobApplication->cover_letter))
                                                    <span class="text-md">{{ $jobApplication->cover_letter }}</span>
                                                @else
                                                    <div class="mx-4">-</div>
                                                @endif
                                            </dd>

                                        </dl>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card card-fluid">
                                    <div class="card-header">
                                        <div class="row">
                                            <div class="col-6">
                                                <h5>{{ __('Additional Details') }}</h5>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        {{ Form::open(['route' => ['job.application.skill.store', $jobApplication->id], 'method' => 'post']) }}
                                        <div class="form-group">
                                            <label class="form-label">{{ __('Skills') }}</label>
                                            <input type="text" class="form-control"
                                                value="{{ $jobApplication->skill }}" data-toggle="tags" name="skill"
                                                placeholder="{{ __('Type here....') }}" />
                                        </div>
                                        @permission('jobapplication add skill')
                                            <div class="form-group">
                                                <input type="submit" value="{{ __('Add Skills') }}"
                                                    class="btn-sm btn btn-primary">
                                            </div>
                                        @endpermission
                                        {{ Form::close() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="interview" role="tabpanel" aria-labelledby="pills-user-tab-2">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="row">
                                            <div class="col-6">
                                                <h5>{{ __('Interview details') }}</h5>
                                            </div>
                                            @permission('interview schedule create')
                                                <div class="col-6 text-end create_btn">
                                                    <a data-url="{{ route('interview-schedule.create', $jobApplication->id) }}"
                                                        data-size="md" class="btn-sm btn btn-primary text-white"
                                                        data-ajax-popup="true"
                                                        data-title="{{ __('Create Interview Schedule') }}">
                                                        <i class="ti ti-plus "></i> {{ __('Create Interview Schedule') }}
                                                    </a>
                                                </div>
                                            @endpermission
                                        </div>
                                    </div>
                                    <div class="card-body ">
                                        <div class="table-responsive">
                                            <table class="table mb-0 pc-dt-simple" id="products">
                                                <thead>
                                                    <tr>
                                                        <th>{{ __('#') }}</th>
                                                        <th>{{ __('Assign Employee') }}</th>
                                                        <th>{{ __('Interview Date') }}</th>
                                                        <th>{{ __('Interview Time') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $key = 0;
                                                    @endphp
                                                    @forelse ($interview as $interviews)
                                                        <tr>
                                                            <td>{{ ++$key }}</td>
                                                            <td>{{ !empty($interviews->employee) ? $interviews->users->name : '-' }}
                                                            </td>
                                                            <td>{{ !empty($interviews->date) ? company_date_formate($interviews->date) : '-' }}
                                                            </td>
                                                            <td>{{ !empty($interviews->time) ? company_Time_formate($interviews->time) : '-' }}
                                                            </td>
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
                    <div class="tab-pane fade" id="rating" role="tabpanel" aria-labelledby="pills-user-tab-3">
                        <div class="row">
                            <div class="col-sm-6 d-flex">
                                <div class="card employee-detail-body w-100 fulls-card">
                                    <div class="card-header">
                                        <h5>{{ __('Rating details') }}</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class='rating-stars'>
                                                    <ul id='stars'>
                                                        <li class='star {{ in_array($jobApplication->rating, [1, 2, 3, 4, 5]) == true ? 'selected' : '' }}'
                                                            data-bs-toggle="tooltip" data-bs-original-title="Poor"
                                                            data-value='1'>
                                                            <i class='fa fa-star fa-fw'></i>
                                                        </li>
                                                        <li class='star {{ in_array($jobApplication->rating, [2, 3, 4, 5]) == true ? 'selected' : '' }}'
                                                            data-bs-toggle="tooltip" data-bs-original-title='Fair'
                                                            data-value='2'>
                                                            <i class='fa fa-star fa-fw'></i>
                                                        </li>
                                                        <li class='star {{ in_array($jobApplication->rating, [3, 4, 5]) == true ? 'selected' : '' }}'
                                                            data-bs-toggle="tooltip" data-bs-original-title='Good'
                                                            data-value='3'>
                                                            <i class='fa fa-star fa-fw'></i>
                                                        </li>
                                                        <li class='star {{ in_array($jobApplication->rating, [4, 5]) == true ? 'selected' : '' }}'
                                                            data-bs-toggle="tooltip" data-bs-original-title='Excellent'
                                                            data-value='4'>
                                                            <i class='fa fa-star fa-fw'></i>
                                                        </li>
                                                        <li class='star {{ in_array($jobApplication->rating, [5]) == true ? 'selected' : '' }}'
                                                            data-bs-toggle="tooltip" data-bs-original-title='WOW!!!'
                                                            data-value='5'>
                                                            <i class='fa fa-star fa-fw'></i>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 d-flex">
                                <div class="card employee-detail-body w-100 fulls-card">
                                    <div class="card-header">
                                        <h5>{{ __('Question & Answer') }}</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-12">
                                                @if (!empty(json_decode($jobApplication->custom_question)))
                                                    <div class="list-group list-group-flush mb-4">
                                                        @foreach (json_decode($jobApplication->custom_question) as $que => $ans)
                                                            @if (!empty($ans))
                                                                <div class="list-group-item px-0">
                                                                    <div class="row align-items-center">
                                                                        <div class="col">
                                                                            <a
                                                                                class="d-block h6 text-md mb-0">{{ $que }}</a>
                                                                            <p class="card-text text-md text-muted mb-0">
                                                                                {{ $ans }}
                                                                            </p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 d-flex">
                                <div class="card employee-detail-body w-100 fulls-card">
                                    <div class="card-header">
                                        <h5>{{ __('Applicant Notes') }}</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-12">
                                                {{ Form::open(['route' => ['job.application.note.store', $jobApplication->id], 'method' => 'post']) }}
                                                <div class="form-group">
                                                    <label class="form-label">{{ __('Applicant Notes') }}</label>
                                                    <textarea name="note" class="form-control" id="" rows="3" placeholder="{{__('Enter Application Notes')}}"></textarea>
                                                </div>
                                                @permission('jobapplication add note')
                                                        <input type="submit" value="{{ __('Add Notes') }}"
                                                            class="btn-sm btn btn-primary">
                                                @endpermission
                                                {{ Form::close() }}
                                                <div class="list-group list-group-flush">
                                                    @foreach ($notes as $note)
                                                        <div class="list-group-item px-0">
                                                            <div class="row align-items-center">
                                                                <div class="col">
                                                                    <a href="#!"
                                                                        class="d-block h6 text-sm mb-0">{{ !empty($note->note_created) ? $note->noteCreated->name : '-' }}</a>
                                                                    <p class="card-text text-sm text-muted mb-0">
                                                                        {{ $note->note }}
                                                                    </p>
                                                                </div>
                                                                <div class="col-auto">
                                                                    <a class="">
                                                                        {{ company_date_formate($note->created_at) }}</a>
                                                                </div>
                                                                @permission('jobapplication delete note')
                                                                    @if ($note->note_created == creatorId())
                                                                        <div class="col-auto text-end">
                                                                            {!! Form::open([
                                                                                'method' => 'DELETE',
                                                                                'route' => ['job.application.note.destroy', $note->id],
                                                                                'id' => 'delete-form' . $note->id,
                                                                            ]) !!}
                                                                            <a class="mx-3 btn btn-sm btn btn-danger  align-items-center bs-pass-para show_confirm"
                                                                                data-bs-toggle="tooltip" title=""
                                                                                data-bs-original-title="Delete"
                                                                                aria-label="Delete" data-confirm="{{ __('Are You Sure?') }}" data-text="{{__('This action can not be undone. Do you want to continue?')}}"><i
                                                                                    class="ti ti-trash text-white"></i></a>
                                                                            </form>
                                                                        </div>
                                                                    @endif
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
                    </div>
                    <div class="tab-pane fade" id="attachment" role="tabpanel" aria-labelledby="pills-user-tab-5">
                        <div class="row">
                            @permission('jobapplication attachment manage')
                                <h5 class="d-inline-block my-3">{{ __('Attachments') }}</h5>
                                @permission('jobapplication attachment upload')
                                    <div class="col-3">
                                        <div class="card border-primary border">
                                            <div class="card-body table-border-style">
                                                <div class="col-md-12 dropzone browse-file" id="dropzonewidget">
                                                    <div class="dz-message my-5" data-dz-message>
                                                        <span>{{ __('Drop files here to upload') }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endpermission
                                <div class="col-9">
                                    <div class="card border-primary border">
                                        <div class="card-body table-border-style">
                                            <div class="table-responsive">
                                                <table class="table mb-0 pc-dt-simple" id="assets">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-dark">{{ __('#') }}</th>
                                                            <th class="text-dark">{{ __('File Name') }}</th>
                                                            <th class="text-dark">{{ __('File Size') }}</th>
                                                            <th class="text-dark">{{ __('Date Created') }}</th>
                                                            @if (Laratrust::hasPermission('jobapplication attachment delete'))
                                                                <th class="text-dark">{{ __('Action') }}</th>
                                                            @endif
                                                        </tr>
                                                    </thead>
                                                    @forelse($jobApplication_attachments as $key =>$jobApplication_attachment)
                                                        <td>{{ ++$key }}</td>
                                                        <td>{{ $jobApplication_attachment->file_name }}</td>
                                                        <td>{{ $jobApplication_attachment->file_size }}</td>
                                                        <td>{{ company_date_formate($jobApplication_attachment->created_at) }}
                                                        </td>
                                                        <td>
                                                            <div class="action-btn bg-primary ms-2">
                                                                <a href="{{ url($jobApplication_attachment->file_path) }}"
                                                                    class="mx-3 btn btn-sm align-items-center"
                                                                    title="{{ __('Download') }}"
                                                                    data-bs-original-title="{{ __('Download') }}"
                                                                    target="_blank" download>
                                                                    <i class="ti ti-download text-white"></i>
                                                                </a>
                                                            </div>
                                                            @permission('jobapplication attachment delete')
                                                                <div class="action-btn">
                                                                    {{ Form::open(['route' => ['jobapplication.attachment.destroy', $jobApplication_attachment->id], 'class' => 'm-0']) }}
                                                                    @method('DELETE')
                                                                    <a href="#"
                                                                        class="mx-3 btn btn-sm  align-items-center bs-pass-para show_confirm bg-danger"
                                                                        data-bs-toggle="tooltip" title=""
                                                                        aria-label="Delete"
                                                                        data-bs-original-title="{{ __('Delete') }}"
                                                                        data-confirm="{{ __('Are You Sure?') }}"
                                                                        data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                                        data-confirm-yes="delete-form-{{ $jobApplication_attachment->id }}">
                                                                        <i class="ti ti-trash text-white text-white"></i>
                                                                    </a>
                                                                    {{ Form::close() }}
                                                                </div>
                                                            @endpermission
                                                        </td>
                                                        </tr>
                                                    @empty
                                                        @include('layouts.nodatafound')
                                                    @endforelse
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endpermission
                        </div>
                    </div>
                    <div class="tab-pane fade" id="notes" role="tabpanel" aria-labelledby="pills-user-tab-4">
                        <div class="row">
                            @permission('jobapplication note manage')
                                <div class="col-sm-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <div class="row">
                                                <div class="col-6">
                                                    <h5>{{ __('Notes') }}</h5>
                                                </div>
                                                @permission('jobapplication note create')
                                                    <div class="col-6 text-end create_btn">
                                                        <a data-url="{{ route('jobapplicationnote.create', ['id' => $jobApplication->id]) }}"
                                                            data-ajax-popup="true" data-size="md"
                                                            data-title="{{ __('Create Note') }}" data-bs-toggle="tooltip"
                                                            title="" class="btn btn-sm btn-primary"
                                                            data-bs-original-title="{{ __('Create') }}">
                                                            <i class="ti ti-plus"></i>
                                                        </a>
                                                    </div>
                                                @endpermission
                                            </div>
                                        </div>
                                        <div class="card-body table-border-style">
                                            <div class="table-responsive">
                                                <table class="table mb-0 pc-dt-simple" id="products1">
                                                    <thead>
                                                        <tr>
                                                            <th>{{ __('created_by') }}</th>
                                                            <th>{{ __('Description') }}</th>
                                                            @if (Laratrust::hasPermission('jobapplication note show') ||
                                                                    Laratrust::hasPermission('jobapplication note edit') ||
                                                                    Laratrust::hasPermission('jobapplication note delete'))
                                                                <th>{{ __('Action') }}</th>
                                                            @endif
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse ($jobApplication_notes as $jobApplicationNote)
                                                            <tr>
                                                                <td>{{ !empty($jobApplicationNote->created_by) ? $jobApplicationNote->createdBy->name : '' }}
                                                                </td>
                                                                <td>
                                                                    <p class="job-to-do">
                                                                        {{ !empty($jobApplicationNote->description) ? $jobApplicationNote->description : '' }}
                                                                    </p>
                                                                </td>
                                                                @if (Laratrust::hasPermission('jobapplication note show') ||
                                                                        Laratrust::hasPermission('jobapplication note edit') ||
                                                                        Laratrust::hasPermission('jobapplication note delete'))
                                                                    <td class="Action">
                                                                        <span>
                                                                            @permission('jobapplication note show')
                                                                                <div class="action-btn me-2">
                                                                                    <a data-url="{{ route('jobapplicationnote.description', $jobApplicationNote->id) }}"
                                                                                        class="mx-3 btn btn-sm  align-items-center bg-warning"
                                                                                        data-ajax-popup="true"
                                                                                        data-bs-original-title="{{ __('View') }}"
                                                                                        data-bs-toggle="tooltip" data-size="md"
                                                                                        data-bs-placement="top"
                                                                                        data-title="{{ __('Desciption') }}">
                                                                                        <i class="ti ti-eye text-white"></i>
                                                                                    </a>
                                                                                </div>
                                                                            @endpermission
                                                                            @permission('jobapplication note edit')
                                                                                <div class="action-btn me-2">
                                                                                    <a data-url="{{ route('jobapplicationnote.edit', $jobApplicationNote->id) }}"
                                                                                        data-ajax-popup="true" data-size="md"
                                                                                        data-title="{{ __('Edit Note') }}"
                                                                                        data-bs-toggle="tooltip" title=""
                                                                                        data-bs-original-title="{{ __('Edit') }}"
                                                                                        class="mx-3 btn btn-sm  align-items-center bg-info">
                                                                                        <i class="ti ti-pencil text-white"></i>
                                                                                    </a>
                                                                                </div>
                                                                            @endpermission
                                                                            @permission('jobapplication note delete')
                                                                                <div class="action-btn delete_btn">
                                                                                    {{ Form::open(['route' => ['jobapplicationnote.destroy', $jobApplicationNote->id], 'class' => 'm-0']) }}
                                                                                    @method('DELETE')
                                                                                    <a class="mx-3 btn btn-sm  align-items-center bs-pass-para show_confirm bg-danger"
                                                                                        data-bs-toggle="tooltip" title=""
                                                                                        aria-label="Delete"
                                                                                        data-bs-original-title="{{ __('Delete') }}"
                                                                                        data-confirm="{{ __('Are You Sure?') }}"
                                                                                        data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                                                        data-confirm-yes="delete-form-{{ $jobApplicationNote->id }}"><i
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
                            @endpermission
                        </div>
                    </div>
                    <div class="tab-pane fade" id="to-do" role="tabpanel" aria-labelledby="pills-user-tab-4">
                        <div class="row">
                            @permission('jobapplication todo manage')
                                <div class="col-sm-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <div class="row">
                                                <div class="col-6">
                                                    <h5>{{ __('To Do') }}</h5>
                                                </div>
                                                @permission('jobapplication todo create')
                                                    <div class="col-6 text-end create_btn">
                                                        <a data-url="{{ route('jobapplicationtodo.create', ['id' => $jobApplication->id]) }}"
                                                            data-ajax-popup="true" data-size="md"
                                                            data-title="{{ __('Create Todo') }}"
                                                            data-bs-toggle="tooltip" title=""
                                                            class="btn btn-sm btn-primary"
                                                            data-bs-original-title="{{ __('Create') }}">
                                                            <i class="ti ti-plus"></i>
                                                        </a>
                                                    </div>
                                                @endpermission
                                            </div>
                                        </div>
                                        <div class="card-body table-border-style">
                                            <div class="table-responsive">
                                                <table class="table mb-0 pc-dt-simple" id="products2">
                                                    <thead>
                                                        <tr>
                                                            <th>{{ __('Title') }}</th>
                                                            <th>{{ __('Description') }}</th>
                                                            <th>{{ __('Due date') }}</th>
                                                            <th>{{ __('Assigned by') }}</th>
                                                            <th>{{ __('Assigned to') }}</th>
                                                            @if (Laratrust::hasPermission('jobapplication todo show') ||
                                                                    Laratrust::hasPermission('jobapplication todo edit') ||
                                                                    Laratrust::hasPermission('jobapplication todo delete'))
                                                                <th>{{ __('Action') }}</th>
                                                            @endif
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse ($jobapplication_todos as $todo)
                                                            <tr>
                                                                <td>{{ !empty($todo->title) ? $todo->title : '' }}
                                                                <td>
                                                                    <p class="job-to-do">
                                                                        {{ !empty($todo->description) ? $todo->description : '' }}
                                                                    </p>
                                                                </td>
                                                                <td>{{ !empty($todo->due_date) ? company_date_formate($todo->due_date) : '' }}
                                                                <td>{{ !empty($todo->assign_by) ? $todo->assignedByUser->name : '' }}
                                                                </td>
                                                                <td>
                                                                    @if ($users = $todo->users())
                                                                        @foreach ($users as $key => $user)
                                                                            @if ($key < 3)
                                                                                <img alt="image" data-bs-toggle="tooltip"
                                                                                    data-bs-placement="top"
                                                                                    title="{{ $user->name }}"
                                                                                    @if ($user->avatar) src="{{ get_file($user->avatar) }}" @else src="{{ get_file('avatar.png') }}" @endif
                                                                                    class="rounded-circle " width="20px"
                                                                                    height="20px">
                                                                            @endif
                                                                        @endforeach
                                                                    @endif
                                                                </td>
                                                                @if (Laratrust::hasPermission('jobapplication todo show') ||
                                                                        Laratrust::hasPermission('jobapplication todo edit') ||
                                                                        Laratrust::hasPermission('jobapplication todo delete'))
                                                                    <td class="Action">
                                                                        <span>
                                                                            @permission('jobapplication todo show')
                                                                                <div class="action-btn me-2">
                                                                                    <a class="mx-3 btn btn-sm align-items-center bg-warning"
                                                                                        data-url="{{ route('jobapplicationtodo.show', $todo->id) }}"
                                                                                        data-ajax-popup="true" data-size="md"
                                                                                        data-title="{{ __('To Do Detail') }}"
                                                                                        data-bs-original-title="{{ __('View') }}"
                                                                                        data-bs-toggle="tooltip">
                                                                                        <i class="ti ti-eye text-white"></i>
                                                                                    </a>
                                                                                </div>
                                                                            @endpermission
                                                                            @permission('jobapplication todo edit')
                                                                                <div class="action-btn me-2">
                                                                                    <a data-url="{{ route('jobapplicationtodo.edit', $todo->id) }}"
                                                                                        data-ajax-popup="true" data-size="md"
                                                                                        data-title="{{ __('Edit Notes') }}"
                                                                                        data-bs-toggle="tooltip" title=""
                                                                                        data-bs-original-title="{{ __('Edit') }}"
                                                                                        class="mx-3 btn btn-sm  align-items-center bg-info">
                                                                                        <i class="ti ti-pencil text-white"></i>
                                                                                    </a>
                                                                                </div>
                                                                            @endpermission
                                                                            @permission('jobapplication todo delete')
                                                                                <div class="action-btn delete_btn">
                                                                                    {{ Form::open(['route' => ['jobapplicationtodo.destroy', $todo->id], 'class' => 'm-0']) }}
                                                                                    @method('DELETE')
                                                                                    <a class="mx-3 btn btn-sm  align-items-center bs-pass-para show_confirm bg-danger"
                                                                                        data-bs-toggle="tooltip" title=""
                                                                                        aria-label="Delete"
                                                                                        data-bs-original-title="{{ __('Delete') }}"
                                                                                        data-confirm="{{ __('Are You Sure?') }}"
                                                                                        data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                                                        data-confirm-yes="delete-form-{{ $todo->id }}"><i
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
                            @endpermission
                        </div>
                    </div>
                    <div class="tab-pane fade" id="activity-log" role="tabpanel" aria-labelledby="pills-user-tab-4">
                        <div class="row">
                            @permission('jobapplication activity manage')
                                <div class="col-sm-12">
                                    <div class="col-sm-12">
                                        <div class=" multi-collapse mt-2" id="multiCollapseExample1">
                                            <div class="card">
                                                <div class="card-body">
                                                    {{ Form::open(['route' => ['job-application.show', \Crypt::encrypt($jobApplication->id)], 'method' => 'GET', 'id' => 'module_form']) }}
                                                    <div class="row d-flex align-items-center justify-content-end">
                                                        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 mr-2">
                                                            <div class="btn-box">
                                                                <label for="staff">{{ __('Staff') }}</label>
                                                                <select class="form-control staff " name="staff"
                                                                    id="staff" tabindex="-1" aria-hidden="true">
                                                                    <option value="">{{ __('Select staff') }}</option>
                                                                    @foreach ($staffs as $staff)
                                                                        @if ($staff->id == $creatorId)
                                                                            <span class="badge bg-dark">
                                                                                {{ Auth::user()->roles->first()->name }}</span>
                                                                        @else
                                                                            <span class="badge bg-dark">
                                                                                {{ __('') }}</span>
                                                                        @endif
                                                                        <option value="{{ $staff->id }}"
                                                                            {{ isset(request()->staff) && request()->staff == $staff->id ? 'selected' : '' }}>
                                                                            {{ $staff->name }}@if ($staff->id == $creatorId)
                                                                                <span class="badge bg-dark">
                                                                                    {{ '(' . $staff->type . ')' }}</span>
                                                                            @endif
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <input type="hidden" name="filter" value="Job Application">
                                                        <div class="col-auto float-end ms-2 mt-4">
                                                            <div class="d-flex">
                                                                <a class="btn btn-sm btn-primary me-2"
                                                                    onclick="document.getElementById('module_form').submit(); return false;"
                                                                    data-bs-toggle="tooltip" title="{{ __('Apply') }}"
                                                                    data-original-title="{{ __('apply') }}">
                                                                    <span class="btn-inner--icon"><i
                                                                            class="ti ti-search"></i></span>
                                                                </a>
                                                                <a href="{{ route('job-application.show', \Crypt::encrypt($jobApplication->id)) }}"
                                                                    class="btn btn-sm btn-danger" data-bs-toggle="tooltip"
                                                                    title="{{ __('Reset') }}"
                                                                    data-original-title="{{ __('Reset') }}">
                                                                    <span class="btn-inner--icon"><i
                                                                            class="ti ti-trash-off text-white-off"></i></span>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    {{ Form::close() }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card">
                                        <div class="card-header">
                                            <div class="row">
                                                <div class="col-6">
                                                    <h5>{{ __('Job Activity Log') }}</h5>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body table-border-style">
                                            <div class="table-responsive">
                                                <table class="table mb-0 pc-dt-simple" id="products3">
                                                    <thead>
                                                        <tr>
                                                            <th>{{ __('Description') }}</th>
                                                            <th>{{ __('Staff') }}</th>
                                                            @if (Laratrust::hasPermission('jobapplication activity delete'))
                                                                <th>{{ __('Action') }}</th>
                                                            @endif
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse ($activitys as $activity)
                                                            <tr>
                                                                <td>{{ $activity->description . (!empty($activity->name) ? $activity->name : '') . '.' }}
                                                                </td>
                                                                <td>{{ !empty($activity->name) ? $activity->name : '--' }}
                                                                    @if (!empty($activity->user_id) && $activity->user_id == $creatorId)
                                                                        <span
                                                                            class="badge bg-primary p-2">{{ $activity->type }}</span>
                                                                    @endif
                                                                </td>
                                                                @if (Laratrust::hasPermission('jobapplication activity delete'))
                                                                    <td class="Action">
                                                                        <span>
                                                                            <div class="action-btn">
                                                                                {!! Form::open([
                                                                                    'method' => 'DELETE',
                                                                                    'route' => ['jobapplicationactivitylog.destroy', $activity->id],
                                                                                    'id' => 'delete-form-' . $activity->id,
                                                                                ]) !!}
                                                                                <a class="mx-3 btn btn-sm  align-items-center bs-pass-para show_confirm bg-danger"
                                                                                    data-bs-toggle="tooltip"
                                                                                    title="{{ __('Delete') }}"><i
                                                                                        class="ti ti-trash text-white text-white"></i></a>
                                                                                {!! Form::close() !!}
                                                                            </div>
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
                            @endpermission
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
