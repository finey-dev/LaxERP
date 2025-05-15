@extends('layouts.main')
@section('page-title')
    {{ __('Manage Business Process Mapping') }}
@endsection
@section('page-breadcrumb')
    {{ __('Business Process Mapping') }}
@endsection
@section('page-action')
    <div>
        @can('businessprocessmapping create')
            <a class="btn btn-sm btn-primary" data-ajax-popup="true" data-size="md"
                data-title="{{ __('Create Business Process Mapping') }}"
                data-url="{{ route('business-process-mapping.create') }}" data-bs-toggle="tooltip"
                data-bs-original-title="{{ __('Create') }}">
                <i class="ti ti-plus"></i>
            </a>
        @endcan
    </div>
@endsection
@push('css')
    <link rel="stylesheet"
        href="{{ asset('packages/workdo/BusinessProcessMapping/src/Resources/assets/summernote/summernote-bs4.css') }}">
@endpush
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table mb-0 pc-dt-simple" id="assets">
                            <thead>
                                <tr>
                                    <th> {{ __('Title') }}</th>
                                    <th> {{ __('Description') }}</th>
                                    <th> {{ __('Related To') }}</th>
                                    <th width="10%"> {{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($businessProcesses as $business)
                                    <tr class="font-style">
                                        <td>{{ $business->title }}</td>
                                        <td>{{ substr($business->description, 0, 50) }}
                                            @if (strlen($business->description) > 50)
                                                ...
                                            @endif
                                        </td>
                                        @if (!empty($business->relatedTo))
                                            <td>
                                                @foreach (explode(',', $business->relatedTo) as $relatedItem)
                                                    <span
                                                        class="badge rounded p-2 m-1 px-3 bg-primary">{{ $relatedItem }}</span>
                                                @endforeach
                                            </td>
                                        @endif
                                        <td class="Action">
                                            <span>
                                                <div class="action-btn bg-success ms-2">
                                                    <a class="mx-3 btn btn-sm align-items-center"
                                                        data-url="{{ route('send.business.mail', $business->id) }}"
                                                        data-ajax-popup="true" data-size="md" data-bs-toggle="tooltip"
                                                        title="{{ __('Add Email') }}" data-title="{{ __('Add Email') }}">
                                                        <i class="ti ti-mail text-white"></i>
                                                    </a>
                                                </div>
                                                <div class="action-btn bg-secondary ms-2">
                                                    <a class="mx-3 btn btn-sm align-items-center"
                                                        href="{{ route('business.preview', $business->id) }}"
                                                        target="_blank">
                                                        <i class="ti ti-crosshair text-white" data-bs-toggle="tooltip"
                                                            data-bs-original-title="{{ __('Preview') }}"></i>
                                                    </a>
                                                </div>
                                                <div class="action-btn bg-warning ms-2">
                                                    <a href="{{ route('store.flowchart', $business->id) }}"
                                                        class="mx-3 btn btn-sm align-items-center"><i
                                                            class="ti ti-pencil text-white" data-bs-toggle="tooltip"
                                                            title="{{ __('Edit Flowchart') }}"></i>
                                                    </a>
                                                </div>
                                                <div class="action-btn bg-primary ms-2">
                                                    <a href="#"
                                                        class="mx-3 btn btn-sm d-inline-flex align-items-center text-white cp_link"
                                                        data-link="{{ route('business.shared.link', \Illuminate\Support\Facades\Crypt::encrypt($business->id)) }}"
                                                        data-bs-toggle="tooltip" title="{{ __('Copy') }}"
                                                        data-original-title="{{ __('Copy') }}">
                                                        <span class="btn-inner--icon text-white"><i
                                                                class="ti ti-file"></i></span>
                                                    </a>
                                                </div>
                                                @can('businessprocessmapping edit')
                                                    <div class="action-btn bg-info ms-2">
                                                        <a class="mx-3 btn btn-sm align-items-center"
                                                            data-url="{{ route('business-process-mapping.edit', $business->id) }}"
                                                            data-ajax-popup="true" data-size="md" data-bs-toggle="tooltip"
                                                            title="{{ __('Edit') }}"
                                                            data-title="{{ __('Edit Business Process Mapping') }}">
                                                            <i class="ti ti-pencil text-white"></i>
                                                        </a>
                                                    </div>
                                                @endcan
                                                @can('businessprocessmapping delete')
                                                    <div class="action-btn bg-danger ms-2">
                                                        {{ Form::open(['route' => ['business-process-mapping.destroy', $business->id], 'class' => 'm-0']) }}
                                                        @method('DELETE')
                                                        <a class="mx-3 btn btn-sm  align-items-center bs-pass-para show_confirm"
                                                            data-bs-toggle="tooltip" title=""
                                                            data-bs-original-title="Delete" aria-label="Delete"
                                                            data-confirm="{{ __('Are You Sure?') }}"
                                                            data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                            data-confirm-yes="delete-form-">
                                                            <i class="ti ti-trash text-white text-white"></i>
                                                        </a>
                                                        {{ Form::close() }}
                                                    </div>
                                                @endcan
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            $('.cp_link').on('click', function() {
                var value = $(this).attr('data-link');
                var $temp = $("<input>");
                $("body").append($temp);
                $temp.val(value).select();
                document.execCommand("copy");
                $temp.remove();
                toastrs('success', '{{ __('Link Copy on Clipboard') }}', 'success')
            });
        });
    </script>
@endpush
