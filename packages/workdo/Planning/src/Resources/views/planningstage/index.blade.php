@extends('layouts.main')
@section('page-title')
    {{ __('Stage') }}
@endsection
@section('page-breadcrumb')
{{ __('Stage') }}
@endsection
@section('page-action')
<div>
    @permission('planning stage manage')
        <a  class="btn btn-sm btn-primary" data-ajax-popup="true" data-size="md" data-title="{{ __('Create Stage') }}" data-url="{{route('planning-stage.create')}}" data-toggle="tooltip" title="{{ __('Create') }}">
            <i class="ti ti-plus"></i>
        </a>
    @endpermission
</div>
@endsection
@section('content')
    <div class="row">
        <div class="col-sm-3">
            @include('planning::layouts.planning_setup')
        </div>
        <div class="col-sm-9">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table mb-0 ">
                            <thead>
                                <tr>
                                    <th>{{ __('Stage Name') }}</th>
                                    @if (Laratrust::hasPermission('planning stage edit') || Laratrust::hasPermission('planning stage delete'))
                                        <th width="200px">{{ __('Action') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($Planningstages as $Planningstage)
                                    <tr>
                                        <td>{{ $Planningstage->name }}</td>
                                        @if (Laratrust::hasPermission('planning stage edit') || Laratrust::hasPermission('planning stage delete'))
                                            <td class="Action">
                                                <span>
                                                    @permission('planning stage edit')
                                                        <div class="action-btn me-2">
                                                            <a class="mx-3 btn bg-info btn-sm align-items-center"
                                                                data-url="{{ route('planning-stage.edit', $Planningstage->id) }}"
                                                                data-ajax-popup="true" data-size="md" data-bs-toggle="tooltip"
                                                                title="" data-title="{{ __('Edit Stage') }}"
                                                                data-bs-original-title="{{ __('Edit') }}">
                                                                <i class="text-white ti ti-pencil"></i>
                                                            </a>
                                                        </div>
                                                    @endpermission
                                                    @permission('planning stage delete')
                                                        <div class="action-btn">
                                                            {{ Form::open(['route' => ['planning-stage.destroy', $Planningstage->id], 'class' => 'm-0']) }}
                                                            @method('DELETE')
                                                            <a class="mx-3 btn btn-sm align-items-center bs-pass-para show_confirm bg-danger"
                                                                data-bs-toggle="tooltip" title=""
                                                                data-bs-original-title="Delete" aria-label="Delete"
                                                                data-confirm="{{ __('Are You Sure?') }}"
                                                                data-text="{{ __('This action permission not be undone. Do you want to continue?') }}"
                                                                data-confirm-yes="delete-form-{{ $Planningstage->id }}"><i
                                                                    class="text-white ti ti-trash"></i></a>
                                                            {{ Form::close() }}
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

