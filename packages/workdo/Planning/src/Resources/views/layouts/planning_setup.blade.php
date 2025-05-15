<div class="card sticky-top" style="top:30px">
    <div class="list-group list-group-flush" id="useradd-sidenav">

        @permission('planning categories manage')
            <a href="{{ route('planning-categories.index') }}"
                class="list-group-item list-group-item-action border-0 {{ request()->is('planning-categories*') ? 'active' : '' }}">{{ __('Categories') }}
                <div class="float-end"><i class="ti ti-chevron-right"></i></div></a>
        @endpermission

        @permission('planning stage manage')
            <a href="{{ route('planning-stage.index') }}"
                class="list-group-item list-group-item-action border-0 {{ request()->is('planning-stage*') ? 'active' : '' }}">{{ __('Stages') }}
                <div class="float-end"><i class="ti ti-chevron-right"></i></div></a>
        @endpermission

        @permission('planning status manage')
            <a href="{{ route('planning-status.index') }}"
                class="list-group-item list-group-item-action border-0 {{ request()->is('planning-status*') ? 'active' : '' }}">{{ __('Status') }}
                <div class="float-end"><i class="ti ti-chevron-right"></i></div></a>
        @endpermission
    </div>
</div>
