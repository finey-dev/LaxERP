{{ Form::open(['route' => ['mail.flowchart'], 'method' => 'post', 'enctype' => 'multipart/form-data']) }}
<div class="modal-body">
    <div class="row">
        <div class="form-group col-md-12">
            {{ Form::label('subject', __('Subject'), ['class' => 'form-label']) }}
            {{ Form::text('subject', '', ['class' => 'form-control subject', 'required' => 'required', 'placeholder' => 'Enter subject']) }}
        </div>
        <div class="form-group col-md-12">
            {{ Form::label('email_to', __('Email To'), ['class' => 'form-label']) }}
            {{ Form::email('email_to', '', ['class' => 'form-control email_to', 'required' => 'required', 'placeholder' => 'Enter Email']) }}
        </div>
        <div class="form-group col-md-12">
            <label for="editor">{{ __('Content') }}</label>
            <textarea class="summernote" id="editor" rows="2" name="content" required>{{ __('Hi, <br><br>
                                        You’ve been invited to view a Flowchart with, you can view the Flowchart by clicking on the link.<br><br>
                                        Many Thanks') }}</textarea>
        </div>
        <input type="hidden" name="businessId" value="{{ $businessId }}">
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{ __('Cancel') }}" class="btn  btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Send') }}" class="btn  btn-primary">
</div>
{{ Form::close() }}
<script>
    if ($(".summernote").length > 0) {
        $('.summernote').summernote({
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'italic', 'underline', 'strikethrough']],
                ['list', ['ul', 'ol', 'paragraph']],
                ['insert', ['link', 'unlink']],
            ],
            height: 200,
        });
    }
</script>
