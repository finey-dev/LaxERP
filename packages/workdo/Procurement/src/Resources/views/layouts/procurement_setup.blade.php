<div class="card sticky-top" style="top:30px">
    <div class="list-group list-group-flush" id="useradd-sidenav">
        @permission('rfxcategory manage')
            <a href="{{ route('rfx-category.index') }}"
                class="list-group-item list-group-item-action border-0 {{ request()->is('rfx-category*') ? 'active' : '' }}">{{ __('RFx Category') }}
                <div class="float-end"><i class="ti ti-chevron-right"></i></div></a>
        @endpermission

        @permission('rfxstage manage')
            <a href="{{ route('rfx-stage.index') }}"
                class="list-group-item list-group-item-action border-0 {{ request()->is('rfx-stage*') ? 'active' : '' }}">{{ __('RFx Stage') }}
                <div class="float-end"><i class="ti ti-chevron-right"></i></div></a>
        @endpermission

        @permission('budgettype manage')
            <a href="{{ route('budgettype.index') }}" class="list-group-item list-group-item-action border-0 {{ (request()->is('budgettype*') ? 'active' : '')}}">{{__('Budget Type')}}<div class="float-end"><i class="ti ti-chevron-right"></i></div></a>
        @endpermission
    </div>
</div>