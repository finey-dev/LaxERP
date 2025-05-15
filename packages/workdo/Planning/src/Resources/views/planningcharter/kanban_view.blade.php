@extends('layouts.main')

@section('page-title')
    {{ __('Manage Charters') }}
@endsection
@section('page-breadcrumb')
    {{ __('Charters') }}
@endsection

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/dragula.min.css') }}">
    <style>
        .card .card-header .card-header-right {
            position: unset;
        }
    </style>
@endpush

@push('scripts')
    <script src="{{ asset('assets/js/plugins/dragula.min.js') }}"></script>

    <script>
        ! function(a) {
            "use strict";

            var t = function() {
                this.$body = a("body")
            };
            t.prototype.init = function() {
                a('[data-plugin="dragula"]').each(function() {

                    var t = a(this).data("containers"),

                        n = [];
                    if (t)
                        for (var i = 0; i < t.length; i++) n.push(a("#" + t[i])[0]);
                    else n = [a(this)[0]];
                    var r = a(this).data("handleclass");
                    r ? dragula(n, {
                        moves: function(a, t, n) {
                            return n.classList.contains(r)
                        }
                    }) : dragula(n).on('drop', function(el, target, source, sibling) {
                        var order = [];
                        $("#" + target.id + " > div").each(function() {
                            order[$(this).index()] = $(this).attr('data-id');
                        });

                        var id = $(el).attr('data-id');

                        var old_status = $("#" + source.id).data('status');
                        var new_status = $("#" + target.id).data('status');
                        var stage_id = $(target).attr('data-id');


                        $("#" + source.id).parent().find('.count').text($("#" + source.id +
                            " > div").length);
                        $("#" + target.id).parent().find('.count').text($("#" + target.id +
                            " > div").length);
                        $.ajax({
                            url: '{{ route('charters.order') }}',
                            type: 'POST',
                            data: {
                                creativity_id: id,
                                stage_id: stage_id,
                                order: order,
                                new_status: new_status,
                                old_status: old_status,
                                "_token": $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(data) {
                                if (data.message) {
                                    toastrs('Success', data.message, 'success');
                                } else if (data.error) {
                                    toastrs('Error', data.error, 'error');
                                }
                            }
                        });
                    });
                })
            }, a.Dragula = new t, a.Dragula.Constructor = t
        }(window.jQuery),
        function(a) {
            "use strict";

            a.Dragula.init()

        }(window.jQuery);
    </script>
@endpush

@section('page-action')
    <div>
        <a href="{{ route('planningcharters.index') }}" class="btn btn-sm btn-primary btn-icon me-1" data-bs-toggle="tooltip"
            title="{{ __('List View') }}">
            <i class="ti ti-list text-white"></i>
        </a>

        <a href="{{ route('charters.grid') }}" class="btn btn-sm btn-primary btn-icon me-1"
            data-bs-toggle="tooltip"title="{{ __('Grid View') }}">
            <i class="ti ti-layout-grid text-white"></i>
        </a>

        <a href="{{ route('charters.treeview') }}" data-bs-toggle="tooltip" data-bs-placement="top"
            title="{{ __('Tree View') }}" class="btn btn-sm btn-primary btn-icon me-1"><i class="ti ti-sitemap"></i> </a>


        @permission('charters create')
            <a href="{{ route('charters.create', [0]) }}" data-title="{{ __('Create Charter') }}" data-bs-toggle="tooltip"
                title="" class="btn btn-sm btn-primary" data-bs-original-title="{{ __('Create') }}">
                <i class="ti ti-plus"></i>
            </a>
        @endpermission

    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            @php
                $json = [];
                foreach ($CharterStage as $charterstage) {
                    $json[] = 'task-list-' . $charterstage->id;
                }
            @endphp
            <div class="row kanban-wrapper horizontal-scroll-cards pt-3" data-plugin="dragula"
                data-containers='{!! json_encode($json) !!}'>
                @foreach ($CharterStage as $key => $charterstage)
                    @php

                        $creativityCount = $charterstage->charter()->count();
                        $charters = $charterstage->charter();

                    @endphp
                    <div class="col" id="progress">
                        <div class="card card-list">
                            <div class="card-header">
                                <div class="float-end">
                                    <button class="btn-submit btn btn-md btn-primary btn-icon px-1 py-0">
                                        <span class="badge badge-secondary rounded-pill count">
                                            {{ $creativityCount }}
                                        </span>
                                    </button>
                                </div>
                                <h4 class="mb-0">{{ $charterstage->name }}</h4>
                            </div>

                            <div class="card-body kanban-box" id="{{ $json[$key] }}" data-id="{{ $charterstage->id }}">
                                @foreach ($charters as $charter)
                                    <div class="card" data-id="{{ $charter->id }}">
                                        <div
                                            class="card-header border-0 pb-0 position-relative  d-flex justify-content-between gap-2">
                                            <h5><a
                                                    href="{{ route('planningcharters.show', $charter->id) }}">{{ $charter->charter_name }}</a>
                                            </h5>
                                            <div class="card-header-right">
                                                <button type="button" class="btn dropdown-toggle" data-bs-toggle="dropdown"
                                                    aria-haspopup="true" aria-expanded="false">
                                                    <i class="feather icon-more-vertical"></i>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    <a href="#"
                                                        data-url="{{ route('charters.receipt', $charter->id) }}"
                                                        data-size="lg" data-bs-whatever="{{ __('Print') }}"
                                                        data-ajax-popup="true" class="dropdown-item"
                                                        data-bs-toggle="tooltip"><i class="ti ti-printer"></i>
                                                        {{ __('Print') }}</a>

                                                    @permission('charters show')
                                                        <a href="{{ route('planningcharters.show', $charter->id) }}"
                                                            class="dropdown-item">
                                                            <i class="ti ti-eye"></i><span
                                                                class="ms-1">{{ __('View') }}</span>
                                                        </a>
                                                    @endpermission

                                                    @permission('charters edit')
                                                        <a href="{{ route('planningcharters.edit', $charter->id) }}"
                                                            data-size="md" class="dropdown-item"
                                                            data-bs-whatever="{{ __('Edit') }}" data-bs-toggle="tooltip"><i
                                                                class="ti ti-pencil"></i>
                                                            {{ __('Edit') }}</a>
                                                    @endpermission


                                                    @permission('charters delete')
                                                        {!! Form::open([
                                                            'method' => 'DELETE',
                                                            'route' => ['planningcharters.destroy', $charter->id],
                                                            'id' => 'delete-form-' . $charter->id,
                                                        ]) !!}
                                                        <a class="bs-pass-para text-danger dropdown-item show_confirm"
                                                            data-bs-toggle="tooltip" data-bs-placement="top">
                                                            <i class="ti ti-trash"></i><span
                                                                class="ms-1">{{ __('Delete') }}</span>
                                                        </a>
                                                        {!! Form::close() !!}
                                                    @endpermission
                                                </div>
                                            </div>
                                        </div>

                                        <div class="card-body pt-0">
                                            <div class="row my-3">
                                                <div class="align-items-center">
                                                    <a href="{{ isset($charter->thumbnail_image) && !empty($charter->thumbnail_image) && check_file($charter->thumbnail_image) ? get_file($charter->thumbnail_image) : get_file('packages/workdo/Planning/src/Resources/assets/img/thumbnail-not-found.png') }}" target="_blank">
                                                        <img src="{{ isset($charter->thumbnail_image) && !empty($charter->thumbnail_image) && check_file($charter->thumbnail_image) ? get_file($charter->thumbnail_image) : get_file('packages/workdo/Planning/src/Resources/assets/img/thumbnail-not-found.png') }}"
                                                            alt="Thumbnail" id="thumbnail"
                                                            class="card-img rounded border-2 border border-primary"
                                                            style="height: 200px">
                                                    </a>
                                                </div>
                                            </div>


                                            <div class="card-content">
                                                <a class="task-title">
                                                    {{ !empty($charter->dsescription) ? strip_tags($charter->dsescription) : '' }}
                                                </a>
                                            </div>

                                            <div
                                                class="d-flex align-items-center justify-content-between pt-2 mt-2 border-secondary-subtle border-top">
                                                <div class="user-group">
                                                    @php
                                                        $user_id = explode(',', $charter->user_id);
                                                        $users = App\Models\User::whereIn('id', $user_id)->get();
                                                    @endphp
                                                    @foreach ($users as $user)
                                                        <img alt="image" data-bs-toggle="tooltip"
                                                            data-bs-original-title="{{ $user->name }}"
                                                            data-bs-placement="top" aria-label="{{ $user->name }}"
                                                            title="{{ $user->name }}"
                                                            @if ($user->avatar) src="{{ get_file($user->avatar) }}" @else src="{{ get_file('uploads/users-avatar/avatar.png') }}" @endif
                                                            class="rounded-circle " width="25" height="25">
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
