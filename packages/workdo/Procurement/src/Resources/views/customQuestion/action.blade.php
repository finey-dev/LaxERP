@permission('rfx custom question edit')
<div class="action-btn  me-2">
    <a  class="mx-3 btn bg-info btn-sm  align-items-center"
        data-url="{{ route('rfx-custom-question.edit', $question->id) }}"
        data-ajax-popup="true" data-size="md" data-bs-toggle="tooltip" title=""
        data-title="{{ __('Edit Custom Question') }}"
        data-bs-original-title="{{ __('Edit') }}">
        <i class="ti ti-pencil text-white"></i>
    </a>
</div>
@endpermission

@permission('rfx custom question delete')
<div class="action-btn">
    {!! Form::open(['method' => 'DELETE', 'route' => ['rfx-custom-question.destroy', $question->id], 'id' => 'delete-form-' . $question->id]) !!}
    <a  class="mx-3 btn bg-danger btn-sm  align-items-center bs-pass-para show_confirm"
        data-bs-toggle="tooltip" title="" data-bs-original-title="{{__('Delete')}}"
        aria-label="Delete"><i
            class="ti ti-trash text-white text-white"></i></a>
    </form>
</div>
@endpermission