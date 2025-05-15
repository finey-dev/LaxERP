
@extends('layouts.main')

@section('page-title')
   {{ __("Manage RFx Category") }}
@endsection

@section('page-breadcrumb')
   {{ __("RFx Catgory") }}
@endsection

@section('page-action')
<div>
@permission('rfxcategory create')
        <a  data-url="{{ route('rfx-category.create') }}" data-ajax-popup="true"
            data-title="{{ __('Create RFx Category') }}" data-bs-toggle="tooltip" title="" class="btn btn-sm btn-primary"
            data-bs-original-title="{{ __('Create') }}">
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
                            {{ __('RFx Category') }}
                        </h5>
                    </div>
                </div>
            </div>
            <div class="card-body table-border-style">
                <div class="table-responsive">
                    <table class="table mb-0 " >
                        <thead>
                            <tr>
                                <th>{{ __('Category') }}</th>
                                @if (Laratrust::hasPermission('rfxcategory edit') || Laratrust::hasPermission('rfxcategory delete'))
                                 <th width="200px">{{ __('Action') }}</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($categories as $category)
                                <tr>
                                    <td>{{ $category->name }}</td>
                                    <td class="Action">
                                        <span>
                                            @permission('rfxcategory edit')
                                                <div class="action-btn me-2">
                                                    <a  class="mx-3 btn bg-info btn-sm  align-items-center"
                                                        data-url="{{route('rfx-category.edit', $category->id) }}"
                                                        data-ajax-popup="true" data-size="md" data-bs-toggle="tooltip" title=""
                                                        data-title="{{ __('Edit Rfx Category') }}"
                                                        data-bs-original-title="{{ __('Edit') }}">
                                                        <i class="ti ti-pencil text-white"></i>
                                                    </a>
                                                </div>
                                            @endpermission

                                            @permission('rfxcategory delete')
                                                <div class="action-btn">
                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['rfx-category.destroy', $category->id], 'id' => 'delete-form-' . $category->id]) !!}
                                                    <a  class="mx-3 btn bg-danger btn-sm  align-items-center bs-pass-para show_confirm"
                                                        data-bs-toggle="tooltip" title="" data-bs-original-title="Delete"
                                                        aria-label="Delete"><i
                                                            class="ti ti-trash text-white text-white"></i></a>
                                                    </form>
                                                </div>
                                            @endpermission
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                @include('layouts.nodatafound')
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

