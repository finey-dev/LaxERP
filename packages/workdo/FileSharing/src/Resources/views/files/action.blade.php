
@if (check_file($fileShare->file_path))
    <div class="action-btn me-2">
        <a class="mx-3 btn btn-sm align-items-center bg-primary" data-bs-toggle="tooltip"
            data-bs-placement="top" title="{{ __('Download') }}" href="{{ get_file($fileShare->file_path) }}" download>
            <i class="ti ti-download text-white"></i>
        </a>
    </div>
    <div class="action-btn me-2">
        <a class="mx-3 btn btn-sm align-items-center bg-secondary"
            href="{{ get_file($fileShare->file_path) }}" target="_blank" data-bs-toggle="tooltip"
            data-bs-original-title="{{ __('Preview') }}">
            <i class="ti ti-crosshair text-white"></i>
        </a>
    </div>
@endif
<div class="action-btn me-2">
    <a href="#" class="btn btn-sm d-inline-flex align-items-center bg-primary"
        id="{{ route('file.shared.link', \Illuminate\Support\Facades\Crypt::encrypt($fileShare->id)) }}"
        onclick="copyToClipboard(this)" data-bs-toggle="tooltip" data-title="{{ __('Click To Copy Link') }}"
        title="{{ __('Copy Link') }}"><span class="btn-inner--icon text-white"><i class="ti ti-file"></i></span></a>
</div>
@permission('files edit')
    <div class="action-btn me-2">
        <a class="mx-3 btn btn-sm align-items-center bg-info" data-url="{{ route('files.edit', $fileShare->id) }}"
            data-ajax-popup="true" data-size="md" data-title="{{ __('Edit File') }}" data-bs-toggle="tooltip"
            title="{{ __('Edit') }}" data-original-title="{{ __('Edit') }}">
            <i class="ti ti-pencil text-white"></i>
        </a>
    </div>
@endpermission
@permission('files delete')
    <div class="action-btn">
        {!! Form::open(['method' => 'DELETE', 'route' => ['files.destroy', $fileShare->id]]) !!}
        <a href="#!" class="mx-3 btn btn-sm align-items-center show_confirm bg-danger" data-bs-toggle="tooltip"
            data-bs-placement="top" title="{{ __('Move To Trash') }}">
            <span class="text-white"> <i class="ti ti-trash"></i></span>
        </a>
        {!! Form::close() !!}
    </div>
@endpermission
