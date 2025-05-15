@extends('layouts.main')
@section('page-title')
    {{ __('Diagnosis Detail') }}
@endsection
@section('page-breadcrumb')
    {{ __('Diagnosis Detail') }}
@endsection
@push('css')
    <style>
        #card-element {
            border: 1px solid #a3afbb !important;
            border-radius: 10px !important;
            padding: 10px !important;
        }
    </style>
@endpush
@push('scripts')
    <script type="text/javascript">
        $('.cp_link').on('click', function() {
            var value = $(this).attr('data-link');
            var $temp = $("<input>");
            $("body").append($temp);
            $temp.val(value).select();
            document.execCommand("copy");
            $temp.remove();
            toastrs('success', '{{ __('Link Copy on Clipboard') }}', 'success')
        });
    </script>
@endpush
@section('page-action')
{{-- <div>
    <div class="float-end">
        <a href="#" class="btn btn-sm btn-primary  cp_link"
            data-link="{{ route('pay.invoice', \Illuminate\Support\Facades\Crypt::encrypt($invoice->id)) }}"
            data-bs-toggle="tooltip" title="{{ __('Copy') }}" data-original-title="{{ __('Click to copy invoice link') }}">
            <span class="btn-inner--icon text-white"><i class="ti ti-file"></i></span>
        </a>
    </div>
</div> --}}
<div>
    <div class="float-end">
        @permission('machine diagnosis edit')
            <a href="{{ route('machine-repair-invoice.edit', \Crypt::encrypt($invoice->id)) }}"
                class="btn btn-sm btn-info" data-bs-toggle="tooltip"
                data-original-title="{{ __('Edit') }}"><i
                    class="ti ti-pencil mr-2"></i>{{ __('Edit') }}</a>
        @endpermission
        {{-- @if ($invoice->status != 0) --}}
            @permission('machine invoice payment create')
                <a href="#" data-url="{{ route('machine.invoice.payment', $invoice->id) }}"
                    data-ajax-popup="true" data-title="{{ __('Add Payment') }}" class="btn btn-sm btn-primary"
                    data-original-title="{{ __('Add Payment') }}"><i
                        class="ti ti-report-money mr-2"></i>{{ __('Add Payment') }}</a> <br>
            @endpermission
        {{-- @endif --}}
    </div>
