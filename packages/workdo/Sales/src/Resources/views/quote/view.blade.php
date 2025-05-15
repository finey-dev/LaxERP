@extends('layouts.main')
@section('page-title')
    {{ __('Quote Details') }}
@endsection
@section('title')
    {{ __('Quote') }} {{ '(' . $quote->name . ')' }}
@endsection
@section('page-breadcrumb')
    {{ __('Quote') }},
    {{ __('Details') }}
@endsection
@section('page-action')
    <div class="d-flex">
        <a href="{{ route('quote.pdf', \Crypt::encrypt($quote->id)) }}" target="_blank"
            class="btn btn-sm btn-primary btn-icon me-2" data-bs-toggle="tooltip" title="{{ __('Print') }}">
            <span class="btn-inner--icon text-white"><i class="ti ti-printer"></i></span>
        </a>
        @if (Auth::user()->type == 'company')
            <a class="btn btn-sm btn-warning btn-icon cp_link me-2"
                data-link="{{ route('pay.quote', \Illuminate\Support\Facades\Crypt::encrypt($quote->id)) }}"
                data-bs-toggle="tooltip"
                data-title="{{ __('Click to copy Quote link') }}"title="{{ __('copy link') }}"><span
                    class="btn-inner--icon text-white"><i class="ti ti-file"></i></span></a>
        @endif
        @permission('quote edit')
            <a href="{{ route('quote.edit', $quote->id) }}" class="btn btn-sm btn-info btn-icon me-2" data-bs-toggle="tooltip"
                title="{{ __('Edit') }}" data-title="{{ __('Edit Quote') }}"><i class="ti ti-pencil"></i></a>
        @endpermission
        @if (module_is_active('ProductService'))
            <a data-url="{{ route('quote.quoteitem', $quote->id) }}" data-size="md" data-ajax-popup="true"
                data-bs-toggle="tooltip" data-title="{{ __('Create Quote Item') }}"title="{{ __('Create') }}"
                class="btn btn-sm btn-primary btn-icon">
                <i class="ti ti-plus"></i>
            </a>
        @endif
    </div>
