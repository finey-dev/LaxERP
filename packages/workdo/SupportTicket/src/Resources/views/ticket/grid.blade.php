@extends('layouts.main')
@section('page-title')
    {{ __('Manage Tickets') }}
@endsection
@section('title')
    {{ __('Tickets') }}
@endsection
@section('page-breadcrumb')
    {{ __('Tickets') }}
@endsection
@section('page-action')
    <div class="d-flex align-items-center">
        <select class="form-select me-2" id="projects"
            onchange="this.options[this.selectedIndex].value && (window.location = this.options[this.selectedIndex].value);"
            style="width: 121px;">
            <option value="{{ route('support-tickets.grid') }}">{{ __('All Tickets') }}</option>
            <option value="{{ route('support-tickets.grid', 'in-progress') }}"
                @if ($status == 'in-progress') selected @endif>{{ __('In Progress') }}</option>
            <option value="{{ route('support-tickets.grid', 'on-hold') }}" @if ($status == 'on-hold') selected @endif>
                {{ __('On Hold') }}</option>
            <option value="{{ route('support-tickets.grid', 'closed') }}" @if ($status == 'closed') selected @endif>
                {{ __('Closed') }}</option>
        </select>

        {{-- Add Button Hook --}}
        @stack('addButtonHook')
        
        <a href="{{ route('support-tickets.index') }}" class="btn btn-sm btn-primary btn-icon me-2" data-bs-toggle="tooltip"
            title="{{ __('List View') }}">
            <i class="ti ti-list text-white"></i>
        </a>

        @permission('ticket create')
            <a href="{{ route('support-tickets.create') }}" data-size="lg" data-bs-toggle="tooltip"
                data-title="{{ __('Create New Ticket') }}" title="{{ __('Create') }}"
                class="btn btn-sm btn-primary btn-icon">
                <i class="ti ti-plus"></i>
            </a>
        @endpermission
    </div>
