@extends('layouts.main')
@section('page-title')
    {{ __('Manage Job') }}
@endsection

@section('page-breadcrumb')
    {{ __('Job') }}
@endsection
@section('page-action')
    <div class="d-flex">
        @stack('addButtonHook')
        <a href="{{ route('job.index') }}" class="btn btn-sm btn-primary btn-icon me-2" data-bs-toggle="tooltip"
            title="{{ __('List View') }}">
            <i class="ti ti-list text-white"></i>
        </a>
        @permission('job create')
            <a href="{{ route('job.create') }}" data-size="md" data-title="{{ __('Create New Job') }}" data-bs-toggle="tooltip"
                title="" class="btn btn-sm btn-primary" data-bs-original-title="{{ __('Create') }}">
                <i class="ti ti-plus"></i>
            </a>
        @endpermission
    </div>
@endsection
@push('scripts')
    <script src="{{ asset('js/letter.avatar.js') }}"></script>
@endpush
@section('filter')
@endsection
@section('content')
    <div class="row">
        <div class="col-lg-4 col-sm-6 d-flex">
            <div class="card w-100">
                <div class="card-body">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-auto mb-3 mb-sm-0">
                            <div class="d-flex align-items-center">
                                <div class="theme-avtar bg-primary badge">
                                    <i class="ti ti-briefcase"></i>
                                </div>
                                <div class="ms-3">
                                    <small class="text-muted">{{ __('Total') }}</small>
                                    <h6 class="m-0">{{ __('Jobs') }}</h6>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto text-end">
                            <h4 class="m-0">{{ $data['total'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-sm-6 d-flex">
            <div class="card w-100">
                <div class="card-body">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-auto mb-3 mb-sm-0">
                            <div class="d-flex align-items-center">
                                <div class="theme-avtar bg-info badge">
                                    <i class="ti ti-cast"></i>
                                </div>
                                <div class="ms-3">
                                    <small class="text-muted">{{ __('Active') }}</small>
                                    <h6 class="m-0">{{ __('Jobs') }}</h6>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto text-end">
                            <h4 class="m-0">{{ $data['active'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-sm-6 d-flex">
            <div class="card w-100">
                <div class="card-body">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-auto mb-3 mb-sm-0">
                            <div class="d-flex align-items-center">
                                <div class="theme-avtar bg-warning badge">
                                    <i class="ti ti-cast"></i>
                                </div>
                                <div class="ms-3">
                                    <small class="text-muted">{{ __('Inactive') }}</small>
                                    <h6 class="m-0">{{ __('Jobs') }}</h6>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto text-end">
                            <h4 class="m-0">{{ $data['in_active'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row  row-gap-2 mb-4">
        @foreach ($jobs as $job)
            <div class="col-xxl-3 col-xl-4 col-md-6">
                <div class="card grid-card user-card">
                    <div class="card-header border border-bottom p-3 h-100 d-flex gap-1 justify-content-between">
                        <div class="user-img-wrp d-flex align-items-center">
                            <div class="user-image rounded border-2 border border-primary">
                                <img alt="user-image" avatar="{{ $job->title }}" class="h-100 w-100">
                            </div>
                            <div class="user-content">
                                <h4 class="mb-2"> <a class=" text-primary"
                                        href="{{ route('job.show', $job->id) }}">{{ ucfirst($job->title) }}</a>
                                </h4>
                                <span class="text-dark text-md">
                                    {{ !empty($job->location) ? $job->location : '-' }}</span>
                            </div>
                        </div>
                        <div class="card-header-right">
                            <div class="btn-group card-option">
                                <button type="button" class="btn dropdown-toggle" data-bs-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                    <i class="feather icon-more-vertical"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end">
                                    @if (Laratrust::hasPermission('job show') || Laratrust::hasPermission('job edit') || Laratrust::hasPermission('job delete'))
                                        @permission('job template manage')
                                            {!! Form::open(['method' => 'POST', 'route' => ['job-template.store', ['job_id' => $job->id]]]) !!}
                                            <a href="#" class="dropdown-item show_confirm" data-bs-toggle="tooltip"
                                                data-title="{{ __('Save as template') }}"><i
                                                    class="ti ti-bookmark"></i>
                                                    <span class="ms-1">{{ __('Save as template') }}</span>
                                            </a>
                                            {!! Form::close() !!}
                                        @endpermission
                                        @if ($job->status != 'in_active' && $job->is_post == 1)
                                            <a href="#" id="{{ route('job.requirement', [$job->code, !empty($job) ? $job->createdBy->lang : 'en']) }}"
                                                class="dropdown-item" onclick="copyToClipboard(this)" data-bs-toggle="tooltip"
                                                title="{{ __('Copy') }}" data-original-title="{{ __('Click to copy') }}"><i
                                                    class="ti ti-link"></i>  <span class="ms-1">{{ __('Click to copy') }}</span></a>
                                        @endif
                                        @permission('job show')
                                            <a href="{{ route('job.show', $job->id) }}" class="dropdown-item"
                                                data-bs-whatever="{{ __('Job Details') }}" data-bs-toggle="tooltip"
                                                data-title="{{ __('Job Details') }}"><i class="ti ti-eye"></i>
                                                <span class="ms-1">{{ __('View') }}</span>
                                            </a>
                                        @endpermission
                                        @permission('job edit')
                                            <a href="{{ route('job.edit', $job->id) }}" data-size="md" class="dropdown-item"
                                                data-bs-whatever="{{ __('Edit Job') }}" data-bs-toggle="tooltip"
                                                data-title="{{ __('Edit Job') }}"><i class="ti ti-pencil"></i>
                                                <span class="ms-1">{{ __('Edit') }}</span></a>
                                        @endpermission
                                        @permission('job delete')
                                            {!! Form::open(['method' => 'DELETE', 'route' => ['job.destroy', $job->id]]) !!}
                                            <a href="#!" class="dropdown-item  show_confirm" data-bs-toggle="tooltip" data-text="{{ __('This action can not be undone. Do you want to continue?') }}">
                                                <i class="ti ti-trash"></i>
                                                <span class="ms-1">{{ __('Delete') }}</span>
                                            </a>
                                            {!! Form::close() !!}
                                        @endpermission
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body  text-center p-3">
                        <div class="bottom-icons d-flex flex-wrap align-items-center justify-content-between">
                            <div class="edit-btn-wrp d-flex flex-wrap align-items-center">
                                @if (Laratrust::hasPermission('job show') ||
                                     Laratrust::hasPermission('job edit') ||
                                     Laratrust::hasPermission('job delete'))
                                    @permission('job template manage')
                                        {{ Form::open(['method' => 'POST', 'route' => ['job-template.store', ['job_id' => $job->id]]]) }}
                                            <a href="#" class="btn btn-sm border show_confirm" data-bs-toggle="tooltip"
                                                data-title="{{ __('Save as template') }}" data-bs-toggle="tooltip" title="{{ __('Save as template') }}">
                                                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M10.9542 0H3.04573C2.34877 0 1.75684 0.562124 1.75684 1.2889V12.9829C1.75889 13.9053 2.8856 14.3382 3.52615 13.6906L6.54063 10.677C6.78221 10.425 7.21772 10.425 7.4593 10.677L10.4738 13.6906C11.1146 14.3384 12.2412 13.9049 12.2431 12.9829V1.2889C12.2431 0.562124 11.6512 0 10.9542 0ZM11.4227 12.9829C11.4217 13.1782 11.1789 13.2427 11.0533 13.11C10.4507 12.5065 8.73214 10.7884 8.03993 10.0975C7.49539 9.52797 6.50536 9.52781 5.96106 10.0964C5.48289 10.5737 4.54689 11.5094 3.80904 12.247L2.95065 13.105C2.82697 13.2363 2.58115 13.1924 2.57719 12.9903V1.2889C2.57719 1.02687 2.78958 0.82038 3.04573 0.82038H10.9542C11.2099 0.82038 11.4227 1.02654 11.4227 1.2889V12.9829Z" fill="#060606"/>
                                                    </svg>
                                            </a>
                                        {{ Form::close() }}
                                    @endpermission
                                    @if ($job->status != 'in_active' && $job->is_post == 1)
                                        <a href="#" class="cp_link1 btn btn-sm border"
                                            data-link="{{ route('job.requirement', [$job->code, !empty($job) ? $job->createdBy->lang : 'en']) }}"
                                            data-bs-original-title="{{ __('Click to copy') }}" data-bs-toggle="tooltip"
                                            data-bs-placement="top">
                                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M9.06071 7.43949C8.93271 7.43949 8.80471 7.39074 8.70721 7.29299C7.92746 6.51324 6.65871 6.51299 5.87871 7.29299C5.68371 7.48824 5.36671 7.48824 5.17171 7.29299C4.97646 7.09774 4.97646 6.78124 5.17171 6.58599C6.34146 5.41599 8.24471 5.41624 9.41421 6.58599C9.60946 6.78124 9.60946 7.09774 9.41421 7.29299C9.31671 7.39074 9.18846 7.43949 9.06071 7.43949Z"
                                                    fill="#060606" />
                                                <path
                                                    d="M8.70726 10.2917C7.93901 10.2917 7.17076 9.99922 6.58601 9.41447C6.39076 9.21922 6.39076 8.90272 6.58601 8.70747C6.78101 8.51222 7.09801 8.51222 7.29301 8.70747C8.07276 9.48722 9.34151 9.48747 10.1215 8.70747C10.3165 8.51222 10.6335 8.51222 10.8285 8.70747C11.0238 8.90272 11.0238 9.21922 10.8285 9.41447C10.2435 9.99922 9.47526 10.2917 8.70726 10.2917Z"
                                                    fill="#060606" />
                                                <path
                                                    d="M10.4747 9.56073C10.3467 9.56073 10.2187 9.51198 10.1212 9.41423C9.92598 9.21898 9.92598 8.90248 10.1212 8.70723L12.9142 5.91423C13.1092 5.71898 13.4262 5.71898 13.6212 5.91423C13.8165 6.10948 13.8165 6.42598 13.6212 6.62123L10.8282 9.41423C10.7307 9.51198 10.6027 9.56073 10.4747 9.56073Z"
                                                    fill="#060606" />
                                                <path
                                                    d="M6.26776 13.7677C6.13976 13.7677 6.01176 13.719 5.91426 13.6212C5.71901 13.426 5.71901 13.1095 5.91426 12.9142L7.47551 11.353C7.67051 11.1577 7.98751 11.1577 8.18251 11.353C8.37776 11.5482 8.37776 11.8647 8.18251 12.06L6.62126 13.6212C6.52376 13.719 6.39576 13.7677 6.26776 13.7677Z"
                                                    fill="#060606" />
                                                <path
                                                    d="M2.73224 10.2322C2.60424 10.2322 2.47624 10.1835 2.37874 10.0857C2.18349 9.89048 2.18349 9.57398 2.37874 9.37873L5.17174 6.58598C5.36699 6.39073 5.68374 6.39073 5.87874 6.58598C6.07399 6.78123 6.07399 7.09773 5.87874 7.29298L3.08574 10.0857C2.98799 10.1832 2.85999 10.2322 2.73224 10.2322Z"
                                                    fill="#060606" />
                                                <path
                                                    d="M8.17096 4.79374C8.04296 4.79374 7.91521 4.74499 7.81746 4.64749C7.62221 4.45224 7.62221 4.13574 7.81746 3.94024L9.37871 2.37874C9.57396 2.18349 9.89046 2.18349 10.086 2.37874C10.2812 2.57399 10.2812 2.89049 10.086 3.08599L8.52471 4.64749C8.42696 4.74474 8.29871 4.79374 8.17096 4.79374Z"
                                                    fill="#060606" />
                                                <path
                                                    d="M4.49999 14.4837C3.72674 14.4837 2.95349 14.1962 2.37874 13.6212C1.22874 12.4712 1.22874 10.5285 2.37874 9.37849C2.57374 9.18324 2.89074 9.18324 3.08574 9.37849C3.28099 9.57374 3.28099 9.89024 3.08574 10.0855C2.31924 10.852 2.31924 12.1475 3.08574 12.914C3.85224 13.6805 5.14774 13.6805 5.91424 12.914C6.10924 12.7187 6.42624 12.7187 6.62124 12.914C6.81649 13.1092 6.81649 13.4257 6.62124 13.621C6.04649 14.1962 5.27324 14.4837 4.49999 14.4837Z"
                                                    fill="#060606" />
                                                <path
                                                    d="M13.2677 6.76774C13.1397 6.76774 13.0117 6.71899 12.9142 6.62124C12.719 6.42599 12.719 6.10949 12.9142 5.91424C13.6807 5.14774 13.6807 3.85224 12.9142 3.08574C12.1477 2.31924 10.8522 2.31924 10.0857 3.08574C9.89074 3.28099 9.57374 3.28099 9.37874 3.08574C9.18349 2.89049 9.18349 2.57399 9.37874 2.37874C10.5287 1.22874 12.4715 1.22874 13.6215 2.37874C14.7715 3.52874 14.7715 5.47149 13.6215 6.62149C13.5237 6.71899 13.3957 6.76774 13.2677 6.76774Z"
                                                    fill="#060606" />
                                            </svg>
                                        </a>
                                    @endif

                                    @permission('job show')
                                        <a class="btn btn-sm border" href="{{ route('job.show', $job->id) }}"
                                            data-bs-whatever="{{ __('Job Details') }}" data-title="{{ __('Job Details') }}"
                                            data-bs-toggle="tooltip" title="{{ __('View') }}">
                                            <svg width="16" height="16" viewBox="0 0 15 15" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M7.9997 10.8869C6.40637 10.8869 5.11304 9.59353 5.11304 8.00019C5.11304 6.40686 6.40637 5.11353 7.9997 5.11353C9.59304 5.11353 10.8864 6.40686 10.8864 8.00019C10.8864 9.59353 9.59304 10.8869 7.9997 10.8869ZM7.9997 6.11353C6.9597 6.11353 6.11304 6.96019 6.11304 8.00019C6.11304 9.04019 6.9597 9.88686 7.9997 9.88686C9.0397 9.88686 9.88637 9.04019 9.88637 8.00019C9.88637 6.96019 9.0397 6.11353 7.9997 6.11353Z"
                                                    fill="#060606" />
                                                <path
                                                    d="M7.99967 14.0134C5.493 14.0134 3.12633 12.5467 1.49967 10C0.792999 8.90003 0.792999 7.10669 1.49967 6.00003C3.133 3.45336 5.49967 1.98669 7.99967 1.98669C10.4997 1.98669 12.8663 3.45336 14.493 6.00003C15.1997 7.10003 15.1997 8.89336 14.493 10C12.8663 12.5467 10.4997 14.0134 7.99967 14.0134ZM7.99967 2.98669C5.84633 2.98669 3.78633 4.28003 2.34633 6.54003C1.84633 7.32003 1.84633 8.68003 2.34633 9.46003C3.78633 11.72 5.84633 13.0134 7.99967 13.0134C10.153 13.0134 12.213 11.72 13.653 9.46003C14.153 8.68003 14.153 7.32003 13.653 6.54003C12.213 4.28003 10.153 2.98669 7.99967 2.98669Z"
                                                    fill="#060606" />
                                            </svg>
                                        </a>
                                    @endpermission

                                    @permission('job edit')
                                        <a href="{{ route('job.edit', $job->id) }}" data-size="md"
                                            data-bs-whatever="{{ __('Edit Job') }}" data-title="{{ __('Edit Job') }}"
                                            data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Edit') }}"
                                            class="btn btn-sm border">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                viewBox="0 0 16 16" fill="none">
                                                <path
                                                    d="M1.56382 11.5713C1.40611 11.5713 1.24871 11.5112 1.12827 11.3908C0.887704 11.1502 0.887704 10.7603 1.12827 10.5197L10.7553 0.892668C10.9956 0.6521 11.3858 0.6521 11.6264 0.892668C11.867 1.13324 11.867 1.5232 11.6264 1.76376L1.99937 11.3908C1.87924 11.5109 1.72153 11.5713 1.56382 11.5713Z"
                                                    fill="#060606" />
                                                <path
                                                    d="M0.61263 16.0078C0.569815 16.0078 0.526383 16.0031 0.482952 15.9939C0.150284 15.9224 -0.0616371 15.595 0.00982476 15.2623L0.961623 10.8258C1.03308 10.4932 1.36206 10.2819 1.69318 10.3527C2.02585 10.4242 2.23777 10.7516 2.16631 11.0843L1.21451 15.5208C1.1526 15.81 0.896938 16.0078 0.61263 16.0078Z"
                                                    fill="#060606" />
                                                <path
                                                    d="M5.04863 15.056C4.89092 15.056 4.73352 14.9959 4.61308 14.8755C4.37251 14.6349 4.37251 14.245 4.61308 14.0044L14.2401 4.37767C14.4804 4.1371 14.8706 4.1371 15.1112 4.37767C15.3518 4.61824 15.3518 5.0082 15.1112 5.24877L5.48448 14.8755C5.36404 14.9959 5.20633 15.056 5.04863 15.056Z"
                                                    fill="#060606" />
                                                <path
                                                    d="M0.611348 16.0078C0.32704 16.0078 0.0716875 15.81 0.0094664 15.5208C-0.0616875 15.1881 0.149926 14.8607 0.482593 14.7892L4.91908 13.8374C5.25206 13.7669 5.57949 13.9782 5.65064 14.3105C5.7218 14.6432 5.51018 14.9706 5.17752 15.0421L0.741027 15.9939C0.697595 16.0034 0.654163 16.0078 0.611348 16.0078Z"
                                                    fill="#060606" />
                                                <path
                                                    d="M12.9331 7.17142C12.7754 7.17142 12.6177 7.11136 12.4976 6.99092L9.01287 3.50623C8.7723 3.26566 8.7723 2.8757 9.01287 2.63514C9.25313 2.39457 9.6437 2.39457 9.88396 2.63514L13.3687 6.11983C13.6092 6.36039 13.6092 6.75035 13.3687 6.99092C13.2485 7.11136 13.0908 7.17142 12.9331 7.17142Z"
                                                    fill="#060606" />
                                                <path
                                                    d="M14.6757 5.42925C14.518 5.42925 14.3603 5.36919 14.2399 5.24875C13.9993 5.00818 13.9993 4.61822 14.2399 4.37735C14.5827 4.03452 14.7715 3.57032 14.7715 3.0707C14.7715 2.57109 14.5827 2.10689 14.2399 1.76406C13.8967 1.42092 13.4325 1.2321 12.9329 1.2321C12.4333 1.2321 11.9691 1.42092 11.6263 1.76406C11.386 2.00463 10.996 2.00494 10.7549 1.76406C10.5143 1.52349 10.5143 1.13353 10.7549 0.892657C11.3303 0.316958 12.1037 0 12.9329 0C13.7618 0 14.5356 0.316958 15.111 0.892657C15.6867 1.46805 16.0036 2.2415 16.0036 3.0707C16.0036 3.89991 15.6867 4.67336 15.111 5.24875C14.9911 5.36888 14.8334 5.42925 14.6757 5.42925Z"
                                                    fill="#060606" />
                                            </svg>
                                        </a>
                                    @endpermission

                                    @permission('job delete')
                                        {{ Form::open(['route' => ['job.destroy', $job->id], 'class' => 'm-0']) }}
                                        @method('DELETE')
                                        <a href="#!" aria-label="Delete" data-confirm="{{ __('Are You Sure?') }}"
                                            data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                            data-confirm-yes="delete-form-{{ $job->id }}" data-bs-toggle="tooltip"
                                            data-bs-placement="top" data-bs-original-title="{{ __('Delete') }}"
                                            class="btn btn-sm border bs-pass-para show_confirm">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                viewBox="0 0 16 16" fill="none">
                                                <g clip-path="url(#clip0_11_8426)">
                                                    <path
                                                        d="M13.625 1.875H11.2812V1.40625C11.2812 0.630844 10.6504 0 9.875 0H6.125C5.34959 0 4.71875 0.630844 4.71875 1.40625V1.875H2.375C1.59959 1.875 0.96875 2.50584 0.96875 3.28125C0.96875 3.904 1.37578 4.43316 1.93766 4.61753L2.77375 14.7105C2.83397 15.4336 3.44953 16 4.17513 16H11.8249C12.5505 16 13.1661 15.4336 13.2263 14.7103L14.0623 4.6175C14.6242 4.43316 15.0312 3.904 15.0312 3.28125C15.0312 2.50584 14.4004 1.875 13.625 1.875ZM5.65625 1.40625C5.65625 1.14778 5.86653 0.9375 6.125 0.9375H9.875C10.1335 0.9375 10.3438 1.14778 10.3438 1.40625V1.875H5.65625V1.40625ZM12.292 14.6327C12.2719 14.8737 12.0667 15.0625 11.8249 15.0625H4.17513C3.93328 15.0625 3.72809 14.8737 3.70806 14.6329L2.88419 4.6875H13.1158L12.292 14.6327ZM13.625 3.75H2.375C2.11653 3.75 1.90625 3.53972 1.90625 3.28125C1.90625 3.02278 2.11653 2.8125 2.375 2.8125H13.625C13.8835 2.8125 14.0938 3.02278 14.0938 3.28125C14.0938 3.53972 13.8835 3.75 13.625 3.75Z"
                                                        fill="#060606" />
                                                    <path
                                                        d="M6.12409 13.6272L5.65534 6.06472C5.63931 5.80631 5.41566 5.60978 5.1585 5.62588C4.90009 5.64191 4.70363 5.86435 4.71963 6.12272L5.18838 13.6853C5.20378 13.9338 5.41016 14.125 5.65578 14.125C5.92725 14.125 6.14075 13.8964 6.12409 13.6272Z"
                                                        fill="#060606" />
                                                    <path
                                                        d="M8 5.625C7.74112 5.625 7.53125 5.83487 7.53125 6.09375V13.6562C7.53125 13.9151 7.74112 14.125 8 14.125C8.25888 14.125 8.46875 13.9151 8.46875 13.6562V6.09375C8.46875 5.83487 8.25888 5.625 8 5.625Z"
                                                        fill="#060606" />
                                                    <path
                                                        d="M10.8415 5.62591C10.5837 5.60987 10.3606 5.80634 10.3446 6.06475L9.87587 13.6272C9.85991 13.8856 10.0564 14.1081 10.3147 14.1241C10.5733 14.1401 10.7956 13.9435 10.8116 13.6852L11.2803 6.12275C11.2963 5.86434 11.0999 5.64191 10.8415 5.62591Z"
                                                        fill="#060606" />
                                                </g>
                                                <defs>
                                                    <clipPath id="clip0_11_8426">
                                                        <rect width="16" height="16" fill="white" />
                                                    </clipPath>
                                                </defs>
                                            </svg>
                                        </a>
                                        {{ Form::close() }}
                                    @endpermission
                                @endif
                            </div>
                            @if ($job->status == 'active')
                                <span
                                    class="badge bg-success p-2 px-3">{{ Workdo\Recruitment\Entities\Job::$status[$job->status] }}</span>
                            @else
                                <span
                                    class="badge bg-danger p-2 px-3">{{ Workdo\Recruitment\Entities\Job::$status[$job->status] }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
        @permission('job create')
            <div class="col-xxl-3 col-xl-4 col-md-6">
                <a class="btn-addnew-project border-primary" href="{{ route('job.create') }}"
                    data-title="{{ __('Create Job') }}">
                    <div class="bg-primary proj-add-icon">
                        <i class="ti ti-plus my-2"></i>
                    </div>
                    <h6 class="my-2 text-center">{{ __('New Job') }}</h6>
                    <p class="text-muted text-center">{{ __('Click here to Create New Job') }}</p>
                </a>
            </div>
        @endpermission
    </div>
    {!! $jobs->links('vendor.pagination.global-pagination') !!}
@endsection
@push('scripts')
    <script type="text/javascript">
        $('.cp_link1').on('click', function() {
            var value = $(this).attr('data-link');
            var $temp = $("<input>");
            $("body").append($temp);
            $temp.val(value).select();
            document.execCommand("copy");
            $temp.remove();
            toastrs('{{ __('Success ') }}', '{{ __('Link Copy on Clipboard ') }}', 'success');
        });
    </script>
@endpush
