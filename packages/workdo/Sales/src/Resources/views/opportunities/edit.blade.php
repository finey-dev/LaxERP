@extends('layouts.main')
@section('page-title')
    {{ __('Edit Opportunities') }}
@endsection
@section('title')
    {{ __('Edit Opportunities') }} {{ '(' . $opportunities->name . ')' }}
@endsection
@push('css')
    <link rel="stylesheet" href="{{  asset('packages/workdo/Sales/src/Resources/assets/css/custom.css') }}">
@endpush
@section('page-action')
    <div class="d-flex">
        <a href="{{ route('salesaccount.index') }}" class="btn-submit btn btn-sm btn-primary"
            data-bs-toggle="tooltip" title="{{ __('Back') }}">
            <i class=" ti ti-arrow-back-up"></i>
        </a>
    </div>
@endsection
@section('page-breadcrumb')
    {{ __('Opportunities') }},
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
                            <a href="#useradd-3"
                                class="list-group-item list-group-item-action border-0">{{ __('Sales Documents') }} <div
                                    class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                            <a href="#useradd-4"
                                class="list-group-item list-group-item-action border-0">{{ __('Quotes') }} <div
                                    class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                            <a href="#useradd-5"
                                class="list-group-item list-group-item-action border-0">{{ __('Sales Orders') }} <div
                                    class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                            <a href="#useradd-6"
                                class="list-group-item list-group-item-action border-0">{{ __('Sales Invoices') }} <div
                                    class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                        </div>
                    </div>
                </div>
                <div class="col-xl-9">
                    <div id="useradd-1" class="card">
                        {{ Form::model($opportunities, ['route' => ['opportunities.update', $opportunities->id], 'class'=>'needs-validation','novalidate','method' => 'PUT', 'enctype' => 'multipart/form-data']) }}
                        <div class="card-header">
                            <div class="float-end">
                                @if (module_is_active('AIAssistant'))
                                    @include('aiassistant::ai.generate_ai_btn', [
                                        'template_module' => 'opportunities',
                                        'module' => 'Sales',
                                    ])
                                @endif
                            </div>
                            <h5>{{ __('Overview') }}</h5>
                            <small class="text-muted">{{ __('Edit about your opportunities information') }}</small>
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
                                            {{ Form::label('account', __('Account'), ['class' => 'form-label']) }} <x-required></x-required>
                                            {!! Form::select('account', $account_name, null, ['class' => 'form-control']) !!}
                                        </div>
                                        @error('account')
                                            <span class="invalid-account" role="alert">
                                                <strong class="text-danger">{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            {{ Form::label('contact', __('Contact'), ['class' => 'form-label']) }}
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
                                            {{ Form::label('stage', __('Stage'), ['class' => 'form-label']) }} <x-required></x-required>
                                            {!! Form::select('stage', $stages, null, [
                                                'class' => 'form-control',
                                                'required' => 'required',
                                                'placeholder' => 'select Opportunities Stage',
                                            ]) !!}
                                        </div>
                                        @error('stage')
                                            <span class="invalid-stage" role="alert">
                                                <strong class="text-danger">{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            {{ Form::label('amount', __('Amount'), ['class' => 'form-label']) }} <x-required></x-required>
                                            {{ Form::number('amount', null, ['class' => 'form-control', 'placeholder' => __('Enter Amount'), 'required' => 'required']) }}
                                            @error('amount')
                                                <span class="invalid-amount" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            {{ Form::label('probability', __('Probability'), ['class' => 'form-label']) }} <x-required></x-required>
                                            {{ Form::number('probability', null, ['class' => 'form-control', 'placeholder' => __('Enter Phone'), 'required' => 'required']) }}
                                            @error('probability')
                                                <span class="invalid-probability" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            {{ Form::label('close_date', __('Close Date'), ['class' => 'form-label']) }}
                                            {{ Form::date('close_date', null, ['class' => 'form-control ', 'placeholder' => __('Enter Phone'), 'required' => 'required']) }}
                                            @error('close_date')
                                                <span class="invalid-close_date" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    @if (module_is_active('Lead'))
                                        <div class="col-6">
                                            <div class="form-group">
                                                {{ Form::label('lead_source', __('Lead Source'), ['class' => 'form-label']) }}
                                                {!! Form::select('lead_source', $lead_source, null, ['class' => 'form-control ', 'required' => 'required']) !!}
                                                @error('lead_source')
                                                    <span class="invalid-lead_source" role="alert">
                                                        <strong class="text-danger">{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    @endif
                                    <div class="col-12">
                                        <div class="form-group">
                                            {{ Form::label('user', __(' Assigned User'), ['class' => 'form-label']) }}
                                            {!! Form::select('user', $user, $opportunities->user_id, ['class' => 'form-control']) !!}
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
                                            {!! Form::textarea('description', null, ['class' => 'form-control ', 'rows' => 3,'placeholder'=>__('Enter Description')]) !!}
                                            @error('description')
                                                <span class="invalid-description" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    @if (module_is_active('CustomField') && !$customFields->isEmpty())
                                        <div class="col-12">
                                            <div class="tab-pane fade show" id="tab-2" role="tabpanel">
                                                @include('custom-field::formBuilder', [
                                                    'fildedata' => $opportunities->customField,
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
                        {{ Form::open(['route' => ['streamstore', ['opportunities', $opportunities->name, $opportunities->id]], 'method' => 'post', 'enctype' => 'multipart/form-data']) }}
                        <div class="card-header">
                            <h5>{{ __('Stream') }}</h5>
                            <small class="text-muted">{{ __('Add stream comment') }}</small>
                        </div>
                        <div class="card-body">
                            <form>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            {{ Form::label('stream', __('Stream'), ['class' => 'form-label']) }}
                                            {{ Form::text('stream_comment', null, ['class' => 'form-control', 'placeholder' => __('Enter Stream Comment'), 'required' => 'required']) }}
                                        </div>
                                    </div>
                                    <input type="hidden" name="log_type" value="opportunities comment">

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
                            @if ($remark->data_id == $opportunities->id)
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
                                                        <div class="h6 ">{{ $remark->user_name }}
                                                        </div>
                                                        <span class="text-sm mb-0">
                                                            {{__('posted to ')}}<a href="#">{{ $remark->title }}</a> {{ __(',') }}
                                                            {{ $stream->log_type }} <a
                                                                href="#">{{ $remark->stream_comment }}</a>
                                                        </span>
                                                    </div>
                                                    <div class="col-2 d-flex align-items-center ">
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

                    <div id="useradd-3" class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col">
                                    <h5>{{ __('Sales Documents') }}</h5>
                                    <small class="text-muted">{{ __('Assigned document for this opportunities') }}</small>
                                </div>
                                <div class="col">
                                    <div class="float-end">
                                        <a data-size="lg"
                                            data-url="{{ route('salesdocument.create', ['opportunities', $opportunities->id]) }}"
                                            data-ajax-popup="true" data-bs-toggle="tooltip"
                                            data-title="{{ __('Create Document') }}"title="{{ __('Create') }}"
                                            class="btn btn-sm btn-primary btn-icon-only   ">
                                            <i class="ti ti-plus"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table datatable" id="datatable">
                                    <thead>
                                        <tr>
                                            <th scope="col" class="sort" data-sort="name">{{ __('Name') }}</th>
                                            <th scope="col" class="sort" data-sort="budget">{{ __('File') }}
                                            </th>
                                            <th scope="col" class="sort" data-sort="status">{{ __('Status') }}
                                            </th>
                                            <th scope="col" class="sort" data-sort="completion">
                                                {{ __('Created At') }}</th>
                                            <th scope="col" class="text-end">{{ __('Action') }}</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($documents as $document)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('salesdocument.edit', $document->id) }}"
                                                        data-size="md" data-title="{{ __('Document Details') }}"
                                                        class="action-item text-primary">
                                                        {{ $document->name }}</a>
                                                </td>
                                                <td class="budget">
                                                    @if (!empty($document->attachment))
                                                        <a href="{{ get_file($document->attachment) }}" download=""><i
                                                                class="ti ti-download"></i></a>
                                                    @else
                                                        <span>
                                                            {{ __('No File') }}
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($document->status == 0)
                                                        <span
                                                            class="badge bg-success p-2 px-3">{{ __(Workdo\Sales\Entities\SalesDocument::$status[$document->status]) }}</span>
                                                    @elseif($document->status == 1)
                                                        <span
                                                            class="badge bg-warning p-2 px-3">{{ __(Workdo\Sales\Entities\SalesDocument::$status[$document->status]) }}</span>
                                                    @elseif($document->status == 2)
                                                        <span
                                                            class="badge bg-danger p-2 px-3">{{ __(Workdo\Sales\Entities\SalesDocument::$status[$document->status]) }}</span>
                                                    @elseif($document->status == 3)
                                                        <span
                                                            class="badge bg-danger p-2 px-3">{{ __(Workdo\Sales\Entities\SalesDocument::$status[$document->status]) }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span
                                                        class="budget">{{ company_date_formate($document->created_at) }}</span>
                                                </td>
                                                @if (Laratrust::hasPermission('salesdocument show') || Laratrust::hasPermission('salesdocument edit') || Laratrust::hasPermission('salesdocument delete'))
                                                    <td class="text-end">
                                                        @permission('salesdocument show')
                                                            <div class="action-btn me-2">
                                                                <a data-size="lg"
                                                                    data-url="{{ route('salesdocument.show', $document->id) }}"
                                                                    data-ajax-popup="true" data-bs-toggle="tooltip"
                                                                    title="{{ __('View') }}"
                                                                    data-title="{{ __('Document Details') }}"
                                                                    class="mx-3 btn btn-sm align-items-center text-white bg-warning">
                                                                    <i class="ti ti-eye"></i>
                                                                </a>
                                                            </div>
                                                        @endpermission
                                                        @permission('salesdocument edit')
                                                            <div class="action-btn me-2">
                                                                <a href="{{ route('salesdocument.edit', $document->id) }}"
                                                                    class="mx-3 btn btn-sm align-items-center text-white bg-info"
                                                                    data-bs-toggle="tooltip" title="{{ __('Edit') }}"
                                                                    data-title="{{ __('Document Edit') }}"><i
                                                                        class="ti ti-pencil"></i></a>
                                                            </div>
                                                        @endpermission
                                                        @permission('salesdocument delete')
                                                            <div class="action-btn">
                                                                {!! Form::open(['method' => 'DELETE', 'route' => ['salesdocument.destroy', $document->id]]) !!}
                                                                <a href="#!"
                                                                    class="mx-3 btn btn-sm  align-items-center text-white show_confirm bg-danger"
                                                                    data-bs-toggle="tooltip" title="{{__('Delete')}}" data-confirm="{{ __('Are You Sure?') }}"
                                                                    data-text="{{ __('This action can not be undone. Do you want to continue?') }}">
                                                                    <i class="ti ti-trash"></i>
                                                                </a>
                                                                {!! Form::close() !!}
                                                            </div>
                                                        @endpermission
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

                    <div id="useradd-4" class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col">
                                    <h5>{{ __('Quotes') }}</h5>
                                    <small class="text-muted">{{ __('Assigned Quotes for this opportunities') }}</small>
                                </div>
                                <div class="col">

                                    <div class="float-end">
                                        <a data-url="{{ route('quote.create', ['opportunity', $opportunities->id]) }}"
                                            data-size="lg" data-ajax-popup="true" data-bs-toggle="tooltip"
                                            data-title="{{ __('Create Quote') }}"
                                            title="{{ __('Create') }}"class="btn btn-sm btn-primary btn-icon">
                                            <i class="ti ti-plus"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table datatable" id="datatable3">
                                    <thead>
                                        <tr>
                                            <th scope="col" class="sort" data-sort="name">{{ __('Name') }}</th>
                                            <th scope="col" class="sort" data-sort="status">{{ __('Status') }}
                                            </th>
                                            <th scope="col" class="sort" data-sort="completion">
                                                {{ __('Created At') }}</th>
                                            <th scope="col" class="sort" data-sort="completion">
                                                {{ __('Amount') }}</th>
                                            <th scope="col" class="sort" data-sort="completion">
                                                {{ __('Assign User') }}</th>

                                            @if (Laratrust::hasPermission('quote show') || Laratrust::hasPermission('quote edit') || Laratrust::hasPermission('quote delete'))
                                                <th scope="col" class="text-end">{{ __('Action') }}</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($quotes as $quote)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('quote.edit', $quote->id) }}" data-size="md"
                                                        data-title="{{ __('Quote') }}"
                                                        class="action-item text-primary">
                                                        {{ $quote->name }}</a>
                                                </td>
                                                <td>
                                                    @if ($quote->status == 0)
                                                        <span class="badge bg-secondary p-2 px-3"
                                                            style="width: 79px;">{{ __(Workdo\Sales\Entities\Quote::$status[$quote->status]) }}</span>
                                                    @elseif($quote->status == 1)
                                                        <span class="badge bg-info p-2 px-3"
                                                            style="width: 79px;">{{ __(Workdo\Sales\Entities\Quote::$status[$quote->status]) }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span
                                                        class="budget">{{ company_date_formate($quote->created_at) }}</span>
                                                </td>
                                                <td>
                                                    <span
                                                        class="budget">{{ currency_format_with_sym($quote->getTotal()) }}</span>
                                                </td>
                                                <td>
                                                    <span class="col-sm-12"><span
                                                            class="text-m">{{ ucfirst(!empty($quote->assign_user) ? $quote->assign_user->name : '-') }}</span></span>
                                                </td>
                                                @if (Laratrust::hasPermission('quote show') || Laratrust::hasPermission('quote edit') || Laratrust::hasPermission('quote delete'))
                                                    <td class="text-end">

                                                        @permission('quote show')
                                                            <div class="action-btn me-2">
                                                                <a href="{{ route('quote.show', $quote->id) }}"
                                                                    data-size="md"class="mx-3 btn btn-sm align-items-center text-white bg-warning"
                                                                    data-bs-toggle="tooltip" title="{{ __('View') }}"
                                                                    data-title="{{ __('Quote Details') }}">
                                                                    <i class="ti ti-eye"></i>
                                                                </a>
                                                            </div>
                                                        @endpermission
                                                        @permission('quote edit')
                                                            <div class="action-btn me-2">
                                                                <a href="{{ route('quote.edit', $quote->id) }}"
                                                                    class="mx-3 btn btn-sm align-items-center text-white bg-info"
                                                                    data-bs-toggle="tooltip" title="{{ __('Edit') }}"
                                                                    data-title="{{ __('Edit Quote') }}"><i
                                                                        class="ti ti-pencil"></i></a>
                                                            </div>
                                                        @endpermission
                                                        @permission('quote delete')
                                                            <div class="action-btn">
                                                                {!! Form::open(['method' => 'DELETE', 'route' => ['quote.destroy', $quote->id]]) !!}
                                                                <a href="#!"
                                                                    class="mx-3 btn btn-sm   align-items-center text-white show_confirm bg-danger"
                                                                    data-bs-toggle="tooltip" title="{{__('Delete')}}" data-confirm="{{ __('Are You Sure?') }}"
                                                                    data-text="{{ __('This action can not be undone. Do you want to continue?') }}">
                                                                    <i class="ti ti-trash"></i>
                                                                </a>
                                                                {!! Form::close() !!}
                                                            </div>
                                                        @endpermission
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

                    <div id="useradd-5" class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col">
                                    <h5>{{ __('Sales Orders') }}</h5>
                                    <small
                                        class="text-muted">{{ __('Assigned SalesOrder for this opportunities') }}</small>
                                </div>
                                <div class="col">
                                    <div class="float-end">
                                        <a data-size="lg"
                                            data-url="{{ route('salesorder.create', ['opportunity', $opportunities->id]) }}"
                                            data-ajax-popup="true" data-bs-toggle="tooltip" title="{{ __('Create') }}"
                                            data-title="{{ __('Create Sales Order') }}"
                                            class="btn btn-sm btn-primary btn-icon-only">
                                            <i class="ti ti-plus"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table datatable" id="datatable3">
                                    <thead>
                                        <tr>
                                            <th scope="col" class="sort" data-sort="name">{{ __('Name') }}</th>
                                            <th scope="col" class="sort" data-sort="status">{{ __('Status') }}
                                            </th>
                                            <th scope="col" class="sort" data-sort="completion">
                                                {{ __('Created At') }} </th>
                                            <th scope="col" class="sort" data-sort="completion">
                                                {{ __('Amount') }}</th>
                                            <th scope="col" class="sort" data-sort="completion">
                                                {{ __('Assigned User') }}</th>
                                            @if (Laratrust::hasPermission('salesorder show') || Laratrust::hasPermission('salesorder edit') || Laratrust::hasPermission('salesorder delete'))
                                                <th scope="col" class="text-end">{{ __('Action') }}</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($salesorders as $salesorder)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('salesorder.edit', $salesorder->id) }}"
                                                        data-size="md" data-title="{{ __('SalesOrder') }}"
                                                        class="action-item text-primary">
                                                        {{ $salesorder->name }}</a>
                                                </td>
                                                <td>
                                                    @if ($salesorder->status == 0)
                                                        <span class="badge bg-secondary p-2 px-3"
                                                            style="width: 79px;">{{ __(Workdo\Sales\Entities\SalesOrder::$status[$salesorder->status]) }}</span>
                                                    @elseif($salesorder->status == 1)
                                                        <span class="badge bg-info p-2 px-3"
                                                            style="width: 79px;">{{ __(Workdo\Sales\Entities\SalesOrder::$status[$salesorder->status]) }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span
                                                        class="budget">{{ company_date_formate($salesorder->created_at) }}</span>
                                                </td>
                                                <td>
                                                    <span
                                                        class="budget">{{ currency_format_with_sym($salesorder->getTotal()) }}</span>
                                                </td>
                                                <td>
                                                    <span
                                                        class="budget">{{ ucfirst(!empty($salesorder->assign_user) ? $salesorder->assign_user->name : '-') }}</span>
                                                </td>
                                                @if (Laratrust::hasPermission('salesorder show') || Laratrust::hasPermission('salesorder edit') || Laratrust::hasPermission('salesorder delete'))
                                                    <td class="text-end">
                                                        @permission('salesorder show')
                                                            <div class="action-btn me-2">
                                                                <a href="{{ route('salesorder.show', $salesorder->id) }}"
                                                                    data-size="md"
                                                                    class="mx-3 btn btn-sm align-items-center text-white bg-warning"
                                                                    data-bs-toggle="tooltip" title="{{ __('View') }}"
                                                                    data-title="{{ __('SalesOrders Details') }}">
                                                                    <i class="ti ti-eye"></i>
                                                                </a>
                                                            </div>
                                                        @endpermission
                                                        @permission('salesorder edit')
                                                            <div class="action-btn me-2">
                                                                <a href="{{ route('salesorder.edit', $salesorder->id) }}"
                                                                    class="mx-3 btn btn-sm align-items-center text-white bg-info"
                                                                    data-bs-toggle="tooltip" title="{{ __('Edit') }}"
                                                                    data-title="{{ __('Edit SalesOrders') }}"><i
                                                                        class="ti ti-pencil"></i></a>
                                                            </div>
                                                        @endpermission
                                                        @permission('salesorder delete')
                                                            <div class="action-btn">
                                                                {!! Form::open(['method' => 'DELETE', 'route' => ['salesorder.destroy', $salesorder->id]]) !!}
                                                                <a href="#!"
                                                                    class="mx-3 btn btn-sm   align-items-center text-white show_confirm bg-danger"
                                                                    data-bs-toggle="tooltip" title="{{__('Delete')}}" data-confirm="{{ __('Are You Sure?') }}"
                                                                    data-text="{{ __('This action can not be undone. Do you want to continue?') }}">
                                                                    <i class="ti ti-trash"></i>
                                                                </a>
                                                                {!! Form::close() !!}
                                                            </div>
                                                        @endpermission
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

                    <div id="useradd-6" class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col">
                                    <h5>{{ __('Sales Invoices') }}</h5>
                                    <small
                                        class="text-muted">{{ __('Assigned SalesInvoice for this opportunities') }}</small>
                                </div>
                                <div class="col">
                                    <div class="float-end">
                                        <a data-size="lg"
                                            data-url="{{ route('salesinvoice.create', ['opportunity', $opportunities->id]) }}"
                                            data-ajax-popup="true" data-bs-toggle="tooltip" title="{{ __('Create') }}"
                                            data-title="{{ __('Create Sales Invoice') }}"
                                            class="btn btn-sm btn-primary btn-icon-only">
                                            <i class="ti ti-plus"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table datatable" id="datatable3">
                                    <thead>
                                        <tr>
                                            <th scope="col" class="sort" data-sort="name">{{ __('Name') }}</th>
                                            <th scope="col" class="sort" data-sort="status">{{ __('Status') }}
                                            </th>
                                            <th scope="col" class="sort" data-sort="completion">
                                                {{ __('Created At') }} </th>
                                            <th scope="col" class="sort" data-sort="completion">
                                                {{ __('Amount') }}</th>
                                            <th scope="col" class="sort" data-sort="completion">
                                                {{ __('Assigned User') }}</th>
                                            @if (Laratrust::hasPermission('salesinvoice show') || Laratrust::hasPermission('salesinvoice edit') || Laratrust::hasPermission('salesinvoice delete'))
                                                <th scope="col" class="text-end">{{ __('Action') }}</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($salesinvoices as $salesinvoice)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('salesinvoice.edit', $salesinvoice->id) }}"
                                                        data-size="md" data-title="{{ __('SalesInvoice') }}"
                                                        class="action-item text-primary">
                                                        {{ $salesinvoice->name }}</a>
                                                </td>
                                                <td>
                                                    @if ($salesinvoice->status == 0)
                                                        <span class="badge bg-secondary p-2 px-3"
                                                            style="width: 91px;">{{ __(Workdo\Sales\Entities\SalesInvoice::$status[$salesinvoice->status]) }}</span>
                                                    @elseif($salesinvoice->status == 1)
                                                        <span class="badge bg-danger p-2 px-3"
                                                            style="width: 91px;">{{ __(Workdo\Sales\Entities\SalesInvoice::$status[$salesinvoice->status]) }}</span>
                                                    @elseif($salesinvoice->status == 2)
                                                        <span class="badge bg-warning p-2 px-3"
                                                            style="width: 91px;">{{ __(Workdo\Sales\Entities\SalesInvoice::$status[$salesinvoice->status]) }}</span>
                                                    @elseif($salesinvoice->status == 3)
                                                        <span class="badge bg-success p-2 px-3"
                                                            style="width: 91px;">{{ __(Workdo\Sales\Entities\SalesInvoice::$status[$salesinvoice->status]) }}</span>
                                                    @elseif($salesinvoice->status == 4)
                                                        <span class="badge bg-info p-2 px-3"
                                                            style="width: 91px;">{{ __(Workdo\Sales\Entities\SalesInvoice::$status[$salesinvoice->status]) }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span
                                                        class="budget">{{ company_date_formate($salesinvoice->created_at) }}</span>
                                                </td>
                                                <td>
                                                    <span
                                                        class="budget">{{ currency_format_with_sym($salesinvoice->getTotal()) }}</span>
                                                </td>
                                                <td>
                                                    <span
                                                        class="budget">{{ ucfirst(!empty($salesinvoice->assign_user) ? $salesinvoice->assign_user->name : '-') }}</span>
                                                </td>
                                                @if (Laratrust::hasPermission('salesinvoice show') || Laratrust::hasPermission('salesinvoice edit') || Laratrust::hasPermission('salesinvoice delete'))
                                                    <td class="text-end">
                                                        @permission('salesinvoice show')
                                                            <div class="action-btn me-2">
                                                                <a href="{{ route('salesinvoice.show', $salesinvoice->id) }}"
                                                                    data-bs-toggle="tooltip" title="{{ __('View') }}"
                                                                    class="mx-3 btn btn-sm align-items-center text-white bg-warning"
                                                                    data-title="{{ __('Invoice Details') }}">
                                                                    <i class="ti ti-eye"></i>
                                                                </a>
                                                            </div>
                                                        @endpermission
                                                        @permission('salesinvoice edit')
                                                            <div class="action-btn me-2">
                                                                <a href="{{ route('salesinvoice.edit', $salesinvoice->id) }}"
                                                                    data-bs-toggle="tooltip" title="{{ __('Edit') }}"
                                                                    class="mx-3 btn btn-sm align-items-center text-white bg-info"
                                                                    data-title="{{ __('Edit Invoice') }}"><i
                                                                        class="ti ti-pencil"></i></a>
                                                            </div>
                                                        @endpermission
                                                        @permission('salesinvoice delete')
                                                            <div class="action-btn">
                                                                {!! Form::open(['method' => 'DELETE', 'route' => ['salesinvoice.destroy', $salesinvoice->id]]) !!}
                                                                <a href="#!"
                                                                    class="mx-3 btn btn-sm   align-items-center text-white show_confirm bg-danger"
                                                                    data-bs-toggle="tooltip" title="{{__('Delete')}}" data-confirm="{{ __('Are You Sure?') }}"
                                                                    data-text="{{ __('This action can not be undone. Do you want to continue?') }}">
                                                                    <i class="ti ti-trash"></i>
                                                                </a>
                                                                {!! Form::close() !!}
                                                            </div>
                                                        @endpermission

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
    <script>
        $(document).on('click', '#billing_data', function() {
            $("[name='shipping_address']").val($("[name='billing_address']").val());
            $("[name='shipping_city']").val($("[name='billing_city']").val());
            $("[name='shipping_state']").val($("[name='billing_state']").val());
            $("[name='shipping_country']").val($("[name='billing_country']").val());
            $("[name='shipping_postalcode']").val($("[name='billing_postalcode']").val());
        })
    </script>
    <script>
        $(document).on('change', 'select[name=opportunity]', function() {
            var opportunities = $(this).val();
            getaccount(opportunities);
        });

        function getaccount(opportunities_id) {
            $.ajax({
                url: '{{ route('quote.getaccount') }}',
                type: 'POST',
                data: {
                    "opportunities_id": opportunities_id,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(data) {
                    $('#amount').val(data.opportunitie.amount);
                    $('#name').val(data.opportunitie.name);
                    $('#account_name').val(data.account.name);
                    $('#account_id').val(data.account.id);
                    $('#billing_address').val(data.account.billing_address);
                    $('#shipping_address').val(data.account.shipping_address);
                    $('#billing_city').val(data.account.billing_city);
                    $('#billing_state').val(data.account.billing_state);
                    $('#shipping_city').val(data.account.shipping_city);
                    $('#shipping_state').val(data.account.shipping_state);
                    $('#billing_country').val(data.account.billing_country);
                    $('#billing_postalcode').val(data.account.billing_postalcode);
                    $('#shipping_country').val(data.account.shipping_country);
                    $('#shipping_postalcode').val(data.account.shipping_postalcode);

                }
            });
        }
    </script>

@endpush
