@extends('layouts.main')
@section('page-title')
    {{ __('Manage Job On-Boarding') }}
@endsection

@section('page-breadcrumb')
    {{ __('Job On-Boarding') }}
@endsection
@section('page-action')
    <div>
        <a href="{{ route('job.on.board') }}" class="btn btn-sm btn-primary btn-icon"
            data-bs-toggle="tooltip"title="{{ __('List View') }}">
            <i class="ti ti-list text-white"></i>
        </a>
        @permission('jobonboard create')
            <a data-url="{{ route('job.on.board.create', 0) }}" data-ajax-popup="true"
                data-title="{{ __('Create Job On-Boarding') }}" data-bs-toggle="tooltip" title=""
                class="btn btn-sm btn-primary" data-bs-original-title="{{ __('Create') }}">
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
    <div class="row  row-gap-2 mb-4">
        @foreach ($jobOnBoards as $job)
            <div class="col-xxl-3 col-xl-4 col-md-6">
                <div class="card grid-card user-card">
                    <div class="card-header border border-bottom p-3 h-100 d-flex gap-1 justify-content-between">
                        <div class="user-img-wrp d-flex align-items-center">
                            <div class="user-image rounded border-2 border border-primary">
                                <img alt="user-image"
                                    avatar="{{ !empty($job->applications) ? $job->applications->name : '-' }}"
                                    class="h-100 w-100">
                            </div>
                            <div class="user-content">
                                <h4 class="mb-2"> <a href="{{ route('employee.show', \Crypt::encrypt($job->convert_to_employee)) }}"
                                        class=" text-primary">{{ !empty($job->applications) ? $job->applications->name : '-' }}</a>
                                </h4>
                                <span class="text-dark text-md">
                                    {{ !empty($job->applications) ? (!empty($job->applications->jobs) ? (!empty($job->applications->jobs) ? (!empty($job->applications->jobs->branches) ? $job->applications->jobs->branches->name : $job->applications->jobs->location) : '-') : '-') : '-' }}</span>
                            </div>
                        </div>
                        <div class="card-header-right">
                            <div class="btn-group card-option">
                                <button type="button" class="btn dropdown-toggle" data-bs-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                    <i class="feather icon-more-vertical"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end">
                                    @if (Laratrust::hasPermission('jobonboard convert') || Laratrust::hasPermission('job edit') || Laratrust::hasPermission('job delete'))

                                        @if ($job->status == 'confirm' )
                                            <a href="{{ route('offerlatter.download.pdf',$job->id) }}" class="dropdown-item" target="_blanks">
                                                <i class="ti ti-file "></i>
                                                <span class="ms-1">{{ __('Offer Letter PDF') }}</span>
                                            </a>
                                            <a href="{{ route('offerlatter.download.doc',$job->id) }}" class="dropdown-item" target="_blanks">
                                                <i class="ti ti-file "></i>
                                                <span class="ms-1">{{ __('Offer Letter DOC') }}</span>
                                            </a>
                                        @endif
                                        @permission('jobonboard convert')
                                            @if ($job->status == 'confirm' && $job->convert_to_employee == 0 && module_is_active('Hrm') && $job->applications->jobs->recruitment_type == 'internal')
                                                <a href="{{ route('job.on.board.convert', $job->id) }}" class="dropdown-item">
                                                    <i class="ti ti-arrows-right-left "></i>
                                                    <span class="ms-1">{{ __('Convert to Employee') }}</span>
                                                </a>

                                            @elseif($job->status == 'confirm' && $job->convert_to_employee != 0)
                                                <a href="{{ route('employee.show', \Crypt::encrypt($job->convert_to_employee)) }}"
                                                    class="dropdown-item" data-bs-toggle="tooltip" data-bs-placement="top"
                                                    title="{{ __('View') }}">
                                                    <i class="ti ti-eye "></i>
                                                    <span class="ms-1">{{ __('View') }}</span>
                                                </a>
                                            @endif
                                        @endpermission
                                        @permission('job edit')
                                            <a  data-url="{{ route('job.on.board.edit', $job->id) }}" data-ajax-popup="true" data-size="md"
                                                class="dropdown-item" data-bs-whatever="{{ __('Edit Job') }}"
                                                data-bs-toggle="tooltip" data-title="{{ __('Edit Job') }}"><i
                                                    class="ti ti-pencil"></i>
                                                    <span class="ms-1">{{ __('Edit') }}</span>
                                               </a>
                                        @endpermission
                                        @permission('job delete')
                                            {!! Form::open(['method' => 'DELETE', 'route' => ['job.on.board.delete', $job->id]]) !!}
                                            <a href="#!" class="dropdown-item  show_confirm" data-bs-toggle="tooltip" data-text="{{ __('This action can not be undone. Do you want to continue?') }}">
                                                <i class="ti ti-trash text-danger"></i>
                                                <span class="ms-1 text-danger">{{ __('Delete') }}</span>
                                            </a>
                                            {!! Form::close() !!}
                                        @endpermission
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body text-center p-3">
                        <div class="bottom-icons d-flex flex-wrap align-items-center justify-content-between">
                            <div class="edit-btn-wrp d-flex flex-wrap align-items-center">
                                @if (Laratrust::hasPermission('jobonboard convert') ||
                                        Laratrust::hasPermission('job edit') ||
                                        Laratrust::hasPermission('job delete'))


                                    @permission('jobonboard convert')
                                        @if (
                                            $job->status == 'confirm' &&
                                                $job->convert_to_employee == 0 &&
                                                module_is_active('Hrm') &&
                                                $job->applications->jobs->recruitment_type == 'internal')
                                            <a class="btn btn-sm border" href="{{ route('job.on.board.convert', $job->id) }}"
                                                data-bs-toggle="tooltip" data-bs-placement="top"
                                                title="{{ __('Convert to Employee') }}">
                                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <g clip-path="url(#clip0_77_206)">
                                                        <path
                                                            d="M3.64583 8.35356C3.74349 8.45121 3.8724 8.50004 4 8.50004C4.1276 8.50004 4.25651 8.45121 4.35417 8.35356C4.54818 8.15824 4.54818 7.84184 4.35417 7.64652L1.87378 5.16671H15.3333C15.6094 5.16671 15.8333 4.94275 15.8333 4.66671C15.8333 4.39067 15.6094 4.16671 15.3333 4.16671H1.87378L4.35417 1.68689C4.54818 1.49158 4.54818 1.17517 4.35417 0.979858C4.15885 0.784546 3.84115 0.784546 3.64583 0.979858L0.3125 4.31319C0.11849 4.5085 0.11849 4.82491 0.3125 5.02022L3.64583 8.35356Z"
                                                            fill="#060606" />
                                                        <path
                                                            d="M12.3545 7.64648C12.1592 7.45117 11.8415 7.45117 11.6462 7.64648C11.4521 7.8418 11.4521 8.1582 11.6462 8.35352L14.1265 10.8333H0.666992C0.39095 10.8333 0.166992 11.0573 0.166992 11.3333C0.166992 11.6094 0.390951 11.8333 0.666992 11.8333H14.1265L11.6462 14.3132C11.4521 14.5085 11.4521 14.8249 11.6462 15.0202C11.7438 15.1178 11.8727 15.1667 12.0003 15.1667C12.1279 15.1667 12.2568 15.1178 12.3545 15.0202L15.6878 11.6868C15.8818 11.4915 15.8818 11.1751 15.6878 10.9798L12.3545 7.64648Z"
                                                            fill="#060606" />
                                                    </g>
                                                    <defs>
                                                        <clipPath id="clip0_77_206">
                                                            <rect width="16" height="16" fill="white" />
                                                        </clipPath>
                                                    </defs>
                                                </svg>
                                            </a>
                                        @elseif($job->status == 'confirm' && $job->convert_to_employee != 0)
                                            <a class="btn btn-sm border"
                                                href="{{ route('employee.show', \Crypt::encrypt($job->convert_to_employee)) }}"
                                                data-bs-toggle="tooltip" data-bs-placement="top"
                                                title="{{ __('View') }}">
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
                                        @endif
                                    @endpermission
                                    @permission('jobonboard edit')
                                        <a data-url="{{ route('job.on.board.edit', $job->id) }}" data-ajax-popup="true"
                                            data-title="{{ __('Edit Job On-Boarding') }}" data-bs-toggle="tooltip"
                                            data-bs-placement="top" title="{{ __('Edit') }}" class="btn btn-sm border">
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
                                    @permission('jobonboard delete')
                                        {{ Form::open(['route' => ['job.on.board.delete', $job->id], 'class' => 'm-0']) }}
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
                            @if ($job->status == 'pending')
                                <span
                                    class="badge bg-warning p-2 px-3">{{ Workdo\Recruitment\Entities\JobOnBoard::$status[$job->status] }}</span>
                            @elseif($job->status == 'cancel')
                                <span
                                    class="badge bg-danger p-2 px-3">{{ Workdo\Recruitment\Entities\JobOnBoard::$status[$job->status] }}</span>
                            @else
                                <span
                                    class="badge bg-success p-2 px-3">{{ Workdo\Recruitment\Entities\JobOnBoard::$status[$job->status] }}</span>
                            @endif
                        </div>

                    </div>
                </div>
            </div>
        @endforeach
        @permission('job create')
            <div class="col-xxl-3 col-xl-4 col-md-6">
                <a class="btn-addnew-project border-primary" data-url="{{ route('job.on.board.create', 0) }}"
                    data-ajax-popup="true" data-title="{{ __('Create Job On-Boarding') }}">
                    <div class="bg-primary proj-add-icon">
                        <i class="ti ti-plus my-2"></i>
                    </div>
                    <h6 class="my-2 text-center">{{ __('Job On-Boarding') }}</h6>
                    <p class="text-muted text-center">{{ __('Click here to Create New Job On-Boarding') }}</p>
                </a>
            </div>
        @endpermission
    </div>
    {!! $jobOnBoards->links('vendor.pagination.global-pagination') !!}
@endsection
