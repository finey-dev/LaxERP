<div class="card sticky-top" style="top:30px">
    <div class="list-group list-group-flush" id="useradd-sidenav">
        @permission('jobcategory manage')
            <a href="{{ route('job-category.index') }}"
                class="list-group-item list-group-item-action border-0 {{ request()->is('job-category*') ? 'active' : '' }}">{{ __('Job Category') }}
                <div class="float-end"><i class="ti ti-chevron-right"></i></div></a>
        @endpermission

        @permission('jobstage manage')
            <a href="{{ route('job-stage.index') }}"
                class="list-group-item list-group-item-action border-0 {{ request()->is('job-stage*') ? 'active' : '' }}">{{ __('Job Stage') }}
                <div class="float-end"><i class="ti ti-chevron-right"></i></div></a>
        @endpermission

        @permission('paysliptype manage')
            <a href="{{ route('paysliptype.index') }}" class="list-group-item list-group-item-action border-0 {{ (request()->is('paysliptype*') ? 'active' : '')}}">{{__('Salary Type')}}<div class="float-end"><i class="ti ti-chevron-right"></i></div></a>
        @endpermission
        @permission('jobcandidate-category manage')
            <a href="{{ route('jobcandidate-category.index') }}" class="list-group-item list-group-item-action border-0 {{ (request()->is('jobcandidate-category*') ? 'active' : '')}}">{{__('Candidate Category')}}<div class="float-end"><i class="ti ti-chevron-right"></i></div></a>
        @endpermission
        @permission('screening type manage')
            <a href="{{ route('screening-type.index') }}" class="list-group-item list-group-item-action border-0 {{ (request()->is('screening-type*') ? 'active' : '')}}">{{__('Screening Type')}}<div class="float-end"><i class="ti ti-chevron-right"></i></div></a>
        @endpermission
        @permission('screen indicator manage')
            <a href="{{ route('screen-indicator.index') }}" class="list-group-item list-group-item-action border-0 {{ (request()->is('screen-indicator*') ? 'active' : '')}}">{{__('Screen Indicator')}}<div class="float-end"><i class="ti ti-chevron-right"></i></div></a>
        @endpermission
        @permission('letter offer manage')
            <a href="{{ route('offerletter.index') }}" class="list-group-item list-group-item-action border-0 {{ (request()->is('offerletter*') ? 'active' : '')}}">{{__('Offer Letter Settings')}}<div class="float-end"><i class="ti ti-chevron-right"></i></div></a>
        @endpermission
    </div>
</div>
