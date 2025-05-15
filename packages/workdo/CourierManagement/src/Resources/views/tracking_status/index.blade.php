@extends('layouts.main')

@section('page-title')
    {{ __('Manage Tracking Status') }}
@endsection

@section('page-breadcrumb')
    {{ __('Tracking Status') }}
@endsection
@push('scripts')
    <script src="{{ asset('packages/workdo/CourierManagement/src/Resources/assets/js/jscolor.js') }}"></script>
@endpush
@section('page-action')
    <div>
        @permission('tracking create')
            <a class="btn btn-sm btn-primary" data-ajax-popup="true" data-size="md" data-title="{{ __('Create Tracking Status') }}"
                data-url="{{ route('courier.tracking.status.create') }}" data-toggle="tooltip" title="{{ __('Create') }}">
                <i class="ti ti-plus"></i>
            </a>
        @endpermission
    </div>
@endsection


@section('content')
    <div class="row">
        <div class="col-sm-3">
            @include('courier-management::layouts.courier_setup')
        </div>
        <div class="col-sm-9">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-11">
                            <h5 class="">
                                {{ __('Tracking Status') }}
                            </h5>
                            <small class="form-check-label pe-5 text-danger" for="enable_chat">{{__('You can not drag & drop the pending tracking status')}}</small>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover " data-repeater-list="stages">
                        <thead>
                            <th><i class="fas fa-crosshairs"></i></th>
                            <th>{{ __('Icon') }}</th>
                            <th>{{ __('Tracking Status') }}</th>
                            <th width="200px">{{ __('Action') }}</th>
                        </thead>
                        <tbody class="tracking-status">
                            @forelse($trackingStatusData as $trackingStatus)
                                <tr data-id="{{ $trackingStatus->id }}" class="{{ $trackingStatus->status_name == 'Pending' ? 'non-sortable' : '' }}">
                                    <td><i class="fas fa-crosshairs sort-handler"></i></td>
                                    <th>
                                        <span class='action-btn btn-warning btn btn-sm d-inline-flex align-items-center'
                                        style="width: fit-content">
                                            <i class="text-white {{ $trackingStatus->icon_name }}"></i>
                                        </span>
                                    </th>

                                    <td class="sort-handler">
                                        {{ !empty($trackingStatus->status_name) ? $trackingStatus->status_name : '' }}</td>
                                    @if ($trackingStatus->status_name !== 'Pending' && $trackingStatus->status_name !== 'Delivered')
                                        <td class="">
                                            <span>
                                            @permission('servicetype edit')
                                                <div class="action-btn me-2">
                                                    <a class="mx-3 btn btn-sm bg-info align-items-center"
                                                        data-url="{{ route('courier.tracking.status.edit', ['trackingStatusId' => $trackingStatus->id]) }}"
                                                        data-ajax-popup="true" data-size="md" data-bs-toggle="tooltip"
                                                        title="" data-title="{{ __('Edit Tracking Status') }}"
                                                        data-bs-original-title="{{ __('Edit') }}">
                                                        <i class="ti ti-pencil text-white"></i>
                                                    </a>
                                                </div>
                                            @endpermission

                                            @permission('servicetype delete')
                                                <div class="action-btn">
                                                    {{ Form::open(['route' => ['courier.tracking.status.delete', $trackingStatus->id], 'class' => 'm-0']) }}
                                                    @method('DELETE')
                                                    <a class="mx-3 btn btn-sm bg-danger align-items-center bs-pass-para show_confirm"
                                                        data-bs-toggle="tooltip" title="" data-bs-original-title="Delete"
                                                        aria-label="Delete" data-confirm="{{ __('Are You Sure?') }}"
                                                        data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                        data-confirm-yes="delete-form-{{ $trackingStatus->id }}"><i
                                                            class="ti ti-trash text-white text-white"></i></a>
                                                    {{ Form::close() }}
                                                </div>
                                            @endpermission
                                            </span>
                                        </td>
                                    @endif

                                </tr>
                            @endforeach
                        </tbody>

                    </table>
                </div>
                </div>

            </div>
            <div class="alert alert-dark" role="alert">
                {{ __('Note : You can easily order change of job stage using drag & drop.') }}
            </div>
        </div>
    @endsection

    @push('scripts')
        <script src="{{ asset('js/jquery-ui.min.js') }}"></script>
        @if (\Auth::user()->type == 'company')
            <script>
                $(document).ready(function() {
                    var $dragAndDrop = $("body .tracking-status tbody").sortable({
                        handle: '.sort-handler',
                        items: 'tr:not(.non-sortable)'
                    });

                    myFunction();
                });

                function myFunction() {
                    $(".tracking-status").sortable({
                        handle: '.sort-handler',
                        items: 'tr:not(.non-sortable)',
                        stop: function() {
                            var order = [];
                            $(this).find('tr').each(function(index, data) {
                                order[index] = $(data).attr('data-id');
                            });
                            $.ajax({
                                url: "{{ route('tracking.status.order') }}",
                                data: {
                                    order: order,
                                    _token: $('meta[name="csrf-token"]').attr('content')
                                },
                                type: 'POST',
                                success: function(data) {
                                    if (data.success == true) {
                                        toastrs('Success', data.message, 'success');
                                    } else {
                                        toastrs('Error', 'Something Went Wrong !!', 'error');

                                    }
                                },
                            })
                        }
                    });
                }
            </script>
        @endif
    @endpush
