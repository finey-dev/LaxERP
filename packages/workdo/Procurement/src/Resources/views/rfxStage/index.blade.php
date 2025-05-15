@extends('layouts.main')

@section('page-title')
    {{ __('Manage RFx Stage') }}
@endsection

@section('page-breadcrumb')
    {{ __('RFx Stage') }}
@endsection

@section('page-action')
    <div>
        @permission('rfxstage create')
            <a data-url="{{ route('rfx-stage.create') }}" data-ajax-popup="true" data-title="{{ __('Create RFx Stage') }}"
                data-bs-toggle="tooltip" title="" class="btn btn-sm btn-primary" data-bs-original-title="{{ __('Create') }}">
                <i class="ti ti-plus"></i>
            </a>
        @endpermission
    </div>
@endsection


@section('content')
    <div class="row">
        <div class="col-sm-3">
            @include('procurement::layouts.procurement_setup')
        </div>
        <div class="col-sm-9">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-11">
                            <h5 class="">
                                {{ __('RFx Stages') }}
                            </h5>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-hover " data-repeater-list="stages">
                        <thead>
                            <th><i class="fas fa-crosshairs"></i></th>
                            <th>{{ __('Name') }}</th>
                            <th width="200px">{{ __('Action') }}</th>
                        </thead>
                        <tbody class="task-stages">
                            @foreach ($stages as $stage)
                                <tr data-id="{{ $stage->id }}">
                                    <td><i class="fas fa-crosshairs sort-handler "></i></td>
                                    <td>{{ $stage->title }}</td>
                                    <td class="Action">
                                        @permission('rfxstage edit')
                                            <div class="action-btn  me-2">
                                                <a class=" btn bg-info btn-sm  align-items-center"
                                                    data-url="{{ route('rfx-stage.edit', $stage->id) }}" data-ajax-popup="true"
                                                    data-size="md" data-bs-toggle="tooltip" title=""
                                                    data-title="{{ __('Edit RFx Stage') }}"
                                                    data-bs-original-title="{{ __('Edit') }}"><i
                                                        class="ti ti-pencil  text-white"></i></a>
                                            </div>
                                        @endpermission
                                        @permission('rfxstage delete')
                                            <div class="action-btn">
                                                {!! Form::open([
                                                    'method' => 'DELETE',
                                                    'route' => ['rfx-stage.destroy', $stage->id],
                                                    'id' => 'delete-form-' . $stage->id,
                                                ]) !!}
                                                <a class=" btn bg-danger btn-sm  align-items-center bs-pass-para show_confirm"
                                                    data-bs-toggle="tooltip" title="" data-bs-original-title="Delete"
                                                    aria-label="Delete"><i class="ti ti-trash text-white "></i></a>
                                                </form>
                                            </div>
                                        @endpermission
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
            <div class="alert alert-dark" role="alert">
                {{ __('Note : You can easily order change of rfx stage using drag & drop.') }}
            </div>
        </div>
    </div>
 @endsection

    @push('scripts')
        <script src="{{ asset('packages/workdo/Procurement/src/Resources/assets/js/jquery-ui.min.js') }}"></script>
        @if (\Auth::user()->type == 'company')
            <script>
                $(document).ready(function() {
                    var $dragAndDrop = $("body .task-stages tbody").sortable({
                        handle: '.sort-handler'
                    });

                    myFunction();
                });

                function myFunction() {
                    $(".task-stages").sortable({
                        stop: function() {
                            var order = [];
                            $(this).find('tr').each(function(index, data) {
                                order[index] = $(data).attr('data-id');
                            });
                            $.ajax({
                                url: "{{ route('rfx.stage.order') }}",
                                data: {
                                    order: order,
                                    _token: $('meta[name="csrf-token"]').attr('content')
                                },
                                type: 'POST',
                                success: function(data) {},
                                error: function(data) {
                                    data = data.responseJSON;
                                }
                            })
                        }
                    });
                }
            </script>
        @endif
    @endpush
