{{ Form::open(['route' => ['article.store'], 'method' => 'post', 'enctype' => 'multipart/form-data','class'=>'needs-validation','novalidate']) }}
<div class="modal-body">
    <div class="row">
        <div class="form-group col-md-12">
            {{ Form::label('book', __('Book'), ['class' => 'form-label']) }}<x-required></x-required>
            <select class="form-control select book" required="required" id="book" name="book">
                @foreach ($books as $book)
                    <option value="{{ $book->id }}">{{ $book->title }} </option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-md-12">
            {{ Form::label('title', __('Title'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::text('title', '', ['class' => 'form-control title', 'required' => 'required', 'placeholder' => 'Enter Title']) }}
        </div>
        <div class="form-group col-md-12">
            {{ Form::label('description', __('Description'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::textarea('description', '', ['class' => 'form-control description', 'rows' => '3', 'placeholder' => 'Enter description', 'required' => 'required']) }}
        </div>
        <div class="form-group col-md-12">
            {{ Form::label('type', __('Type'), ['class' => 'form-label']) }}
            <select class="form-control select type" required="required" id="type" name="type">
                <option value="document">{{ __('Document') }}</option>
                <option value="mindmap">{{ __('Mindmap') }}</option>
            </select>
        </div>
        <div class="form-group col-md-12" id="editor-container">
            <label for="editor" class="mb-2">{{ __('Content') }}</label><x-required></x-required>
            <textarea class="summernote" id="editor" rows="2" name="content" required="required"></textarea>
        </div>
        <div class="form-group col-md-12 article_list" id="mindmap-container">
            <a href="{{ route('mindmap') }}">
                <button type="button" value="SAVE_AND_BUILD" class="btn btn-success" id="save-and-build" name="content"
                required="required">{{ 'Save & Build Map' }}</button>
            </a>
            <input type="hidden" name="article_url" value="{{ url('/') }}">
        </div>
        @if (module_is_active('CustomField') && !$customFields->isEmpty())
        <div class="form-group col-md-12">
                <div class="tab-pane fade show" id="tab-2" role="tabpanel">
                    @include('custom-field::formBuilder')
                </div>
            </div>
        @endif
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{ __('Cancel') }}" class="btn  btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Create') }}" class="btn  btn-primary">
</div>

{{ Form::close() }}

<script src="{{ asset('assets/js/plugins/summernote-0.8.18-dist/summernote-lite.min.js') }}"></script>
<script>
    summernote();
    function summernote() {
        if ($(".summernote").length > 0) {
            $($(".summernote")).each(function(index, element) {
                var id = $(element).attr('id');
                $('#' + id).summernote({
                    placeholder: "Write Here… ",
                    dialogsInBody: !0,
                    tabsize: 2,
                    minHeight: 200,
                    toolbar: [
                        ['style', ['style']],
                        ["font", ["bold", "italic", "underline", "clear", "strikethrough"]],
                        ['fontname', ['fontname']],
                    ]
                });
            });
        }
    }
</script>
<script>
    $(document).ready(function() {
        changetype();
    });
    document.querySelector('#type').addEventListener('change', function() {
        changetype();

    });

    function changetype() {
        if ($("#type").val() == "document") {
            $('#mindmap-container').addClass('d-none');
            $('#editor-container').removeClass('d-none');

        } else {
            $('#mindmap-container').removeClass('d-none');
            $('#editor-container').addClass('d-none');
        }
    }
</script>

<script>
    $(document).on('click', '#save-and-build', function(e) {
        e.preventDefault(); // Prevent the default behavior (navigating to a new page)

        var book = $('.book').find(":selected").val();
        var title = $('.title').val();
        var description = $('.description').val();
        var type = $('.type').val();

        var data = {
            book: book,
            title: title,
            description: description,
            type: type,
        };

        $.ajax({
            url: '{{ route('mindmap') }}',
            method: 'GET',
            data: data,
            context: this,
            success: function(response) {
                const currentUrl = window.location.href;
                const textBox = document.getElementsByName("article_url")[0];
                const textBoxValue = textBox.value;
                var articleId = response.article_id;
                var newUrl = textBoxValue + '/internalknowledge/article/mindmap/' +
                    articleId;
                window.location.href = newUrl;
            }
        });
    });
</script>
