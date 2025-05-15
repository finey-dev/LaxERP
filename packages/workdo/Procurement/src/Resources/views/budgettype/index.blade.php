@extends('layouts.main')
@section('page-title')
    {{ __('Manage Budget Type') }}
@endsection
@section('page-breadcrumb')
{{ __('Budget Type') }}
@endsection
@section('page-action')
<div>
    @permission('budgettype create')
        <a  class="btn btn-sm btn-primary" data-ajax-popup="true" data-size="md" data-title="{{ __('Create Budget Type') }}" data-url="{{route('budgettype.create')}}" data-bs-toggle="tooltip"  data-bs-original-title="{{ __('Create') }}">
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
                            {{ __('Budget Type') }}
                        </h5>
                    </div>
                </div>
            </div>
            <div class="card-body table-border-style">
                <div class="table-responsive">
                    <table class="table mb-0 " >
                        <thead>
                            <tr>
                                <th>{{ __('Budget Type') }}</th>
                                @if (Laratrust::hasPermission('budgettype edit') || Laratrust::hasPermission('budgettype delete'))
                                    <th width="200px">{{ __('Action') }}</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($budgettypes as $budgettype)
                            <tr>
                                <td>{{ $budgettype->name }}</td>
                                @if (Laratrust::hasPermission('budgettype edit') || Laratrust::hasPermission('budgettype delete'))
                                    <td class="Action">
                                        <span>
                                            @permission('budgettype edit')
                                            <div class="action-btn me-2">
                                                <a  class="mx-3 btn bg-info btn-sm  align-items-center"
                                                    data-url="{{ route('budgettype.edit', $budgettype->id) }}"
                                                    data-ajax-popup="true" data-size="md" data-bs-toggle="tooltip" title=""
                                                    data-title="{{ __('Edit Budget Type') }}"
                                                    data-bs-original-title="{{ __('Edit') }}">
                                                    <i class="ti ti-pencil text-white"></i>
                                                </a>
                                            </div>
                                            @endpermission
                                            @permission('budgettype delete')
                                            <div class="action-btn">
                                                {{Form::open(array('route'=>array('budgettype.destroy', $budgettype->id),'class' => 'm-0'))}}
                                                @method('DELETE')
                                                    <a
                                                        class="mx-3 bg-danger btn btn-sm  align-items-center bs-pass-para show_confirm"
                                                        data-bs-toggle="tooltip" title="" data-bs-original-title="Delete"
                                                        aria-label="Delete" data-confirm="{{__('Are You Sure?')}}" data-text="{{__('This action can not be undone. Do you want to continue?')}}"  data-confirm-yes="delete-form-{{$budgettype->id}}"><i
                                                            class="ti ti-trash text-white text-white"></i></a>
                                                {{Form::close()}}
                                            </div>
                                            @endpermission
                                        </span>
                                    </td>
                                @endif
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

