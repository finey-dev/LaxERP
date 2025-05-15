@extends('layouts.main')
@section('page-title')
    {{ __('Manage Job Template') }}
@endsection

@section('page-breadcrumb')
    {{ __('Job Template') }}
@endsection
@section('page-action')
    <div class="d-flex">
        <a href="{{ route('job-template.index') }}" class="btn btn-sm btn-primary btn-icon" data-bs-toggle="tooltip"
            title="{{ __('List View') }}">
            <i class="ti ti-list text-white"></i>
        </a>
    </div>
@endsection
@push('scripts')
    <script src="{{ asset('js/letter.avatar.js') }}"></script>
@endpush
@section('filter')
@endsection
@section('content')
    <div class="row  row-gap-2 mb-4">
        @foreach ($job_templates as $job_template)
            <div class="col-xxl-3 col-xl-4 col-md-6">
                <div class="card grid-card user-card">
                    <div class="card-header border border-bottom p-3 h-100 d-flex gap-1 justify-content-between">

                        <div class="user-img-wrp d-flex align-items-center">
                            <div class="user-image rounded border-2 border border-primary">

                                <img alt="user-image" avatar="{{ $job_template->title }}" class="h-100 w-100">


                            </div>
                            <div class="user-content">
                                <h4 class="mb-2"> <a class=" text-primary"
                                        href="{{ route('job.show', $job_template->id) }}">{{ ucfirst($job_template->title) }}</a>
                                </h4>
                                <span class="text-dark text-md">
                                    {{ !empty($job_template->location) ? $job_template->location : '-' }}</span>
                            </div>
                        </div>
                        <div class="card-header-right">
                            <div class="btn-group card-option">
                                <button type="button" class="btn dropdown-toggle" data-bs-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                    <i class="feather icon-more-vertical"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end">
                                    @if (Laratrust::hasPermission('job template show') ||
                                            Laratrust::hasPermission('job template edit') ||
                                            Laratrust::hasPermission('job template delete'))
                                        @permission('job manage')
                                            {!! Form::open([
                                                'method' => 'POST',
                                                'route' => ['job-template.convertToJob', ['job_template_id' => $job_template->id]],
                                            ]) !!}
                                            <a href="#" class="dropdown-item show_confirm" data-bs-toggle="tooltip"
                                                data-title="{{ __('Convert to job') }}"><i class="ti ti-replace"></i>
                                                <span class="ms-1">{{ __('Convert to job') }}</span>
                                            </a>
                                            {!! Form::close() !!}
                                        @endpermission
                                        @permission('job template show')
                                            <a href="{{ route('job.show', $job_template->id) }}" class="dropdown-item"
                                                data-bs-whatever="{{ __('Job Details') }}" data-bs-toggle="tooltip"
                                                data-title="{{ __('Job Details') }}"><i class="ti ti-eye"></i>
                                                <span class="ms-1">{{ __('Details') }}</span>
                                            </a>
                                        @endpermission
                                        @permission('job template edit')
                                            <a href="{{ route('job.edit', $job_template->id) }}" data-size="md"
                                                class="dropdown-item" data-bs-whatever="{{ __('Edit Job') }}"
                                                data-bs-toggle="tooltip" data-title="{{ __('Edit Job') }}"><i
                                                    class="ti ti-pencil"></i>
                                                <span class="ms-1">{{ __('Edit') }}</span></a>
                                        @endpermission
                                        @permission('job template delete')
                                            {!! Form::open(['method' => 'DELETE', 'route' => ['job.destroy', $job_template->id]]) !!}
                                            <a href="#!" class="dropdown-item  show_confirm" data-bs-toggle="tooltip"
                                                data-text="{{ __('This action can not be undone. Do you want to continue?') }}">
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
                                @if (Laratrust::hasPermission('job template show') ||
                                        Laratrust::hasPermission('job template edit') ||
                                        Laratrust::hasPermission('job template delete'))
                                    @permission('job manage')
                                        {!! Form::open([
                                            'method' => 'POST',
                                            'route' => ['job-template.convertToJob', ['job_template_id' => $job_template->id]],
                                        ]) !!}
                                        <a href="#" class="btn btn-sm border show_confirm" data-bs-toggle="tooltip"
                                            data-title="{{ __('Convert to job') }}" data-bs-toggle="tooltip" title="{{ __('Convert to job') }}">
                                            <svg width="14" height="14" viewBox="0 0 14 14" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <g clip-path="url(#clip0_426_104)">
                                                    <path
                                                        d="M10.9959 0.729126H9.42139C7.86906 0.729126 7.14616 1.45203 7.14616 3.00435V5.16223C7.14616 6.71456 7.86906 7.43746 9.42139 7.43746H10.9959C12.5483 7.43746 13.2712 6.71456 13.2712 5.16223V3.00435C13.2712 1.45203 12.5483 0.729126 10.9959 0.729126ZM12.3962 5.16223C12.3962 6.23547 12.0692 6.56246 10.9959 6.56246H9.42139C8.34815 6.56246 8.02116 6.23547 8.02116 5.16223V3.00435C8.02116 1.93111 8.34815 1.60413 9.42139 1.60413H10.9959C12.0692 1.60413 12.3962 1.93111 12.3962 3.00435V5.16223ZM4.57926 6.56246H3.00472C1.45239 6.56246 0.729492 7.28536 0.729492 8.83769V10.9956C0.729492 12.5479 1.45239 13.2708 3.00472 13.2708H4.57926C6.13159 13.2708 6.85449 12.5479 6.85449 10.9956V8.83769C6.85449 7.28536 6.13159 6.56246 4.57926 6.56246ZM5.97949 10.9956C5.97949 12.0688 5.6525 12.3958 4.57926 12.3958H3.00472C1.93148 12.3958 1.60449 12.0688 1.60449 10.9956V8.83769C1.60449 7.76445 1.93148 7.43746 3.00472 7.43746H4.57926C5.6525 7.43746 5.97949 7.76445 5.97949 8.83769V10.9956ZM13.2712 8.74996C13.2698 9.94855 12.7931 11.0977 11.9456 11.9452C11.098 12.7927 9.94891 13.2695 8.75033 13.2708C8.67295 13.2707 8.59698 13.2501 8.53015 13.2111C8.46332 13.1721 8.40801 13.1161 8.36987 13.0488C8.33172 12.9814 8.31209 12.9052 8.31298 12.8278C8.31387 12.7505 8.33524 12.6747 8.37492 12.6083L8.98731 11.5874C9.04719 11.4883 9.14393 11.417 9.25632 11.389C9.36872 11.3611 9.48761 11.3788 9.58695 11.4383C9.68629 11.4979 9.75798 11.5944 9.78632 11.7067C9.81465 11.819 9.79732 11.9379 9.73812 12.0375L9.58032 12.3007C10.3809 12.1123 11.0944 11.6592 11.6055 11.0148C12.1165 10.3704 12.3951 9.57239 12.3962 8.74996C12.3962 8.63393 12.4423 8.52265 12.5243 8.4406C12.6063 8.35855 12.7176 8.31246 12.8337 8.31246C12.9497 8.31246 13.061 8.35855 13.143 8.4406C13.2251 8.52265 13.2712 8.63393 13.2712 8.74996ZM0.729492 5.24996C0.730832 4.05137 1.20756 2.90226 2.05509 2.05473C2.90262 1.2072 4.05174 0.730466 5.25033 0.729126C5.3277 0.729224 5.40367 0.749823 5.47051 0.788825C5.53734 0.827826 5.59264 0.883839 5.63079 0.951161C5.66893 1.01848 5.68856 1.09471 5.68767 1.17208C5.68678 1.24946 5.66541 1.32521 5.62573 1.39164L5.01335 2.41247C4.95346 2.51161 4.85672 2.58296 4.74433 2.61091C4.63193 2.63886 4.51304 2.62112 4.4137 2.56157C4.31436 2.50203 4.24267 2.40554 4.21434 2.29324C4.186 2.18094 4.20333 2.06199 4.26253 1.96244L4.42033 1.69926C3.61976 1.88766 2.90621 2.34073 2.39518 2.98512C1.88415 3.62952 1.60555 4.42753 1.60449 5.24996C1.60449 5.36599 1.5584 5.47727 1.47635 5.55932C1.3943 5.64137 1.28302 5.68746 1.16699 5.68746C1.05096 5.68746 0.93968 5.64137 0.857633 5.55932C0.775586 5.47727 0.729492 5.36599 0.729492 5.24996Z"
                                                        fill="#060606" />
                                                </g>
                                                <defs>
                                                    <clipPath id="clip0_426_104">
                                                        <rect width="14" height="14" fill="white" />
                                                    </clipPath>
                                                </defs>
                                            </svg>
                                        </a>
                                        {!! Form::close() !!}
                                    @endpermission
                                    @permission('job show')
                                        <a class="btn btn-sm border" href="{{ route('job.show', $job_template->id) }}"
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
                                    @permission('job template edit')
                                        <a href="{{ route('job.edit', $job_template->id) }}" data-size="md"
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
                                    @permission('job template delete')
                                        {{ Form::open(['route' => ['job.destroy', $job_template->id], 'class' => 'm-0']) }}
                                        @method('DELETE')
                                        <a href="#!" aria-label="Delete" data-confirm="{{ __('Are You Sure?') }}"
                                            data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                            data-confirm-yes="delete-form-{{ $job_template->id }}" data-bs-toggle="tooltip"
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
                            @if ($job_template->status == 'active')
                                <span
                                    class="badge bg-success p-2 px-3">{{ Workdo\Recruitment\Entities\Job::$status[$job_template->status] }}</span>
                            @else
                                <span
                                    class="badge bg-danger p-2 px-3">{{ Workdo\Recruitment\Entities\Job::$status[$job_template->status] }}</span>
                            @endif
                        </div>

                    </div>
                </div>
            </div>
        @endforeach
    </div>
    {!! $job_templates->links('vendor.pagination.global-pagination') !!}
@endsection
