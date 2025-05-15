@permission('reminder edit')
<div class="action-btn  me-2">
    <a href="{{ route('reminder.edit', $reminder->id) }}" class="mx-3 btn btn-sm bg-info align-items-center" data-title="{{ __('Reminder Edit') }}"
        data-bs-toggle="tooltip" title="" data-size="lg"
        data-bs-original-title="{{ __('Edit') }}">
        <i class="ti ti-pencil text-white"></i>
    </a>
</div>
@endpermission
@permission('reminder delete')
<div class="action-btn">
    {{ Form::open(['route' => ['reminder.destroy', $reminder->id], 'class' => 'm-0']) }}
    @method('DELETE')
    <a class="mx-3 btn btn-sm bg-danger align-items-center bs-pass-para show_confirm"
        data-bs-toggle="tooltip" title="" data-bs-original-title="Delete"
        aria-label="Delete" data-confirm="{{ __('Are You Sure?') }}"
        data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
        data-confirm-yes="delete-form-{{ $reminder->id }}"><i
            class="ti ti-trash text-white text-white"></i></a>
    {{ Form::close() }}
</div>
@endpermission
