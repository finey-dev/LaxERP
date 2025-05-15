@permission('appraisal show')
    <div class="action-btn me-2">
        <a href="#" class="btn btn-sm d-inline-flex align-items-center bg-warning" data-size="lg"
            data-url="{{ route('appraisal.show', $appraisals->id) }}" data-ajax-popup="true" data-bs-toggle="tooltip"
            data-bs-original-title="{{ __('View') }}" data-title="{{ __('Appraisal Detail') }}"> <span class="text-white"><i
                    class="ti ti-eye"></i></a>
    </div>
@endpermission
@permission('appraisal edit')
    <div class="action-btn me-2">
        <a href="#" class="btn btn-sm d-inline-flex align-items-center bg-info" data-size="lg"
            data-url="{{ route('appraisal.edit', $appraisals->id) }}" class="dropdown-item" data-ajax-popup="true"
            data-title="{{ __('Edit Appraisal') }}" data-bs-toggle="tooltip" data-bs-original-title="{{ __('Edit') }}">
            <span class="text-white"> <i class="ti ti-pencil"></i></span></a>
    </div>
@endpermission
@permission('appraisal delete')
    <div class="action-btn">
        {{ Form::open([
            'method' => 'DELETE',
            'route' => ['appraisal.destroy', $appraisals->id],
            'id' => 'delete-form-' . $appraisals->id,
        ]) }}
        <a href="#" class="btn btn-sm  align-items-center bs-pass-para show_confirm bg-danger"
            data-bs-toggle="tooltip" title="{{ __('Delete') }}" data-confirm="{{ __('Are You Sure?') }}"
            data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
            data-bs-original-title="{{ __('Delete') }}" aria-label="{{ __('Delete') }}"
            data-confirm-yes="delete-form-{{ $appraisals->id }}"><i class="ti ti-trash text-white text-white"></i></a>
        {{ Form::close() }}
    </div>
@endpermission
