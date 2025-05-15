@extends('layouts.main')
@section('page-title')
    {{ __('Sales Order Details') }}
@endsection
@section('title')
    {{ __('Sales Order') }} {{ '(' . $salesOrder->name . ')' }}
@endsection
@section('page-breadcrumb')
    {{ __('Sales Order') }},
    {{ __('Details') }}
@endsection
@section('page-action')
    <div class="d-flex">
        <a href="{{ route('salesorder.pdf', \Crypt::encrypt($salesOrder->id)) }}" target="_blank"
            class="btn btn-sm btn-primary btn-icon me-2" data-bs-toggle="tooltip" title="{{ __('Print') }}">
            <span class="btn-inner--icon text-white"><i class="ti ti-printer"></i></span>
        </a>
        <a href="#" class="btn btn-sm btn-warning btn-icon cp_link me-2"
            data-link="{{ route('pay.salesorder', \Illuminate\Support\Facades\Crypt::encrypt($salesOrder->id)) }}"
            data-bs-toggle="tooltip"
            data-title="{{ __('Click to copy SalesOrder link') }}"title="{{ __('copy link') }}"><span
                class="btn-inner--icon text-white"><i class="ti ti-file"></i></span></a>
        @permission('salesorder edit')
            <a href="{{ route('salesorder.edit', $salesOrder->id) }}" class="btn btn-sm btn-info btn-icon me-2"
                data-bs-toggle="tooltip" data-title="{{ __('Sales order Edit') }}" title="{{ __('Edit') }}"><i
                    class="ti ti-pencil"></i>
            </a>
        @endpermission
        @if (module_is_active('ProductService'))
            <a href="#" data-size="md" data-url="{{ route('salesorder.salesorderitem', $salesOrder->id) }}"
                data-ajax-popup="true" data-bs-toggle="tooltip" data-title="{{ __('Create Sales Order') }}"
                title="{{ __('Create') }}" class="btn btn-sm btn-primary btn-icon">
                <i class="ti ti-plus"></i>
            </a>
        @endif
    </div>
