@extends('layouts.main')
@section('page-title')
    {{ __('Purchase Order Details') }}
@endsection
@section('page-breadcrumb')
    {{ __('Purchase Order Details') }}
@endsection

@section('page-action')
    <div class="">
        @if (\Auth::user()->type == 'company')
            <a class="dash-head-link dropdown-toggle border  arrow-none py-2 px-3 mx-3" data-bs-toggle="dropdown"
                href="#" role="button" aria-haspopup="false" aria-expanded="false" id="dropdownLanguage">
                <span class="drp-text hide-mob format text-primary">{{ __('Status') }}</span>
                <i class="ti ti-chevron-down drp-arrow nocolor"></i>
            </a>
            <div class="dropdown-menu dash-h-dropdown dropdown-menu-end" aria-labelledby="dropdownLanguage">
                @foreach (Workdo\SalesAgent\Entities\SalesAgentPurchase::$purchaseOrder as $key => $status)
                    <a href={{ route('salesagents.update.purchase.order.status', [$purchaseOrder->id, $key]) }}
                        class="dropdown-item {{ $purchaseOrder->order_status == $key ? 'text-primary' : '' }}">{{ $status }}</a>
                @endforeach
            </div>
            @if (empty($purchaseOrder['invoice_id']))
                <a class="btn btn-md text-white btn-primary" data-ajax-popup="true" data-size="md"
                    data-title="{{ __('Create Invoice') }}"
                    data-url="{{ route('salesagents.purchase.invoice.model', $purchaseOrder->id) }}"
                    data-bs-toggle="tooltip" data-bs-original-title="{{ __('Create') }}">
                    {{ __('Create Invoice') }}
                </a>
            @endif
        @endif
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="invoice">
                        <div class="invoice-print">
                            <div class="row row-gap invoice-title border-1 border-bottom  pb-3 mb-3">
                                <div class="col-sm-4  col-12">
                                    <h2 class="h3 mb-0">{{ __('Purchase Order') }}</h2>
                                </div>
                                <div class="col-sm-8  col-12">
                                    <div
                                        class="d-flex invoice-wrp flex-wrap align-items-center gap-md-2 gap-1 justify-content-end">
                                        <div
                                            class="d-flex invoice-date flex-wrap align-items-center justify-content-end gap-md-3 gap-1">
                                            <p class="mb-0"><strong>{{ __('Order Date') }} :</strong>
                                                {{ company_date_formate($purchaseOrder->order_date) }}</p>
                                            <p class="mb-0"><strong>{{ __('Delivery Date') }} :</strong>
                                                {{ company_date_formate($purchaseOrder->delivery_date) }}</p>
                                        </div>
                                        <h3 class="invoice-number mb-0">
                                            {{ Workdo\SalesAgent\Entities\SalesAgent::purchaseOrderNumberFormat($purchaseOrder->id) }}
                                        </h3>
                                    </div>
                                </div>
                            </div>

                            <div class="p-sm-4 p-3 invoice-billed">
                                <div class="row row-gap">
                                    <div class="col-lg-4 col-sm-6">
                                        @if (!empty($salesagent->billing_name))
                                            <p class="mb-2"><strong class="h5 mb-1 d-block">{{ __('Billed To') }}
                                                    :</strong>
                                                <span class="text-muted d-block" style="max-width:80%">
                                                    {{ !empty($salesagent->billing_name) ? $salesagent->billing_name . ',' : '' }}
                                                    {{ !empty($salesagent->billing_address) ? $salesagent->billing_address . ',' : '' }}
                                                    {{ !empty($salesagent->billing_city) ? $salesagent->billing_city . ' ,' : '' }}
                                                    {{ !empty($salesagent->billing_state) ? $salesagent->billing_state . ' ,' : '' }}
                                                    {{ !empty($salesagent->billing_zip) ? $salesagent->billing_zip . ',' : '' }}
                                                    {{ !empty($salesagent->billing_country) ? $salesagent->billing_country : '' }}
                                                </span>
                                            </p>
                                            <p class="mb-1 text-dark">
                                                {{ !empty($salesagent->billing_phone) ? $salesagent->billing_phone : '' }}
                                            </p>
                                            <p class="mb-0">
                                                <strong>{{ __('Tax Number ') }} :
                                                </strong>{{ !empty($salesagent->tax_number) ? $salesagent->tax_number : '' }}
                                            </p>
                                        @endif
                                    </div>
                                    <div class="col-lg-4 col-sm-6">
                                        @if (!empty($salesagent->shipping_name))
                                            <p class="mb-2">
                                                <strong class="h5 mb-1 d-block">{{ __('Shipped To') }}
                                                    :</strong>
                                                <span class="text-muted d-block" style="max-width:80%">
                                                    {{ !empty($salesagent->shipping_name) ? $salesagent->shipping_name . ',' : '' }}
                                                    {{ !empty($salesagent->shipping_address) ? $salesagent->shipping_address . ',' : '' }}
                                                    {{ !empty($salesagent->shipping_city) ? $salesagent->shipping_city . ' ,' : '' }}
                                                    {{ !empty($salesagent->shipping_state) ? $salesagent->shipping_state . ' ,' : '' }}
                                                    {{ !empty($salesagent->shipping_zip) ? $salesagent->shipping_zip . ',' : '' }}
                                                    {{ !empty($salesagent->shipping_country) ? $salesagent->shipping_country : '' }}
                                                </span>
                                            </p>
                                            <p class="mb-1 text-dark">
                                                {{ !empty($salesagent->shipping_phone) ? $salesagent->shipping_phone : '' }}
                                            </p>
                                            <p class="mb-0">
                                                <strong>{{ __('Tax Number ') }} :
                                                </strong>{{ !empty($salesagent->tax_number) ? $salesagent->tax_number : '' }}
                                            </p>
                                        @endif
                                    </div>
                                    <div class="col-lg-4 col-sm-6 text-end">
                                        <strong class="h5 d-block mb-2">{{ __('Status') }} :</strong>
                                        @if ($purchaseOrder->order_status == 0)
                                            <span
                                                class="badge fix_badge f-12 p-2 d-inline-block bg-primary">{{ __(Workdo\SalesAgent\Entities\SalesAgentPurchase::$purchaseOrder[$purchaseOrder->order_status]) }}</span>
                                        @elseif($purchaseOrder->order_status == 1)
                                            <span
                                                class="badge fix_badge f-12 p-2 d-inline-block bg-info">{{ __(Workdo\SalesAgent\Entities\SalesAgentPurchase::$purchaseOrder[$purchaseOrder->order_status]) }}</span>
                                        @elseif($purchaseOrder->order_status == 2)
                                            <span
                                                class="badge fix_badge f-12 p-2 d-inline-block bg-secondary">{{ __(Workdo\SalesAgent\Entities\SalesAgentPurchase::$purchaseOrder[$purchaseOrder->order_status]) }}</span>
                                        @elseif($purchaseOrder->order_status == 3)
                                            <span
                                                class="badge fix_badge f-12 p-2 d-inline-block bg-warning">{{ __(Workdo\SalesAgent\Entities\SalesAgentPurchase::$purchaseOrder[$purchaseOrder->order_status]) }}</span>
                                        @elseif($purchaseOrder->order_status == 4)
                                            <span
                                                class="badge fix_badge f-12 p-2 d-inline-block bg-danger">{{ __(Workdo\SalesAgent\Entities\SalesAgentPurchase::$purchaseOrder[$purchaseOrder->order_status]) }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @if (!empty($customFields) && count($purchaseOrder->customField) > 0)
                                <div class="px-4 mt-3">
                                    <div class="row row-gap">
                                        @foreach ($customFields as $field)
                                            <div class="col-xxl-3 col-sm-6">
                                                <strong class="d-block mb-1">{{ $field->name }} </strong>
                                                @if ($field->type == 'attachment')
                                                    <a href="{{ get_file($purchaseOrder->customField[$field->id]) }}"
                                                        target="_blank">
                                                        <img src=" {{ get_file($purchaseOrder->customField[$field->id]) }} "
                                                            class="wid-120 rounded">
                                                    </a>
                                                @else
                                                    <p class="mb-0">
                                                        {{ !empty($purchaseOrder->customField[$field->id]) ? $purchaseOrder->customField[$field->id] : '-' }}
                                                    </p>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <div class="invoice-summary mt-3">
                                <div class="invoice-title border-1 border-bottom mb-3 pb-2">
                                    <h3 class="h4 mb-0">{{ __('Item Summary') }}</h3>
                                </div>
                                <div class="table-responsive mt-2">
                                    <table class="table mb-0 table-striped">
                                        <tr>
                                            <th class="text-white bg-primary text-uppercase" data-width="40">#</th>
                                            <th class="text-white bg-primary text-uppercase">{{ __('Programs') }}</th>
                                            <th class="text-white bg-primary text-uppercase">{{ __('Item') }}</th>
                                            <th class="text-white bg-primary text-uppercase">{{ __('Quantity') }}</th>
                                            <th class="text-white bg-primary text-uppercase">{{ __('Rate') }}</th>
                                            <th class="text-white bg-primary text-uppercase">{{ __('Discount') }}</th>
                                            <th class="text-white bg-primary text-uppercase">{{ __('Tax') }}</th>
                                            <th class="text-white bg-primary text-uppercase">{{ __('Description') }}</th>
                                            <th class="text-right text-white bg-primary text-uppercase" width="12%">
                                                {{ __('Price') }}
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
                                        @foreach ($purchaseOrder->items as $key => $iteam)
                                            @php
                                                $totalQuantity += $iteam->quantity;
                                                $totalRate += $iteam->price;
                                                $totalDiscount += $iteam->discount;
                                            @endphp
                                            @if (!empty($iteam->tax))
                                                @php
                                                    $taxes = Workdo\SalesAgent\Entities\SalesAgentUtility::tax(
                                                        $iteam->tax,
                                                    );
                                                    foreach ($taxes as $taxe) {
                                                        $taxDataPrice = Workdo\SalesAgent\Entities\SalesAgentUtility::taxRate(
                                                            $taxe->rate,
                                                            $iteam->price,
                                                            $iteam->quantity,
                                                            $iteam->discount,
                                                        );
                                                        if (array_key_exists($taxe->name, $taxesData)) {
                                                            $taxesData[$taxe->name] =
                                                                $taxesData[$taxe->name] + $taxDataPrice;
                                                        } else {
                                                            $taxesData[$taxe->name] = $taxDataPrice;
                                                        }
                                                    }
                                                @endphp
                                            @endif
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ !empty($iteam->program) ? Str::ucfirst($iteam->program->name) : '--' }}
                                                </td>
                                                <td>{{ !empty($iteam->product) ? $iteam->product->name : '' }}</td>
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
                                                                    $taxPrice = Workdo\SalesAgent\Entities\SalesAgentUtility::taxRate(
                                                                        $tax->rate,
                                                                        $iteam->price,
                                                                        $iteam->quantity,
                                                                        $iteam->discount,
                                                                    );
                                                                    $totalTaxPrice += $taxPrice;
                                                                    $data += $taxPrice;
                                                                @endphp
                                                                <tr>
                                                                    <td>{{ $tax->name . ' (' . $tax->rate . '%)' }}
                                                                    </td>
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
                                                </td>
                                                <td>
                                                    {{ !empty($iteam->description) ? $iteam->description : '-' }}</td>
                                                @php
                                                    $tr_tex =
                                                        array_key_exists($key, $TaxPrice_array) == true
                                                            ? $TaxPrice_array[$key]
                                                            : 0;
                                                @endphp
                                                <td>
                                                    {{ currency_format_with_sym($iteam->price * $iteam->quantity - $iteam->discount + $tr_tex) }}
                                                </td>
                                            </tr>
                                        @endforeach


                                        <tfoot>
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td class="bg-color"><b>{{ __('Total') }}</b></td>
                                                <td class="bg-color"><b>{{ $totalQuantity }}</b></td>
                                                <td class="bg-color"><b>{{ currency_format_with_sym($totalRate) }}</b></td>
                                                <td class="bg-color"><b>{{ currency_format_with_sym($totalDiscount) }}</b>
                                                </td>
                                                <td class="bg-color"><b>{{ currency_format_with_sym($totalTaxPrice) }}</b>
                                                </td>

                                            </tr>
                                            <tr>
                                                <td colspan="7"></td>
                                                <td class="text-right"><b>{{ __('Sub Total') }}</b></td>
                                                <td class="text-right">
                                                    {{ currency_format_with_sym($purchaseOrder->getSubTotal()) }}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="7"></td>
                                                <td class="text-right"><b>{{ __('Discount') }}</b></td>
                                                <td class="text-right">
                                                    {{ currency_format_with_sym($purchaseOrder->getTotalDiscount()) }}
                                                </td>
                                            </tr>
                                            @if (!empty($taxesData))
                                                @foreach ($taxesData as $taxName => $taxPrice)
                                                    <tr>
                                                        <td colspan="7"></td>
                                                        <td class="text-right"><b>{{ $taxName }}</b></td>
                                                        <td class="text-right">
                                                            {{ currency_format_with_sym($taxPrice) }}</td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                            <tr>
                                                <td colspan="7"></td>
                                                <td class="text-right"><b>{{ __('Total') }}</b></td>
                                                <td class="text-right">
                                                    {{ currency_format_with_sym($purchaseOrder->getTotal()) }}</td>
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
    </div>
@endsection
