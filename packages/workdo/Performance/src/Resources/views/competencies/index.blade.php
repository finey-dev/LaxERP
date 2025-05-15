@extends('layouts.main')

@section('page-title')
    {{ __('Manage Competencies') }}
@endsection

@section('page-breadcrumb')
    {{ __('Competencies') }}
@endsection

@section('page-action')
    <div>
        @permission('competencies create')
            <a data-url="{{ route('competencies.create') }}" data-ajax-popup="true"
                data-title="{{ __('Create Competencies') }}" data-bs-toggle="tooltip" title=""
                class="btn btn-sm btn-primary" data-bs-original-title="{{ __('Create') }}">
                <i class="ti ti-plus"></i>
            </a>
        @endpermission
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-3">
            @include('hrm::layouts.hrm_setup')
        </div>
        <div class="col-sm-9">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table mb-0 ">
                            <thead>
                                <tr>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Type') }}</th>
                                    <th width="200px">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($competencies as $competency)
                                    <tr>
                                        <td>{{ $competency->name }}</td>
                                        <td>{{ !empty($competency->getPerformance_type->name) ? $competency->getPerformance_type->name : '-' }}
                                        <td class="Action">
                                            <span>
                                                @permission('competencies edit')
                                                    <div class="action-btn me-2">
                                                        <a href="#"
                                                            class="btn btn-sm d-inline-flex align-items-center bg-info"
                                                            data-size="md"
                                                            data-url="{{ route('competencies.edit', $competency->id) }}"
                                                            class="dropdown-item" data-ajax-popup="true"
                                                            data-title="{{ __('Edit Competencies') }}" data-bs-toggle="tooltip"
                                                            data-bs-original-title="{{ __('Edit') }}">
                                                            <span class="text-white"> <i class="ti ti-pencil"></i></span></a>
                                                    </div>
                                                @endpermission

                                                @permission('competencies delete')
                                                    <div class="action-btn">
                                                        {{ Form::open([
                                                            'method' => 'DELETE',
                                                            'route' => ['competencies.destroy', $competency->id],
                                                            'id' => 'delete-form-' . $competency->id,
                                                        ]) }}
                                                        <a href="#"
                                                            class="btn btn-sm  align-items-center bs-pass-para show_confirm bg-danger"
                                                            data-bs-toggle="tooltip" title="{{ __('Delete') }}"
                                                            data-confirm="{{ __('Are You Sure?') }}"
                                                            data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                            data-bs-original-title="{{ __('Delete') }}"
                                                            aria-label="{{ __('Delete') }}"
                                                            data-confirm-yes="delete-form-{{ $competency->id }}"><i
                                                                class="ti ti-trash text-white text-white"></i></a>
                                                        {{ Form::close() }}
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
