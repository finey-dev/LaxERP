@extends('layouts.main')

@section('page-title')
    {{ $asset->title }}
@endsection

@section('page-breadcrumb')
    {{ __('Asset') }}
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-3">
            <div class="card sticky-top" style="top:30px">
                <div class="list-group list-group-flush" id="useradd-sidenav">
                    @permission('equipment location manage')
                        <a href="#asset" class="list-group-item list-group-item-action border-0 active">{{ __('Asset') }}
                            <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                        </a>
                    @endpermission

                    @permission('depreciation manage')
                        <a href="#accessories" class="list-group-item list-group-item-action border-0">{{ __('Accessories') }}
                            <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                        </a>
                    @endpermission

                    @permission('asset manufacturers manage')
                        <a href="#component" class="list-group-item list-group-item-action border-0">{{ __('Component') }}
                            <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                        </a>
                    @endpermission

                    @permission('equipment categories manage')
                        <a href="#consumables" class="list-group-item list-group-item-action border-0">{{ __('Consumables') }}
                            <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                        </a>
                    @endpermission

                    @permission('equipment status labels manage')
                        <a href="#maintenance" class="list-group-item list-group-item-action border-0">{{ __('Maintenance') }}
                            <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                        </a>
                    @endpermission

                    @permission('equipment status labels manage')
                        <a href="#licenses" class="list-group-item list-group-item-action border-0">{{ __('Licenses') }}
                            <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                        </a>
                    @endpermission
                </div>
            </div>
        </div>

        <div class="col-xl-9">
            <div class="row">

                <div class="col-xl-5 d-flex">
                    <div id="asset" class="card w-100">
                        <div class="card-header">
                            <h5>{{ $asset->title }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <img src="{{ !empty($asset->asset_image) ? get_file($asset->asset_image) : asset('packages/workdo/FixEquipment/src/Resources/assets/images/defualt.png') }}"
                                    alt="" class="img-fluid">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-7">
                    <div class="card">
                        <div class="card-header">
                            <h5>{{ __('Details') }}</h5>
                        </div>
                        <div class="row card-body">
                            <div class="col-6">
                                <dt class="h6 text-lg">{{ __('Purchase Date') }}</dt>
                                <dd class="text-lg">
                                    {{ company_date_formate($asset->purchase_date) }}
                                </dd>

                                <dt class="h6 text-lg mt-4">{{ __('Manufacturer') }}</dt>
                                <dd class="text-lg">
                                    {{ !empty($asset->Manufacturer->title) ? $asset->Manufacturer->title : '' }}
                                </dd>

                                <dt class="h6 text-lg mt-4">{{ __('Purchase Price') }}</dt>
                                <dd class="text-lg">
                                    {{ currency_format_with_sym($asset->purchase_price) }}
                                </dd>

                                <dt class="h6 text-lg mt-4">{{ __('Location') }}</dt>
                                <dd class="text-lg">
                                    {{ !empty($asset->Location) ? $asset->Location->location_name : '' }}
                                </dd>
                                @if (module_is_active('DoubleEntry'))
                                    <dt class="h6 text-lg mt-4">{{ __('Account') }}</dt>
                                    <dd class="text-lg">
                                        {{ !empty($account->name) ? $account->name : '' }}
                                    </dd>
                                @endif
                            </div>
                            <div class="col-6">
                                <dt class="h6 text-lg">{{ __('Category') }}</dt>
                                <dd class="text-lg">
                                    {{ !empty($asset->equipmentCategory) ? $asset->equipmentCategory->title : '' }}
                                </dd>

                                <dt class="h6 text-lg mt-4">{{ __('Manufacturer') }}</dt>
                                <dd class="text-lg">
                                    {{ !empty($asset->Manufacturer) ? $asset->Manufacturer->title : '' }}
                                </dd>

                                <dt class="h6 text-lg mt-4">{{ __('Supplier') }}</dt>
                                <dd class="text-lg">
                                    {{ !empty($asset->Supplier) ? $asset->Supplier->name : '' }}
                                </dd>

                                <dt class="h6 text-lg mt-4">{{ __('Depreciation') }}</dt>
                                <dd class="text-lg">
                                    {{ !empty($asset->Depreciation) ? $asset->Depreciation->title : '' }}
                                </dd>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card" id="accessories">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>{{ __('Accessories') }}</h5>
                        </div>
                        <div class="col-md-6">
                            @permission('accessories create')
                                <a class="btn btn-sm btn-primary float-end" data-ajax-popup="true" data-size="md"
                                    data-title="{{ __('Create Accessories') }}"
                                    data-url="{{ route('fix.equipment.accessories.create') }}" data-toggle="tooltip"
                                    title="{{ __('Create') }}">
                                    <i class="ti ti-plus"></i>
                                </a>
                            @endpermission
                        </div>
                    </div>
                </div>
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table mb-0 pc-dt-simple" id="assets">
                            <thead>
                                <tr>
                                    <th class="text-left">{{ __('Title') }}</th>
                                    <th class="text-left">{{ __('Purchase Price') }}</th>
                                    <th class="text-left">{{ __('Quantity') }}</th>
                                    <th class="text-end">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($assessories as $assessory)
                                    <tr>
                                        <td class="text-left">{{ $assessory->title }}</td>
                                        <td class="text-left">{{ currency_format_with_sym($assessory->price) }}</td>
                                        <td class="text-left">{{ $assessory->quantity }}</td>
                                        <td>
                                            <div class="float-end">
                                                @permission('accessories edit')
                                                <div class="action-btn me-2">
                                                    <a class="btn btn-sm  bg-info align-items-center"
                                                    data-url="{{ route('fix.equipment.accessories.edit', $assessory->id) }}"
                                                    data-ajax-popup="true" data-size="md" data-bs-toggle="tooltip"
                                                    title="" data-title="{{ __('Edit Accessories') }}"
                                                    data-bs-original-title="{{ __('Edit') }}">
                                                    <i class="ti ti-pencil text-white"></i>
                                                </a>
                                            </div>
                                            @endpermission
                                            @permission('accessories delete')
                                                <div class="action-btn">
                                                    {{ Form::open(['route' => ['fix.equipment.accessories.delete', $assessory->id], 'class' => 'm-0']) }}
                                                    @method('GET')
                                                    <a class="btn btn-sm  bg-danger align-items-center bs-pass-para show_confirm"
                                                        data-bs-toggle="tooltip" title="" data-bs-original-title={{__('Delete')}}
                                                        aria-label="Delete" data-confirm="{{ __('Are You Sure?') }}"
                                                        data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                        data-confirm-yes="delete-form-{{ $assessory->id }}"><i
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

            <div class="card" id="component">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>{{ __('Component') }}</h5>
                        </div>
                        <div class="col-md-6">
                            @permission('equipment components create')
                                <a class="btn btn-sm btn-primary float-end" data-ajax-popup="true" data-size="md"
                                    data-title="{{ __('Create Component') }}"
                                    data-url="{{ route('fix.equipment.component.create') }}" data-toggle="tooltip"
                                    title="{{ __('Create') }}">
                                    <i class="ti ti-plus"></i>
                                </a>
                            @endpermission
                        </div>
                    </div>
                </div>
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table mb-0 pc-dt-simple" id="assets">
                            <thead>
                                <tr>
                                    <th class="text-left">{{ __('Title') }}</th>
                                    <th class="text-left">{{ __('Purchase Price') }}</th>
                                    <th class="text-left">{{ __('Quantity') }}</th>
                                    <th class="text-end">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($components as $component)
                                    <tr>
                                        <td class="text-left">{{ $component->title }}</td>
                                        <td class="text-left">{{ currency_format_with_sym($component->price) }}</td>
                                        <td class="text-left">{{ $component->quantity }}</td>
                                        <td>
                                            <div class="float-end">
                                                @permission('equipment components edit')
                                                <div class="action-btn me-2">
                                                    <a class="btn btn-sm bg-info align-items-center"
                                                    data-url="{{ route('fix.equipment.component.edit', $component->id) }}"
                                                    data-ajax-popup="true" data-size="md" data-bs-toggle="tooltip"
                                                    title="" data-title="{{ __('Edit Component') }}"
                                                    data-bs-original-title="{{ __('Edit') }}">
                                                    <i class="ti ti-pencil text-white"></i>
                                                </a>
                                            </div>
                                            @endpermission
                                            @permission('equipment components delete')
                                                <div class="action-btn">
                                                    {{ Form::open(['route' => ['fix.equipment.component.delete', $component->id], 'class' => 'm-0']) }}
                                                    @method('GET')
                                                    <a class="btn btn-sm bg-danger align-items-center bs-pass-para show_confirm"
                                                        data-bs-toggle="tooltip" title=""
                                                        data-bs-original-title={{__('Delete')}} aria-label="Delete"
                                                        data-confirm="{{ __('Are You Sure?') }}"
                                                        data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                        data-confirm-yes="delete-form-{{ $component->id }}"><i
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

            <div class="card" id="consumables">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>{{ __('Consumables') }}</h5>
                        </div>
                        <div class="col-md-6">
                            @permission('consumables create')
                                <a class="btn btn-sm btn-primary float-end" data-ajax-popup="true" data-size="md"
                                    data-title="{{ __('Create consumables') }}"
                                    data-url="{{ route('fix.equipment.consumables.create') }}" data-toggle="tooltip"
                                    title="{{ __('Create') }}">
                                    <i class="ti ti-plus"></i>
                                </a>
                            @endpermission
                        </div>
                    </div>
                </div>
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table mb-0 pc-dt-simple" id="assets">
                            <thead>
                                <tr>
                                    <th class="text-left">{{ __('Title') }}</th>
                                    <th class="text-left">{{ __('Category') }}</th>
                                    <th class="text-left">{{ __('Purchase Price') }}</th>
                                    <th class="text-left">{{ __('Quantity') }}</th>
                                    <th class="text-end">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($consumables as $consumable)
                                    <tr>
                                        <td class="text-left">{{ $consumable->title }}</td>
                                        <td class="text-left">
                                            {{ !empty($consumable->equipmentCategory->title) ? $consumable->equipmentCategory->title : '' }}
                                        </td>
                                        <td class="text-left">{{ currency_format_with_sym($consumable->price) }}</td>
                                        <td class="text-left">{{ $consumable->quantity }}</td>

                                        <td>
                                            <div class="float-end">
                                                @permission('consumables edit')
                                                <div class="action-btn me-2">
                                                    <a class="btn btn-sm bg-info align-items-center"
                                                    data-url="{{ route('fix.equipment.consumables.edit', $consumable->id) }}"
                                                    data-ajax-popup="true" data-size="md" data-bs-toggle="tooltip"
                                                    title="" data-title="{{ __('Edit Consumable') }}"
                                                    data-bs-original-title="{{ __('Edit') }}">
                                                    <i class="ti ti-pencil text-white"></i>
                                                </a>
                                            </div>
                                            @endpermission
                                            @permission('consumables delete')
                                                <div class="action-btn">
                                                    {{ Form::open(['route' => ['fix.equipment.consumables.delete', $consumable->id], 'class' => 'm-0']) }}
                                                    @method('GET')
                                                    <a class="btn btn-sm bg-danger align-items-center bs-pass-para show_confirm"
                                                        data-bs-toggle="tooltip" title=""
                                                        data-bs-original-title={{__('Delete')}} aria-label="Delete"
                                                        data-confirm="{{ __('Are You Sure?') }}"
                                                        data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                        data-confirm-yes="delete-form-{{ $consumable->id }}"><i
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

            <div class="card" id="maintenance">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>{{ __('Maintenance') }}</h5>
                        </div>
                        <div class="col-md-6">
                            @permission('equipment maintenance create')
                                <a class="btn btn-sm btn-primary float-end" data-ajax-popup="true" data-size="md"
                                    data-title="{{ __('Create Maintenance') }}"
                                    data-url="{{ route('fix.equipment.maintenance.create') }}" data-toggle="tooltip"
                                    title="{{ __('Create') }}">
                                    <i class="ti ti-plus"></i>
                                </a>
                            @endpermission
                        </div>
                    </div>
                </div>
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table mb-0 pc-dt-simple" id="assets">
                            <thead>
                                <tr>
                                    <th class="text-left">{{ __('Title') }}</th>
                                    <th class="text-left">{{ __('Maintenance Date') }}</th>
                                    <th class="text-left">{{ __('Price') }}</th>
                                    <th class="text-end">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($maintenances as $maintenance)
                                    <tr>
                                        <td class="text-left">{{ $maintenance->maintenance_type }}</td>
                                        <td class="text-left">{{ company_date_formate($maintenance->maintenance_date) }}</td>
                                        <td class="text-left">{{ currency_format_with_sym($maintenance->price) }}</td>
                                        <td>
                                            <div class="float-end">
                                                @permission('equipment maintenance edit')
                                                <div class="action-btn me-2">
                                                    <a class="btn btn-sm  bg-info align-items-center"
                                                    data-url="{{ route('fix.equipment.maintenance.edit', $maintenance->id) }}"
                                                    data-ajax-popup="true" data-size="md" data-bs-toggle="tooltip"
                                                    title="" data-title="{{ __('Edit Maintenance') }}"
                                                    data-bs-original-title="{{ __('Edit') }}">
                                                    <i class="ti ti-pencil text-white"></i>
                                                </a>
                                            </div>
                                            @endpermission
                                            @permission('equipment maintenance delete')
                                                <div class="action-btn">
                                                    {{ Form::open(['route' => ['fix.equipment.maintenance.delete', $maintenance->id], 'class' => 'm-0']) }}
                                                    @method('GET')
                                                    <a class="btn btn-sm  bg-danger align-items-center bs-pass-para show_confirm"
                                                        data-bs-toggle="tooltip" title=""
                                                        data-bs-original-title={{__('Delete')}} aria-label="Delete"
                                                        data-confirm="{{ __('Are You Sure?') }}"
                                                        data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                        data-confirm-yes="delete-form-{{ $maintenance->id }}"><i
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

            <div class="card" id="licenses">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>{{ __('Licenses') }}</h5>
                        </div>
                        <div class="col-md-6">
                            @permission('asset licenses create')
                                <a class="btn btn-sm btn-primary float-end" data-ajax-popup="true" data-size="md"
                                    data-title="{{ __('Create Licenses') }}"
                                    data-url="{{ route('fix.equipment.licence.create') }}" data-toggle="tooltip"
                                    title="{{ __('Create') }}">
                                    <i class="ti ti-plus"></i>
                                </a>
                            @endpermission
                        </div>
                    </div>
                </div>
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table mb-0 pc-dt-simple" id="assets">
                            <thead>
                                <tr>
                                    <th class="text-left">{{ __('Title') }}</th>
                                    <th class="text-left">{{ __('Licence Number') }}</th>
                                    <th class="text-left">{{ __('Purchase Date') }}</th>
                                    <th class="text-left">{{ __('Expier Date') }}</th>
                                    <th class="text-left">{{ __('Price') }}</th>
                                    <th class="text-end">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($licenses as $license)
                                    <tr>
                                        <td class="text-left">{{ $license->title }}</td>
                                        <td class="text-left">{{ $license->license_number }}</td>
                                        <td class="text-left">{{ company_date_formate($license->purchase_date) }}</td>
                                        <td class="text-left">{{ company_date_formate($license->expire_date)}}</td>
                                        <td class="text-left">{{ currency_format_with_sym($license->purchase_price) }}
                                        </td>
                                        <td>
                                            <div class="float-end">
                                                @permission('asset licenses edit')
                                                <div class="action-btn me-2">
                                                    <a class="btn btn-sm  bg-info align-items-center"
                                                    data-url="{{ route('fix.equipment.licence.edit', $license->id) }}"
                                                    data-ajax-popup="true" data-size="md" data-bs-toggle="tooltip"
                                                    title="" data-title="{{ __('Edit License') }}"
                                                    data-bs-original-title="{{ __('Edit') }}">
                                                    <i class="ti ti-pencil text-white"></i>
                                                </a>
                                            </div>
                                            @endpermission
                                            @permission('asset licenses delete')
                                                <div class="action-btn">
                                                    {{ Form::open(['route' => ['fix.equipment.licence.delete', $license->id], 'class' => 'm-0']) }}
                                                    @method('GET')
                                                    <a class="btn btn-sm  bg-danger align-items-center bs-pass-para show_confirm"
                                                        data-bs-toggle="tooltip" title=""
                                                        data-bs-original-title={{__('Delete')}} aria-label="Delete"
                                                        data-confirm="{{ __('Are You Sure?') }}"
                                                        data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                        data-confirm-yes="delete-form-{{ $license->id }}"><i
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

@push('scripts')
    <script>
        if ($('#asset').length > 0) {
            var scrollSpy = new bootstrap.ScrollSpy(document.body, {
                target: '#asset',
                offset: 300,
            });

            const links = document.querySelectorAll('#useradd-sidenav a');

            links.forEach(link => {
                link.addEventListener('click', () => {
                    links.forEach(otherLink => {
                        otherLink.classList.remove('active');
                    });
                    link.classList.add('active');
                });
            });
        }
    </script>
@endpush
