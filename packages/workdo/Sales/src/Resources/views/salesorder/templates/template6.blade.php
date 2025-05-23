<!DOCTYPE html>
<html lang="en" dir="{{ $settings['site_rtl'] == 'on' ? 'rtl' : '' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>
        {{ Workdo\Sales\Entities\SalesOrder::salesorderNumberFormat($salesorder->salesorder_id, $salesorder->created_by, $salesorder->workspace) }}
        |
        {{ !empty(company_setting('title_text', $salesorder->created_by, $salesorder->workspace)) ? company_setting('title_text', $salesorder->created_by, $salesorder->workspace) : (!empty(admin_setting('title_text')) ? admin_setting('title_text') : 'WorkDo') }}
    </title>
    <link
        href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&display=swap"
        rel="stylesheet">


    <style type="text/css">
        html[dir="rtl"] {
            letter-spacing: 0.1px;
        }

        :root {
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
        }

        .vertical-align-top td {
            vertical-align: top;
        }

        .view-qrcode {
            max-width: 114px;
            height: 114px;
            margin-left: auto;
            margin-top: 15px;
            background: var(--white);
        }

        .view-qrcode img {
            width: 100%;
            height: 100%;
        }

        .invoice-body {
            padding: 30px 25px 0;
        }

        table.add-border tr {
            border-top: 1px solid var(--theme-color);
        }

        tfoot tr:first-of-type {
            border-bottom: 1px solid var(--theme-color);
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
        <div class="invoice-header" style="border-top: 15px solid var(--theme-color);">
            <table>
                <tbody>
                    <tr>
                        <td>
                            <h3
                                style="text-transform: uppercase; font-size: 40px; font-weight: bold; color: {{ $color }};">
                                {{ __('Sales Order') }}</h3>
                        </td>
                        <td class="text-right">
                            <img class="invoice-logo" src="{{ $img }}" alt="">
                        </td>

                    </tr>
                </tbody>
            </table>

        </div>
        <div class="invoice-body">
            <table class="vertical-align-top">
                <tbody>
                    <tr>
                        <td style="font-size: 13px;">
                            <strong style="margin-bottom: 10px; display:block;">{{ __('From:') }}</strong>
                            <p>
                                @if (!empty($settings['company_name']))
                                    {{ $settings['company_name'] }}
                                @endif
                                <br>
                                @if (!empty($settings['company_email']))
                                    {{ $settings['company_email'] }}
                                @endif
                                <br>
                                @if (!empty($settings['company_telephone']))
                                    {{ $settings['company_telephone'] }}
                                @endif
                                <br>
                                @if (!empty($settings['company_address']))
                                    {{ $settings['company_address'] }}
                                @endif
                                @if (!empty($settings['company_city']))
                                    <br> {{ $settings['company_city'] }},
                                @endif
                                @if (!empty($settings['company_state']))
                                    {{ $settings['company_state'] }}
                                @endif
                                @if (!empty($settings['company_country']))
                                    <br>{{ $settings['company_country'] }}
                                @endif
                                @if (!empty($settings['company_zipcode']))
                                    - {{ $settings['company_zipcode'] }}
                                @endif
                            </p>
                        </td>

                        <td style="font-size: 13px;">
                            <strong style="margin-bottom: 10px; display:block;">{{ __('Bill To') }}:</strong>
                            <p>
                                {{ !empty($user->name) ? $user->name : '' }}<br>
                                {{ !empty($user->email) ? $user->email : '' }}<br>
                                {{ !empty($user->mobile) ? $user->mobile : '' }}<br>
                                {{ !empty($user->bill_address) ? $user->bill_address : '' }}<br>
                                {{ !empty($user->bill_city) ? $user->bill_city : '' . ', ' }}
                                {{ !empty($user->bill_state) ? $user->bill_state : '' }},{{ !empty($user->bill_country) ? $user->bill_country : '' }}
                                {{ !empty($user->bill_zip) ? $user->bill_zip : '' }}<br>
                            </p>
                        </td>
                        @if ($settings['salesorder_shipping_display'] == 'on')
                            <td style="font-size: 13px;" class="text-right">
                                <strong style="margin-bottom: 10px; display:block;">{{ __('Ship To') }}:</strong>
                                <p>
                                    {{ !empty($user->name) ? $user->name : '' }}<br>
                                    {{ !empty($user->email) ? $user->email : '' }}<br>
                                    {{ !empty($user->mobile) ? $user->mobile : '' }}<br>
                                    {{ !empty($user->address) ? $user->address : '' }}<br>
                                    {{ !empty($user->city) ? $user->city : '' . ', ' }},{{ !empty($user->state) ? $user->state : '' }},{{ !empty($user->country) ? $user->country : '' }}<br>
                                    {{ !empty($user->zip) ? $user->zip : '' }}
                                </p>
                            </td>
                        @endif
                    </tr>
                    <tr style="border-bottom: 1px solid var(--theme-color);">
                        <td>
                            <p>
                                @if (!empty($settings['registration_number']))
                                    {{ __('Registration Number') }} : {{ $settings['registration_number'] }}
                                @endif
                                <br>
                                @if (!empty($settings['tax_type']) && !empty($settings['vat_number']))
                                    {{ $settings['tax_type'] . ' ' . __('Number') }} : {{ $settings['vat_number'] }} <br>
                                @endif
                            </p>
                        </td>
                        @if ($settings['salesorder_qr_display'] == 'on')
                            <td colspan="2">
                                <div class="view-qrcode" style="margin-top: 0;">
                                    <p>{!! DNS2D::getBarcodeHTML(
                                        route('pay.salesorder', \Illuminate\Support\Facades\Crypt::encrypt($salesorder->id)),
                                        'QRCODE',
                                        2,
                                        2,
                                    ) !!}
                                </div>
                            </td>
                        @endif
                    </tr>
                </tbody>
            </table>
            <table>
                <tbody>
                    <tr>
                        <td>
                            <table class="no-space vertical-align-top">
                                <tbody>
                                    <tr>
                                        <td>{{ __('Number: ') }}</td>
                                        <td class="text-right">
                                            {{ Workdo\Sales\Entities\SalesOrder::salesorderNumberFormat($salesorder->salesorder_id, $salesorder->created_by, $salesorder->workspace) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('Issue Date:') }}</td>
                                        <td class="text-right">
                                            {{ company_date_formate($salesorder->issue_date, $salesorder->created_by, $salesorder->workspace) }}
                                        </td>
                                    </tr>
                                    @if (!empty($customFields) && count($salesorder->customField) > 0)
                                        @foreach ($customFields as $field)
                                            <tr>
                                                <td>{{ $field->name }} :</td>
                                                <td class="text-right" style="white-space: normal;">
                                                    {{ !empty($salesorder->customField[$field->id]) ? $salesorder->customField[$field->id] : '-' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </tbody>
            </table>
            <table class="add-border invoice-summary" style="margin-top: 30px;">
                <thead style="background-color: var(--theme-color); color: {{ $font_color }};">
                    <tr>
                        <th>{{ __('Item') }}</th>
                        <th>{{ __('Quantity') }}</th>
                        <th>{{ __('Rate') }}</th>
                        <th>{{ __('Discount') }}</th>
                        <th>{{ __('Tax') }}(%)</th>
                        <th>{{ __('Price') }}<small>{{ __('After discount & tax') }}</small></th>
                    </tr>
                </thead>
                <tbody>
                    @if (isset($salesorder->items) && count($salesorder->items) > 0)
                        @foreach ($salesorder->items as $key => $item)
                            <tr>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ currency_format_with_sym($item->price, $salesorder->created_by, $salesorder->workspace) }}}
                                </td>
                                <td>{{ $item->discount != 0 ? currency_format_with_sym($item->discount, $salesorder->created_by, $salesorder->workspace) : '-' }}
                                </td>
                                <td>
                                    @if (!empty($item->itemTax))
                                        @foreach ($item->itemTax as $taxes)
                                            <span>{{ $taxes['name'] }} </span><span> ({{ $taxes['rate'] }}) </span>
                                            <span>{{ $taxes['price'] }}</span>
                                        @endforeach
                                    @else
                                        <p>-</p>
                                    @endif
                                </td>
                                <td>{{ currency_format_with_sym($item->price * $item->quantity, $salesorder->created_by, $salesorder->workspace) }}
                                </td>
                                @if ($item->description != null)
                            <tr class="border-0 itm-description ">
                                <td colspan="6">{{ $item->description }} </td>
                            </tr>
                        @endif
                    @endforeach
                @else
                    <tr>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>
                            <p>-</p>
                            <p>-</p>
                        </td>
                        <td>-</td>
                        <td>-</td>
                    <tr class="border-0 itm-description ">
                        <td colspan="6">-</td>
                    </tr>
                    </tr>
                    @endif
                </tbody>
                <tfoot>
                    <tr>
                        <td>{{ __('Total') }}</td>
                        <td>{{ $salesorder->totalQuantity }}</td>
                        <td>{{ currency_format_with_sym($salesorder->totalRate, $salesorder->created_by, $salesorder->workspace) }}
                        </td>
                        <td>{{ currency_format_with_sym($salesorder->totalDiscount, $salesorder->created_by, $salesorder->workspace) }}
                        </td>
                        <td>{{ currency_format_with_sym($salesorder->totalTaxPrice, $salesorder->created_by, $salesorder->workspace) }}
                        </td>
                        <td>{{ currency_format_with_sym($salesorder->getSubTotal(), $salesorder->created_by, $salesorder->workspace) }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4"></td>
                        <td colspan="2" class="sub-total">
                            <table class="total-table">
                                @if ($salesorder->getTotalDiscount())
                                    <tr>
                                        <td>{{ __('Discount') }}:</td>
                                        <td>{{ currency_format_with_sym($salesorder->getTotalDiscount(), $salesorder->created_by, $salesorder->workspace) }}
                                        </td>
                                    </tr>
                                @endif
                                @if (!empty($salesorder->taxesData))
                                    @foreach ($salesorder->taxesData as $taxName => $taxPrice)
                                        <tr>
                                            <td>{{ $taxName }} :</td>
                                            <td>{{ currency_format_with_sym($taxPrice, $salesorder->created_by, $salesorder->workspace) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                                <tr>
                                    <td>{{ __('Total') }}:</td>
                                    <td>{{ currency_format_with_sym($salesorder->getSubTotal() - $salesorder->getTotalDiscount() + $salesorder->getTotalTax(), $salesorder->created_by, $salesorder->workspace) }}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </tfoot>
            </table>
            <div class="invoice-footer">
                <p> {{ $settings['salesorder_footer_title'] }} <br>
                    {{ $settings['salesorder_footer_notes'] }} </p>
            </div>
        </div>
    </div>
    @if (!isset($preview))
        @include('sales::salesorder.script');
    @endif
</body>

</html>
