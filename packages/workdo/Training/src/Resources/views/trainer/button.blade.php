@permission('trainer show')
    <div class="action-btn me-2">
        <a href="#" class="mx-3 btn btn-sm bg-warning align-items-center" data-size="lg"
            data-url="{{ route('trainer.show', $trainers->id) }}" data-ajax-popup="true" data-size="md" data-bs-toggle="tooltip"
            title="" data-title="{{ __('Trainer Details') }}" data-bs-original-title="{{ __('View') }}">
            <i class="ti ti-eye text-white"></i>
        </a>
    </div>
@endpermission

@permission('trainer edit')
    <div class="action-btn me-2">
        <a href="#" class="mx-3 btn btn-sm bg-info align-items-center" data-size="lg"
            data-url="{{ route('trainer.edit', $trainers->id) }}" data-ajax-popup="true" data-size="md"
            data-bs-toggle="tooltip" title="" data-title="{{ __('Edit Trainer') }}"
            data-bs-original-title="{{ __('Edit') }}">
            <i class="ti ti-pencil text-white"></i>
        </a>
    </div>
@endpermission

@permission('trainer delete')
    <div class="action-btn">
        {!! Form::open([
            'method' => 'DELETE',
            'route' => ['trainer.destroy', $trainers->id],
            'id' => 'delete-form-' . $trainers->id,
        ]) !!}
        <a class="mx-3 btn btn-sm bg-danger align-items-center bs-pass-para show_confirm" data-bs-toggle="tooltip"
            title="{{ __('Delete') }}" data-confirm="{{ __('Are You Sure?') }}" data-text="{{ __('This action can not be undone. Do you want to continue?') }}"><i
                class="ti ti-trash text-white text-white"></i></a>
        {!! Form::close() !!}
    </div>
@endpermission
