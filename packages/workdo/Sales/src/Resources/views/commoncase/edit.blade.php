@extends('layouts.main')
@section('page-title')
    {{ __('Edit Case') }}
@endsection
@section('title')
    {{ __('Edit Case') }} {{ '(' . $commonCase->name . ')' }}
@endsection
@section('page-action')
    <div class="d-flex">
        <a href="{{ route('salesaccount.index') }}" class="btn-submit btn btn-sm btn-primary"
            data-bs-toggle="tooltip" title="{{ __('Back') }}">
            <i class=" ti ti-arrow-back-up"></i>
        </a>
    </div>
@endsection
@section('page-breadcrumb')
    {{ __('Case') }},
    {{ __('Edit') }}
@endsection
@section('content')
    <div class="row">
        <!-- [ sample-page ] start -->
        <div class="col-sm-12">
            <div class="row">
                <div class="col-xl-3">
                    <div class="card sticky-top" style="top:30px">
                        <div class="list-group list-group-flush" id="useradd-sidenav">
                            <a href="#useradd-1"
                                class="list-group-item list-group-item-action border-0">{{ __('Overview') }} <div
                                    class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                            <a href="#useradd-2"
                                class="list-group-item list-group-item-action border-0">{{ __('Stream') }} <div
                                    class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                        </div>
                    </div>
                </div>
                <div class="col-xl-9">
                    <div id="useradd-1" class="card">
                        {{ Form::model($commonCase, ['route' => ['commoncases.update', $commonCase->id],'class'=>'needs-validation','novalidate', 'method' => 'PUT', 'enctype' => 'multipart/form-data']) }}
                        <div class="card-header">
                            <div class="text-end">
                                @if (module_is_active('AIAssistant'))
                                    @include('aiassistant::ai.generate_ai_btn', [
                                        'template_module' => 'cases',
                                        'module' => 'Sales',
                                    ])
                                @endif
                            </div>
                            <h5>{{ __('Overview') }}</h5>
                            <small class="text-muted">{{ __('Edit about your case information') }}</small>
                        </div>

                        <div class="card-body">
                            <form>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            {{ Form::label('name', __('Name'), ['class' => 'form-label']) }} <x-required></x-required>
                                            {{ Form::text('name', null, ['class' => 'form-control', 'placeholder' => __('Enter Name'), 'required' => 'required']) }}
                                            @error('name')
                                                <span class="invalid-name" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            {{ Form::label('number', __('Number'), ['class' => 'form-label']) }}
                                            {{ Form::text('number', null, ['class' => 'form-control', 'placeholder' => __('Enter Name'), 'disabled']) }}
                                        </div>
                                        @error('number')
                                            <span class="invalid-number" role="alert">
                                                <strong class="text-danger">{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            {{ Form::label('status', __('Status'), ['class' => 'form-label']) }} <x-required></x-required>
                                            {!! Form::select('status', $status, null, ['class' => 'form-control', 'required' => 'required']) !!}
                                        </div>
                                        @error('status')
                                            <span class="invalid-status" role="alert">
                                                <strong class="text-danger">{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            {{ Form::label('account', __('Account'), ['class' => 'form-label']) }}
                                            {!! Form::select('account', $account, null, ['class' => 'form-control']) !!}
                                            @error('account')
                                                <span class="invalid-account" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            {{ Form::label('priority', __('Priority'), ['class' => 'form-label']) }} <x-required></x-required>
                                            {!! Form::select('priority', $priority, null, ['class' => 'form-control', 'required' => 'required']) !!}
                                            @error('Priority')
                                                <span class="invalid-priority" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            {{ Form::label('contact', __('Contacts'), ['class' => 'form-label']) }}
                                            {!! Form::select('contact', $contact, null, ['class' => 'form-control']) !!}
                                            @error('contacts')
                                                <span class="invalid-contacts" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            {{ Form::label('type', __('Type'), ['class' => 'form-label']) }} <x-required></x-required>
                                            {!! Form::select('type', $type, null, ['class' => 'form-control', 'required' => 'required']) !!}
                                            @error('type')
                                                <span class="invalid-type" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            {{ Form::label('user', __('Assigned User'), ['class' => 'form-label']) }}
                                            {!! Form::select('user', $user, $commonCase->user_id, ['class' => 'form-control']) !!}
                                        </div>
                                        @error('user')
                                            <span class="invalid-user" role="alert">
                                                <strong class="text-danger">{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            {{ Form::label('description', __('Description'), ['class' => 'form-label']) }}
                                            {!! Form::textarea('description', null, ['class' => 'form-control ', 'rows' => 3, 'placeholder' => __('Enter Description')]) !!}
                                            @error('description')
                                                <span class="invalid-description" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 field" data-name="attachments">
                                        <div class="attachment-upload">
                                            <div class="attachment-button">
                                                <div class="pull-left">
                                                    <div class="form-group">
                                                        {{ Form::label('attachments', __('Attachment'), ['class' => 'form-label']) }}
                                                        <input type="file"name="attachments" class="form-control mb-2"
                                                            onchange="document.getElementById('blah1').src = window.URL.createObjectURL(this.files[0])">
                                                        <img id="blah1" class="mt-1"
                                                            @if (!empty($commonCase->attachments)) src="{{ get_file($commonCase->attachments) }}" @endif
                                                            style="width:25%;" />
                                                    </div>
                                                </div>
                                            </div>
                                            @error('description')
                                                <span class="invalid-description" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    @if (module_is_active('CustomField') && !$customFields->isEmpty())
                                        <div class="col-6">
                                            <div class="tab-pane fade show" id="tab-2" role="tabpanel">
                                                @include('custom-field::formBuilder', [
                                                    'fildedata' => $commonCase->customField,
                                                ])
                                            </div>
                                        </div>
                                    @endif

                                    <div class="text-end">
                                        {{ Form::submit(__('Update'), ['class' => 'btn-submit btn btn-primary']) }}
                                    </div>

                                </div>
                            </form>
                        </div>
                        {{ Form::close() }}
                    </div>

                    <div id="useradd-2" class="card">
                        {{ Form::open(['route' => ['streamstore', ['commoncases', $commonCase->name, $commonCase->id]], 'method' => 'post', 'enctype' => 'multipart/form-data']) }}
                        <div class="card-header">
                            <h5>{{ __('Stream') }}</h5>
                            <small class="text-muted">{{ __('Add stream comment') }}</small>
                        </div>
                        <div class="card-body">
                            <form>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            {{ Form::label('stream', __('Stream'), ['class' => 'form-label']) }} <x-required></x-required>
                                            {{ Form::text('stream_comment', null, ['class' => 'form-control', 'placeholder' => __('Enter Stream Comment'), 'required' => 'required']) }}
                                        </div>
                                    </div>
                                    <input type="hidden" name="log_type" value="commoncases comment">
                                    <div class="col-12 field" data-name="attachments">
                                        <div class="attachment-upload">
                                            <div class="attachment-button">
                                                <div class="pull-left">
                                                    <div class="form-group">
                                                        {{ Form::label('attachment', __('Attachment'), ['class' => 'form-label']) }}
                                                        <input type="file"name="attachment" class="form-control mb-2"
                                                            onchange="document.getElementById('blah').src = window.URL.createObjectURL(this.files[0])">
                                                        <img id="blah" width="20%" height="20%" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="attachments"></div>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        {{ Form::submit(__('Update'), ['class' => 'btn-submit btn btn-primary']) }}
                                    </div>
                                </div>
                            </form>
                        </div>
                        {{ Form::close() }}
                    </div>

                    <div id="useradd-2-1" class="card">
                        <div class="card-header">
                            <h5>{{ __('Latest comments') }}</h5>
                        </div>
                        @php
                            $displayed = false;
                        @endphp
                        @foreach ($streams as $stream)
                            @php
                                $remark = json_decode($stream->remark);
                            @endphp
                            @if ($remark->data_id == $commonCase->id)
                                <div class="row">
                                    <div class="col-xl-12">
                                        <ul class="list-group">
                                            <li class="list-group-item border-0 d-flex align-items-start">
                                                <div class="avatar col-1">
                                                    <a href="{{ (!empty($stream->file_upload) && check_file($stream->file_upload)) ? get_file($stream->file_upload) : get_file('uploads/users-avatar/avatar.png') }}"
                                                        target="_blank" class="users-image">

                                                        <img src="{{ (!empty($stream->file_upload) && check_file($stream->file_upload)) ? get_file($stream->file_upload) : get_file('uploads/users-avatar/avatar.png') }}"
                                                            class="rounded border-2 border border-primary">
                                                    </a>
                                                </div>
                                                <div class="col-11 d-block d-sm-flex align-items-center right-side">
                                                    <div
                                                        class="col-10 d-flex align-items-start flex-column justify-content-center mb-sm-0">
                                                        <div class="h6 mb-1">{{ $remark->user_name }}
                                                        </div>
                                                        <span class="text-sm mb-0">
                                                            {{__('posted to ')}}<a href="#">{{ $remark->title }}</a> {{ __(',') }}
                                                            {{ $stream->log_type }} <a
                                                                href="#">{{ $remark->stream_comment }}</a>
                                                        </span>
                                                    </div>
                                                    <div class="col-2  d-flex align-items-center ">
                                                        <small class="float-end ">{{ $stream->created_at }}</small>
                                                    </div>
                                                </div>

                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                @php $displayed = true; @endphp
                            @endif
                        @endforeach
                        @if (!$displayed)
                            <div class="m-3">
                                @include('layouts.nodatafound')
                            </div>
                        @endif
                    </div>

                </div>
            </div>
            <!-- [ sample-page ] end -->
        </div>
        <!-- [ Main Content ] end -->
    </div>
@endsection

@push('scripts')
    <script>
        var scrollSpy = new bootstrap.ScrollSpy(document.body, {
            target: '#useradd-sidenav',
            offset: 300
        })
    </script>
@endpush