</div>
@endsection
@section('content')

    <div class="row justify-content-between align-items-center mb-3">
        <div class="col-md-6">
            <ul class="nav nav-pills nav-fill cust-nav information-tab" id="pills-tab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="invoice-tab" data-bs-toggle="pill"
                        data-bs-target="#invoice" type="button">{{ __('Diagnosis') }}</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="receipt-summary-tab" data-bs-toggle="pill"
                        data-bs-target="#receipt-summary" type="button">{{ __('Receipt Summary') }}</button>
                </li>
            </ul>
        </div>

        <div class="col-md-6 d-flex align-items-center justify-content-between justify-content-md-end">
            <div class="all-button-box mx-2">
                <a href="{{ route('machine.invoice.pdf', \Crypt::encrypt($invoice->id)) }}" target="_blank"
                    class="btn btn-sm btn-primary">{{ __('Download') }}</a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="tab-content" id="pills-tabContent">
                <div class="tab-pane fade active show" id="invoice" role="tabpanel"
                aria-labelledby="pills-user-tab-1">
                    <div class="card">
                        <div class="card-body">
                            <div class="invoice">
                                <div class="invoice-print">
                                    <div class="row invoice-title mt-2">
                                        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 col-12">
                                            <h2>{{ __('Diagnosis') }}</h2>
                                        </div>
                                        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 col-12 text-end">
                                            <h3 class="invoice-number">
                                                {{ \Workdo\MachineRepairManagement\Entities\MachineInvoice::machineInvoiceNumberFormat($invoice->invoice_id) }}</h3>
                                        </div>
                                        <div class="col-12">
                                            <hr>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col text-start">
                                            <div class="d-flex align-items-center justify-content-start">
                                                <p class="font-style">
                                                    <strong>{{__('Estimated Repair Time')}} :</strong><br>
                                                    {{ !empty($invoice->estimated_time) ? $invoice->estimated_time.__(' Hours') : '0'.__(' Hours') }}<br>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="col text-end">
                                            <div class="d-flex align-items-center justify-content-end">
                                                <div class="me-4">
                                                    <p>
                                                        <strong>{{ __('Issue Date') }} :</strong><br>
                                                        {{ company_date_formate($invoice->issue_date) }}<br><br>
                                                    </p>
                                                </div>
                                                <div>
                                                    <p>
                                                        <strong>{{ __('Due Date') }} :</strong><br>
                                                        {{ company_date_formate($invoice->due_date) }}<br><br>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        @if (!empty($invoice->request_id) && !empty($invoice->customer_name) && !empty($invoice->customer_email))
                                            <div class="col">
                                                <p class="font-style">
                                                    <strong>{{ __('Request Number') }} :</strong><br>
                                                    {{ !empty($invoice->request_id) ? \Workdo\MachineRepairManagement\Entities\MachineRepairRequest::machineRepairNumberFormat($invoice->request_id) : '' }}<br>
                                                </p>
                                                <p class="font-style">
                                                    <strong>{{ __('Billed To') }} :</strong><br>
                                                    {{ !empty($invoice->customer_name) ? $invoice->customer_name : '' }}<br>
                                                    {{ !empty($invoice->customer_email) ? $invoice->customer_email : '' }}<br>
                                                </p>
                                            </div>
                                            @php
                                                $repair_request = \Workdo\MachineRepairManagement\Entities\MachineRepairRequest::find($invoice->request_id);
                                                $machine_details = \Workdo\MachineRepairManagement\Entities\Machine::find($repair_request->machine_id);
                                            @endphp
                                            <div class="col">
                                                <p class="font-style">
                                                    <strong>{{ __('Machine Details') }} :</strong><br>
                                                    <dl class="row align-items-center">
                                                        <dt class="col-sm-4" style="font-weight: 600;">{{ __('Name') }}</dt>
                                                        <dd class="col-sm-8  ms-0" style="margin-bottom: 0px;"> : {{ !empty($machine_details->name) ? $machine_details->name : '' }}</dd>
                                                        <dt class="col-sm-4" style="font-weight: 600;">{{ __('Model') }}</dt>
                                                        <dd class="col-sm-8  ms-0" style="margin-bottom: 0px;"> : {{ !empty($machine_details->model) ? $machine_details->model : '' }}</dd>
                                                        <dt class="col-sm-4" style="font-weight: 600;">{{ __('Manufacturer') }}</dt>
                                                        <dd class="col-sm-8  ms-0" style="margin-bottom: 0px;"> : {{ !empty($machine_details->manufacturer) ? $machine_details->manufacturer : '' }}</dd>
                                                    </dl>
                                                </p>
                                            </div>
                                        @endif

                                        <div class="col">
                                            <div class="col">
                                                <div class="float-end mt-3">
                                                    {!! DNS2D::getBarcodeHTML(
                                                        route('machine-repair-invoice.show', \Illuminate\Support\Facades\Crypt::encrypt($invoice->id)),
                                                        'QRCODE',
                                                        2,
                                                        2,
                                                    ) !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col">
                                            <p>
                                                <strong>{{ __('Status') }} :</strong><br>
                                                @if ($invoice->status == 0)
                                                    <span
                                                        class="badge fix_badge p-1 px-3 bg-primary">{{ __(\Workdo\MachineRepairManagement\Entities\MachineInvoice::$statues[$invoice->status]) }}</span>
                                                @elseif($invoice->status == 1)
                                                    <span
                                                        class="badge fix_badge p-1 px-3 bg-secondary">{{ __(\Workdo\MachineRepairManagement\Entities\MachineInvoice::$statues[$invoice->status]) }}</span>
                                                @elseif($invoice->status == 2)
                                                    <span
                                                        class="badge fix_badge p-1 px-3 bg-warning">{{ __(\Workdo\MachineRepairManagement\Entities\MachineInvoice::$statues[$invoice->status]) }}</span>
                                                @elseif($invoice->status == 3)
                                                    <span
                                                        class="badge fix_badge p-1 px-3 bg-danger">{{ __(\Workdo\MachineRepairManagement\Entities\MachineInvoice::$statues[$invoice->status]) }}</span>
                                                @endif
                                            </p>
                                        </div>

                                    </div>
                                    <div class="row mt-4">
                                        <div class="col-md-12">
                                            <div class="font-weight-bold">{{ __('Item Summary') }}</div>
                                            <small>{{ __('All items here cannot be deleted.') }}</small>
                                            <div class="table-responsive mt-2">
                                                <table class="table mb-0 table-striped">
                                                    <tr>
                                                        <th data-width="40" class="text-dark">#</th>
                                                        <th class="text-dark">{{ __('Item Type') }}</th>
                                                        <th class="text-dark">{{ __('Item') }}</th>
                                                        <th class="text-dark">{{ __('Quantity') }}</th>
                                                        <th class="text-dark">{{ __('Rate') }}</th>
                                                        <th class="text-dark">{{ __('Discount') }}</th>
                                                        <th class="text-dark">{{ __('Tax') }}</th>
                                                        <th class="text-dark">{{ __('Description') }}</th>
                                                        <th class="text-right text-dark" width="12%">{{ __('Price') }}<br>
                                                            <small
                                                                class="text-danger font-weight-bold">{{ __('After discount & tax') }}</small>
                                                        </th>
                                                    </tr>
                                                    @php
                                                        $totalQuantity = 0;
                                                        $totalRate = 0;
                                                        $totalTaxPrice = 0;
                                                        $totalDiscount = 0;
                                                        $taxesData = [];
                                                        $TaxPrice_array = [];
                                                    @endphp
                                                    @foreach ($iteams as $key => $iteam)
                                                        @if (!empty($iteam->tax))
                                                            @php
                                                                $taxes = \Workdo\MachineRepairManagement\Entities\MachineInvoice::tax($iteam->tax);
                                                                $totalQuantity += $iteam->quantity;
                                                                $totalRate += $iteam->price;
                                                                $totalDiscount += $iteam->discount;
                                                                foreach ($taxes as $taxe) {
                                                                    $taxDataPrice = \Workdo\MachineRepairManagement\Entities\MachineInvoice::taxRate($taxe->rate, $iteam->price, $iteam->quantity, $iteam->discount);
                                                                    if (array_key_exists($taxe->name, $taxesData)) {
                                                                        $taxesData[$taxe->name] = $taxesData[$taxe->name] + $taxDataPrice;
                                                                    } else {
                                                                        $taxesData[$taxe->name] = $taxDataPrice;
                                                                    }
                                                                }
                                                            @endphp
                                                        @endif
                                                        <tr>
                                                            <td>{{ $key + 1 }}</td>
                                                            <td>{{ !empty($iteam->product_type) ? Str::ucfirst($iteam->product_type) : '--' }}
                                                            </td>
                                                            <td>{{ !empty($iteam->product()) ? $iteam->product()->name : '' }}</td>
                                                            <td>{{ $iteam->quantity }}</td>
                                                            <td>{{ currency_format_with_sym($iteam->price) }}</td>
                                                            <td>
                                                                {{ currency_format_with_sym($iteam->discount) }}
                                                            </td>
                                                            <td>

                                                                @if (!empty($iteam->tax))
                                                                    <table>
                                                                        @php
                                                                            $totalTaxRate = 0;
                                                                            $data = 0;
                                                                        @endphp
                                                                        @foreach ($taxes as $tax)
                                                                            @php
                                                                                $taxPrice = \Workdo\MachineRepairManagement\Entities\MachineInvoice::taxRate($tax->rate, $iteam->price, $iteam->quantity, $iteam->discount);
                                                                                $totalTaxPrice += $taxPrice;
                                                                                $data += $taxPrice;
                                                                            @endphp
                                                                            <tr>
                                                                                <td>{{ $tax->name . ' (' . $tax->rate . '%)' }}</td>
                                                                                <td>{{ currency_format_with_sym($taxPrice) }}</td>
                                                                            </tr>
                                                                        @endforeach
                                                                        @php
                                                                            array_push($TaxPrice_array, $data);
                                                                        @endphp
                                                                    </table>
                                                                @else
                                                                    -
                                                                @endif
                                                            </td>

                                                            <td style="white-space: break-spaces;">{{ !empty($iteam->description) ? $iteam->description : '-' }}</td>
                                                            @php
                                                                $tr_tex = array_key_exists($key, $TaxPrice_array) == true ? $TaxPrice_array[$key] : 0;
                                                            @endphp
                                                            <td class="">{{ currency_format_with_sym($iteam->price * $iteam->quantity - $iteam->discount + $tr_tex) }}
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                    <tfoot>
                                                        <tr>
                                                            <td></td>
                                                            <td></td>
                                                            <td><b>{{ __('Total') }}</b></td>
                                                            <td><b>{{ $totalQuantity }}</b></td>
                                                            <td><b>{{ currency_format_with_sym($totalRate) }}</b></td>
                                                            <td><b>{{ currency_format_with_sym($totalDiscount) }}</b></td>
                                                            <td><b>{{ currency_format_with_sym($totalTaxPrice) }}</b></td>
                                                            <td></td>
                                                        </tr>
                                                        @php
                                                            $colspan = 7;
                                                        @endphp
                                                        <tr>
                                                            <td colspan="{{ $colspan }}"></td>
                                                            <td class="text-right"><b>{{ __('Sub Total') }}</b></td>
                                                            <td class="text-right">
                                                                {{ currency_format_with_sym($invoice->getSubTotal()) }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="{{ $colspan }}"></td>
                                                            <td class="text-right"><b>{{ __('Discount') }}</b></td>
                                                            <td class="text-right">
                                                                {{ currency_format_with_sym($invoice->getTotalDiscount()) }}</td>
                                                        </tr>
                                                        @if (!empty($taxesData))
                                                            @foreach ($taxesData as $taxName => $taxPrice)
                                                                <tr>
                                                                    <td colspan="{{ $colspan }}"></td>
                                                                    <td class="text-right"><b>{{ $taxName }}</b></td>
                                                                    <td class="text-right">{{ currency_format_with_sym($taxPrice) }}
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        @endif
                                                        <tr>
                                                            <td colspan="{{ $colspan }}"></td>
                                                            <td class="blue-text text-right"><b>{{ __('Service Charge') }}</b></td>
                                                            <td class="blue-text text-right">
                                                                {{ currency_format_with_sym($invoice->getServiceCharge()) }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="{{ $colspan }}"></td>
                                                            <td class="blue-text text-right"><b>{{ __('Total') }}</b></td>
                                                            <td class="blue-text text-right">
                                                                {{ currency_format_with_sym($invoice->getTotal()) }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="{{ $colspan }}"></td>
                                                            <td class="text-right"><b>{{ __('Paid') }}</b></td>
                                                            <td class="text-right">
                                                                {{ currency_format_with_sym($invoice->getTotal() - $invoice->getDue()) }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="{{ $colspan }}"></td>
                                                            <td class="text-right"><b>{{ __('Due') }}</b></td>
                                                            <td class="text-right">{{ currency_format_with_sym($invoice->getDue()) }}
                                                            </td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- </div> -->
                    </div>
                </div>
                <div class="tab-pane fade" id="receipt-summary" role="tabpanel"
                aria-labelledby="pills-user-tab-2">
                    <h5 class="h4 d-inline-block font-weight-400 my-2">{{ __('Receipt Summary') }}</h5>
                    <div class="card">
                        <div class="card-body table-border-style">
                            <div class="table-responsive">
                                <table class="table mb-0 pc-dt-simple" id="invoice-receipt-summary">
                                    <thead>
                                        <tr>
                                            <th class="text-dark">{{ __('Date') }}</th>
                                            <th class="text-dark">{{ __('Amount') }}</th>
                                            <th class="text-dark">{{ __('Payment Type') }}</th>
                                            <th class="text-dark">{{ __('Reference') }}</th>
                                            <th class="text-dark">{{ __('Description') }}</th>
                                            <th class="text-dark">{{ __('Receipt') }}</th>
                                            @permission('machine invoice payment delete')
                                                <th class="text-dark">{{ __('Action') }}</th>
                                            @endpermission
                                        </tr>
                                    </thead>
                                    @if (!empty($invoice->payments))
                                        @foreach ($invoice->payments as $key => $payment)
                                            <tr>
                                                <td>{{ company_date_formate($payment->date) }}</td>
                                                <td>{{ currency_format_with_sym($payment->amount) }}</td>
                                                <td>{{ $payment->payment_type }}</td>

                                                <td>{{ !empty($payment->reference) ? $payment->reference : '--' }}</td>
                                                <td style="white-space: break-spaces;">{{ !empty($payment->description) ? $payment->description : '--' }}</td>
                                                <td>
                                                    @if (!empty($payment->add_receipt) && empty($payment->receipt) && (check_file($payment->add_receipt)))
                                                        <a href="{{ get_file($payment->add_receipt) }}" download=""
                                                            class="btn btn-sm btn-primary btn-icon rounded-pill" target="_blank"><span
                                                                class="btn-inner--icon"><i class="ti ti-download"></i></span></a>
                                                        <a href="{{ get_file($payment->add_receipt) }}"
                                                            class="btn btn-sm btn-secondary btn-icon rounded-pill"
                                                            target="_blank"><span class="btn-inner--icon"><i
                                                                    class="ti ti-crosshair"></i></span></a>
                                                    @elseif (!empty($payment->receipt) && empty($payment->add_receipt) && $payment->payment_type == 'STRIPE')
                                                        <a href="{{ $payment->receipt }}" target="_blank">
                                                            <i class="ti ti-file"></i>
                                                        </a>
                                                    @else
                                                        --
                                                    @endif
                                                </td>
                                                @permission('machine invoice payment delete')
                                                    <td>
                                                        <div class="action-btn">
                                                            {{ Form::open(['route' => ['machine.invoice.payment.destroy', $invoice->id, $payment->id], 'class' => 'm-0']) }}
                                                            <a href="#"
                                                                class="btn btn-sm  bg-danger align-items-center bs-pass-para show_confirm"
                                                                data-bs-toggle="tooltip" title="" data-bs-original-title="Delete"
                                                                aria-label="Delete" data-confirm="{{ __('Are You Sure?') }}"
                                                                data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                                data-confirm-yes="delete-form-{{ $payment->id }}">
                                                                <i class="ti ti-trash text-white text-white"></i>
                                                            </a>
                                                            {{ Form::close() }}
                                                        </div>
                                                    </td>
                                                @endpermission
                                            </tr>
                                        @endforeach
                                    @else
                                        @include('layouts.nodatafound')
                                    @endif
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
