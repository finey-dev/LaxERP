@extends('layouts.main')

@section('page-title')
    {{ __('Manage Status') }}
@endsection

@section('page-breadcrumb')
    {{ __('Status') }}
@endsection

@push('scripts')
    <script src="{{ asset('packages/workdo/FixEquipment/src/Resources/assets/js/jscolor.js') }}"></script>
@endpush

@section('page-action')
    <div>
        @permission('equipment status labels create')
            <a class="btn btn-sm btn-primary" data-ajax-popup="true" data-size="md" data-title="{{ __('Create Status') }}"
                data-url="{{ route('fix.equipment.status.create') }}" data-toggle="tooltip" title="{{ __('Create') }}">
                <i class="ti ti-plus"></i>
            </a>
        @endpermission
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-3">
            @include('fix-equipment::layouts.equipment_setup')
        </div>
        <div class="col-sm-9">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table mb-0 pc-dt-simple" id="assets">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th class="text-left">{{ __('Title') }}</th>
                                    <th class="text-end">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($statuses as $index => $status)
                                    <tr>
                                        <td>{{ ++$index }}</td>
                                        <td class="text-left">{{ $status->title }}</td>
                                        <td>
                                            <div class="float-end">
                                            @permission('equipment status labels edit')
                                                <div class="action-btn me-2">
                                                    <a class="btn btn-sm   bg-info align-items-center"
                                                        data-url="{{ route('fix.equipment.status.edit', $status->id) }}"
                                                        data-ajax-popup="true" data-size="md" data-bs-toggle="tooltip"
                                                        title="" data-title="{{ __('Edit Status') }}"
                                                        data-bs-original-title="{{ __('Edit') }}">
                                                        <i class="ti ti-pencil text-white"></i>
                                                    </a>
                                                </div>
                                            @endpermission

                                            @permission('equipment status labels delete')
                                                <div class="action-btn">
                                                    {{ Form::open(['route' => ['fix.equipment.status.delete', $status->id], 'class' => 'm-0']) }}
                                                    @method('GET')
                                                    <a class="btn btn-sm  bg-danger  align-items-center bs-pass-para show_confirm"
                                                        data-bs-toggle="tooltip" title="" data-bs-original-title="{{ __('Delete')}}"
                                                        aria-label="Delete" data-confirm="{{ __('Are You Sure?') }}"
                                                        data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                        data-confirm-yes="delete-form-{{ $status->id }}"><i
                                                            class="ti ti-trash text-white text-white"></i></a>
                                                    {{ Form::close() }}
                                                </div>
                                            @endpermission
                                        </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
