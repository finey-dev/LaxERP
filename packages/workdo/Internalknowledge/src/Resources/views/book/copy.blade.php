@extends('layouts.invoicepayheader')
@section('page-title')
    {{ __('Book: ') . $books->title }}
@endsection
@section('language-bar')
@endsection
@section('action-btn')
@endsection
@push('css')
    <style>
        .mindmape-row {
            height: 100%;
            padding-bottom: 30px;
        }

        .mindmape-row .tab-content .tab-pane {
            border-radius: 10px
        }
    </style>
@endpush
@section('content')
    <div class="row mindmape-row">
        <div class="col-xl-3">
            <div class="card sticky-top" style="top:30px">
                <div class="list-group list-group-flush" id="useradd-sidenav">
                    @foreach ($articles as $key => $article)
                        <a href="#tabs-{{ $key }}"
                            class="list-group-item list-group-item-action border-0 {{ $key == 0 ? 'active' : '' }}"
                            data-bs-toggle="tab">{{ $article->title }}
                            <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="col-xl-9">
            <div class="tab-content">
                @foreach ($articles as $key => $article)
                    <div id="tabs-{{ $key }}" class="tab-pane fade  {{ $key == 0 ? 'show active' : '' }}">
                        @if ($article->type == 'mindmap')
                            <div>
                                <iframe class="w-100 h-100" frameborder="0"
                                    src="{{ url('internalknowledge/article/mindmap', $article->id) }}"></iframe>
                            </div>
                        @else
                            {!! $article->content !!}
                        @endif
                    </div>
                @endforeach
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

        function generateKey() {
            const arr = new Uint8Array((8 / 2));
            window.crypto.getRandomValues(arr);
            const date = new Date();
            return `${date.getUTCFullYear()}${(date.getUTCMonth() + 1).toString().padStart(2, '0')}${Array.from(arr, dec => dec.toString(16).padStart(2, '0')).join('')}`;
        }
        $(document).ready(function() {
            $("iframe").each(function(index) {
                var src = $(this).attr('src');
                var key = generateKey();
                var url = src + '/?k=' + key;
                $(this).attr('src', url);
            });
        });
    </script>
@endpush
