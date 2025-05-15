@extends('layouts.invoicepayheader')
@section('page-title')
    @if ($articles->type == 'document')
        {{ isset($articles->book_name->title) ? __('Article: ') . $articles->book_name->title : __('Article') }}
    @endif
@endsection
@section('language-bar')
@endsection
@section('action-btn')
@endsection
@section('content')
    <div class="row">
        <div class="col-xl-9">
            <div class="tab-content">
                @if ($articles->type == 'document')
                    {!! $articles->content !!}
                @else
                    <button type="button" class="btn btn-success save-and-build d-none" data-id="{{ $articles->id }}">
                        {{ $articles->title }}
                    </button>
                @endif
                <input type="hidden" id="article_url" value="{{ url('/') }}">
            </div>
        </div>
    </div>
@endsection
<script src="{{ asset('js/jquery.min.js') }}"></script>
@push('scripts')
    <script>
        if ($('#useradd-sidenav').length > 0) {
            var scrollSpy = new bootstrap.ScrollSpy(document.body, {
                target: '#useradd-sidenav',
                offset: 300,
            });
            $(".list-group-item").click(function() {
                $('.list-group-item').filter(function() {
                    // return this.href == id;
                }).parent().removeClass('text-primary');
            });
        }
    </script>
@endpush
<script>
    function generateKey() {
        const arr = new Uint8Array(4);
        window.crypto.getRandomValues(arr);
        return Array.from(arr, dec => dec.toString(16).padStart(2, '0')).join('');
    }

    @if (!empty($articles->type == 'mindmap'))

        $(document).ready(function() {


            const articleId = $('.save-and-build').attr('data-id');
            const textBoxValue = $('#article_url').val();

            $.ajax({
                url: '{{ route('mindmap.show') }}',
                method: 'PUT',
                data: JSON.stringify({
                    id: articleId
                }),
                contentType: 'application/json',
                dataType: 'json',
                success: function(response) {
                    const key = generateKey();
                    const getApi =
                        textBoxValue + '/internalknowledge/article/mindmap/' +
                        articleId + '/?k=' + key;
                    window.location.href = getApi;
                },
                error: function(error) {
                    console.error(error);
                }
            });
        });
    @endif
</script>
