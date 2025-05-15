@permission('goal edit')
    <div class="action-btn me-2">
        <a class="mx-3 btn bg-info btn-sm align-items-center" data-url="{{ route('goal.edit', $goal->id) }}" data-ajax-popup="true"
            data-size="md" data-title="{{ __('Edit Goal') }}" data-bs-toggle="tooltip" title="{{ __('Edit') }}"
            data-original-title="{{ __('Edit') }}">
            <i class="ti ti-pencil text-white"></i>
        </a>
    </div>
@endpermission

@permission('goal delete')
    <div class="action-btn">
        {!! Form::open([
            'method' => 'DELETE',
            'route' => ['goal.destroy', $goal->id],
            'id' => 'delete-form-' . $goal->id,
        ]) !!}
        <a class="mx-3 btn btn-sm bg-danger align-items-center bs-pass-para show_confirm" data-bs-toggle="tooltip" title=""
            data-bs-original-title="Delete" aria-label="Delete"><i class="ti ti-trash text-white text-white"></i></a>
        </form>
    </div>
@endpermission
