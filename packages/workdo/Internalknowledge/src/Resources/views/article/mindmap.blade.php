@php
    $favicon = isset($company_settings['favicon']) ? $company_settings['favicon'] : (isset($admin_settings['favicon']) ? $admin_settings['favicon'] : 'uploads/logo/favicon.png');
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta charset="utf-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link rel="icon" href="{{ check_file($favicon) ? get_file($favicon) : get_file('uploads/logo/favicon.png')  }}{{'?'.time()}}" type="image/x-icon" />
    <style>
        html, body, #diagram {
            height: 100%;
            width: 100%;
            margin: 0;
            user-select: none;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            font-size: 16px;
            color: rgb(73, 80, 87);
            outline: none;
        }
        a {
            color: #0d6efd;
            text-decoration: underline;
        }
        @media only screen and (max-width: 700px) {
            .links {
                display: none;
            }
        }
    </style>
</head>
<body>
    <ap-menu id="menu"></ap-menu>
    <ap-menu-shape id="menu-shape"></ap-menu-shape>
    <input type="hidden" name="articleId" value="{{ $id }}">
    <input type="hidden" name="article_url" value="{{ url('/') }}">
    <svg id="diagram" tabindex="0"
        style="
        touch-action: none;
        background-color: #fff;
        display: block;
        user-select: none;
        -webkit-user-select: none;
        -webkit-touch-callout: none;
        pointer-events: none;
      ">
        <style type="text/css">
            text {
                white-space: pre-wrap;
                font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
                font-size: 16px;
                color: rgb(73, 80, 87);
            }
            textarea {
                text-align: center;
                border: none;
                padding: 10px;
                padding-top: 0.8em;
                font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
                font-size: 16px;
                background-color: transparent;
                color: transparent;
                outline: none;
                overflow: hidden;
                resize: none;
                line-height: 1em;
                caret-color: #fff;
            }
            [data-connect] {
                display: none;
            }
            .select path[data-key="selected"],
            .select .path-end,
            .select [data-connect],
            .highlight-e [data-key="end"] .path-end,
            .highlight-s [data-key="start"] .path-end,
            .hover [data-connect] {
                display: unset;
                opacity: 0.51;
                stroke: rgb(108 187 247);
                fill: rgb(108 187 247);
            }
            [data-connect].hover {
                stroke-width: 25px;
            }
            .select path[data-key="selected"] {
                fill: none;
                infrastructure/move-evt-mobile-fix.js
            }
            .shpath [data-key="end"] .path,
            .shpath [data-key="start"] .path {
                display: none;
            }
            .shpath.arw-e [data-key="end"] .path,
            .shpath.arw-s [data-key="start"] .path {
                display: unset;
            }
            .shpath.dash [data-key="path"] {
                stroke-dasharray: 5;
            }
            @media (pointer: coarse) {
                circle.path-end {
                    r: 20px;
                    width [data-connect] {
                        SAVE_AND_BUILD .shrect.ta-2 text,
                        .shtxt.ta-2 text {
                            text-anchor: middle;
                        }
                        .shrect.ta-3 text,
                        .shtxt.ta-3 text {
                            text-anchor: end;
                        }
                        .shrect.ta-1 textarea,
                        .shtxt.ta-1 textarea {
                            text-align: left;
                        }
                        .shrect.ta-2 textarea,
                        .shtxt.ta-2 textarea {
                            text-align: center;
                        }
                        .shrect.ta-3 textarea,
                        .shtxt.ta-3 textarea {
                            text-align: right;
                        }
                        .shtxt textarea {
                            caret-color: rgb(73, 80, 87);
                        }
                        .shtxt text {
                            fill: rgb(73, 80, 87);
                        }
                        .shtxt [data-key="main"] {
                            fill: transparent;
                            stroke: transparent;
                        }
                        .shtxt.select [data-key="main"],
                        .shtxt.highlight [data-key="main"] {
                            stroke: rgb(108 187 247 / 51%);
                            stroke-width: 2px;
                        }
                        /* rhomb shape */
                        .shrhomb.highlight [data-key="border"] {
                            stroke-width: 28px;
                            stroke: rgb(108 187 247 / 51%);
                        }
                        .shrhomb.highlight [data-key="main"] {
                            stroke-width: 18px;
                            stroke: #1d809f;
                        }
                        /* shape settings styles */
                        .cl-red [data-key="main"] {
                            fill: #e74c3c;
                        }
                        .cl-red .path {
                            stroke: #e74c3c;
                        }
                        .cl-orange [data-key="main"] {
                            fill: #ff6600;
                        }
                        .cl-orange .path {
                            stroke: #ff6600;
                        }
                        .cl-green [data-key="main"] {
                            fill: #19bc9b;
                        }
                        .cl-green .path {
                            stroke: #19bc9b;
                        }
                        .cl-blue [data-key="main"] {
                            fill: #1aaee5;
                        }
                        .cl-blue .path {
                            stroke: #1aaee5;
                        }
                        .cl-dblue [data-key="main"] {
                            fill: #1d809f;
                        }
                        .cl-dblue .path {
                            stroke: #1d809f;
                        }
                        .cl-dgray [data-key="main"] {
                            fill: #495057;
                        }
                        .cl-dgray .path {
                            stroke: #495057;
                        }
                    }
                }
            }
        </style>
        <g id="canvas"></g>
    </svg>
    <script src="{{ asset('packages/workdo/Internalknowledge/src/Resources/assets/index.js') }}" type="module"></script>
</body>
</html>
