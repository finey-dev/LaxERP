@extends('layouts.main')

@section('page-title')
    {{ $formBuilder->name.__("'s Form Field") }}
@endsection

@section('page-action')
<div class="d-flex">
    <a href="{{ route('form_builder.index') }}" class="btn btn-sm btn-primary btn-icon me-2" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Back')}}" ><i class="ti ti-arrow-back-up text-white"></i></a>
    
        @permission('formbuilder form field create')
        <a class="btn btn-sm btn-primary btn-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Create')}}" data-ajax-popup="true" data-size="md" data-title="{{__('Create Field')}}" data-url="{{ route('form.field.create',$formBuilder->id) }}"><i class="ti ti-plus text-white"></i></a>
        @endpermission
    </div>
@endsection

@section('page-breadcrumb')
{{__('Form Field')}}
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table mb-0 pc-dt-simple" id="assets">
                            <thead>
                                <tr>
                                    <th>{{__('Name')}}</th>
                                    <th>{{__('Type')}}</th>
                                    <th width="250px">{{__('Action')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($formBuilder->form_field->count())
                                    @foreach ($formBuilder->form_field as $field)
                                        <tr>
                                            <td>{{ $field->name }}</td>
                                            <td>{{ ucfirst($field->type) }}</td>
                                            <td class="Action">
                                                <span>
                                                    @permission('formbuilder form field edit')
                                                    <div class="action-btn me-2">
                                                        <a data-size="md" data-url="{{ route('form.field.edit',[$formBuilder->id,$field->id]) }}" data-ajax-popup="true" data-title="{{__('Edit Field')}}" class="mx-3 btn btn-sm align-items-center bg-info" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Edit')}}" ><i class="ti ti-pencil text-white"></i></a>
                                                    </div>
                                                    @endpermission

                                                    @permission('formbuilder form field delete')
                                                    <div class="action-btn">
                                                        {!! Form::open(['method' => 'DELETE', 'route' => ['form.field.destroy', [$formBuilder->id,$field->id]]]) !!}
                                                            <a href="#!" class="mx-3 btn btn-sm align-items-center show_confirm bg-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Delete')}}" data-confirm="{{ __('Are You Sure?') }}" data-text="{{ __('This action can not be undone. Do you want to continue?') }}">
                                                               <span class="text-white"> <i class="ti ti-trash"></i></span></a>
                                                        {!! Form::close() !!}
                                                    </div>
                                                    @endpermission
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="3" class="text-center">{{__('No data available in table')}}</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
