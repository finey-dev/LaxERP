@extends('layouts.main')
@section('page-title')
    {{ __('Manage Salary Type') }}
@endsection
@section('page-breadcrumb')
{{ __('Salary Type') }}
@endsection
@section('page-action')
<div>
    @permission('paysliptype create')
        <a  class="btn btn-sm btn-primary" data-ajax-popup="true" data-size="md" data-title="{{ __('Create Salary Type') }}" data-url="{{route('paysliptype.create')}}" data-bs-toggle="tooltip"  data-bs-original-title="{{ __('Create') }}">
            <i class="ti ti-plus"></i>
        </a>
    @endpermission
</div>
@endsection
@section('content')
<div class="row">
    <div class="col-sm-3">
        @include('recruitment::layouts.recruitment_setup')
    </div>
    <div class="col-sm-9">
        <div class="card">
            <div class="card-body table-border-style">
                <div class="table-responsive">
                    <table class="table mb-0 " >
                        <thead>
                            <tr>
                                <th>{{ __('Salary Type') }}</th>
                                @if (Laratrust::hasPermission('paysliptype edit') || Laratrust::hasPermission('paysliptype delete'))
                                    <th width="200px">{{ __('Action') }}</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($paysliptypes as $paysliptype)
                            <tr>
                                <td>{{ $paysliptype->name }}</td>
                                @if (Laratrust::hasPermission('paysliptype edit') || Laratrust::hasPermission('paysliptype delete'))
                                    <td class="Action">
                                        <span>
                                            @permission('paysliptype edit')
                                            <div class="action-btn me-2">
                                                <a  class="mx-3 btn btn-sm  align-items-center  bg-info "
                                                    data-url="{{ route('paysliptype.edit', $paysliptype->id) }}"
                                                    data-ajax-popup="true" data-size="md" data-bs-toggle="tooltip" title=""
                                                    data-title="{{ __('Edit Salary Type') }}"
                                                    data-bs-original-title="{{ __('Edit') }}">
                                                    <i class="ti ti-pencil text-white"></i>
                                                </a>
                                            </div>
                                            @endpermission
                                            @permission('paysliptype delete')
                                            <div class="action-btn">
                                                {{Form::open(array('route'=>array('paysliptype.destroy', $paysliptype->id),'class' => 'm-0'))}}
                                                @method('DELETE')
                                                    <a
                                                        class="mx-3 btn btn-sm  align-items-center bs-pass-para show_confirm  bg-danger"
                                                        data-bs-toggle="tooltip" title="{{ __('Delete') }}" data-bs-original-title="Delete"
                                                        aria-label="Delete" data-confirm="{{__('Are You Sure?')}}" data-text="{{__('This action can not be undone. Do you want to continue?')}}"  data-confirm-yes="delete-form-{{$paysliptype->id}}"><i
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

