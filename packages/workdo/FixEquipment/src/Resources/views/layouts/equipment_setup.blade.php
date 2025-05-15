<div class="card sticky-top" style="top:30px">
    <div class="list-group list-group-flush" id="useradd-sidenav">
        @permission('equipment location manage')
            <a href="{{route('fix.equipment.location.index')}}" class="list-group-item list-group-item-action border-0 {{ (request()->is('fixequipment/locations*') ? 'active' : '')}}">{{__('Location')}} <div class="float-end"><i class="ti ti-chevron-right"></i></div></a>
        @endpermission

        @permission('depreciation manage')
            <a href="{{ route('fix.equipment.depreciation.index') }}" class="list-group-item list-group-item-action border-0 {{ (request()->is('fixequipment/depreciation*') ? 'active' : '') }}">{{ __('Depreciation') }} <div class="float-end"><i class="ti ti-chevron-right"></i></div></a>
        @endpermission
        
        @permission('asset manufacturers manage')
            <a href="{{ route('fix.equipment.manufacturer.index') }}" class="list-group-item list-group-item-action border-0 {{ (request()->is('fixequipment/manufacturer*') ? 'active' : '') }}">{{ __('Manufacturer') }} <div class="float-end"><i class="ti ti-chevron-right"></i></div> </a>
        @endpermission

        @permission('equipment categories manage')
            <a href="{{ route('fix.equipment.category.index') }}" class="list-group-item list-group-item-action border-0 {{ (request()->is('fixequipment/category*') ? 'active' : '') }}">{{ __('Category') }} <div class="float-end"><i class="ti ti-chevron-right"></i></div> </a>
        @endpermission

        @permission('equipment status labels manage')
            <a href="{{ route('fix.equipment.status.index') }}" class="list-group-item list-group-item-action border-0 {{ (request()->is('fixequipment/status*') ? 'active' : '') }}">{{ __('Status') }} <div class="float-end"><i class="ti ti-chevron-right"></i></div></a>
        @endpermission
    </div>
</div>