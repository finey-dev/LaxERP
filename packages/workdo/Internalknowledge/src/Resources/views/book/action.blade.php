<div class="action-btn me-2">
    <a href="#" class="btn btn-sm  align-items-center cp_link bg-primary"
        data-link="{{ route('book.shared.link', \Illuminate\Support\Facades\Crypt::encrypt($book->id)) }}"
        data-bs-toggle="tooltip" title="{{ __('Copy Link') }}" data-original-title="{{ __('Click to copy book link') }}">
        <i class="ti ti-file text-white"></i>
    </a>
</div>
@permission('book show')
    <div class="action-btn me-2">
        <a href="{{ route('book.show', $book->id) }}" class="mx-3 btn btn-sm align-items-center text-white bg-warning"
            value="" data-bs-toggle="tooltip" data-title="{{ __('show article') }}"
            title="{{ __('View') }}"><i class="ti ti-eye"></i>
        </a>
    </div>
@endpermission
@permission('book edit')
    <div class="action-btn me-2">
        <a class="mx-3 btn btn-sm align-items-center bg-info" data-url="{{ route('book.edit', $book->id) }}" data-ajax-popup="true"
            data-size="md" data-bs-toggle="tooltip" title="{{ __('Edit') }}" data-title="{{ __('Edit Book') }}">
            <i class="ti ti-pencil text-white"></i>
        </a>
    </div>
@endpermission
@permission('book delete')
    <div class="action-btn ">
        {{ Form::open(['route' => ['book.destroy', $book->id], 'class' => 'm-0']) }}
        @method('DELETE')
        <a class="mx-3 btn btn-sm  align-items-center bs-pass-para show_confirm bg-danger" data-bs-toggle="tooltip" title=""
            data-bs-original-title="{{ __('Delete') }}" aria-label="Delete" data-confirm="{{ __('Are You Sure?') }}"
            data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
            data-confirm-yes="delete-form-{{ $book->id }}"><i class="ti ti-trash text-white text-white"></i></a>
        {{ Form::close() }}
    </div>
@endpermission
