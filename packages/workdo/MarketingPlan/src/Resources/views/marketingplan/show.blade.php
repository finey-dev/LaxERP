@extends('layouts.main')
@section('page-title')
    {{ __('Marketing Plan Details') }}
@endsection
@section('page-breadcrumb')
    {{ __('Marketing Plan Details') }}
@endsection
@push('css')
    <link rel="stylesheet" href="{{ asset('packages/workdo/MarketingPlan/src/Resources/assets/css/custom.css') }}">
    <link rel="stylesheet"
        href="{{ asset('packages/workdo/MarketingPlan/src/Resources/assets/summernote/summernote-bs4.css') }}">
    <link href="{{ asset('assets/js/plugins/summernote-0.8.18-dist/summernote-lite.min.css') }}" rel="stylesheet">
@endpush
@section('page-action')
    <div>
        @if (URL::previous() == URL::current())
            <a href="{{ route('marketing-plan.index') }}" class="btn-submit btn btn-sm btn-primary " data-toggle="tooltip"
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
                        url: '{{ route('marketing-plan.rating', $MarketingPlans->id) }}',
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
                            <a href="#general" class="border-0 list-group-item list-group-item-action">{{ __('General') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                            <a href="#business_summary"
                                class="border-0 list-group-item list-group-item-action">{{ __(' Business Summary') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                            <a href="#company_description"
                                class="border-0 list-group-item list-group-item-action">{{ __('Company Description') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                            <a href="#team" class="border-0 list-group-item list-group-item-action">{{ __('Team') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                            <a href="#business_initiative"
                                class="border-0 list-group-item list-group-item-action">{{ __('Business initiative') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                            <a href="#target_market"
                                class="border-0 list-group-item list-group-item-action">{{ __('Target Market') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                            <a href="#marketing_channels"
                                class="border-0 list-group-item list-group-item-action">{{ __('Marketing Channels') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                            <a href="#budget" class="border-0 list-group-item list-group-item-action">{{ __('Budget') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                            <a href="#notes" class="border-0 list-group-item list-group-item-action">{{ __('Notes') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                            <a href="#comment" class="border-0 list-group-item list-group-item-action">{{ __('Comment') }}
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
                                    ->where('id', $MarketingPlans->challenge)
                                    ->first();
                            @endphp

                            <div class="card table-card">
                                <div class="card-header">
                                    <div class="d-flex card-counter-div justify-content-between align-items-center">
                                        <div>
                                            <h5>{{ __('Video') }}</h5>
                                        </div>
                                        <div class="count-main-Div">
                                            <div id="Accounts_I_like_Publish_{{ !empty($MarketingPlans) ? $MarketingPlans->id : '' }}"
                                                data-date="{{ !empty($MarketingPlans) ? $MarketingPlans->end_date : '' }}"
                                                class="mt-5 set_countdown"
                                                data-publish="{{ !empty($MarketingPlans->id) ? $MarketingPlans->id : '' }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="card-body">

                                            @if ($MarketingPlans->video_file)
                                                <video width="100%" controls>
                                                    <source id="videoresource"
                                                        src="{{ get_file($MarketingPlans->video_file) }}" type="video/mp4">
                                                </video>
                                            @else
                                                <video width="100%" controls>
                                                    <source id="videoresource"
                                                        src="{{ asset('packages/workdo/MarketingPlan/src/Resources/assets/img/no-video.jpg') }}"
                                                        type="image">
                                                </video>
                                            @endif
                                        </div>
                                    </div>
                                    <div id="stars" class='text-right rating-stars'
                                        data-creativity-id="{{ $MarketingPlans->id }}">
                                        <ul id='stars'>
                                            <li class='star {{ in_array($MarketingPlans->rating, [1, 2, 3, 4, 5]) == true ? 'selected' : '' }}'
                                                data-bs-toggle="tooltip" data-bs-original-title="Poor" data-value='1'>
                                                <i class='fa fa-star fa-fw'></i>
                                            </li>
                                            <li class='star {{ in_array($MarketingPlans->rating, [2, 3, 4, 5]) == true ? 'selected' : '' }}'
                                                data-bs-toggle="tooltip" data-bs-original-title='Fair' data-value='2'>
                                                <i class='fa fa-star fa-fw'></i>
                                            </li>
                                            <li class='star {{ in_array($MarketingPlans->rating, [3, 4, 5]) == true ? 'selected' : '' }}'
                                                data-bs-toggle="tooltip" data-bs-original-title='Good' data-value='3'>
                                                <i class='fa fa-star fa-fw'></i>
                                            </li>
                                            <li class='star {{ in_array($MarketingPlans->rating, [4, 5]) == true ? 'selected' : '' }}'
                                                data-bs-toggle="tooltip" data-bs-original-title='Excellent'
                                                data-value='4'>
                                                <i class='fa fa-star fa-fw'></i>
                                            </li>
                                            <li class='star {{ in_array($MarketingPlans->rating, [5]) == true ? 'selected' : '' }}'
                                                data-bs-toggle="tooltip" data-bs-original-title='WOW!!!' data-value='5'>
                                                <i class='fa fa-star fa-fw'></i>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="details-card">
                                <div class="card details">

                                    <div class="card-body" style="min-height: 190px;">
                                        <div class="row mt-2 mb-0 align-items-center">


                                            <div class="col-md-12 col-sm-4">
                                                <div class="d-flex align-items-start">
                                                    <div class="theme-avtar bg-primary badge">
                                                        <i class="ti ti-trophy"></i>
                                                    </div>
                                                    <div class="ms-2">
                                                        <strong>{{ __('Plan Name') }}</strong>
                                                        <h5 class="mb-0 text-primary">{{ $MarketingPlans->name }}</h5>
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="col-md-12 col-sm-4 mt-4">
                                                <div class="d-flex align-items-start">
                                                    <div class="theme-avtar bg-warning badge">
                                                        <i class="ti ti-test-pipe"></i>
                                                    </div>
                                                    <div class="ms-2">
                                                        <strong>{{ __('Status') }}</strong>
                                                        <h5 class="mb-0 text-warning">
                                                            {{ !empty($MarketingPlans->Status) ? $MarketingPlans->Status->name : '-' }}
                                                        </h5>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-12 col-sm-4 mt-4">
                                                <div class="d-flex align-items-start">
                                                    <div class="theme-avtar bg-info badge">
                                                        <i class="ti ti-server"></i>
                                                    </div>
                                                    <div class="ms-2">
                                                        <strong>{{ __('Stage') }}</strong>
                                                        <h5 class="mb-0 text-info">
                                                            {{ !empty($MarketingPlans->Stage) ? $MarketingPlans->Stage->name : '-' }}
                                                        </h5>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-12 col-sm-4 mt-4">
                                                <div class="d-flex align-items-start">
                                                    <div class="theme-avtar bg-primary badge">
                                                        <i class="ti ti-star"></i>
                                                    </div>
                                                    <div class="ms-2">
                                                        <strong>{{ __('Rating Points') }}</strong>
                                                        <h5 class="mb-0 text-primary"> {{ $MarketingPlans->rating }}</h5>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-12 col-sm-4 mt-4">
                                                <div class="d-flex align-items-start">
                                                    <div class="theme-avtar bg-warning badge">
                                                        <i class="ti ti-calendar"></i>
                                                    </div>
                                                    <div class="ms-2">
                                                        <strong>{{ __('Due Date') }}</strong>
                                                        <h5 class="mb-0 text-warning">
                                                            {{ company_date_formate($MarketingPlans['created_at']) }}</h5>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="business_summary">
                            <div class="row">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5>{{ __('Business Summary') }}</h5>
                                        </div>
                                        <div class="card-body">
                                            {{ Form::open(['route' => ['marketing-plan.businesssummary.store', $MarketingPlans->id]]) }}
                                            <textarea name="business_summary"
                                                class="form-control summernote {{ !empty($errors->first('business_summary')) ? 'is-invalid' : '' }}"
                                                id="description_ck1">{!! $MarketingPlans->business_summary !!}</textarea>
                                            @permission('marketingplan business summary create')
                                                <div class="mb-0 col-md-12 text-end">
                                                    {{ Form::submit(__('Add'), ['class' => 'btn  btn-primary']) }}
                                                </div>
                                            @endpermission
                                            {{ Form::close() }}
                                            @if ($errors->has('business_summary'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('business_summary') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="company_description">
                            <div class="row">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5>{{ __('Company Description') }}</h5>
                                        </div>
                                        <div class="card-body">
                                            {{ Form::open(['route' => ['marketing-plan.companydescription.store', $MarketingPlans->id]]) }}
                                            <textarea name="company_description"
                                                class="form-control summernote {{ !empty($errors->first('company_description')) ? 'is-invalid' : '' }}"
                                                id="description_ck2">{!! $MarketingPlans->company_description !!}</textarea>
                                            @permission('marketingplan company description create')
                                                <div class="mb-0 col-md-12 text-end">
                                                    {{ Form::submit(__('Add'), ['class' => 'btn  btn-primary']) }}
                                                </div>
                                            @endpermission
                                            {{ Form::close() }}
                                            @if ($errors->has('company_description'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('company_description') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="team">
                            <div class="row">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5>{{ __('Team') }}</h5>
                                        </div>
                                        <div class="card-body">
                                            {{ Form::open(['route' => ['marketing-plan.team.store', $MarketingPlans->id]]) }}
                                            <textarea name="team" class="form-control summernote {{ !empty($errors->first('team')) ? 'is-invalid' : '' }}"
                                                id="description_ck3">{!! $MarketingPlans->team !!}</textarea>
                                            @permission('marketingplan team create')
                                                <div class="mb-0 col-md-12 text-end">
                                                    {{ Form::submit(__('Add'), ['class' => 'btn  btn-primary']) }}
                                                </div>
                                            @endpermission
                                            {{ Form::close() }}
                                            @if ($errors->has('team'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('team') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="business_initiative">
                            <div class="row">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5>{{ __('Business Initiative') }}</h5>
                                        </div>
                                        <div class="card-body">
                                            {{ Form::open(['route' => ['marketing-plan.businessinitiative.store', $MarketingPlans->id]]) }}
                                            <textarea name="business_initiative"
                                                class="form-control summernote {{ !empty($errors->first('business_initiative')) ? 'is-invalid' : '' }}"
                                                id="description_ck4">{!! $MarketingPlans->business_initiative !!}</textarea>
                                            @permission('marketingplan business initiative create')
                                                <div class="mb-0 col-md-12 text-end">
                                                    {{ Form::submit(__('Add'), ['class' => 'btn  btn-primary']) }}
                                                </div>
                                            @endpermission
                                            {{ Form::close() }}
                                            @if ($errors->has('business_initiative'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('business_initiative') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="target_market">
                            <div class="row">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5>{{ __('Target Market') }}</h5>
                                        </div>
                                        <div class="card-body">
                                            {{ Form::open(['route' => ['marketing-plan.targetmarket.store', $MarketingPlans->id]]) }}
                                            <textarea name="target_market"
                                                class="form-control summernote {{ !empty($errors->first('target_market')) ? 'is-invalid' : '' }}"
                                                id="description_ck5">{!! $MarketingPlans->target_market !!}</textarea>
                                            @permission('marketingplan target market create')
                                                <div class="mb-0 col-md-12 text-end">
                                                    {{ Form::submit(__('Add'), ['class' => 'btn  btn-primary']) }}
                                                </div>
                                            @endpermission
                                            {{ Form::close() }}
                                            @if ($errors->has('target_market'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('target_market') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="marketing_channels">
                            <div class="row">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5>{{ __('Marketing Channels') }}</h5>
                                        </div>
                                        <div class="card-body">
                                            {{ Form::open(['route' => ['marketing-plan.marketingchannels.store', $MarketingPlans->id]]) }}
                                            <textarea name="marketing_channels"
                                                class="form-control summernote {{ !empty($errors->first('marketing_channels')) ? 'is-invalid' : '' }}"
                                                id="description_ck">{!! $MarketingPlans->marketing_channels !!}</textarea>
                                            @permission('marketingplan marketing channels create')
                                                <div class="mb-0 col-md-12 text-end">
                                                    {{ Form::submit(__('Add'), ['class' => 'btn  btn-primary']) }}
                                                </div>
                                            @endpermission
                                            {{ Form::close() }}
                                            @if ($errors->has('marketing_channels'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('marketing_channels') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="budget">
                            <div class="row">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5>{{ __('Budget') }}</h5>
                                        </div>
                                        <div class="card-body">
                                            {{ Form::open(['route' => ['marketing-plan.budget.store', $MarketingPlans->id]]) }}
                                            <textarea name="budget" class="form-control summernote {{ !empty($errors->first('budget')) ? 'is-invalid' : '' }}"
                                                id="description_ck6">{!! $MarketingPlans->budget !!}</textarea>
                                            @permission('marketingplan budget create')
                                                <div class="mb-0 col-md-12 text-end">
                                                    {{ Form::submit(__('Add'), ['class' => 'btn  btn-primary']) }}
                                                </div>
                                            @endpermission
                                            {{ Form::close() }}
                                            @if ($errors->has('budget'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('budget') }}
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
                                            {{ Form::open(['route' => ['marketing-plan.notes.store', $MarketingPlans->id]]) }}
                                            <textarea name="notes" class="form-control summernote {{ !empty($errors->first('notes')) ? 'is-invalid' : '' }}"
                                                id="description_ck10">{!! $MarketingPlans->notes !!}</textarea>
                                            @permission('marketingplan notes create')
                                                <div class="mb-0 col-md-12 text-end">
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
                                                    class="rounded border-2 border border-primary" alt=""
                                                    width="40" height="40">
                                            </a>

                                            <div class="media-body ms-2">
                                                <h6 class="mt-0 ms-2">
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
                                                                <a href="#" class="like active"
                                                                    style="margin-bottom: -13px;">
                                                                    <i class="ni ni-cloud-download-95"></i>
                                                                    <a href="{{ get_file('uploads/MarketingPlan') . '/' . $comment->file }}"
                                                                        download=""
                                                                        class="p-1 m-0 btn btn-sm d-inline-flex align-items-center"
                                                                        data-bs-toggle="tooltip"
                                                                        title="{{ __('Download') }}">
                                                                        <i class="ti ti-download text-primary"></i> </a>
                                                                </a>
                                                            </div>
                                                            <div class="d-flex me-2">
                                                                <a href="{{ get_file('uploads/MarketingPlan') . '/' . $comment->file }}"
                                                                    target=_blank
                                                                    class="p-1 text-white btn btn-sm d-inline-flex align-items-center "
                                                                    data-bs-toggle="tooltip" title="{{ __('Preview') }}">
                                                                    <i class="ti ti-crosshair text-primary"></i>
                                                                </a>
                                                            </div>
                                                        @endif
                                                        @permission('marketingplan comment replay')
                                                            <div class="d-flex me-2">
                                                                <a href="#"
                                                                    data-url="{{ route('marketing-plan.comment.reply', [$MarketingPlans->id, $comment->id]) }}"
                                                                    class="p-1 text-white btn btn-sm d-inline-flex align-items-center "
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
                                                    @include('marketing-plan::marketingplan.comment', [
                                                        'subComment' => $subComment,
                                                    ])
                                                @endforeach
                                            </div>

                                        </div>
                                    @endforeach

                                    <div class="mt-4 border rounded">
                                        {{ Form::open(['route' => ['marketing-plan.comment.store', $MarketingPlans->id], 'enctype' => 'multipart/form-data', 'class' => 'd-flex align-items-center gap-3 form-wrp needs-validation']) }}
                                        <textarea rows="3" class="form-control resize-none project_comment border-4" name="comment"
                                            placeholder="Your comment..." required style="flex: 1;"></textarea>
                                            <div class="btn-wrp p-2 gap-2 d-flex  justify-content-between align-items-center">
                                            <div class="choose-file">
                                                <input class="custom-input-file custom-input-file-link commentFile d-none"
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
        <script src="{{ asset('packages/workdo/MarketingPlan/src/Resources/assets/summernote/summernote-bs4.js') }}"></script>
        <script src="{{ asset('/js/custom.js') }}"></script>
        {{-- <script src="{{ asset('assets/js/plugins/summernote-0.8.18-dist/summernote-lite.min.js') }}"></script> --}}
        <script src="{{ asset('packages/workdo/MarketingPlan/src/Resources/assets/js/moment.js') }}"></script>
        <script src="{{ asset('packages/workdo/MarketingPlan/src/Resources/assets/js/moment-timezone.min.js') }}"></script>

        <script src="{{ asset('packages/workdo/MarketingPlan/src/Resources/assets/js/main.js') }}"></script>
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
                                                                                                            <span class="static digit">0</span>\
                                                                                                        </span>\
                                                                                                        <span class="position">\
                                                                                                            <span class="static digit">0</span>\
                                                                                                        </span>'
                                ).appendTo(elem);

                                if (this != "Seconds") {
                                    elem.append('<span class="countDiv' + i + '"></span>');
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
