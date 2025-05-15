<!DOCTYPE html>
<html lang="en" dir="{{ isset($settings['site_rtl']) && $settings['site_rtl'] == 'on' ? 'rtl' : '' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>
        {{ \Workdo\MachineRepairManagement\Entities\MachineInvoice::machineInvoiceNumberFormat($invoice->invoice_id) }}
        |
        {{ !empty(company_setting('title_text', $invoice->created_by, $invoice->workspace)) ? company_setting('title_text', $invoice->created_by, $invoice->workspace) : (!empty(admin_setting('title_text')) ? admin_setting('title_text') : 'WorkDo') }}
    </title>
    <link
        href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&display=swap"
        rel="stylesheet">
    <style type="text/css">
        :root {
            /* --theme-color: #ff8d8d; */
            --theme-color: {{ $color }};
            --white: #ffffff;
            --black: #000000;
        }

        body {
            font-family: 'Lato', sans-serif;
        }

        p,
        li,
        ul,
        ol {
            margin: 0;
            padding: 0;
            list-style: none;
            line-height: 1.5;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table tr th {
            padding: 0.75rem;
            text-align: left;
        }

        table tr td {
            padding: 0.75rem;
            text-align: left;
        }

        table th small {
            display: block;
            font-size: 12px;
        }

        .invoice-preview-main {
            max-width: 700px;
            width: 100%;
            margin: 0 auto;
            background: #ffff;
            box-shadow: 0 0 10px #ddd;
        }

        .invoice-logo {
            max-width: 200px;
            width: 100%;
        }

        .invoice-header table td {
            padding: 15px 30px;
        }

        .text-right {
            text-align: right;
        }

        .no-space tr td {
            padding: 0;
            white-space: nowrap;
        }

        .vertical-align-top td {
            vertical-align: top;
        }

        .view-qrcode {
            max-width: 139px;
            height: 139px;
            width: 100%;
            margin-left: auto;
            margin-top: 15px;
            background: var(--white);
            padding: 13px;
            /* padding: 9px; */
            border-radius: 10px;
        }

        .view-qrcode img {
            width: 100%;
            height: 100%;
        }

        .invoice-body {
            padding: 30px 25px 0;
        }

        table.add-border tr {
            /* border-top: 1px solid var(--theme-color); */
            border-top: 1px solid #000000;
        }

        tfoot tr:first-of-type {
            /* border-bottom: 1px solid var(--theme-color); */
            border-bottom: 1px solid #000000;
        }

        .total-table tr:first-of-type td {
            padding-top: 0;
        }

        .total-table tr:first-of-type {
            border-top: 0;
        }

        .sub-total {
            padding-right: 0;
            padding-left: 0;
        }

        .border-0 {
            border: none !important;
        }

        .invoice-summary td,
        .invoice-summary th {
            font-size: 13px;
            font-weight: 600;
        }

        .invoice-summary th {
            font-size: 15px;
            font-weight: 600;
        }

        .total-table td:last-of-type {
            width: 146px;
        }

        .invoice-footer {
            padding: 15px 20px;
        }

        .itm-description td {
            padding-top: 0;
        }

        html[dir="rtl"] table tr td,
        html[dir="rtl"] table tr th {
            text-align: right;
        }

        html[dir="rtl"] .text-right {
            text-align: left;
        }

        html[dir="rtl"] .view-qrcode {
            margin-left: 0;
            margin-right: auto;
        }

        p:not(:last-of-type) {
            margin-bottom: 15px;
        }

        .invoice-summary p {
            margin-bottom: 0;
        }
        .wid-75 {
            width: 75px;
        }
    </style>
</head>

<body>
    <div class="invoice-preview-main" id="boxes">
        <div class="invoice-header" style="background-color: var(--theme-color); color: {{ $font_color }};">
            <table>
                <tbody>
                    <tr>
                        <td>
                            <img class="invoice-logo" src="{{ $img }}" alt="">
                        </td>
                        <td class="text-right">
                            <h3 style="text-transform: uppercase; font-size: 40px; font-weight: bold; ">
                                {{ __('DIAGNOSIS') }}</h3>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div style="display: flex;flex-wrap: wrap;">
            <div style="flex: 0 0 auto;width: 66.66667%;">
                <table class="vertical-align-top">
                    <tbody>
                        <tr>
                            <td style="padding:3px 15px 3px 30px;"><b>{{ __('Number: ') }}</b></td>
                            <td class="text-right" style="padding:3px 15px 3px 30px;">
                                {{\Workdo\MachineRepairManagement\Entities\MachineInvoice::machineInvoiceNumberFormat($invoice->invoice_id) }}
                            </td>
                        </tr>
                        <tr>
                            <td style="padding:3px 15px 3px 30px;"><b>{{ __('Issue Date:') }}</b></td>
                            <td class="text-right" style="padding:3px 15px 3px 30px;">
                                {{ company_date_formate($invoice->issue_date, $invoice->created_by, $invoice->workspace) }}
                            </td>
                        </tr>
                        <tr>
                            <td style="padding:3px 15px 3px 30px;"><b>{{ __('Due Date') }}:</b></td>
                            <td class="text-right" style="padding:3px 15px 3px 30px;">
                                {{ company_date_formate($invoice->due_date, $invoice->created_by, $invoice->workspace) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div style="flex: 0 0 auto;width: 33.33333%;">
                <table class="">{{-- no-space --}}
                    <tbody>
                        <tr>
                            <td colspan="2" style="padding-top: 0px;padding-bottom: 0px;">
                                <div class="view-qrcode" style="margin-top: 0px;padding-top: 0px;padding-bottom: 0px;">
                                    {!! DNS2D::getBarcodeHTML(
                                        route('machine-repair-invoice.show', \Illuminate\Support\Facades\Crypt::encrypt($invoice->id)),
                                        'QRCODE',
                                        2,
                                        2,
                                    ) !!}
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        </div>
        <div class="invoice-body" style="padding-top: 0px;">
            <table>
                <tbody>
                    <tr>
                        <td>
                            <p class="font-style">
                                <strong>{{ __('Request Number') }} :</strong><br>
                                {{ !empty($invoice->request_id) ? \Workdo\MachineRepairManagement\Entities\MachineRepairRequest::machineRepairNumberFormat($invoice->request_id) : '' }}<br>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        @if (!empty($invoice->customer_name) && !empty($invoice->customer_email))
                            <td>
                                <strong style="margin-bottom: 10px; display:block;">{{ __('Billed To') }}:</strong>
                                <p>
                                    {{ !empty($invoice->customer_name) ? $invoice->customer_name : '' }}<br>
                                    {{ !empty($invoice->customer_email) ? $invoice->customer_email : '' }}<br>
                                </p>
                            </td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        @endif
                        @if (!empty($machine->name) && !empty($machine->manufacturer) && !empty($machine->model))
                            <td>
                                <p class="font-style">
                                    <strong>{{ __('Machine Details') }} :</strong><br>
                                    <dl class="row align-items-center" style="display: flex;flex-wrap: wrap;">
                                        <dt style="font-weight: 600;flex: 0 0 auto;width: 33.33333%;">{{ __('Name') }}</dt>
                                        <dd style="margin-bottom: 0px;flex: 0 0 auto;width: 66.66667%;"> : {{ !empty($machine->name) ? $machine->name : '' }}</dd>
                                        <dt style="font-weight: 600;flex: 0 0 auto;width: 33.33333%;">{{ __('Model') }}</dt>
                                        <dd style="margin-bottom: 0px;flex: 0 0 auto;width: 66.66667%;"> : {{ !empty($machine->model) ? $machine->model : '' }}</dd>
                                        <dt style="font-weight: 600;flex: 0 0 auto;width: 33.33333%;">{{ __('Manufacturer') }}</dt>
                                        <dd style="margin-bottom: 0px;flex: 0 0 auto;width: 66.66667%;"> : {{ !empty($machine->manufacturer) ? $machine->manufacturer : '' }}</dd>
                                    </dl>
                                </p>
                            </td>
                        @endif
                    </tr>
                </tbody>
            </table>
            <table class="add-border invoice-summary" style="margin-top: 30px;">
                <thead style="background-color: var(--theme-color);color: {{ $font_color }};">
                    <tr>
                        <th data-width="40" class="text-dark">#</th>
                        <th class="text-dark">{{ __('Item Type') }}</th>
                        <th class="text-dark">{{ __('Item') }}</th>
                        <th class="text-dark">{{ __('Quantity') }}</th>
                        <th class="text-dark">{{ __('Rate') }}</th>
                        <th class="text-dark">{{ __('Discount') }}</th>
                        <th class="text-dark">{{ __('Tax') }}</th>
                        <th class="text-right text-dark" width="12%">{{ __('Price') }}<br>
                            <small
                                class="text-danger font-weight-bold">{{ __('After discount & tax') }}</small>
                        </th>
                    </tr>
                </thead>
                <tbody>
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
                            @php
                                $tr_tex = array_key_exists($key, $TaxPrice_array) == true ? $TaxPrice_array[$key] : 0;
                            @endphp
                            <td class="">{{ currency_format_with_sym($iteam->price * $iteam->quantity - $iteam->discount + $tr_tex) }}
                            </td>
                        </tr>
                        <tr class="border-0 itm-description ">
                            <td colspan="6">{{ !empty($iteam->description) ? $iteam->description : '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
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
                        $colspan = 6;
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
    @if (!isset($preview))
        @include('machine-repair-management::invoice.script');
    @endif
</body>

</html>
