@permission('files trash-restore')
    <div class="action-btn me-2">
        {!! Form::open([
            'method' => 'get',
            'route' => ['file.restore', encrypt($fileShare->id)],
            'id' => 'restore-form-' . $fileShare->id,
        ]) !!}
        <a href="#" class="mx-3 btn btn-sm align-items-center bs-pass-para show_confirm bg-info" aria-label="Delete"
            data-text="{{ __('You want to confirm restore. Press Yes to continue or Cancel to go back') }}"
            data-confirm-yes="restore-form- {{ $fileShare->id }}">
            <i class="ti ti-arrow-back-up text-white"></i>
        </a>
        {{ Form::close() }}
    </div>
@endpermission

@permission('files trash-delete')
    <div class="action-btn">
        {!! Form::open(['method' => 'DELETE', 'route' => ['files-trash.destroy', encrypt($fileShare->id)]]) !!}
        <a href="#!" class="mx-3 btn btn-sm align-items-center show_confirm bg-danger" data-bs-toggle="tooltip"
            data-bs-placement="top" title="{{ __('Delete') }}">
            <span class="text-white"> <i class="ti ti-trash"></i></span>
        </a>
        {!! Form::close() !!}
    </div>
@endpermission
