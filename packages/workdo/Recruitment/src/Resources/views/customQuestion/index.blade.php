@extends('layouts.main')

@section('page-title')
    {{ __('Mange Job screening') }}
@endsection
@section('page-breadcrumb')
    {{ __('Job screening') }}
@endsection
@push('css')
    <link rel="stylesheet" href="{{ asset('packages/workdo/Recruitment/src/Resources/assets/css/custom.css') }}">
@endpush

@section('page-action')
    <div>
        @permission('custom question create')
            <a data-url="{{ route('custom-question.create') }}" data-ajax-popup="true"
                data-title="{{ __('Create Job screening') }}" data-bs-toggle="tooltip" title=""
                class="btn btn-sm btn-primary" data-bs-original-title="{{ __('Create') }}">
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
                                    <th>{{ __('Question') }}</th>
                                    <th>{{ __('Is Required?*') }}</th>
                                    @if (Laratrust::hasPermission('custom question edit') || Laratrust::hasPermission('custom question delete'))
                                        <th width="200px">{{ __('Action') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($questions as $question)
                                    <tr>
                                        <td>{{ $question->question }}</td>
                                        <td>
                                            @if ($question->is_required == 'yes')
                                                <span
                                                    class="badge bg-success p-2 px-3 ">{{ Workdo\Recruitment\Entities\CustomQuestion::$is_required[$question->is_required] }}</span>
                                            @else
                                                <span
                                                    class="badge bg-danger p-2 px-3 ">{{ Workdo\Recruitment\Entities\CustomQuestion::$is_required[$question->is_required] }}</span>
                                            @endif
                                        </td>
                                        <td class="Action">
                                            <span>
                                                @permission('custom question edit')
                                                    <div class="action-btn me-2">
                                                        <a class="mx-3 btn btn-sm  align-items-center bg-info"
                                                            data-url="{{ route('custom-question.edit', $question->id) }}"
                                                            data-ajax-popup="true" data-size="md" data-bs-toggle="tooltip"
                                                            title="" data-title="{{ __('Edit Job screening') }}"
                                                            data-bs-original-title="{{ __('Edit') }}">
                                                            <i class="ti ti-pencil text-white"></i>
                                                        </a>
                                                    </div>
                                                @endpermission

                                                @permission('custom question delete')
                                                    <div class="action-btn">
                                                        {!! Form::open([
                                                            'method' => 'DELETE',
                                                            'route' => ['custom-question.destroy', $question->id],
                                                            'id' => 'delete-form-' . $question->id,
                                                        ]) !!}
                                                        <a class="mx-3 btn btn-sm  align-items-center bs-pass-para show_confirm bg-danger"
                                                            data-bs-toggle="tooltip" title="{{ __('Delete') }}" data-confirm="{{ __('Are You Sure?') }}"
                                                            data-text="{{ __('This action can not be undone. Do you want to continue?') }}"><i
                                                                class="ti ti-trash text-white text-white"></i></a>
                                                        {!! Form::close() !!}
                                                    </div>
                                                @endpermission
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
