{{ Form::model($knowledge_category, ['route' => ['knowledge-category.update', $knowledge_category->id], 'method' => 'PUT', 'class' => 'needs-validation', 'novalidate']) }}
@csrf
@method('PUT')
<div class="modal-body">
    <div class="text-end">
        @if (module_is_active('AIAssistant'))
            @include('aiassistant::ai.generate_ai_btn', [
                'template_module' => 'knowledge_category',
                'module' => 'SupportTicket',
            ])
        @endif
    </div>
    <div class="row">
        <div class="form-group col-md-12">
            {{ Form::label('title', __('Title'), ['class' => 'form-label']) }}<x-required />
                <div class="col-sm-12 col-md-12">
                <input type="text" placeholder="{{ __('Enter Title') }}" name="title"
                    class="form-control" value="{{ $knowledge_category->title  }}" required>
            </div>
        </div>
    </div>

    <div class="col-sm-12 col-md-12 text-end mt-3">
        <input type="button" value="{{ __('Cancel') }}" class="btn  btn-secondary me-1" data-bs-dismiss="modal">
        <input type="submit" value="{{ __('Update') }}" class="btn  btn-primary">
    </div>
</div>
{{ Form::close() }}