@endsection
@section('filter')
@endsection
@section('content')
    <div class="row  row-gap-2 mb-4">
        @foreach ($tickets as $index => $ticket)
            <div class="col-xxl-3 col-xl-4 col-md-6">
                <div class="card user-card">
                    <div class="card-header border border-bottom p-3 h-100">
                        <div class="user-img-wrp d-flex align-items-center">
                            <div class="user-image rounded border-2 border border-primary">
                                <img alt="user-image" class="h-100 w-100"
                                    @if (!empty($ticket->avatar)) src="{{ !empty($ticket->avatar) ? asset(Storage::url('upload/profile/' . $ticket->avatar)) : asset(url('./assets/img/clients/160x160/img-1.png')) }}" @else  avatar="{{ $ticket->name }}" @endif>
                            </div>
                            <div class="user-content">
                                <h4 class="mb-2 text-break"><a
                                        href="{{ route('ticket.view', [$ticket->workspace->slug, \Illuminate\Support\Facades\Crypt::encrypt($ticket->ticket_id)]) }}">{{ $ticket->name }}</a>
                                </h4>

                                <span class="text-dark text-md">
                                    {{ $ticket->email }}</span>

                            </div>
                        </div>

                    </div>
                    <div class="card-body p-3 text-center">
                        <div class="bottom-icons d-flex flex-wrap align-items-center justify-content-between">
                            <div class="edit-btn-wrp d-flex flex-wrap align-items-center">
                                @if (Laratrust::hasPermission('ticket show') ||
                                        Laratrust::hasPermission('ticket edit') ||
                                        Laratrust::hasPermission('ticket delete'))
                                    @permission('ticket show')
                                        <a class="btn btn-sm border"
                                            href="{{ route('ticket.view', [$ticket->workspace->slug, \Illuminate\Support\Facades\Crypt::encrypt($ticket->ticket_id)]) }}"
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
                                    @permission('ticket show')
                                        <a href="{{ route('support-tickets.edit', $ticket->id) }}" data-size="md"
                                            data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Edit & Reply') }}"
                                            class="btn btn-sm border">
                                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M11.3333 6.33329H4.93993L5.8066 5.47329C5.93213 5.34776 6.00266 5.1775 6.00266 4.99996C6.00266 4.82243 5.93213 4.65216 5.8066 4.52663C5.68106 4.40109 5.5108 4.33057 5.33326 4.33057C5.15573 4.33057 4.98547 4.40109 4.85993 4.52663L2.85993 6.52663C2.79924 6.59003 2.75166 6.66479 2.71993 6.74663C2.65325 6.90894 2.65325 7.09099 2.71993 7.25329C2.75166 7.33513 2.79924 7.40989 2.85993 7.47329L4.85993 9.47329C4.92191 9.53578 4.99564 9.58538 5.07688 9.61922C5.15812 9.65307 5.24526 9.67049 5.33326 9.67049C5.42127 9.67049 5.50841 9.65307 5.58965 9.61922C5.67089 9.58538 5.74462 9.53578 5.8066 9.47329C5.86908 9.41132 5.91868 9.33759 5.95253 9.25635C5.98637 9.17511 6.0038 9.08797 6.0038 8.99996C6.0038 8.91195 5.98637 8.82482 5.95253 8.74358C5.91868 8.66234 5.86908 8.5886 5.8066 8.52663L4.93993 7.66663H11.3333C11.5101 7.66663 11.6796 7.73687 11.8047 7.86189C11.9297 7.98691 11.9999 8.15648 11.9999 8.33329V11C11.9999 11.1768 12.0702 11.3463 12.1952 11.4714C12.3202 11.5964 12.4898 11.6666 12.6666 11.6666C12.8434 11.6666 13.013 11.5964 13.138 11.4714C13.263 11.3463 13.3333 11.1768 13.3333 11V8.33329C13.3333 7.80286 13.1226 7.29415 12.7475 6.91908C12.3724 6.54401 11.8637 6.33329 11.3333 6.33329Z"
                                                    fill="#060606" />
                                            </svg>
                                        </a>
                                    @endpermission
                                    @permission('ticket delete')
                                        {{ Form::open(['route' => ['support-tickets.destroy', $ticket->id], 'class' => 'm-0']) }}
                                        @method('DELETE')
                                        <a href="#!" aria-label="Delete" data-confirm="{{ __('Are You Sure?') }}"
                                            data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                            data-confirm-yes="delete-form-{{ $ticket->id }}" data-bs-toggle="tooltip"
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
                                @else
                                    <a class="btn btn-sm border login-disabled" data-title="{{ __('Lock') }}"
                                        data-bs-original-title="{{ __('Lock') }}" data-bs-toggle="tooltip">
                                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M8.00009 0.470581C5.64715 0.470581 3.76479 2.35293 3.76479 4.70588V6.58823C2.96479 6.58823 2.35303 7.19999 2.35303 7.99999V14.1176C2.35303 14.9176 2.96479 15.5294 3.76479 15.5294H12.2354C13.0354 15.5294 13.6471 14.9176 13.6471 14.1176V7.99999C13.6471 7.19999 13.0354 6.58823 12.2354 6.58823V4.70588C12.2354 2.35293 10.353 0.470581 8.00009 0.470581ZM12.706 7.99999V14.1176C12.706 14.4 12.5177 14.5882 12.2354 14.5882H3.76479C3.48244 14.5882 3.2942 14.4 3.2942 14.1176V7.99999C3.2942 7.71764 3.48244 7.5294 3.76479 7.5294H12.2354C12.5177 7.5294 12.706 7.71764 12.706 7.99999ZM4.70597 6.58823V4.70588C4.70597 2.87058 6.16479 1.41176 8.00009 1.41176C9.83538 1.41176 11.2942 2.87058 11.2942 4.70588V6.58823H4.70597Z"
                                                fill="#060606" />
                                            <path
                                                d="M8.00014 8.94116C7.20014 8.94116 6.58838 9.55293 6.58838 10.3529C6.58838 10.9647 6.96485 11.4823 7.52956 11.6706V12.7059C7.52956 12.9882 7.71779 13.1765 8.00014 13.1765C8.2825 13.1765 8.47073 12.9882 8.47073 12.7059V11.6706C9.03544 11.4823 9.41191 10.9647 9.41191 10.3529C9.41191 9.55293 8.80014 8.94116 8.00014 8.94116ZM8.00014 10.8235C7.71779 10.8235 7.52956 10.6353 7.52956 10.3529C7.52956 10.0706 7.71779 9.88234 8.00014 9.88234C8.2825 9.88234 8.47073 10.0706 8.47073 10.3529C8.47073 10.6353 8.2825 10.8235 8.00014 10.8235Z"
                                                fill="#060606" />
                                        </svg>
                                    </a>
                                @endif
                            </div>
                            <span
                                class="badge @if ($ticket->status == 'In Progress') bg-warning @elseif($ticket->status == 'On Hold') bg-danger @else bg-primary @endif p-2">{{ __($ticket->status) }}</span>
                        </div>

                    </div>
                </div>
            </div>
        @endforeach

        @permission('ticket create')
            <div class="col-xxl-3 col-xl-4 col-md-6">
                <a href="{{ route('support-tickets.create') }}" class="btn-addnew-project border-primary" data-size="lg"
                    data-title="{{ __('Create New Tickets') }}">
                    <div class="bg-primary proj-add-icon">
                        <i class="ti ti-plus my-2"></i>
                    </div>
                    <h6 class="my-2text-center">{{ __('New Ticketss') }}</h6>
                    <p class="text-muted text-center">{{ __('Click here to add New Ticketss') }}</p>
                </a>
            </div>
        @endpermission
    </div>
    {!! $tickets->links('vendor.pagination.global-pagination') !!}
@endsection
@push('scripts')
    <script src="{{ asset('packages/workdo/SupportTicket/src/Resources/assets/js/letter.avatar.js') }}"></script>
@endpush
