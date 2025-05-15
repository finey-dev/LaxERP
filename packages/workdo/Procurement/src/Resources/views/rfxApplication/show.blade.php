@extends('layouts.main')
@section('page-title')
    {{ __('RFx Application Details') }}
@endsection
@push('css')
    <link href="{{ asset('packages/workdo/Procurement/src/Resources/assets/css/bootstrap-tagsinput.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('packages/workdo/Procurement/src/Resources/assets/css/custom.css') }}">
    @include('layouts.includes.datatable-css')
@endpush

@section('page-breadcrumb')
    {{ __('Archive Application') }},
    {{ __('RFx Application') }}
@endsection

@push('scripts')
    <script src="{{ asset('packages/workdo/Procurement/src/Resources/assets/js/bootstrap-tagsinput.min.js') }}"></script>
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
                    url: '{{ route('rfx.application.rating', $rfxApplication->id) }}',
                    type: 'POST',
                    data: {
                        rating: ratingValue,
                        "_token": $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(data) {

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
                url: "{{ route('rfx.application.stage.change') }}",
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
     @include('layouts.includes.datatable-js')
     {{ $dataTable->scripts() }}
@endpush
@section('content')
    <div class="row">

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-auto">
                            <h6 class="text-muted">{{ __('Basic Details') }}</h6>
                        </div>
                        <div class="col text-end">
                            <ul class="list-inline mb-0">
                                @permission('rfxapplication delete')
                                    <li class="list-inline-item mb-1    ">
                                        {!! Form::open(['method' => 'DELETE', 'route' => ['rfx.application.archive', $rfxApplication->id]]) !!}
                                        <a href="#" class="bs-pass-para show_confirm" data-bs-toggle="tooltip" title=""
                                            data-bs-original-title="Archive" aria-label="Delete">
                                            @if ($rfxApplication->is_archive == 0)
                                                <span class="badge bg-info p-2 px-3">{{ __('Archive') }}</span>
                                            @else
                                                <span class="badge bg-warning p-2 px-3">{{ __('UnArchive') }}</span>
                                            @endif
                                        </a>
                                        {!! Form::close() !!}

                                    </li>
                                    @if ($rfxApplication->is_archive == 0)
                                        <li class="list-inline-item">
                                            {!! Form::open([
                                                'method' => 'DELETE',
                                                'route' => ['rfx-application.destroy', $rfxApplication->id],
                                                'id' => 'delete-form-' . $rfxApplication->id,
                                            ]) !!}
                                            <a href="#" class="bs-pass-para show_confirm" data-bs-toggle="tooltip" title=""
                                                data-bs-original-title="Delete" aria-label="Delete"><span
                                                    class="badge bg-danger p-2 px-3">{{ __('Delete') }}</span></a>
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
                        <div class="d-flex align-items-center" data-toggle="tooltip" data-placement="right"
                            data-title="2 hrs ago" data-original-title="" title="">
                            <div>
                                <a href="{{ check_file($rfxApplication->profile) ? get_file($rfxApplication->profile) : get_file('uploads/users-avatar/avatar.png') }}"
                                    target="_blank" class="avatar rounded-circle avatar-sm">
                                    <img src="{{ check_file($rfxApplication->profile) ? get_file($rfxApplication->profile) : get_file('uploads/users-avatar/avatar.png') }}"
                                        class="img-fluid rounded-circle border border-primary" width="50px" height="50px">
                                </a>
                            </div>
                            <div class="flex-fill ms-3">
                                <div class="h6 text-sm mb-0"> {{ $rfxApplication->name }}</div>
                                <p class="text-sm lh-140 mb-0">
                                    {{ $rfxApplication->email }}
                                </p>
                            </div>
                        </div>
                    </h5>
                    <div class="py-2 my-4 border-top ">
                        <div class="row align-items-center my-3">
                            @foreach ($stages as $stage)
                                <div class="form-check form-check-inline form-group">
                                    <input type="radio" id="stage_{{ $stage->id }}" name="stage"
                                        data-scheduleid="{{ $rfxApplication->id }}" value="{{ $stage->id }}"
                                        class="form-check-input stages"
                                        {{ $rfxApplication->stage == $stage->id ? 'checked' : '' }}>
                                    <label class="form check-label"
                                        for="stage_{{ $stage->id }}">{{ $stage->title }}</label>
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
                        <div class="col-auto">
                            <h6 class="text-muted">{{ __('Basic Information') }}</h6>
                        </div>
                        <div class="col text-end">
                            @if ($vendorOnBoards == null)
                                <div class="col-12 text-end">
                                    <a data-url="{{ route('vendor.on.board.create', $rfxApplication->id) }}"
                                        data-ajax-popup="true" class="btn-sm btn btn-primary text-white"
                                        data-title="{{ __('Add to Vendor OnBoard') }}"
                                        data-bs-original-title="{{ __('Add to Vendor OnBoard') }}">
                                        <i class="ti ti-plus me-1 "></i>{{ __('Add to Vendor OnBoard') }}</a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="card-body job-app">
                    <dl class="row">
                        <dt class="col-sm-3"><span class="h6 text-sm mb-0">{{ __('Phone') }}</span></dt>
                        <dd class="col-sm-9"><span class="text-sm">{{ $rfxApplication->phone }}</span></dd>
                        @if (!empty($rfxApplication->dob))
                            <dt class="col-sm-3"><span class="h6 text-sm mb-0">{{ __('DOB') }}</span></dt>
                            <dd class="col-sm-9"><span
                                    class="text-sm">{{ company_date_formate($rfxApplication->dob) }}</span></dd>
                        @endif
                        @if (!empty($rfxApplication->gender))
                            <dt class="col-sm-3"><span class="h6 text-sm mb-0">{{ __('Gender') }}</span></dt>
                            <dd class="col-sm-9"><span class="text-sm">{{ $rfxApplication->gender }}</span></dd>
                        @endif
                        @if (!empty($rfxApplication->country))
                            <dt class="col-sm-3"><span class="h6 text-sm mb-0">{{ __('Country') }}</span></dt>
                            <dd class="col-sm-9"><span class="text-sm">{{ $rfxApplication->country }}</span>
                            </dd>
                        @endif
                        @if (!empty($rfxApplication->state))
                            <dt class="col-sm-3"><span class="h6 text-sm mb-0">{{ __('State') }}</span></dt>
                            <dd class="col-sm-9"><span class="text-sm">{{ $rfxApplication->state }}</span></dd>
                        @endif
                        @if (!empty($rfxApplication->city))
                            <dt class="col-sm-3"><span class="h6 text-sm mb-0">{{ __('City') }}</span></dt>
                            <dd class="col-sm-9"><span class="text-sm">{{ $rfxApplication->city }}</span></dd>
                        @endif

                        <dt class="col-sm-3"><span class="h6 text-sm mb-0">{{ __('Applied For') }}</span></dt>
                        <dd class="col-sm-9"><span
                                class="text-sm">{{ !empty($rfxApplication->rfxs) ? $rfxApplication->rfxs->title : '-' }}</span>
                        </dd>

                        <dt class="col-sm-3"><span class="h6 text-sm mb-0">{{ __('Applied at') }}</span></dt>
                        <dd class="col-sm-9"><span
                                class="text-sm">{{ company_date_formate($rfxApplication->created_at) }}</span>
                        </dd>
                        <dt class="col-sm-3"><span class="h6 text-sm mb-0">{{ __('CV / Resume') }}</span></dt>
                        <dd class="col-sm-9">
                            @if (!empty($rfxApplication->resume))
                                <span class="text-sm action-btn bg-primary ms-2">
                                    <a class=" btn btn-sm align-items-center"
                                        href="{{ get_file($rfxApplication->resume) }}" data-bs-toggle="tooltip"
                                        data-bs-original-title="{{ __('download') }}" download=""><i
                                            class="ti ti-download text-white"></i></a>
                                </span>

                                <div class="action-btn bg-secondary ms-2 ">
                                    <a class=" mx-3 btn btn-sm align-items-center"
                                        href="{{ get_file($rfxApplication->resume) }}" target="_blank">
                                        <i class="ti ti-crosshair text-white" data-bs-toggle="tooltip"
                                            data-bs-original-title="{{ __('Preview') }}"></i>
                                    </a>
                                </div>
                            @else
                                <div class="mx-4">-</div>
                            @endif
                        </dd>
                        <dt class="col-sm-3"><span class="h6 text-sm mb-0">{{ __('Cover Letter') }}</span></dt>
                        <dd class="col-sm-9">
                            @if (!empty($rfxApplication->cover_letter))
                                <span class="text-sm">{{ $rfxApplication->cover_letter }}</span>
                        </dd>
                    @else
                        <div class="mx-4">-</div>
                        @endif
                        <dt class="col-sm-3"><span class="h6 text-sm mb-0">{{ __('Bid Total') }}</span></dt>
                        <dd class="col-sm-9"><span class="text-sm">{{ $rfxApplication->bid_total_amount }}</span>
                        </dd>
                        <dt class="col-sm-3"><span class="h6 text-sm mb-0">{{ __('Billing Type') }}</span></dt>
                        <dd class="col-sm-9"><span class="text-sm badge bg-primary p-2 px-3 ">{{ ucfirst($rfxApplication->billing_type) }}</span>
                        </dd>
                    </dl>
                    <div class='rating-stars text-right'>
                        <ul id='stars'>
                            <li class='star {{ in_array($rfxApplication->rating, [1, 2, 3, 4, 5]) == true ? 'selected' : '' }}'
                                data-bs-toggle="tooltip" data-bs-original-title="{{__('Poor')}}" data-value='1'>
                                <i class='fa fa-star fa-fw'></i>
                            </li>
                            <li class='star {{ in_array($rfxApplication->rating, [2, 3, 4, 5]) == true ? 'selected' : '' }}'
                                data-bs-toggle="tooltip" data-bs-original-title='{{__('Fair')}}' data-value='2'>
                                <i class='fa fa-star fa-fw'></i>
                            </li>
                            <li class='star {{ in_array($rfxApplication->rating, [3, 4, 5]) == true ? 'selected' : '' }}'
                                data-bs-toggle="tooltip" data-bs-original-title='{{__('Good')}}' data-value='3'>
                                <i class='fa fa-star fa-fw'></i>
                            </li>
                            <li class='star {{ in_array($rfxApplication->rating, [4, 5]) == true ? 'selected' : '' }}'
                                data-bs-toggle="tooltip" data-bs-original-title='{{__('Excellent')}}' data-value='4'>
                                <i class='fa fa-star fa-fw'></i>
                            </li>
                            <li class='star {{ in_array($rfxApplication->rating, [5]) == true ? 'selected' : '' }}'
                                data-bs-toggle="tooltip" data-bs-original-title='{{__('WOW!!!')}}' data-value='5'>
                                <i class='fa fa-star fa-fw'></i>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-auto">
                            <h6 class="text-muted">{{ __('Purchase Detail') }}</h6>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        {{ $dataTable->table(['width' => '100%']) }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="card card-fluid">
                <div class="card-header">
                    <div class="row">
                        <div class="col">
                            <h6 class="text-muted">{{ __('Additional Details') }}</h6>
                        </div>
                        <div class="col text-end">
                            @permission('interview schedule create')
                                <a data-url="{{ route('rfx-interview-schedule.create', $rfxApplication->id) }}"
                                    data-size="lg" class="btn-sm btn btn-primary text-white" data-ajax-popup="true"
                                    data-title="{{ __('Create Interview Schedule') }}">
                                    <i class="ti ti-plus "></i> {{ __('Create Interview Schedule') }}
                                </a>
                            @endpermission
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if (!empty(json_decode($rfxApplication->custom_question)))
                        <div class="list-group list-group-flush mb-4">
                            @foreach (json_decode($rfxApplication->custom_question) as $que => $ans)
                                @if (!empty($ans))
                                    <div class="list-group-item px-0">
                                        <div class="row align-items-center">
                                            <div class="col">
                                                <a href="#!" class="d-block h6 text-sm mb-0">{{ $que }}</a>
                                                <p class="card-text text-sm text-muted mb-0">
                                                    {{ $ans }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endif
                    {{ Form::open(['route' => ['rfx.application.skill.store', $rfxApplication->id], 'method' => 'post']) }}
                    <div class="form-group">
                        <label class="form-label">{{ __('Skills') }}</label>
                        <input type="text" class="form-control" value="{{ $rfxApplication->skill }}"
                            data-toggle="tags" name="skill" placeholder="{{ __('Type here....') }}" />
                    </div>
                    @permission('rfxapplication add skill')
                        <div class="form-group">
                            <input type="submit" value="{{ __('Add Skills') }}" class="btn-sm btn btn-primary">
                        </div>
                    @endpermission
                    {{ Form::close() }}

                    {{ Form::open(['route' => ['rfx.application.note.store', $rfxApplication->id], 'method' => 'post']) }}
                    <div class="form-group">
                        <label class="form-label">{{ __('Applicant Notes') }}</label>
                        <textarea name="note" class="form-control" id="" rows="3"></textarea>
                    </div>
                    @permission('rfxapplication add note')
                        <div class="form-group">
                            <input type="submit" value="{{ __('Add Notes') }}" class="btn-sm btn btn-primary">
                        </div>
                    @endpermission
                    {{ Form::close() }}
                    <div class="list-group list-group-flush mb-4">
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
                                    @permission('rfxapplication delete note')
                                        @if ($note->note_created == creatorId())
                                            <div class="col-auto text-end">
                                                {!! Form::open([
                                                    'method' => 'DELETE',
                                                    'route' => ['rfx.application.note.destroy', $note->id],
                                                    'id' => 'delete-form' . $note->id,
                                                ]) !!}
                                                <a class="mx-3 btn btn-sm btn btn-danger  align-items-center bs-pass-para show_confirm"
                                                    data-bs-toggle="tooltip" title="" data-bs-original-title="Delete"
                                                    aria-label="Delete"><i class="ti ti-trash text-white"></i></a>
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
@endsection
