<div class="card sticky-top" style="top:30px">
    <div class="list-group list-group-flush" id="useradd-sidenav">
        @permission('courier branch manage')
            <a href="{{ route('courier.branch') }}"
                class="list-group-item list-group-item-action border-0 {{ Request::route()->getName() == 'courier.branch' ? 'active' : '' }}">{{ __('Branch') }}
                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
            </a>
        @endpermission
        @permission('servicetype manage')
            <a href="{{ route('courier.servicetype') }}"
                class="list-group-item list-group-item-action border-0 {{ Request::route()->getName() == 'courier.servicetype' ? 'active' : '' }}">{{ __('Service Type') }}
                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
            </a>
        @endpermission
        @permission('tracking manage')
            <a href="{{ route('courier.tracking.status') }}"
                class="list-group-item list-group-item-action border-0 {{ Request::route()->getName() == 'courier.tracking.status' ? 'active' : '' }}">{{ __('Tracking Status') }}
                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
            </a>
        @endpermission

        @permission('package category manage')
        <a href="{{ route('courier.packagecategory.status') }}"
            class="list-group-item list-group-item-action border-0 {{ Request::route()->getName() == 'courier.packagecategory.status' ? 'active' : '' }}">{{ __('Package Category') }}
            <div class="float-end"><i class="ti ti-chevron-right"></i></div>
        </a>
    @endpermission
    </div>
</div>
