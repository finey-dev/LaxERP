@permission('Requests formfield edit')
<div class="action-btn me-2">
    <a data-url="{{ route('requests-formfield.edit', $field->id) }}" class="mx-3 bg-info  btn btn-sm  align-items-center"
     data-title="{{ __('Edit Form Field') }}"
        data-bs-toggle="tooltip" title="" data-size="lg"
        data-bs-original-title="{{ __('Edit') }} " data-ajax-popup="true">
        <i class="ti ti-pencil text-white"></i>
    </a>
</div>
@endpermission
@permission('Requests formfield delete')
<div class="action-btn  ">
    {{ Form::open(['route' => ['requests-formfield.destroy', $field->id], 'class' => 'm-0']) }}
    @method('DELETE')
    <a class="mx-3 btn btn-sm  align-items-center bg-danger bs-pass-para show_confirm"
        data-bs-toggle="tooltip" title="" data-bs-original-title="Delete"
        aria-label="Delete" data-confirm="{{ __('Are You Sure?') }}"
        data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
        data-confirm-yes="delete-form-{{ $field->id }}"><i
            class="ti ti-trash text-white text-white"></i></a>
    {{ Form::close() }}
</div>
@endpermission
