@if ($rfx->status != 'in_active')
<div class="action-btn me-2">
    <a href="#"
        id="{{ route('rfx.requirement', [$rfx->code, !empty($rfx) ? $rfx->createdBy->lang : 'en']) }}"
        class="mx-3 btn bg-primary btn-sm align-items-center"
        onclick="copyToClipboard(this)" data-bs-toggle="tooltip"
        title="{{ __('Copy') }}"
        data-original-title="{{ __('Click to copy link') }}"><i
            class="ti ti-file text-white"></i></a>
</div>
@endif
@permission('rfx show')
<div class="action-btn  me-2">
    <a href="{{ route('rfx.show', $rfx->id) }}"
        class="mx-3 btn bg-warning btn-sm  align-items-center"
        data-bs-toggle="tooltip" data-bs-placement="top"
        title="{{ __('View') }}">
        <i class="ti ti-eye text-white"></i>
    </a>
</div>
@endpermission

@permission('rfx edit')
<div class="action-btn  me-2">
    <a href="{{ route('rfx.edit', $rfx->id) }}"
        class="mx-3 btn bg-info btn-sm  align-items-center" data-url=""
        data-title="{{ __('Edit RFx') }}" data-bs-toggle="tooltip"
        data-bs-placement="top" title="{{ __('Edit') }}">
        <i class="ti ti-pencil text-white"></i>
    </a>
</div>
@endpermission

@permission('rfx delete')
<div class="action-btn">
    {!! Form::open(['method' => 'DELETE', 'route' => ['rfx.destroy', $rfx->id], 'id' => 'delete-form-' . $rfx->id]) !!}
    <a href="#!"
        class="mx-3 btn bg-danger btn-sm  align-items-center bs-pass-para show_confirm"
        data-bs-toggle="tooltip" data-bs-placement="top"
        title="{{ __('Delete') }}">
        <i class="ti ti-trash text-white"></i></a>
    {!! Form::close() !!}
</div>
@endpermission