@endsection
@section('content')
    <!-- [ Invoice ] end -->
    <div class="card mt-sm-4 mt-2">
        <div class="card-body p-3">
           <div class="mb-2">
                <h6 class="mb-1">{{ __('Assigned User') }}</h6>
                <span class="text-sm">{{ !empty($quote->assign_user) ? $quote->assign_user->name : '' }}</span>
           </div>
            <div>
                <h6 class="mb-1">{{ __('Created') }}</h6>
                <span class="text-sm">{{ $quote->created_at }}</span>
            </div>
        </div>
    </div>
    <!-- [ Invoice ] start -->
    <div class="card" id="printTable">
        <div class="card-body">
            <div class="invoice">
                <div class="invoice-print">
                    <div class="row row-gap invoice-title border-1 border-bottom  pb-3 mb-3">
                        <div class="col-sm-4  col-12">
                            <h2 class="h3 mb-0 text-capitalize">{{ __('quote') }}</h2>
                        </div>
                        <div class="col-sm-8  col-12">
                            <div
                                class="d-flex invoice-wrp flex-wrap align-items-center gap-md-2 gap-1 justify-content-end">
                                <p class="mb-0">
                                    <strong>{{ __('Quote Date') }}</strong>{{ company_date_formate($quote->created_at) }}
                                </p>
                                <h3 class="invoice-number mb-0">
                                    {{ Workdo\Sales\Entities\Quote::quoteNumberFormat($quote->quote_id) }}
                                </h3>
                            </div>
                        </div>
                    </div>
                    <div class="p-sm-4 p-3 invoice-billed">
                        <div class="row row-gap">
                            <div class="col-xxl-3 col-md-4 col-sm-6">
                                <h5>{{ __('From') }}</h5>
                                <ul class="row p-0 mb-0">
                                    <li class="d-flex flex-wrap gap-1 mb-1">
                                        <strong>{{ __('Company Address') }} :</strong>
                                        <span>{{ $company_setting['company_address'] }}</span>
                                    </li>
                                    <li class="d-flex flex-wrap gap-1 mb-1">
                                        <strong>{{ __('Company City') }} :</strong>
                                        <span>{{ $company_setting['company_city'] }}</span>
                                    </li>
                                    <li class="d-flex flex-wrap gap-1 mb-1">
                                        <strong>{{ __('Zip Code') }} :</strong>
                                        <span>{{ $company_setting['company_zipcode'] }}</span>
                                    </li>
                                    <li class="d-flex flex-wrap gap-1">
                                        <strong>{{ __('Company Country') }} :</strong>
                                        <span>{{ $company_setting['company_country'] }}</span>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-xxl-3 col-md-4 col-sm-6">
                                <h5>{{ __('Billing Address') }}</h5>
                                <ul class="p-0  mb-0">
                                    <li class="d-flex flex-wrap gap-1 mb-1">
                                        <strong>{{ __('Billing Address') }} :</strong>
                                        <span>{{ $quote->billing_address }}</span>
                                    </li>
                                    <li class="d-flex flex-wrap gap-1 mb-1">
                                        <strong>{{ __('Billing City') }} :</strong>
                                        <span>{{ $quote->billing_city }}</span>
                                    </li>
                                    <li class="d-flex flex-wrap gap-1 mb-1">
                                        <strong>{{ __('Zip Code') }} :</strong>
                                        <span>{{ $quote->billing_postalcode }}</span>
                                    </li>
                                    <li class="d-flex flex-wrap gap-1">
                                        <strong>{{ __('Billing Country') }} :</strong>
                                        <span>{{ $quote->billing_country }}</span>
                                    </li>
                                </ul>
                            </div>
                            @if ($company_setting['quote_shipping_display'] == 'on')
                                <div class="col-xxl-3 col-md-4 col-sm-6">
                                    <h5>{{ __('Shipping Address') }}</h5>
                                    <ul class="p-0  mb-0">
                                        <li class="d-flex flex-wrap gap-1 mb-1">
                                            <strong>{{ __('Shipping Address') }} :</strong>
                                            <span>{{ $quote->shipping_address }}</span>
                                        </li>
                                        <li class="d-flex flex-wrap gap-1 mb-1">
                                            <strong>{{ __('Shipping City') }} :</strong>
                                            <span>{{ $quote->shipping_city }}</span>
                                        </li>
                                        <li class="d-flex flex-wrap gap-1 mb-1">
                                            <strong>{{ __('Zip Code') }} :</strong>
                                            <span>{{ $quote->shipping_postalcode }}</span>
                                        </li>
                                        <li class="d-flex flex-wrap gap-1 mb-1">
                                            <strong>{{ __('Shipping Country') }} :</strong>
                                            <span>{{ $quote->shipping_country }}</span>
                                        </li>
                                        <li class="d-flex flex-wrap gap-1">
                                            <strong>{{ __('Shipping Contact') }} :</strong>
                                            <span>{{ !empty($quote->contacts->name) ? $quote->contacts->name : '--' }}</span>
                                        </li>
                                    </ul>
                                </div>
                            @endif
                            <div class="col-xxl-1 col-md-4 col-sm-6">
                                <h6 class="mb-2 d-print-none">{{ __('Quote ') }} :</h6>
                                @if ($quote->status == 0)
                                    <span class="badge bg-primary p-2 f-12 ">{{ __(Workdo\Sales\Entities\Quote::$status[$quote->status]) }}</span>
                                @elseif($quote->status == 1)
                                    <span class="badge bg-danger p-2 f-12 ">{{ __(Workdo\Sales\Entities\Quote::$status[$quote->status]) }}</span>
                                @elseif($quote->status == 2)
                                    <span class="badge bg-warning p-2 f-12 ">{{ __(Workdo\Sales\Entities\Quote::$status[$quote->status]) }}</span>
                                @elseif($quote->status == 3)
                                    <span class="badge bg-success p-2 f-12 ">{{ __(Workdo\Sales\Entities\Quote::$status[$quote->status]) }}</span>
                                @elseif($quote->status == 4)
                                    <span class="badge bg-info p-2 f-12 ">{{ __(Workdo\Sales\Entities\Quote::$status[$quote->status]) }}</span>
                                @endif
                                @if (!empty($company_setting['quote_qr_display']) && $company_setting['quote_qr_display'] == 'on')
                            </div>
                            <div class="col-xxl-2 col-md-4 col-sm-6">
                                <div class="float-xxl-end qr-code">
                                    {!! DNS2D::getBarcodeHTML(
                                        route('pay.quote', \Illuminate\Support\Facades\Crypt::encrypt($quote->id)),
                                        'QRCODE',
                                        2,
                                        2,
                                    ) !!}
                                </div>
                            </div>

                        </div>
                    </div>
                    @endif
                    @if (!empty($customFields) && count($quote->customField) > 0)
                        @foreach ($customFields as $field)
                            <div class="col text-md-end">
                                <small>
                                    <strong>{{ $field->name }} :</strong><br>
                                    @if ($field->type == 'attachment')
                                        <a href="{{ get_file($quote->customField[$field->id]) }}"
                                            target="_blank">
                                            <img src=" {{ get_file($quote->customField[$field->id]) }} "
                                                class="wid-75 rounded me-3">
                                        </a>
                                    @else
                                        {{ !empty($quote->customField[$field->id]) ? $quote->customField[$field->id] : '-' }}
                                    @endif
                                    <br><br>
                                </small>
                            </div>
                        @endforeach
                    @endif
                    <div class="invoice-summary mt-3">
                        <div class="invoice-title border-1 border-bottom mb-3 pb-2">
                            <h3 class="h4 mb-0">{{ __('Item List') }}</h3>
                        </div>
                        <div class="table-responsive mt-2 top-10-scroll">
                            <table class="table table-striped">
                                <thead>
                                    <tr class="thead-default">
                                        <th class="text-white bg-primary text-uppercase">#</th>
                                        <th class="text-white bg-primary text-uppercase">
                                            {{ __('Item') }}</th>
                                        <th class="text-white bg-primary text-uppercase">
                                            {{ __('Quantity') }}</th>
                                        <th class="text-white bg-primary text-uppercase">
                                            {{ __('Price') }}</th>
                                        <th class="text-white bg-primary text-uppercase">
                                            {{ __('Discount') }}</th>
                                        <th class="text-white bg-primary text-uppercase">
                                            {{ __('Tax') }}</th>
                                        <th class="text-white bg-primary text-uppercase">
                                            {{ __('Description') }}</th>
                                        <th class="text-white bg-primary text-uppercase">
                                            {{ __('Price') }}</th>
                                        <th class="text-white bg-primary text-uppercase"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $totalQuantity = 0;
                                        $totalRate = 0;
                                        $totalAmount = 0;
                                        $totalTaxPrice = 0;
                                        $totalDiscount = 0;
                                        $TaxPrice_array = [];
                                        $taxesData = [];
                                    @endphp
                                    @foreach ($quote->items as $key => $quoteitem)
                                        @php
                                            $taxes = Workdo\Sales\Entities\SalesUtility::tax(
                                                $quoteitem->tax,
                                            );
                                            $totalQuantity += $quoteitem->quantity;
                                            $totalRate += $quoteitem->price;
                                            $totalDiscount += $quoteitem->discount;
                                            if (!empty($taxes[0])) {
                                                foreach ($taxes as $taxe) {
                                                    $taxDataPrice = Workdo\Sales\Entities\SalesUtility::taxRate(
                                                        $taxe->rate,
                                                        $quoteitem->price,
                                                        $quoteitem->quantity,
                                                        $quoteitem->discount,
                                                    );
                                                    if (array_key_exists($taxe->name, $taxesData)) {
                                                        $taxesData[$taxe->name] =
                                                            $taxesData[$taxe->name] + $taxDataPrice;
                                                    } else {
                                                        $taxesData[$taxe->name] = $taxDataPrice;
                                                    }
                                                }
                                            }
                                        @endphp
                                        <tr>
                                            <td>{{ $quoteitem->quantity }} </td>
                                            <td>{{ !empty($quoteitem->items()) ? $quoteitem->items()->name : '' }}
                                            </td>
                                            <td>{{ $quoteitem->quantity }} </td>
                                            <td>{{ currency_format_with_sym($quoteitem->price) }} </td>
                                            <td class="px-0">
                                                {{ currency_format_with_sym($quoteitem->discount) }} </td>
                                            <td>
                                                <table class="w-100">
                                                    <tbody>
                                                        @php
                                                            $totalTaxPrice = 0;
                                                            $data = 0;
                                                            $taxPrice = 0;
                                                        @endphp
                                                        @if (module_is_active('ProductService'))
                                                            @if (!empty($quoteitem->tax))
                                                                @foreach ($quoteitem->tax($quoteitem->tax) as $tax)
                                                                    @php
                                                                        $taxPrice = Workdo\Sales\Entities\SalesUtility::taxRate(
                                                                            $tax->rate,
                                                                            $quoteitem->price,
                                                                            $quoteitem->quantity,
                                                                            $quoteitem->discount,
                                                                        );
                                                                        $totalTaxPrice += $taxPrice;
                                                                        $data += $taxPrice;
                                                                    @endphp
                                                                    <tr>
                                                                        <td>{{ $tax->name . ' (' . $tax->rate . '%)' }}
                                                                            &nbsp;&nbsp;{{ currency_format_with_sym($taxPrice) }}
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                                @php
                                                                    array_push($TaxPrice_array, $data);
                                                                @endphp
                                                            @else
                                                                <tr>
                                                                    <td>{{ __('No Tax') }}
                                                                    </td>
                                                                </tr>
                                                                <a href="#!"
                                                                    class="d-block text-sm text-muted">{{ __('No Tax') }}</a>
                                                            @endif
                                                        @endif
                                                    </tbody>
                                                </table>
                                            </td>
                                            <td>
                                                {{ !empty($quoteitem->description) ? $quoteitem->description : '--' }}
                                            </td>
                                            @php
                                                $tr_tex =
                                                    array_key_exists($key, $TaxPrice_array) == true
                                                        ? $TaxPrice_array[$key]
                                                        : 0;
                                            @endphp
                                            <td>
                                                {{ currency_format_with_sym($quoteitem->price * $quoteitem->quantity - $quoteitem->discount + $tr_tex) }}
                                            </td>
                                            <td>
                                                @if (module_is_active('ProductService'))
                                                    @permission('quote edit')
                                                        <div class="action-btn bg-info">
                                                            <a data-url="{{ route('quote.quoteitem.edit', $quoteitem->id) }}"
                                                                data-ajax-popup="true"
                                                                class="btn btn-sm d-inline-flex align-items-center text-white"
                                                                data-bs-toggle="tooltip"
                                                                title="{{ __('Edit') }}"
                                                                data-title="{{ __('Edit Quote') }}"><i
                                                                    class="ti ti-pencil"></i></a>
                                                        </div>
                                                    @endif
                                                @endpermission
                                                @permission('quote delete')
                                                    <div class="action-btn bg-danger ms-2">
                                                        {!! Form::open(['method' => 'DELETE', 'route' => ['quote.items.delete', $quoteitem->id]]) !!}
                                                        <a href="#!"
                                                            class="btn btn-sm  align-items-center text-white show_confirm"
                                                            data-bs-toggle="tooltip"
                                                            title="{{ __('Delete') }}"
                                                            data-confirm="{{ __('Are You Sure?') }}"
                                                            data-text="{{ __('This action can not be undone. Do you want to continue?') }}">
                                                            <i class="ti ti-trash"></i>
                                                        </a>
                                                        {!! Form::close() !!}
                                                    </div>
                                                @endpermission
                                            </td>
                                            @php
                                                $totalQuantity += $quoteitem->quantity;
                                                $totalRate += $quoteitem->price;
                                                $totalDiscount += $quoteitem->discount;
                                                $totalAmount +=
                                                    $quoteitem->price * $quoteitem->quantity;
                                            @endphp
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="7"></td>
                                        <td><strong>{{ __('Sub Total :') }}</strong></td>
                                        <td>{{ currency_format_with_sym($quote->getSubTotal()) }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="7"></td>
                                        <td><strong>{{ __('Discount :') }}</strong></td>
                                        <td>{{ currency_format_with_sym($quote->getTotalDiscount()) }}
                                        </td>
                                    </tr>
                                    @if (!empty($taxesData))
                                        @foreach ($taxesData as $taxName => $taxPrice)
                                            @if ($taxName != 'No Tax')
                                                <tr>
                                                    <td colspan="7"></td>
                                                    <td><strong>{{ $taxName }} :</strong></td>
                                                    <td>{{ currency_format_with_sym($taxPrice) }}</td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    @endif
                                    <tr>
                                        <td colspan="7"></td>
                                        <td>
                                            <h5 class="text-primary mb-0">{{ __('Total :') }}</h5>
                                        </td>
                                        <td>
                                            <h5 class="text-primary subTotal mb-0">
                                                {{ currency_format_with_sym($quote->getTotal()) }}
                                            </h5>
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

@endsection
@push('scripts')
    <script>
        $(document).on('change', 'select[name=item]', function() {
            var item_id = $(this).val();
            $.ajax({
                url: '{{ route('quote.items') }}',
                type: 'GET',
                headers: {
                    'X-CSRF-TOKEN': jQuery('#token').val()
                },
                data: {
                    'item_id': item_id,
                },
                cache: false,
                success: function(data) {
                    var invoiceItems = JSON.parse(data);
                    $('.taxId').val('');
                    $('.tax').html('');

                    $('.price').val(invoiceItems.sale_price);
                    $('.quantity').val(1);
                    $('.discount').val(0);
                    var taxes = '';
                    var tax = [];

                    for (var i = 0; i < invoiceItems.taxes.length; i++) {
                        taxes += '<span class="badge bg-primary p-2 mx-1">' + invoiceItems
                            .taxes[i].name + ' ' + '(' + invoiceItems.taxes[i].rate + '%)' + '</span>';
                    }
                    $('.taxId').val(invoiceItems.tax_id);
                    $('.tax').html(taxes);
                }
            });
        });
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
