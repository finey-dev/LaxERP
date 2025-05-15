@extends('layouts.main')

@section('page-title')
    {{ __('Manage Candidate Category') }}
@endsection

@section('page-breadcrumb')
    {{ __('Candidate Catgory') }}
@endsection

@section('page-action')
    <div>
        @permission('jobcandidate-category create')
            <a data-url="{{ route('jobcandidate-category.create') }}" data-ajax-popup="true"
                data-title="{{ __('Create Candidate Category') }}" data-bs-toggle="tooltip" title=""
                class="btn btn-sm btn-primary" data-bs-original-title="{{ __('Create') }}">
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
                        <table class="table mb-0 ">
                            <thead>
                                <tr>
                                    <th>{{ __('Name') }}</th>
                                    @if (Laratrust::hasPermission('jobcandidate-category edit') || Laratrust::hasPermission('jobcandidate-category delete'))
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
                                                @permission('jobcandidate-category edit')
                                                    <div class="action-btn me-2">
                                                        <a class="mx-3 btn btn-sm  align-items-center bg-info"
                                                            data-url="{{ route('jobcandidate-category.edit', $category->id) }}"
                                                            data-ajax-popup="true" data-size="md" data-bs-toggle="tooltip"
                                                            title="" data-title="{{ __('Edit Candidate Category') }}"
                                                            data-bs-original-title="{{ __('Edit') }}">
                                                            <i class="ti ti-pencil text-white"></i>
                                                        </a>
                                                    </div>
                                                @endpermission

                                                @permission('jobcandidate-category delete')
                                                    <div class="action-btn">
                                                        {!! Form::open([
                                                            'method' => 'DELETE',
                                                            'route' => ['jobcandidate-category.destroy', $category->id],
                                                            'id' => 'delete-form-' . $category->id,
                                                        ]) !!}
                                                        <a class="mx-3 btn btn-sm  align-items-center bs-pass-para show_confirm bg-danger"
                                                            data-bs-toggle="tooltip" title="{{ __('Delete') }}" data-confirm="{{ __('Are You Sure?') }}"
                                                            data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                            data-bs-original-title="Delete" aria-label="Delete"><i
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
