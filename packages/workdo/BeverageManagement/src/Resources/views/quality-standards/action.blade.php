@permission('quality-standards show')
<div class="action-btn me-2">
    <a class="mx-3 btn btn-sm bg-warning align-items-center" data-url="{{ route('quality-standards.show',$quality_standards->id) }}" data-ajax-popup="true" data-size="lg" data-bs-toggle="tooltip" title="" data-title="{{ __('Show Detail') }}" data-bs-original-title="{{ __('Show') }}">
        <i class="ti ti-eye text-white"></i>
    </a>
</div>
@endpermission

@permission('quality-standards edit')
<div class="action-btn me-2">
    <a class="mx-3 btn btn-sm bg-info align-items-center"
        data-url="{{ route('quality-standards.edit', $quality_standards->id) }}"
        data-ajax-popup="true" data-size="lg" data-bs-toggle="tooltip"
        title="" data-title="{{ __('Edit Quality Standards') }}"
        data-bs-original-title="{{ __('Edit') }}">
        <i class="ti ti-pencil text-white"></i>
    </a>
</div>
@endpermission

@permission('quality-standards delete')
    <div class="action-btn">
        {!! Form::open([
            'method' => 'DELETE',
            'route' => ['quality-standards.destroy', $quality_standards->id],
            'id' => 'delete-form-' . $quality_standards->id,
        ]) !!}
        <a class="mx-3 btn btn-sm bg-danger align-items-center bs-pass-para show_confirm" data-bs-toggle="tooltip"
            title="{{ __('Delete') }}" data-confirm="{{ __('Are You Sure?') }}" data-text="{{ __('This action can not be undone. Do you want to continue?') }}"><i
                class="ti ti-trash text-white text-white"></i></a>
        {!! Form::close() !!}
    </div>
@endpermission
