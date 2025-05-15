@permission('knowledgebase edit')
<div class="action-btn me-2">
    <a data-ajax-popup="true" data-size="md"
        data-title="{{ __('Edit Knowledge') }}"
        data-url="{{ route('support-ticket-knowledge.edit', $knowledge->id) }}"
        data-toggle="tooltip" title="{{ __('Edit') }}"
        class="mx-3 btn btn-sm d-inline-flex align-items-center bg-info"
        data-toggle="tooltip"><span class="text-white"> <i
                class="ti ti-pencil"></i></span></a>
</div>
@endpermission
@permission('knowledgebase delete')
<div class="action-btn">
    <form method="POST"
        action="{{ route('support-ticket-knowledge.destroy', $knowledge->id) }}"
        id="user-form-{{ $knowledge->id }}">
        @csrf
        @method('DELETE')
        <input name="_method" type="hidden" value="DELETE">
        <button type="button"
            class="mx-3 btn btn-sm d-inline-flex align-items-center show_confirm bg-danger"
            data-toggle="tooltip" title="{{ __('Delete') }}">
            <span class="text-white"> <i class="ti ti-trash"></i></span>
        </button>
    </form>
</div>
@endpermission
