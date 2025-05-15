@if (Auth::user()->type == 'super admin')
    @permission('verification edit')
        <div class="action-btn me-2">
            <a class="mx-3 btn btn-sm align-items-center bg-primary"
                data-url="{{ route('file-verification.edit', encrypt($verification->id)) }}" data-ajax-popup="true"
                data-size="md" data-bs-toggle="tooltip" title="" data-title="{{ __('Request Action') }}"
                data-bs-original-title="{{ __('Action') }}">
                <i class="ti ti-caret-right text-white"></i>
            </a>
        </div>
    @endpermission
    @permission('verification delete')
        <div class="action-btn">
            {!! Form::open(['method' => 'DELETE', 'route' => ['file.request.delete', encrypt($verification->id)]]) !!}
            <a href="#!" class="btn btn-sm align-items-center show_confirm bg-danger" data-bs-toggle="tooltip"
                data-bs-placement="top" title="{{ __('Delete') }}">
                <span class="text-white"> <i class="ti ti-trash"></i></span>
            </a>
            {!! Form::close() !!}
        </div>
    @endpermission
@else
    @permission('verification edit')
        <div class="action-btn me-2">
            <a class="btn btn-sm align-items-center bg-info"
                data-url="{{ route('file-verification.edit', encrypt($verification->id)) }}" data-ajax-popup="true"
                data-size="md" data-title="{{ __('Edit Verification Document') }}" data-bs-toggle="tooltip"
                title="{{ __('Edit') }}" data-original-title="{{ __('Edit') }}">
                <i class="ti ti-pencil text-white"></i>
            </a>
        </div>
    @endpermission
    @permission('verification delete')
        <div class="action-btn">
            {!! Form::open(['method' => 'DELETE', 'route' => ['file-verification.destroy', encrypt($verification->id)]]) !!}
            <a href="#!" class="btn btn-sm align-items-center show_confirm bg-danger" data-bs-toggle="tooltip"
                data-bs-placement="top" title="{{ __('Delete') }}">
                <span class="text-white"> <i class="ti ti-trash"></i></span>
            </a>
            {!! Form::close() !!}
        </div>
    @endpermission
@endif
