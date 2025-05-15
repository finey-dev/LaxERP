@permission('budget plan show')
    <div class="action-btn me-2">
        <a href="{{ route('budget.show', \Crypt::encrypt($budget->id)) }}" class="mx-3 btn btn-sm align-items-center bg-warning"
            data-bs-toggle="tooltip" title="{{ __('View') }}" data-original-title="{{ __('Detail') }}">
            <i class="ti ti-eye text-white"></i>
        </a>
    </div>
@endpermission
@permission('budget plan edit')
    <div class="action-btn me-2">
        <a href="{{ route('budget.edit', Crypt::encrypt($budget->id)) }}" class="mx-3 btn btn-sm align-items-center bg-info"
            data-bs-toggle="tooltip" title="{{ __('Edit') }}" data-original-title="{{ __('Edit') }}">
            <i class="ti ti-pencil text-white"></i>
        </a>
    </div>
@endpermission
@permission('budget plan delete')
    <div class="action-btn">
        {!! Form::open([
            'method' => 'DELETE',
            'route' => ['budget.destroy', $budget->id],
            'id' => 'delete-form-' . $budget->id,
        ]) !!}

        <a href="#" class="mx-3 btn btn-sm align-items-center bs-pass-para show_confirm bg-danger" data-bs-toggle="tooltip"
            title="" data-bs-original-title="{{ __('Delete') }}" aria-label="Delete"
            data-confirm="{{ __('Are You Sure?') }}"
            data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
            data-confirm-yes="delete-form-{{ $budget->id }}"><i class="text-white ti ti-trash"></i></a>
        {!! Form::close() !!}
    </div>
@endpermission
