
@extends('layouts.main')

@section('page-title')
   {{ __("Manage Sub Category") }}
@endsection

@section('page-breadcrumb')
   {{ __("Sub Category") }}
@endsection

@section('page-action')
<div>
@permission('Requests subcategory create')
        <a  data-url="{{ route('requests-subcategory.create') }}" data-ajax-popup="true"
            data-title="{{ __('Create Sub Category') }}" data-bs-toggle="tooltip" title="" class="btn btn-sm btn-primary"
            data-bs-original-title="{{ __('Create') }}">
            <i class="ti ti-plus"></i>
        </a>
@endpermission
</div>
@endsection
@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-body table-border-style">
                <div class="table-responsive">
                    <table class="table mb-0 pc-dt-simple" id="assets">
                        <thead>
                            <tr>
                                <th>{{ __('#') }}</th>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Category') }}</th>
                                <th width="200px">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $i = 1;
                            @endphp
                            @foreach ($requestsubcategory as $category)
                                <tr>
                                    <td>{{ $i++ }}</td>
                                    <td>{{ $category->name }}</td>
                                    <td>{{ !empty($category->category) ? $category->category->name :'' }}</td>
                                    <td class="Action">
                                        @permission('Requests subcategory edit')
                                        <div class="action-btn me-2">
                                            <a data-url="{{ route('requests-subcategory.edit', $category->id) }}" class="mx-3 btn btn-sm  align-items-center bg-info"
                                             data-title="{{ __('Edit Sub Category') }}"
                                                data-bs-toggle="tooltip" title="" data-size="md"
                                                data-bs-original-title="{{ __('Edit') }} " data-ajax-popup="true">
                                                <i class="ti ti-pencil text-white"></i>
                                            </a>
                                        </div>
                                        @endpermission
                                        @permission('Requests subcategory delete')
                                        <div class="action-btn">
                                            {{ Form::open(['route' => ['requests-subcategory.destroy', $category->id], 'class' => 'm-0']) }}
                                            @method('DELETE')
                                            <a class="mx-3 btn btn-sm  align-items-center bs-pass-para  bg-danger show_confirm"
                                                data-bs-toggle="tooltip" title="" data-bs-original-title="Delete"
                                                aria-label="Delete" data-confirm="{{ __('Are You Sure?') }}"
                                                data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                data-confirm-yes="delete-form-{{ $category->id }}"><i
                                                    class="ti ti-trash text-white text-white"></i></a>
                                            {{ Form::close() }}
                                        </div>
                                        @endpermission

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
    <script type="text/javascript">
        function copyToClipboard(element) {
            var value = $(element).attr('data-link');
            var $temp = $("<input>");
            $("body").append($temp);
            $temp.val(value).select();
            document.execCommand("copy");
            $temp.remove();
            toastrs('{{ __('Success') }}', '{{ __('Link Copy on Clipboard') }}', 'success');
        }
    </script>
@endpush

