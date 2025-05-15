@permission('repair invoice show')
<div class="action-btn">
    <a class="btn btn-sm bg-warning align-items-center" href="{{ route('repair.request.invoice.show',[\Crypt::encrypt($repair_invoice->id)]) }}" data-bs-toggle="tooltip" title="{{__('View')}}">
        <i class="ti ti-eye text-white"></i>
    </a>
</div>
@endpermission
