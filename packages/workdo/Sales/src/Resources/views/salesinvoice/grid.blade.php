@extends('layouts.main')
@section('page-title')
    {{ __('Manage Sales Invoice') }}
@endsection
@section('page-breadcrumb')
    {{ __('Sales Invoice') }}
@endsection
@section('page-action')
    <div class="d-flex">
        @stack('addButtonHook')
        <a href="{{ route('salesinvoice.index') }}" class="btn btn-sm btn-primary btn-icon me-2" data-bs-toggle="tooltip"
            title="{{ __('List View') }}">
            <i class="ti ti-list text-white"></i>
        </a>
        @permission('salesinvoice create')
            <a data-size="lg" data-url="{{ route('salesinvoice.create', ['invoice', 0]) }}" data-ajax-popup="true"
                data-bs-toggle="tooltip" data-title="{{ __('Create Sales Invoice') }}" title=" {{ __('Create') }}"
                class="btn btn-sm btn-primary btn-icon">
                <i class="ti ti-plus"></i>
            </a>
        @endpermission
    </div>
@endsection
@push('scripts')
    <script src="{{ asset('js/letter.avatar.js') }}"></script>
@endpush
@section('content')
    <div class="row">
        <div class="filters-content">
            <div class="row mb-4 project-wrp d-flex">
                @foreach ($invoices as $invoice)
                    <div class="col-xxl-3 col-xl-4 col-md-6 col-12 All {{ $invoice->status }}">
                        <div class="project-card">
                            <div class="project-card-inner">
                                <div class="project-card-header d-flex justify-content-between">
                                    @if ($invoice->status == 0)
                                        <span class="badge bg-secondary p-2 px-3"
                                            style="width: 91px;">{{ __(Workdo\Sales\Entities\SalesInvoice::$status[$invoice->status]) }}</span>
                                    @elseif($invoice->status == 1)
                                        <span class="badge bg-danger p-2 px-3"
                                            style="width: 91px;">{{ __(Workdo\Sales\Entities\SalesInvoice::$status[$invoice->status]) }}</span>
                                    @elseif($invoice->status == 2)
                                        <span class="badge bg-warning p-2 px-3"
                                            style="width: 91px;">{{ __(Workdo\Sales\Entities\SalesInvoice::$status[$invoice->status]) }}</span>
                                    @elseif($invoice->status == 3)
                                        <span class="badge bg-success p-2 px-3"
                                            style="width: 91px;">{{ __(Workdo\Sales\Entities\SalesInvoice::$status[$invoice->status]) }}</span>
                                    @elseif($invoice->status == 4)
                                        <span class="badge bg-info p-2 px-3"
                                            style="width: 91px;">{{ __(Workdo\Sales\Entities\SalesInvoice::$status[$invoice->status]) }}</span>
                                    @endif
                                    <button type="button"
                                        class="btn btn-light dropdown-toggle d-flex align-items-center justify-content-center"
                                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="ti ti-dots-vertical text-black"></i>
                                    </button>

                                    <div class="dropdown-menu dropdown-menu-end pointer">
                                        @permission('salesinvoice create')
                                            {!! Form::open([
                                                'method' => 'get',
                                                'route' => ['salesinvoice.duplicate', $invoice->id],
                                                'id' => 'duplicate-form-' . $invoice->id,
                                            ]) !!}
                                            <a href="#" class="dropdown-item show_confirm" data-bs-toggle="tooltip"
                                                data-title="{{ __('Duplicate') }}"><i
                                                    class="ti ti-copy me-1"></i>{{ __('Duplicate') }}</a>
                                            {!! Form::close() !!}
                                        @endpermission
                                        @permission('salesinvoice show')
                                            <a href="{{ route('salesinvoice.show', $invoice->id) }}" class="dropdown-item"
                                                data-title="{{ __('Details') }}">
                                                <i class="ti ti-eye me-1"></i> <span>{{ __('View') }}</span>
                                            </a>
                                        @endpermission
                                        @permission('salesinvoice edit')
                                            <a href="{{ route('salesinvoice.edit', $invoice->id) }}" class="dropdown-item"
                                                data-title="{{ __('Edit Quote') }}">
                                                <i class="ti ti-pencil me-1"></i> <span>{{ __('Edit') }}</span>
                                            </a>
                                        @endpermission
                                        @permission('salesinvoice delete')
                                            {!! Form::open(['method' => 'DELETE', 'route' => ['salesinvoice.destroy', $invoice->id]]) !!}
                                            <a href="javascript:void(0)"
                                                class="dropdown-item text-danger delete-popup bs-pass-para show_confirm"
                                                data-confirm="{{ __('Are You Sure?') }}"
                                                data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                data-confirm-yes="delete-form-{{ $invoice->id }}">
                                                <i class="ti ti-trash me-1"></i> <span>{{ __('Delete') }}</span>
                                            </a>
                                            @method('DELETE')
                                            {!! Form::close() !!}
                                        @endpermission
                                    </div>
                                </div>
                                <div class="project-card-content">
                                    <div class="project-content-top">
                                        <div class="user-info  d-flex align-items-center">
                                            <h2 class="h5 mb-0">
                                                @permission('salesinvoice show')
                                                <a href="{{ route('salesinvoice.show', $invoice->id) }}"
                                                    tabindex="0"
                                                    class=""><span class="text-primary">{{Workdo\Sales\Entities\SalesInvoice::invoiceNumberFormat($invoice->invoice_id) }}</span></a>
                                            @else
                                                <a tabindex="0"
                                                    class="">{{Workdo\Sales\Entities\SalesInvoice::invoiceNumberFormat($invoice->invoice_id) }}</a>
                                            @endpermission
                                            </h2>
                                        </div>
                                        <div class="row align-items-center mt-3">
                                            <div class="col-6">
                                                <h6 class="mb-0 text-break">
                                                    {{ currency_format_with_sym($invoice->getTotal()) }}</h6>
                                                <span class="text-sm text-muted">{{ __('Total Amount') }}</span>
                                            </div>
                                            <div class="col-6">
                                                <h6 class="mb-0 text-break">
                                                    {{ currency_format_with_sym($invoice->getSubTotal()) }}
                                                </h6>
                                                <span class="text-sm text-muted">{{ __('Sub Total') }}</span>
                                            </div>
                                        </div>
                                        <div class="row align-items-center mt-3">
                                            <div class="col-6">
                                                <h6 class="mb-0 text-break">
                                                    {{ currency_format_with_sym($invoice->getTotalTax()) }}
                                                </h6>
                                                <span class="text-sm text-muted">{{ __('Total Tax') }}</span>
                                            </div>
                                            <div class="col-6">
                                                <h6 class="mb-0 text-break">
                                                    {{ company_date_formate($invoice->created_at) }}</h6>
                                                <span class="text-sm text-muted">{{ __('Start Date') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
                @auth('web')
                @permission('salesinvoice create')
                        <div class="col-xxl-3 col-xl-4 col-md-6 col-12 All">
                            <div class="project-card-inner">
                                <a href="javascript:void(0)" class="btn-addnew-project " data-ajax-popup="true" data-size="lg"
                                    data-title="{{ __('Create Sales Invoice') }}"
                                    data-url="{{ route('salesinvoice.create', ['invoice', 0]) }}">
                                    <div class="bg-primary proj-add-icon">
                                        <i class="ti ti-plus"></i>
                                    </div>
                                    <h6 class="my-2 text-center">{{ __('Add Sales Invoice') }}</h6>
                                    <p class="text-muted text-center mb-0">{{ __('Click here to add New Sales Invoice') }}</p>
                                </a>
                            </div>
                        </div>
                    @endpermission
                @endauth
            </div>
            {!! $invoices->links('vendor.pagination.global-pagination') !!}
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).on('click', '#billing_data', function() {
            $("[name='shipping_address']").val($("[name='billing_address']").val());
            $("[name='shipping_city']").val($("[name='billing_city']").val());
            $("[name='shipping_state']").val($("[name='billing_state']").val());
            $("[name='shipping_country']").val($("[name='billing_country']").val());
            $("[name='shipping_postalcode']").val($("[name='billing_postalcode']").val());
        })

        $(document).on('change', 'select[name=opportunity]', function() {

            var opportunities = $(this).val();
            getaccount(opportunities);
        });

        function getaccount(opportunities_id) {
            $.ajax({
                url: '{{ route('salesinvoice.getaccount') }}',
                type: 'POST',
                data: {
                    "opportunities_id": opportunities_id,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(data) {
                    if(data != '')                 
                    {
                        $('#amount').val(data.opportunitie.amount);
                        $('#account_name').val(data.account.name);
                        $('#account_id').val(data.account.id);
                        $('#billing_address').val(data.account.billing_address);
                        $('#shipping_address').val(data.account.shipping_address);
                        $('#billing_city').val(data.account.billing_city);
                        $('#billing_state').val(data.account.billing_state);
                        $('#shipping_city').val(data.account.shipping_city);
                        $('#shipping_state').val(data.account.shipping_state);
                        $('#billing_country').val(data.account.billing_country);
                        $('#billing_postalcode').val(data.account.billing_postalcode);
                        $('#shipping_country').val(data.account.shipping_country);
                        $('#shipping_postalcode').val(data.account.shipping_postalcode);
                    }
                }
            });
        }
    </script>
@endpush
