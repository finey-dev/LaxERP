@extends('layouts.main')
@section('page-title')
    {{ __('Manage Opportunities') }}
@endsection
@section('title')
    {{ __('Opportunities') }}
@endsection
@section('page-breadcrumb')
    {{ __('Opportunities') }}
@endsection
@section('page-action')
    <div class="d-flex">
        @stack('addButtonHook')
        <a href="{{ route('opportunities.index') }}" class="btn btn-sm btn-primary btn-icon me-2" data-bs-toggle="tooltip"
            title="{{ __('List View') }}">
            <i class="ti ti-list text-white"></i>
        </a>

        @permission('opportunities create')
            <a data-size="lg" data-url="{{ route('opportunities.create', ['opportunities', 0]) }}" data-ajax-popup="true"
                data-bs-toggle="tooltip" data-title="{{ __('Create Opportunities') }}" title="{{ __('Create') }}"
                class="btn btn-sm btn-primary btn-icon">
                <i class="ti ti-plus"></i>
            </a>
        @endpermission
    </div>
@endsection
@push('css')
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/dragula.min.css') }}">
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
                        var pipeline_id = '1';

                        $("#" + source.id).parent().find('.count').text($("#" + source.id + " > div")
                            .length);
                        $("#" + target.id).parent().find('.count').text($("#" + target.id + " > div")
                            .length);

                        $.ajax({
                            url: '{{ route('opportunities.change.order') }}',
                            type: 'POST',
                            data: {
                                opo_id: id,
                                stage_id: stage_id,
                                order: order,
                                "_token": $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(data) {
                                if (data) {
                                    toastrs('success',"{{__('The opportunities details are updated successfully.')}}",'success');
                                } else {
                                    toastrs('error',"{{__('Something went wrong.')}}",'error');
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
@section('filter')
@endsection
@section('content')
    <div class="row">
        <div class="col-sm-12">
            @php
                $json = [];
                foreach ($stages as $stage) {
                    $json[] = 'kanban-blacklist-' . $stage->id;
                }
            @endphp
            <div class="row kanban-wrapper horizontal-scroll-cards kanban-board pt-3"
                data-containers='{!! json_encode($json) !!}' data-plugin="dragula">
                @foreach ($stages as $stage)
                    @php
                        $opportunities = $stage->opportunity($stage->id);
                    @endphp
                    <div class="col">
                        <div class="card card-list">
                            <div class="card-header">
                                <div class="float-end">
                                    <button class="btn btn-sm btn-primary btn-icon task-header">
                                        <span class="count text-white">{{ count($opportunities) }}</span>
                                    </button>
                                </div>
                                <h4 class="mb-0">{{ $stage->name }}</h4>
                            </div>
                            <div class="card-body kanban-box" id="kanban-blacklist-{{ $stage->id }}"
                                data-id="{{ $stage->id }}">
                                @foreach ($opportunities as $opportunity)
                                    <div class="card" data-id="{{ $opportunity->id }}">
                                        <div class="card-header border-0 pb-0 position-relative">
                                            <h5><a
                                                    href="{{ route('opportunities.edit', $opportunity->id) }}">{{ ucfirst($opportunity->name) }}</a>
                                            </h5>
                                            <div class="card-header-right">
                                                <div class="btn-group card-option">
                                                    @if (Laratrust::hasPermission('opportunities show') ||
                                                            Laratrust::hasPermission('opportunities edit') ||
                                                            Laratrust::hasPermission('opportunities delete'))
                                                        <button type="button" class="btn dropdown-toggle"
                                                            data-bs-toggle="dropdown" aria-haspopup="true"
                                                            aria-expanded="false">
                                                            <i class="ti ti-dots-vertical"></i>
                                                        </button>
                                                        <div class="dropdown-menu dropdown-menu-end">
                                                            @if (module_is_active('SalesForce'))
                                                                @include('sales-force::layouts.addHook', [
                                                                    'object' => $opportunity,
                                                                    'type' => 'Opportunity',
                                                                    'resources_type' => 'grid',
                                                                ])
                                                            @endif
                                                            @permission('opportunities show')   
                                                                <a href="#!" class="dropdown-item" data-size="lg"
                                                                    data-url="{{ route('opportunities.show', $opportunity->id) }}"
                                                                    data-ajax-popup="true"
                                                                    data-title="{{ __('Opportunities Details') }}">
                                                                    <i class="ti ti-eye me-1"></i>
                                                                    <span>{{ __('View') }}</span>
                                                                </a>
                                                            @endpermission

                                                            @permission('opportunities edit')
                                                                <a href="{{ route('opportunities.edit', $opportunity->id) }}"
                                                                    class="dropdown-item"
                                                                    data-title="{{ __('Opportunities Edit') }}">
                                                                    <i class="ti ti-pencil me-1"></i>
                                                                    <span>{{ __('Edit') }}</span>
                                                                </a>
                                                            @endpermission

                                                            @permission('opportunities delete')
                                                                {!! Form::open([
                                                                    'method' => 'DELETE',
                                                                    'route' => ['opportunities.destroy', $opportunity->id],
                                                                    'id' => 'task-delete-form-' . $opportunity->id,
                                                                ]) !!}
                                                                <a href="#!" class="dropdown-item show_confirm"
                                                                    class="dropdown-item"
                                                                    data-confirm="{{ __('Are You Sure?') }}"
                                                                    data-text="{{ __('This action can not be undone. Do you want to continue?') }}">
                                                                    <span class="text-danger"><i class="ti ti-trash me-1"></i> {{ __('Delete') }}</span>
                                                                </a>
                                                                {!! Form::close() !!}
                                                            @endpermission
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <h6> {{ ucfirst(!empty($opportunity->accounts) ? $opportunity->accounts->name : '-') }}
                                            </h6>
                                            <p class="text-muted text-sm">{{ $opportunity->description }}</p>
                                            <div class="d-flex align-items-center justify-content-between">
                                                <ul class="list-inline mb-0">
                                                    <li class="list-inline-item d-inline-flex align-items-center"><i
                                                            class="f-16 text-primary ti ti-message-2"></i>{{ currency_format_with_sym($opportunity->amount) }}
                                                    </li>
                                                </ul>
                                                <ul class="list-inline mb-0">
                                                    <li class="list-inline-item d-inline-flex align-items-center"><i
                                                            class="f-16 text-primary ti ti-message-2"></i>{{ company_date_formate($opportunity->close_date) }}
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- [ sample-page ] end -->
        </div>
    </div>
@endsection
