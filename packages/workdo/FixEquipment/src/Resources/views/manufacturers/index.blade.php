@extends('layouts.main')

@section('page-title')
    {{ __('Manage Manufacturers') }}
@endsection

@section('page-breadcrumb')
    {{ __('Manufacturers') }}
@endsection

@section('page-action')
    <div>
        @permission('asset manufacturers create')
            <a class="btn btn-sm btn-primary" data-ajax-popup="true" data-size="md" data-title="{{ __('Create Manufacturer') }}"
                data-url="{{ route('fix.equipment.manufacturer.create') }}" data-toggle="tooltip" title="{{ __('Create') }}">
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
                                    <th class="text-left">{{ __('Manufacturer') }}</th>
                                    <th class="text-end">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($manufacturers as $index => $manufacturer)
                                    <tr>
                                        <td>{{ ++$index }}</td>
                                        <td class="text-left">{{ $manufacturer->title }}</td>
                                        <td>
                                            <div class="float-end">
                                            @permission('asset manufacturers edit')
                                                <div class="action-btn me-2">
                                                    <a class="btn btn-sm  bg-info align-items-center"
                                                        data-url="{{ route('fix.equipment.manufacturer.edit', $manufacturer->id) }}"
                                                        data-ajax-popup="true" data-size="md" data-bs-toggle="tooltip"
                                                        title="" data-title="{{ __('Edit Manufacturer') }}"
                                                        data-bs-original-title="{{ __('Edit') }}">
                                                        <i class="ti ti-pencil text-white"></i>
                                                    </a>
                                                </div>
                                            @endpermission
                                            @permission('asset manufacturers delete')
                                                <div class="action-btn me-2">
                                                    {{ Form::open(['route' => ['fix.equipment.manufacturer.delete', $manufacturer->id], 'class' => 'm-0']) }}
                                                    @method('GET')
                                                    <a class="btn btn-sm  bg-danger align-items-center bs-pass-para show_confirm"
                                                        data-bs-toggle="tooltip" title="" data-bs-original-title="{{ __('Delete')}}"
                                                        aria-label="Delete" data-confirm="{{ __('Are You Sure?') }}"
                                                        data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                        data-confirm-yes="delete-form-{{ $manufacturer->id }}"><i
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
