@extends('layouts.main')
@section('page-title')
    {{ __('Quotation Detail') }}
@endsection
@section('page-breadcrumb')
    {{ __('Quotation Detail') }}
@endsection
@section('page-action')
    <div>
        <a href="{{ route('quotation.pdf', Crypt::encrypt($quotation->id)) }}" target="_blank" class="btn btn-sm btn-primary"
        data-bs-toggle="tooltip"  title="{{__('Download')}}" >
            <i class="ti ti-download"></i>
        </a>
    </div>
@endsection
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="invoice">
                        <div class="invoice-print">
                            <div class="row row-gap invoice-title border-1 border-bottom  pb-3 mb-3">
                                <div class="col-sm-4  col-12">
                                    <h2 class="h3 mb-0">{{ __('Quotation') }}</h2>
                                </div>
                                <div class="col-sm-8  col-12">
                                    <div
                                        class="d-flex invoice-wrp flex-wrap align-items-center gap-md-2 gap-1 justify-content-end">
                                        <div
                                            class="d-flex invoice-date flex-wrap align-items-center justify-content-end gap-md-3 gap-1">
                                            <p class="mb-0"><strong>{{ __('Start Date') }} :</strong>
                                                {{ company_date_formate($quotation->quotation_date) }}</p>
                                        </div>
                                        <h3 class="invoice-number mb-0">
                                            {{ \Workdo\Quotation\Entities\Quotation::quotationNumberFormat($quotation->quotation_id) }}
                                        </h3>
                                    </div>
                                </div>
                            </div>
                            @if (!empty($customer->billing_name) && !empty($customer->billing_address) && !empty($customer->billing_zip))
                            <div class="p-sm-4 p-3 invoice-billed">
                                <div class="row row-gap">
                                    <div class="col-lg-4 col-sm-6">
                                        @if (!empty($customer->billing_name) && !empty($customer->billing_address) && !empty($customer->billing_zip))
                                            <p class="mb-2"><strong class="h5 mb-1 d-block">{{ __('Billed To') }}
                                                    :</strong>
                                                <span class="text-muted d-block" style="max-width:80%">
                                                    {{ !empty($customer->billing_name) ? $customer->billing_name : '' }}
                                                    {{ !empty($customer->billing_address) ? $customer->billing_address : '' }}
                                                    {{ !empty($customer->billing_city) ? $customer->billing_city . ' ,' : '' }}
                                                    {{ !empty($customer->billing_state) ? $customer->billing_state . ' ,' : '' }}
                                                    {{ !empty($customer->billing_zip) ? $customer->billing_zip : '' }}
                                                    {{ !empty($customer->billing_country) ? $customer->billing_country : '' }}
                                                </span>
                                            </p>
                                            <p class="mb-1 text-dark">
                                                {{ !empty($customer->billing_phone) ? $customer->billing_phone : '' }}
                                            </p>
                                            <p class="mb-0">
                                                <strong>{{ __('Tax Number ') }} :
                                                </strong>{{ !empty($customer->tax_number) ? $customer->tax_number : '' }}
                                            </p>
                                        @endif
                                    </div>
                                    <div class="col-lg-4 col-sm-6">
                                        @if (company_setting('quotation_shipping_display') == 'on')
                                            @if (!empty($customer->shipping_name) && !empty($customer->shipping_address) && !empty($customer->shipping_zip))
                                                <p class="mb-2">
                                                    <strong class="h5 mb-1 d-block">{{ __('Shipped To') }}
                                                        :</strong>
                                                    <span class="text-muted d-block" style="max-width:80%">
                                                        {{ !empty($customer->shipping_name) ? $customer->shipping_name : '' }}
                                                        {{ !empty($customer->shipping_address) ? $customer->shipping_address : '' }}
                                                        {{ !empty($customer->shipping_city) ? $customer->shipping_city . ' ,' : '' }}
                                                        {{ !empty($customer->shipping_state) ? $customer->shipping_state . ' ,' : '' }}
                                                        {{ !empty($customer->shipping_zip) ? $customer->shipping_zip : '' }}
                                                        {{ !empty($customer->shipping_country) ? $customer->shipping_country : '' }}
                                                    </span>
                                                </p>
                                                <p class="mb-1 text-dark">
                                                    {{ !empty($customer->shipping_phone) ? $customer->shipping_phone : '' }}
                                                </p>
                                                <p class="mb-0">
                                                    <strong>{{ __('Tax Number ') }} :
                                                    </strong>{{ !empty($customer->tax_number) ? $customer->tax_number : '' }}
                                                </p>
                                            @endif
                                        @endif
                                    </div>
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
                                            <th data-width="40" class="text-white bg-primary text-uppercase">#</th>

                                            <th class="text-white bg-primary text-uppercase">{{ __('Item Type') }}</th>
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


                                        @foreach ($iteams as $key => $iteam)
                                            @if (!empty($iteam->tax))
                                                @php
                                                    $taxes = \Workdo\Quotation\Entities\Quotation::tax($iteam->tax);
                                                    $totalQuantity += $iteam->quantity;
                                                    $totalRate += $iteam->price;
                                                    $totalDiscount += $iteam->discount;
                                                    foreach ($taxes as $taxe) {
                                                        $taxDataPrice = \Workdo\Quotation\Entities\Quotation::taxRate(
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

                                                <td>{{ !empty($iteam->product_type) ? Str::ucfirst($iteam->product_type) : '--' }}
                                                <td>{{ !empty($iteam->product()) ? $iteam->product()->name : '' }}</td>
                                                <td>{{ $iteam->quantity }}</td>
                                                <td>{{ currency_format_with_sym($iteam->price) }}</td>
                                                <td>
                                                    {{ currency_format_with_sym($iteam->discount) }}
                                                </td>
                                                <td>
                                                    @if (!empty($iteam->tax))
                                                        <table class="w-100">
                                                            @php
                                                                $totalTaxRate = 0;
                                                                $data = 0;
                                                            @endphp
                                                            @foreach ($taxes as $tax)
                                                                @php
                                                                    $taxPrice = \Workdo\Quotation\Entities\Quotation::taxRate(
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
                                                @php
                                                    $tr_tex =
                                                        array_key_exists($key, $TaxPrice_array) == true
                                                            ? $TaxPrice_array[$key]
                                                            : 0;
                                                @endphp
                                                <td>
                                                    {{ !empty($iteam->description) ? $iteam->description : '-' }}</td>
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
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            @php
                                                $colspan = 7;
                                            @endphp
                                            <tr>
                                                <td colspan="{{ $colspan }}"></td>
                                                <td class="text-right"><b>{{ __('Sub Total') }}</b></td>
                                                <td class="text-right">
                                                    {{ currency_format_with_sym($quotation->getSubTotal()) }}</td>
                                            </tr>
                                            <tr>

                                                <td colspan="{{ $colspan }}"></td>
                                                <td class="text-right"><b>{{ __('Discount') }}</b></td>
                                                <td class="text-right">
                                                    {{ currency_format_with_sym($quotation->getTotalDiscount()) }}
                                                </td>
                                            </tr>
                                            @if (!empty($taxesData))
                                                @foreach ($taxesData as $taxName => $taxPrice)
                                                    <tr>
                                                        <td colspan="{{ $colspan }}"></td>
                                                        <td class="text-right"><b>{{ $taxName }}</b></td>
                                                        <td class="text-right">
                                                            {{ currency_format_with_sym($taxPrice) }}</td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                            <tr>
                                                <td colspan="{{ $colspan }}"></td>
                                                <td class="text-right"><b>{{ __('Total') }}</b></td>
                                                <td class="text-right">
                                                    {{ currency_format_with_sym($quotation->getTotal()) }}</td>
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
