@permission('inventory show')
<span>
    <div class="action-btn ">
        <a href="{{ route('inventory.view', [$inventory->feild_id,$inventory->type]) }}" class="mx-3 btn btn-sm align-items-center bg-warning" data-bs-toggle="tooltip" title="" data-bs-original-title="{{ __('View') }}">
            <i class="ti ti-eye text-white"></i>
        </a>
    </div>
</span>
@endpermission

