<div class="action-btn me-2">
    <a href="#"
        class="mx-3 btn btn-sm d-inline-flex align-items-center text-white cp_link bg-primary"
        data-link="{{ route('article.shared.link', \Illuminate\Support\Facades\Crypt::encrypt($article->id)) }}"
        data-bs-toggle="tooltip" title="{{ __('Copy') }}"
        data-original-title="{{ __('Copy') }}">
        <span class="btn-inner--icon text-white"><i
                class="ti ti-file"></i></span>
    </a>
</div>
@permission('article duplicate')
    <div class="action-btn me-2">
        <a data-size="md"
            data-url="{{ route('article.copy', [$article->id]) }}"
            data-ajax-popup="true" data-title="{{ __('Duplicate') }}"
            class="mx-3 btn btn-sm align-items-center bg-secondary"
            data-bs-toggle="tooltip" data-bs-placement="top"
            title="{{ __('Duplicate') }}"><i
                class="ti ti-copy text-white"></i></a>
    </div>
@endpermission
@permission('article edit')
    <div class="action-btn me-2">
        <a class="mx-3 btn btn-sm align-items-center bg-info"
            data-url="{{ route('article.edit', $article->id) }}"
            data-ajax-popup="true" data-size="lg" data-bs-toggle="tooltip"
            title="{{ __('Edit') }}"
            data-title="{{ __('Edit Article') }}">
            <i class="ti ti-pencil text-white"></i>
        </a>
    </div>
@endpermission
@permission('article delete')
    <div class="action-btn">
        {{ Form::open(['route' => ['article.destroy', $article->id], 'class' => 'm-0']) }}
        @method('DELETE')
        <a class="mx-3 btn btn-sm  align-items-center bs-pass-para show_confirm bg-danger"
            data-bs-toggle="tooltip" title=""
            data-bs-original-title="{{ __('Delete') }}" aria-label="Delete"
            data-confirm="{{ __('Are You Sure?') }}"
            data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
            data-confirm-yes="delete-form-{{ $article->id }}">
            <i class="ti ti-trash text-white text-white"></i>
        </a>
        {{ Form::close() }}
    </div>
@endpermission
