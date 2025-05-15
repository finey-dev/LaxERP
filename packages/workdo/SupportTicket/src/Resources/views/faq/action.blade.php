@permission('faq edit')
    <div class="action-btn me-2">
        <a data-size="md" data-title="{{ __('Edit FAQ') }}" data-ajax-popup="true"
            data-url="{{ route('support-ticket-faq.edit', $faq->id) }}"
            class="mx-3 btn btn-sm d-inline-flex align-items-center bg-info" data-toggle="tooltip" title="{{ __('Edit') }}"><span
                class="text-white"> <i class="ti ti-pencil"></i></span></a>
    </div>
@endpermission
@permission('faq delete')
    <div class="action-btn">
        <form method="POST" action="{{ route('support-ticket-faq.destroy', $faq->id) }}"
            id="user-form-{{ $faq->id }}">
            @csrf
            @method('DELETE')
            <input name="_method" type="hidden" value="DELETE">
            <button type="button" class="mx-3 btn btn-sm d-inline-flex align-items-center show_confirm bg-danger"
                data-toggle="tooltip" title="{{ __('Delete') }}">
                <span class="text-white"> <i class="ti ti-trash"></i></span>
            </button>
        </form>
    </div>
@endpermission
