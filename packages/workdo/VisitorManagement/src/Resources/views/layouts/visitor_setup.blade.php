<div class="card sticky-top" style="top:30px">
    <div class="list-group list-group-flush" id="useradd-sidenav">
        @permission('visitor compliance type manage')
            <a href="{{route('visitors-compliance-type.index')}}" class="list-group-item list-group-item-action border-0 {{ (request()->is('visitors-compliance-type*') ? 'active' : '')}}">{{__('Compliance Type')}} <div class="float-end"><i class="ti ti-chevron-right"></i></div></a>
        @endpermission
        @permission('visitor document type manage')
        <a href="{{route('visitors-document-type.index')}}" class="list-group-item list-group-item-action border-0 {{ (request()->is('visitors-document-type*') ? 'active' : '')}}">{{__('Document Type')}} <div class="float-end"><i class="ti ti-chevron-right"></i></div></a>
        @endpermission
        @permission('reason manage')
        <a href="{{route('visit-reason.index')}}" class="list-group-item list-group-item-action border-0 {{ (request()->is('visit-reason*') ? 'active' : '')}}">{{__('Visit Purpose')}} <div class="float-end"><i class="ti ti-chevron-right"></i></div></a>
        @endpermission
    </div>
</div>