@endsection
@section('content')

    <div class="row">
        <div class="col-lg-10">
            <!-- [ Invoice ] start -->
            <div class="container">
                <div>
                    <div class="card" id="printTable">
                        <div class="card-body">
                            <div class="invoice">
                                <div class="invoice-print">
                                    <div class="row row-gap invoice-title border-1 border-bottom  pb-3 mb-3">
                                        <div class="col-sm-4  col-12">
                                            <h2 class="h3 mb-0">{{ __('SalesOrder') }}</h2>
                                        </div>
                                        <div class="col-sm-8  col-12">
                                            <div
                                                class="d-flex invoice-wrp flex-wrap align-items-center gap-md-2 gap-1 justify-content-end">
                                                <div
                                                    class="d-flex invoice-date flex-wrap align-items-center justify-content-end gap-md-3 gap-1">
                                                    <p class="mb-0"><strong>{{ __('SalesOrder Date') }}</strong>
                                                        {{ company_date_formate($salesOrder->created_at) }}</p>
                                                </div>
                                                <h3 class="invoice-number mb-0">
                                                    {{ Workdo\Sales\Entities\SalesOrder::salesorderNumberFormat($salesOrder->salesorder_id) }}
                                                </h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="p-sm-4 p-3 invoice-billed">
                                        <div class="row row-gap">
                                            <div class="col-lg-4 col-sm-6">
                                                <h5>{{ __('From') }}</h5>
                                                <dl class="row mt-4 align-items-center">
                                                    <dt class="col-sm-6"><span
                                                            class="h6 text-sm mb-0">{{ __('Company Address') }}</span></dt>
                                                    <dd class="col-sm-6"><span
                                                            class="text-sm">{{ $company_setting['company_address'] }}</span>
                                                    </dd>

                                                    <dt class="col-sm-6"><span
                                                            class="h6 text-sm mb-0">{{ __('Company City') }}</span></dt>
                                                    <dd class="col-sm-6"><span
                                                            class="text-sm">{{ $company_setting['company_city'] }}</span>
                                                    </dd>

                                                    <dt class="col-sm-6"><span
                                                            class="h6 text-sm mb-0">{{ __('Zip Code') }}</span>
                                                    </dt>
                                                    <dd class="col-sm-6"><span
                                                            class="text-sm">{{ $company_setting['company_zipcode'] }}</span>
                                                    </dd>

                                                    <dt class="col-sm-6"><span
                                                            class="h6 text-sm mb-0">{{ __('Company Country') }}</span></dt>
                                                    <dd class="col-sm-6"><span
                                                            class="text-sm">{{ $company_setting['company_country'] }}</span>
                                                    </dd>
                                                </dl>
                                            </div>
                                            <div class="col-lg-4 col-sm-6">
                                                <h5>{{ __('Billing Address') }}</h5>
                                                <dl class="row mt-4 align-items-center">
                                                    <dt class="col-sm-6"><span
                                                            class="h6 text-sm mb-0">{{ __('Billing Address') }}</span></dt>
                                                    <dd class="col-sm-6"><span
                                                            class="text-sm">{{ $salesOrder->billing_address }}</span></dd>

                                                    <dt class="col-sm-6"><span
                                                            class="h6 text-sm mb-0">{{ __('Billing City') }}</span></dt>
                                                    <dd class="col-sm-6"><span
                                                            class="text-sm">{{ $salesOrder->billing_city }}</span>
                                                    </dd>

                                                    <dt class="col-sm-6"><span
                                                            class="h6 text-sm mb-0">{{ __('Zip Code') }}</span>
                                                    </dt>
                                                    <dd class="col-sm-6"><span
                                                            class="text-sm">{{ $salesOrder->billing_postalcode }}</span>
                                                    </dd>

                                                    <dt class="col-sm-6"><span
                                                            class="h6 text-sm mb-0">{{ __('Billing Country') }}</span></dt>
                                                    <dd class="col-sm-6"><span
                                                            class="text-sm">{{ $salesOrder->billing_country }}</span></dd>

                                                    <dt class="col-sm-6"><span
                                                            class="h6 text-sm mb-0">{{ __('Billing Contact') }}</span></dt>
                                                    <dd class="col-sm-6"><span
                                                            class="text-sm">{{ !empty($salesOrder->contacts->name) ? $salesOrder->contacts->name : '--' }}</span>
                                                    </dd>
                                                </dl>
                                            </div>

                                            <div class="col-lg-2 col-sm-6">
                                                <strong class="h5 d-block mb-2">{{ __('Status :') }}</strong>
                                                @if ($salesOrder->status == 0)
                                                    <span
                                                        class="badge bg-primary p-2 px-3">{{ __(Workdo\Sales\Entities\SalesOrder::$status[$salesOrder->status]) }}</span>
                                                @elseif($salesOrder->status == 1)
                                                    <span
                                                        class="badge bg-danger p-2 px-3">{{ __(Workdo\Sales\Entities\SalesOrder::$status[$salesOrder->status]) }}</span>
                                                @elseif($salesOrder->status == 2)
                                                    <span
                                                        class="badge bg-warning p-2 px-3">{{ __(Workdo\Sales\Entities\SalesOrder::$status[$salesOrder->status]) }}</span>
                                                @elseif($salesOrder->status == 3)
                                                    <span
                                                        class="badge bg-success p-2 px-3">{{ __(Workdo\Sales\Entities\SalesOrder::$status[$salesOrder->status]) }}</span>
                                                @elseif($salesOrder->status == 4)
                                                    <span
                                                        class="badge bg-info p-2 px-3">{{ __(Workdo\Sales\Entities\SalesOrder::$status[$salesOrder->status]) }}</span>
                                                @endif
                                            </div>
                                            @if (!empty($company_setting['salesorder_qr_display']) && $company_setting['salesorder_qr_display'] == 'on')
                                                <div class="col-lg-2 col-sm-6">
                                                    <div class="float-sm-end qr-code">
                                                        <div class="col">
                                                            <div class="float-sm-end">
                                                                {!! DNS2D::getBarcodeHTML(
                                                                    route('pay.salesorder', \Illuminate\Support\Facades\Crypt::encrypt($salesOrder->id)),
                                                                    'QRCODE',
                                                                    2,
                                                                    2,
                                                                ) !!}

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif

                                        </div>
                                        @if (!empty($customFields) && count($salesOrder->customField) > 0)
                                            @foreach ($customFields as $field)
                                                <div class="col text-md-end">
                                                    <small>
                                                        <strong>{{ $field->name }} :</strong><br>
                                                        @if ($field->type == 'attachment')
                                                            <a href="{{ get_file($salesOrder->customField[$field->id]) }}"
                                                                target="_blank">
                                                                <img src=" {{ get_file($salesOrder->customField[$field->id]) }} "
                                                                    class="wid-75 rounded me-3">
                                                            </a>
                                                        @else
                                                            {{ !empty($salesOrder->customField[$field->id]) ? $salesOrder->customField[$field->id] : '-' }}
                                                        @endif
                                                        <br><br>
                                                    </small>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>


                                    <div class="invoice-summary mt-3">
                                        <div class="invoice-title border-1 border-bottom mb-3 pb-2">
                                            <h3 class="h4 mb-0">{{ __('Item Summary') }}</h3>
                                        </div>
                                        <div class="table-responsive mt-2">
                                            <table class="table mb-0 table-striped">
                                                <tbody>
                                                    <tr>
                                                        <th class="text-white bg-primary text-uppercase">
                                                            {{ __('#') }} </th>
                                                        <th class="text-white bg-primary text-uppercase">
                                                            {{ __('Item') }} </th>
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
                                                        <th class="text-right text-white bg-primary text-uppercase"
                                                            width="12%">#</th>
                                                    </tr>
                                                    @php
                                                        $totalQuantity = 0;
                                                        $totalRate = 0;
                                                        $totalAmount = 0;
                                                        $totalTaxPrice = 0;
                                                        $totalDiscount = 0;
                                                        $TaxPrice_array = [];
                                                        $taxesData = [];
                                                    @endphp
                                                    @foreach ($salesOrder->items as $key => $salesOrderitem)
                                                        @php
                                                            $taxes = Workdo\Sales\Entities\SalesUtility::tax(
                                                                $salesOrderitem->tax,
                                                            );
                                                            $totalQuantity += $salesOrderitem->quantity;
                                                            $totalRate += $salesOrderitem->price;
                                                            $totalDiscount += $salesOrderitem->discount;

                                                            if (!empty($taxes[0])) {
                                                                foreach ($taxes as $taxe) {
                                                                    $taxDataPrice = Workdo\Sales\Entities\SalesUtility::taxRate(
                                                                        $taxe->rate,
                                                                        $salesOrderitem->price,
                                                                        $salesOrderitem->quantity,
                                                                        $salesOrderitem->discount,
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
                                                            <td>{{ $key + 1 }}</td>
                                                            <td>{{ !empty($salesOrderitem->items()) ? $salesOrderitem->items()->name : '' }}
                                                            </td>
                                                            <td>{{ $salesOrderitem->quantity }} </td>
                                                            <td>{{ currency_format_with_sym($salesOrderitem->price) }}
                                                            </td>
                                                            <td>{{ currency_format_with_sym($salesOrderitem->discount) }}
                                                            </td>
                                                            <td>

                                                                @php
                                                                        $totalTaxPrice = 0;
                                                                        $data = 0;
                                                                        $taxPrice = 0;
                                                                    @endphp
                                                                    @if (module_is_active('ProductService'))
                                                                    <table>

                                                                        @if (!empty($salesOrderitem->tax))
                                                                            @php $totalTaxRate = 0;@endphp
                                                                            @foreach ($salesOrderitem->tax($salesOrderitem->tax) as $tax)
                                                                                @php
                                                                                    $taxPrice = Workdo\Sales\Entities\SalesUtility::taxRate(
                                                                                        $tax->rate,
                                                                                        $salesOrderitem->price,
                                                                                        $salesOrderitem->quantity,
                                                                                        $salesOrderitem->discount,
                                                                                    );
                                                                                    $totalTaxPrice += $taxPrice;
                                                                                    $data += $taxPrice;
                                                                                @endphp
                                                                                <tr>

                                                                                <td>{{ $tax->name . ' (' . $tax->rate . '%)' }}
                                                                                   {{ currency_format_with_sym($taxPrice) }}</td>
                                                                                </tr>
                                                                            @endforeach
                                                                            @php
                                                                                array_push($TaxPrice_array, $data);
                                                                            @endphp
                                                                        @else
                                                                            <td
                                                                                class="d-block text-sm text-muted">{{ __('No Tax') }}</td>
                                                                        @endif
                                                                    @else
                                                                        <td></td>
                                                                    @endif
                                                                    </table>
                                                            </td>

                                                            <td
                                                            >
                                                                {{ !empty($salesOrderitem->description) ? $salesOrderitem->description : '--' }}                                                            </td>
                                                            @php
                                                                $tr_tex =
                                                                    array_key_exists($key, $TaxPrice_array) == true
                                                                        ? $TaxPrice_array[$key]
                                                                        : 0;
                                                            @endphp
                                                            <td class="">
                                                                {{ currency_format_with_sym($salesOrderitem->price * $salesOrderitem->quantity - $salesOrderitem->discount + $tr_tex) }}
                                                            </td>
                                                            <td class="text-right">
                                                                @if (module_is_active('ProductService'))
                                                                    @permission('salesorder edit')
                                                                        <div class="action-btn me-2">
                                                                            <a href="#"
                                                                                data-url="{{ route('salesorder.item.edit', $salesOrderitem->id) }}"
                                                                                data-ajax-popup="true"
                                                                                class=" bg-info btn btn-sm align-items-center text-white"
                                                                                data-bs-toggle="tooltip"
                                                                                data-title="{{ __('Edit Item') }}"
                                                                                data-original-title="{{ __('Edit') }}"
                                                                                title="{{ __('Edit Item') }}"><i
                                                                                    class="ti ti-pencil"></i></a>
                                                                        </div>
                                                                    @endpermission
                                                                @endif
                                                                @permission('salesorder delete')
                                                                    <div class="action-btn">
                                                                        {!! Form::open(['method' => 'DELETE', 'route' => ['salesorder.items.delete', $salesOrderitem->id]]) !!}
                                                                        <a href="#!"
                                                                            class="bg-danger btn btn-sm  align-items-center text-white show_confirm"
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
                                                                $totalQuantity += $salesOrderitem->quantity;
                                                                $totalRate += $salesOrderitem->price;
                                                                $totalDiscount += $salesOrderitem->discount;
                                                                $totalAmount +=
                                                                    $salesOrderitem->price * $salesOrderitem->quantity;
                                                            @endphp
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                                <div class="invoice-billed ">
                                                    @php
                                                    $colspan = 6;
                                                    @endphp
                                                    <tfoot>
                                                        <tr>
                                                            <td colspan="{{$colspan}}"></td>
                                                            <td class="text-right">{{ __('Sub Total') }}</td>
                                                            <td class="text-right">
                                                                <b>{{ currency_format_with_sym($salesOrder->getSubTotal()) }}</b>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="{{$colspan}}"></td>
                                                            <td class="text-right">{{ __('Discount') }}</td>
                                                            <td class="text-right">
                                                                <b>{{ currency_format_with_sym($salesOrder->getTotalDiscount()) }}</b>
                                                            </td>
                                                        </tr>
                                                        @if (!empty($taxesData))
                                                            @foreach ($taxesData as $taxName => $taxPrice)
                                                                @if ($taxName != 'No Tax')
                                                                    <tr>
                                                                        <td colspan="{{$colspan}}"></td>
                                                                        <td class="text-right">{{ $taxName }}</td>
                                                                        <td class="text-right">
                                                                            <b>{{ currency_format_with_sym($taxPrice) }}</b>
                                                                        </td>
                                                                    </tr>
                                                                @endif
                                                            @endforeach
                                                        @endif
                                                        <tr>
                                                            <td colspan="{{$colspan}}"></td>
                                                            <td class="blue-text text-right">{{ __('Total') }}</td>
                                                            <td class="blue-text text-right">
                                                                <b>{{ currency_format_with_sym($salesOrder->getTotal()) }}</b>
                                                            </td>
                                                        </tr>
                                                    </tfoot>
                                                </div>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-2">
            <div class="card">
                <div class="card-footer py-0">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item px-0">
                            <div class="row align-items-center">
                                <dt class="col-sm-12"><span class="h6 text-sm mb-0">{{ __('Assigned User') }}</span>
                                </dt>
                                <dd class="col-sm-12"><span
                                        class="text-sm">{{ !empty($salesOrder->assign_user) ? $salesOrder->assign_user->name : '' }}</span>
                                </dd>

                                <dt class="col-sm-12"><span class="h6 text-sm mb-0">{{ __('Created') }}</span></dt>
                                <dd class="col-sm-12"><span
                                        class="text-sm">{{ company_date_formate($salesOrder->created_at) }}</span></dd>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- [ Invoice ] end -->
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
                        taxes += '<span class="badge bg-primary p-2 rounded">' + invoiceItems.taxes[i]
                            .name + ' ' + '(' + invoiceItems.taxes[i].rate + '%)' + '</span>';
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

