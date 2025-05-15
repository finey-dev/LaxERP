@permission('rfx applicant edit')
<div class="action-btn  me-2 edit_btn">
    <a href="{{ route('rfx-applicant.edit', Crypt::encrypt($applicant->id)) }}"
        class="mx-3 btn bg-info btn-sm  align-items-center"
        data-bs-toggle="tooltip" data-bs-placement="top"
        data-title="{{ __('Edit RFx Applicant') }}"
        title="{{ __('Edit') }}">
        <i class="ti ti-pencil text-white"></i>
    </a>
</div>
@endpermission
@permission('rfx applicant delete')
<div class="action-btn delete_btn">
    {{ Form::open(['route' => ['rfx-applicant.destroy', $applicant->id], 'class' => 'm-0']) }}
    @method('DELETE')
    <a class="mx-3 btn bg-danger btn-sm  align-items-center bs-pass-para show_confirm"
        data-bs-toggle="tooltip" title=""
        data-bs-original-title="Delete" aria-label="Delete"
        data-confirm="{{ __('Are You Sure?') }}"
        data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
        data-confirm-yes="delete-form-{{ $applicant->id }}"><i
            class="ti ti-trash text-white text-white"></i></a>
    {{ Form::close() }}
</div>
@endpermission