@permission('indicator show')
    <div class="action-btn me-2">
        <a href="#" class="btn btn-sm d-inline-flex align-items-center bg-warning" data-size="lg"
            data-url="{{ route('indicator.show', $indicators->id) }}" data-ajax-popup="true" data-bs-toggle="tooltip"
            data-bs-original-title="{{ __('View') }}" data-title="{{ __('Indicator Detail') }}"> <span class="text-white"><i
                    class="ti ti-eye"></i></a>
    </div>
@endpermission
@permission('indicator edit')
    <div class="action-btn me-2">
        <a href="#" class="btn btn-sm d-inline-flex align-items-center bg-info" data-size="lg"
            data-url="{{ route('indicator.edit', $indicators->id) }}" class="dropdown-item" data-ajax-popup="true"
            data-title="{{ __('Edit Indicator') }}" data-bs-toggle="tooltip" data-bs-original-title="{{ __('Edit') }}">
            <span class="text-white"> <i class="ti ti-pencil"></i></span></a>
    </div>
@endpermission
@permission('indicator delete')
    <div class="action-btn">
        {{ Form::open([
            'method' => 'DELETE',
            'route' => ['indicator.destroy', $indicators->id],
            'id' => 'delete-form-' . $indicators->id,
        ]) }}
        <a href="#" class="btn btn-sm  align-items-center bs-pass-para show_confirm bg-danger"
            data-bs-toggle="tooltip" title="{{ __('Delete') }}" data-confirm="{{ __('Are You Sure?') }}"
            data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
            data-bs-original-title="{{ __('Delete') }}" aria-label="{{ __('Delete') }}"
            data-confirm-yes="delete-form-{{ $indicators->id }}"><i class="ti ti-trash text-white text-white"></i></a>
        {{ Form::close() }}
    </div>
@endpermission
