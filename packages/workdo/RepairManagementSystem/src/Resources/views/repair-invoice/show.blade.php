@extends('layouts.main')
@section('page-title')
{{ __('Manage Repair Invoice Detail') }}
@endsection
@section('page-breadcrumb')
{{ __('Repair Invoice Detail') }}
@endsection
@push('css')
@include('layouts.includes.datatable-css')
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
        toastrs('success', '{{ __('
            Link Copy on Clipboard ') }}', 'success')
    });
</script>
@endpush
@section('page-action')
<div>
    <div class="float-end">
        <a href="{{ route('repair.request.invoice.index') }}" data-bs-toggle="tooltip" title="{{__('Back')}}" class="btn btn-sm btn-primary">
            <i class="ti ti-arrow-back-up"></i>
        </a>
    </div>
</div>
@endsection
@section('content')
    <div class="row justify-content-between align-items-center mb-3">
        <div class="col-md-6">
            <ul class="nav nav-pills nav-fill cust-nav information-tab" id="pills-tab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="invoice-tab" data-bs-toggle="pill" data-bs-target="#invoice" type="button">{{ __('Detail') }}</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="receipt-summary-tab" data-bs-toggle="pill" data-bs-target="#receipt-summary" type="button">{{ __('Payment Summary') }}</button>
                </li>
            </ul>
        </div>
        <div class="col-md-6 d-flex align-items-center justify-content-between justify-content-md-end">
            <div class="all-button-box">
            <div class="d-flex">
                @if($repair_invoice->status != 2)
                @permission('repair part edit')
                <a href="{{ route('repair.parts.edit', [\Crypt::encrypt($repair_invoice->repair_id),$repair_invoice->id]) }}" class="btn btn-sm me-2 btn-primary" data-bs-toggle="tooltip" title="{{__('Edit')}}" data-original-title="{{ __('Edit') }}"><i class="ti ti-pencil mr-2"></i></a>
                @endpermission
                @endif
                @if($repair_invoice->status != 2)
                <a href="#" data-url="{{route('repair.invoice.payment.create',[$repair_invoice->id])}}" data-ajax-popup="true" data-title="{{ __('Add Payment') }}" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="{{__('Pay Now')}}" data-original-title="{{ __('Add Payment') }}"><i class="ti ti-report-money mr-2"></i></a> <br>
                @endif
            </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="tab-content" id="pills-tabContent">
                <div class="tab-pane fade active show" id="invoice" role="tabpanel" aria-labelledby="pills-user-tab-1">
                    <div class="card">
                        <div class="card-body">
                            <div class="invoice">
                                <div class="invoice-print">
                                    <div class="row row-gap invoice-title border-1 border-bottom  pb-3 mb-3">
                                        <div class="col-sm-4  col-12">
                                            <h2 class="h3 mb-0">{{ __('Repair Product Invoice') }}</h2>
                                        </div>
                                        <div class="col-sm-8  col-12">
                                            <div class="d-flex invoice-wrp flex-wrap align-items-center gap-md-2 gap-1 justify-content-end">
                                                <div class="d-flex invoice-date flex-wrap align-items-center justify-content-end gap-md-3 gap-1">
                                                    <p class="mb-0"><strong>{{ __('Issue Date') }} :</strong>
                                                        {{ company_date_formate($repair_invoice->issue_date) }}</p>
                                                    <p class="mb-0"><strong>{{ __('Due Date') }} :</strong>
                                                        {{ company_date_formate($repair_invoice->due_date) }}</p>
                                                </div>
                                                <h3 class="invoice-number mb-0">
                                                    {{ \Workdo\RepairManagementSystem\Entities\RepairOrderRequest::invoiceNumberFormat($repair_invoice->invoice_id) }}
                                                </h3>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="p-sm-4 p-3 invoice-billed">
                                        <div class="row row-gap">
                                            <div class="col-lg-5 col-sm-6">
                                                <div class="font-style">
                                                    <label class="col-form-label" style="font-size: medium;"><b>{{ __('Product Name:  ') }}</b></label>
                                                    <label for="email1" class="col-form-label" style="font-size: medium;">{{$repair_invoice->repairOrderRequest->product_name}}</label><br>
                                                    <label class="col-form-label" style="font-size: medium;"><b>{{ __('Product Quantity:') }}</b></label>
                                                    <label for="email1" class="col-form-label" style="font-size: medium;">{{$repair_invoice->repairOrderRequest->product_quantity}}</label><br>
                                                    <label class="col-form-label" style="font-size: medium;"><b>{{ __('Customer Name:') }}</b></label>
                                                    <label for="email1" class="col-form-label" style="font-size: medium;">{{$repair_invoice->repairOrderRequest->customer_name}}</label><br>
                                                    <label class="col-form-label" style="font-size: medium;"><b>{{ __('Customer Email:') }}</b></label>
                                                    <label for="email1" class="col-form-label" style="font-size: medium;">{{$repair_invoice->repairOrderRequest->customer_email}}</label><br>
                                                </div>
                                            </div>
                                            <div class="col-lg-3 col-sm-6">
                                                <div class="font-style">
                                                    <label class="col-form-label" style="font-size: medium;"><b>{{ __('Customer Mobile No:') }}</b></label>
                                                    <label for="email1" class="col-form-label" style="font-size: medium;">{{$repair_invoice->repairOrderRequest->customer_mobile_no}}</label><br>
                                                    <label class="col-form-label" style="font-size: medium;"><b>{{ __('Location:') }}</b></label>
                                                    <label for="email1" class="col-form-label" style="font-size: medium;">{{$repair_invoice->repairOrderRequest->location}}</label><br>
                                                    <label class="col-form-label" style="font-size: medium;"><b>{{ __('Date:') }}</b></label>
                                                    <label for="email1" class="col-form-label" style="font-size: medium;">{{$repair_invoice->repairOrderRequest->date}}</label><br>
                                                    <label class="col-form-label" style="font-size: medium;"><b>{{ __('Expiry Date:') }}</b></label>
                                                    <label for="email1" class="col-form-label" style="font-size: medium;">{{$repair_invoice->repairOrderRequest->expiry_date}}</label><br>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-sm-6 text-end">
                                                <strong>{{ __('Status') }} :</strong><br>
                                                @if ($repair_invoice->status == 0)
                                                <span class="badge fix_badge f-12 p-2 d-inline-block bg-warning">{{__('Pending')}}</span>
                                                @elseif($repair_invoice->status == 1)
                                                <span class="badge fix_badge f-12 p-2 d-inline-block bg-success">{{__('Partialy Paid')}}</span>
                                                @elseif($repair_invoice->status == 2)
                                                <span class="badge fix_badge f-12 p-2 d-inline-block bg-primary">{{__('Paid')}}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="invoice-summary mt-3">
                                        <div class="invoice-title border-1 border-bottom mb-3 pb-2">
                                            <h3 class="h4 mb-0">{{ __('Part Summary') }}</h3>
                                            <small>{{ __('All parts here cannot be deleted.') }}</small>
                                        </div>
                                        <div class="table-responsive pt-2">
                                            <table class="table mb-0 table-striped">
                                                <tr>
                                                    <th data-width="40" class="text-white bg-primary text-uppercase">#</th>
                                                    <th class="text-white bg-primary text-uppercase">{{ __('Part') }}</th>
                                                    <th class="text-white bg-primary text-uppercase">{{ __('Quantity') }}</th>
                                                    <th class="text-white bg-primary text-uppercase">{{ __('Rate') }}</th>
                                                    <th class="text-white bg-primary text-uppercase">{{ __('Discount') }}</th>
                                                    <th class="text-white bg-primary text-uppercase">{{ __('Tax') }}</th>
                                                    <th class="text-white bg-primary text-uppercase">{{ __('Description') }}</th>
                                                    <th class="text-right text-white bg-primary text-uppercase" width="12%">{{ __('Price') }}<br>
                                                        {{-- <small class="text-danger font-weight-bold">{{ __('After discount & tax') }}</small> --}}
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
                                                $taxes = \Workdo\RepairManagementSystem\Entities\RepairOrderRequest::tax($iteam->tax);
                                                $totalQuantity += $iteam->quantity;
                                                $totalRate += $iteam->price;
                                                $totalDiscount += $iteam->discount;
                                                foreach ($taxes as $taxe) {
                                                $taxDataPrice = \Workdo\RepairManagementSystem\Entities\RepairOrderRequest::taxRate($taxe->rate, $iteam->price, $iteam->quantity, $iteam->discount);
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
                                                    <td>{{ !empty($iteam->product()) ? $iteam->product()->name : '' }}</td>
                                                    <td>{{ $iteam->quantity }}</td>
                                                    <td>{{ currency_format_with_sym($iteam->price) }}</td>
                                                    <td>{{ currency_format_with_sym($iteam->discount) }}</td>
                                                    <td>
                                                        @if (!empty($iteam->tax))
                                                        <table class="w-100">
                                                            @php
                                                            $totalTaxRate = 0;
                                                            $data = 0;
                                                            @endphp
                                                            @foreach ($taxes as $tax)
                                                            @php
                                                            $taxPrice = Workdo\RepairManagementSystem\Entities\RepairOrderRequest::taxRate($tax->rate, $iteam->price, $iteam->quantity, $iteam->discount);
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

                                                    <td>
                                                        {{ !empty($iteam->description) ? $iteam->description : '-' }}
                                                    </td>

                                                    @php
                                                        $tr_tex = array_key_exists($key, $TaxPrice_array) == true ? $TaxPrice_array[$key] : 0;
                                                    @endphp

                                                    <td class="">
                                                        {{ currency_format_with_sym($iteam->price * $iteam->quantity - $iteam->discount + $tr_tex) }}
                                                    </td>
                                                </tr>
                                                @endforeach
                                                <tfoot>
                                                    <tr>
                                                        <td></td>
                                                        <td class="bg-color"><b>{{ __('Total') }}</b></td>
                                                        <td class="bg-color"><b>{{ $totalQuantity }}</b></td>
                                                        <td class="bg-color"><b>{{ currency_format_with_sym($totalRate) }}</b></td>
                                                        <td class="bg-color"><b>{{ currency_format_with_sym($totalDiscount) }}</b></td>
                                                        <td class="bg-color"><b>{{ currency_format_with_sym($totalTaxPrice) }}</b></td>
                                                        <td></td>
                                                    </tr>
                                                    @php
                                                        $colspan = 6;
                                                    @endphp
                                                    <tr>
                                                        <td colspan="{{ $colspan }}"></td>
                                                        <td class="text-right"><b>{{ __('Sub Total') }}</b></td>
                                                        <td class="text-right">{{ currency_format_with_sym($repair_invoice->repairOrderRequest->getSubTotal()) }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="{{ $colspan }}"></td>
                                                        <td class="text-right"><b>{{ __('Discount') }}</b></td>
                                                        <td class="text-right">{{ currency_format_with_sym($repair_invoice->repairOrderRequest->getTotalDiscount()) }}</td>
                                                    </tr>
                                                    @if (!empty($taxesData))
                                                        @foreach ($taxesData as $taxName => $taxPrice)
                                                            <tr>
                                                                <td colspan="{{ $colspan }}"></td>
                                                                <td class="text-right"><b>{{ $taxName }}</b></td>
                                                                <td class="text-right">{{ currency_format_with_sym($taxPrice) }}</td>
                                                            </tr>
                                                        @endforeach
                                                    @endif
                                                    <tr>
                                                        <td colspan="{{ $colspan }}"></td>
                                                        <td class="blue-text text-right"><b>{{ __('Repair Charge') }}</b></td>
                                                        <td class="blue-text text-right">{{ currency_format_with_sym($repair_invoice->repairOrderRequest->getRepairCharge()) }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="{{ $colspan }}"></td>
                                                        <td class="blue-text text-right"><b>{{ __('Total') }}</b></td>
                                                        <td class="blue-text text-right">{{ currency_format_with_sym($repair_invoice->repairOrderRequest->getTotal()) }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="{{ $colspan }}"></td>
                                                        <td class="text-right"><b>{{ __('Paid') }}</b></td>
                                                        <td class="text-right">{{ currency_format_with_sym($repair_invoice->repairOrderRequest->getTotal() - $repair_invoice->repairOrderRequest->getDue()) }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="{{ $colspan }}"></td>
                                                        <td class="text-right"><b>{{ __('Due') }}</b></td>
                                                        <td class="text-right">{{ currency_format_with_sym($repair_invoice->repairOrderRequest->getDue()) }}</td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="receipt-summary" role="tabpanel" aria-labelledby="pills-user-tab-2">
                    <h5 class="h4 d-inline-block font-weight-400 my-2">{{ __('Repair Invoice Payment List') }}</h5>
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-body table-border-style">
                                <div class="table-responsive">
                                    {{ $dataTable->table(['width' => '100%']) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
@include('layouts.includes.datatable-js')
{{ $dataTable->scripts() }}
@endpush
