@extends('layouts.main')
@section('page-title')
    {{ __('Manage Stream') }}
@endsection
@section('title')
    {{ __('Stream') }}
@endsection
@section('page-breadcrumb')
    {{ __('Stream') }}
@endsection
@section('page-action')
@endsection
@section('filter')
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Latest Stream comments') }}</h5>
                </div>
                <div class="card-body">
                    @foreach ($streams as $stream)
                        @php
                            $remark = json_decode($stream->remark);
                        @endphp
                        <div class="row">

                            <div class="col-xl-12">
                                <div class="comment-card p-3  rounded border-1 border border-primary mb-3">
                                    <div class="border-0 d-flex flex-wrap flex-sm-nowrap gap-2 align-items-start">
                                        <div class="avatar">
                                            <a href="{{ (!empty($stream->file_upload) && check_file($stream->file_upload)) ? get_file($stream->file_upload) : get_file('uploads/users-avatar/avatar.png') }}" target="_blank">

                                                <img src="{{ (!empty($stream->file_upload) && check_file($stream->file_upload)) ? get_file($stream->file_upload) : get_file('uploads/users-avatar/avatar.png') }}"
                                                    class="rounded border-2 border border-primary" width="50" height="50" style="object-fit: cover">
                                            </a>
                                        </div>
                                        <div>
                                                <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                                                    <h5 class="mb-0 text-capitalize">{{ ucfirst($remark->user_name) }}
                                                    </h5>
                                                    <span class="fs-6">{{ company_datetime_formate($stream->created_at) }}</span>
                                                </div>
                                                <div class="d-flex align-items-end gap-2">
                                                    <div>
                                                        <p class="text-capitalize text-sm mb-0 d-block">
                                                            <strong>{{ __('posted to :') }}</strong> <small class="fs-6 text-primary text-wrap text-break">{{ $remark->title }}</small>
                                                        </p>
                                                        <p class="text-capitalize text-sm mb-0">
                                                            <strong>{{ $stream->log_type .' : ' }}</strong><small class="fs-6 text-primary text-wrap text-break">{{ $remark->stream_comment }}</small>
                                                        </p>
                                                    </div>
                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['stream.destroy', $stream->id]]) !!}
                                                    <a href="#!" class="btn btn-sm align-items-center text-white show_confirm bg-danger" data-bs-toggle="tooltip"
                                                        title="{{__('Delete')}}" data-confirm="{{ __('Are You Sure?') }}"
                                                        data-text="{{ __('This action can not be undone. Do you want to continue?') }}">
                                                        <i class="ti ti-trash"></i>
                                                    </a>
                                                    {!! Form::close() !!}
                                                </div>
                                            </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
