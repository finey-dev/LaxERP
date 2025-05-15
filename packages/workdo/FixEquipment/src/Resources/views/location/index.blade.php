@extends('layouts.main')

@section('page-title')
    {{ __('Manage Location') }}
@endsection

@section('page-breadcrumb')
    {{ __('Location') }}
@endsection

@section('page-action')
    <div>
        @permission('equipment location create')
            <a class="btn btn-sm btn-primary" data-ajax-popup="true" data-size="md" data-title="{{ __('Create Location') }}"
                data-url="{{ route('fix.equipment.location.create') }}" data-toggle="tooltip" title="{{ __('Create') }}">
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
                                    <th>{{ __('Attachment') }}</th>
                                    <th class="text-left">{{ __('Location Name') }}</th>
                                    <th class="text-left">{{ __('Address') }}</th>
                                    <th class="text-left">{{ __('Description') }}</th>
                                    <th class="text-end">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($locations as $index => $location)
                                    <tr>
                                        <td>{{ ++$index }}</td>
                                        <td>
                                            <a href="{{ !empty($location->attachment) ? get_file($location->attachment) : asset('packages/workdo/FixEquipment/src/Resources/assets/images/defualt.png') }}" class="image-fixsize" target="_blank">
                                            <img src="{{ !empty($location->attachment) ? get_file($location->attachment) : asset('packages/workdo/FixEquipment/src/Resources/assets/images/defualt.png') }}"
                                            class="rounded border-2 border border-primary" alt="asset" id="blah3"></a>
                                        </td>
                                        <td class="text-left">{{ $location->location_name }}</td>
                                        <td class="text-left">{{ $location->address }}</td>
                                        <td class="text-left">{{ !empty($location->location_description) ? $location->location_description : '-' }}</td>
                                        <td>
                                            <div class="float-end">
                                            @permission('equipment location edit')
                                                <div class="action-btn me-2">
                                                    <a class="btn btn-sm  bg-info align-items-center"
                                                        data-url="{{ route('fix.equipment.location.edit', $location->id) }}"
                                                        data-ajax-popup="true" data-size="md" data-bs-toggle="tooltip"
                                                        title="" data-title="{{ __('Edit Location') }}"
                                                        data-bs-original-title="{{ __('Edit') }}">
                                                        <i class="ti ti-pencil text-white"></i>
                                                    </a>
                                                </div>
                                            @endpermission

                                            @permission('equipment location delete')
                                                <div class="action-btn">
                                                    {{ Form::open(['route' => ['fix.equipment.location.delete', $location->id], 'class' => 'm-0']) }}
                                                    @method('GET')
                                                    <a class="btn btn-sm  bg-danger align-items-center bs-pass-para show_confirm"
                                                        data-bs-toggle="tooltip" title="" data-bs-original-title={{ __('Delete')}}
                                                        aria-label="Delete" data-confirm="{{ __('Are You Sure?') }}"
                                                        data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                        data-confirm-yes="delete-form-{{ $location->id }}"><i
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
