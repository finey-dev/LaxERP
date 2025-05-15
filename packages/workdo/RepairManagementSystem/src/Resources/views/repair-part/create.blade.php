@extends('layouts.main')
@section('page-title')
{{__('Repair Product Parts')}}
@endsection
@push('script-page')
@endpush
@section('page-breadcrumb')
{{__('Repair Product Parts')}}
@endsection
@php
$company_settings = getCompanyAllSetting();
@endphp
@section('content')
<div class="row">
    {{ Form::open(['route' => 'repair.parts.store', 'class' => 'w-100','id' => 'repair_parts_store']) }}
    <input type="hidden" name="repair_id" id="repair_id" value="{{$repair_order_request->id}}">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="container">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="font-style">
                                <label class="col-form-label" style="font-size: medium;"><b>{{ __('Product Name:  ') }}</b></label>
                                <label for="email1" class="col-form-label" style="font-size: medium;">{{$repair_order_request->product_name}}</label><br>
                                <label class="col-form-label" style="font-size: medium;"><b>{{ __('Product Quantity:') }}</b></label>
                                <label for="email1" class="col-form-label" style="font-size: medium;">{{$repair_order_request->product_quantity}}</label><br>
                                <label class="col-form-label" style="font-size: medium;"><b>{{ __('Customer Name:') }}</b></label>
                                <label for="email1" class="col-form-label" style="font-size: medium;">{{$repair_order_request->customer_name}}</label><br>
                                <label class="col-form-label" style="font-size: medium;"><b>{{ __('Customer Email:') }}</b></label>
                                <label for="email1" class="col-form-label" style="font-size: medium;">{{$repair_order_request->customer_email}}</label><br>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="font-style">
                                <label class="col-form-label" style="font-size: medium;"><b>{{ __('Customer Mobile No:') }}</b></label>
                                <label for="email1" class="col-form-label" style="font-size: medium;">{{$repair_order_request->customer_mobile_no}}</label><br>
                                <label class="col-form-label" style="font-size: medium;"><b>{{ __('Location:') }}</b></label>
                                <label for="email1" class="col-form-label" style="font-size: medium;">{{$repair_order_request->location}}</label><br>
                                <label class="col-form-label" style="font-size: medium;"><b>{{ __('Date:') }}</b></label>
                                <label for="email1" class="col-form-label" style="font-size: medium;">{{$repair_order_request->date}}</label><br>
                                <label class="col-form-label" style="font-size: medium;"><b>{{ __('Expiry Date:') }}</b></label>
                                <label for="email1" class="col-form-label" style="font-size: medium;">{{$repair_order_request->expiry_date}}</label><br>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col">
                            <small>
                                <h5><strong> {{ __('Status') }} :</strong><br></h5>
                                @if ($repair_order_request->status == 0)
                                    <span class="badge fix_badge bg-warning p-2 px-3">{{__('Pending')}}</span>
                                    @elseif($repair_order_request->status == 1)
                                    <span class="badge fix_badge bg-primary p-2 px-3">{{__('Start Repairing')}}</span>
                                    @elseif($repair_order_request->status == 2)
                                    <span class="badge fix_badge bg-primary p-2 px-3">{{__('End Repairing')}}</span>
                                    @elseif($repair_order_request->status == 3)
                                    <span class="badge fix_badge bg-primary p-2 px-3">{{__('Start Testing')}}</span>
                                    @elseif($repair_order_request->status == 4)
                                    <span class="badge fix_badge bg-primary p-2 px-3">{{__('End Testing')}}</span>
                                    @elseif($repair_order_request->status == 5)
                                    <span class="badge fix_badge bg-warning p-2 px-3">{{__('irreparable')}}</span>
                                    @elseif($repair_order_request->status == 6)
                                    <span class="badge fix_badge bg-danger p-2 px-3">{{__('Cnacel')}}</span>
                                    @elseif($repair_order_request->status == 7)
                                    <span class="badge fix_badge bg-info p-2 px-3">{{__('Invoice created')}}</span>
                                    @endif
                            </small>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="col-12 section_div">
            <h5 class="h4 d-inline-block font-weight-400 mb-4">{{ __('Parts Summary') }}</h5>
            <div class="card repeater">
                <div class="item-section py-4">
                    <div class="row justify-content-between align-items-center">
                        <div class="col-md-12 d-flex align-items-center justify-content-md-end px-5">
                            <a href="#" data-repeater-create="" class="btn btn-primary mr-2" data-toggle="modal" data-target="#add-bank">
                                <i class="ti ti-plus"></i> {{ __('Add item') }}
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body table-border-style mt-2">
                    <div class="table-responsive">
                        <table class="table  mb-0 table-custom-style" data-repeater-list="items" id="sortable-table">
                            <thead>
                                <tr>
                                    <th>{{ __('Items') }}</th>
                                    <th>{{ __('Quantity') }}</th>
                                    <th>{{ __('Price') }} </th>
                                    <th>{{ __('Discount') }}</th>
                                    <th>{{ __('Tax') }} (%)</th>
                                    <th class="text-end">{{ __('Amount') }} <br><small class="text-danger font-weight-bold">{{ __('After discount & tax') }}</small></th>
                                    <th></th>
                                </tr>
                            </thead>

                            <tbody class="ui-sortable" data-repeater-item>
                                <tr>
                                    {{ Form::hidden('id', null, ['class' => 'form-control id']) }}
                                    <td width="30%" class="form-group pt-0 product_div">
                                        <select name="item" class="form-control product_id item product_type js-searchBox" data-url="{{ route('repair.parts.product') }}" required>
                                            <option value="0">{{ __('Select Parts') }}</option>
                                            @foreach ($product_parts as $key => $product_part)
                                            <option value="{{ $key }}">{{ $product_part }}</option>
                                            @endforeach
                                        </select>
                                        @if (empty($product_parts_count))
                                        <div class=" text-xs">{{ __('Please create Product first.') }}<a href="{{ route('product-service.index') }}"><b>{{ __('Add Product') }}</b></a>
                                        </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="form-group price-input input-group search-form mb-0">
                                            {{ Form::text('quantity', '', ['class' => 'form-control quantity', 'required' => 'required', 'placeholder' => __('Qty'), 'required' => 'required']) }}
                                            <span class="unit input-group-text bg-transparent"></span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group price-input input-group search-form mb-0" style="width: 160px">
                                            {{ Form::text('price', '', ['class' => 'form-control price', 'required' => 'required', 'placeholder' => __('Price'), 'required' => 'required']) }}
                                            <span class="input-group-text bg-transparent">{{ isset($company_settings['defult_currancy_symbol']) ? $company_settings['defult_currancy_symbol'] : '' }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group price-input input-group search-form mb-0" style="width: 160px">
                                            {{ Form::text('discount', '', ['class' => 'form-control discount', 'required' => 'required', 'placeholder' => __('Discount')]) }}
                                            <span class="input-group-text bg-transparent">{{ isset($company_settings['defult_currancy_symbol']) ? $company_settings['defult_currancy_symbol'] : '' }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <div class="input-group colorpickerinput">
                                                <div class="taxes"></div>
                                                {{ Form::hidden('tax', '', ['class' => 'form-control tax text-dark']) }}
                                                {{ Form::hidden('itemTaxPrice', '', ['class' => 'form-control itemTaxPrice']) }}
                                                {{ Form::hidden('itemTaxRate', '', ['class' => 'form-control itemTaxRate']) }}
                                            </div>
                                        </div>
                                    </td>

                                    <td class="text-end amount">0.00</td>
                                    <td>
                                        <div class="repeater-action-btn action-btn  me-2">
                                        <a href="#" class="btn btn-sm bg-danger align-items-center bs-pass-para repeater-action-btn" data-repeater-delete>
                                                <span><i class="ti ti-trash text-white text-white"></i></span>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <div class="form-group">
                                            {{ Form::textarea('description', null, ['class' => 'form-control pro_description', 'rows' => '2', 'placeholder' => __('Description')]) }}
                                        </div>
                                    </td>
                                    <td colspan="5"></td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td><strong>{{ __('Sub Total') }}
                                            ({{ isset($company_settings['defult_currancy_symbol']) ? $company_settings['defult_currancy_symbol'] : '' }})</strong>
                                    </td>
                                    <td class="text-end subTotal">0.00</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td><strong>{{ __('Discount') }}
                                            ({{ isset($company_settings['defult_currancy_symbol']) ? $company_settings['defult_currancy_symbol'] : '' }})</strong>
                                    </td>
                                    <td class="text-end totalDiscount">0.00</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td><strong>{{ __('Tax') }}
                                            ({{ isset($company_settings['defult_currancy_symbol']) ? $company_settings['defult_currancy_symbol'] : '' }})</strong>
                                    </td>
                                    <td class="text-end totalTax">0.00</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td class="blue-text"><strong>{{ __('Total Amount') }}
                                            ({{ isset($company_settings['defult_currancy_symbol']) ? $company_settings['defult_currancy_symbol'] : '' }})</strong>
                                    </td>
                                    <td class="text-end totalAmount blue-text">0.00</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <input type="button" value="{{ __('Cancel') }}" onclick="location.href = '{{ route('repair.request.index') }}';" class="btn btn-light ">
            <input type="submit" value="{{ __('Create') }}" class="btn  btn-primary mx-3 submit">
        </div>
        {{ Form::close() }}
    </div>
    @endsection
    @push('scripts')
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('js/jquery.repeater.min.js') }}"></script>
    <script src="{{ asset('js/jquery-searchbox.js') }}"></script>
    <script>
        var selector = "body";
        if ($(selector + " .repeater").length) {
            var $repeater = $(selector + ' .repeater').repeater({
                initEmpty: false,
                defaultValues: {
                    'status': 1
                },
                show: function() {
                    $(this).slideDown();
                    var file_uploads = $(this).find('input.multi');
                    if (file_uploads.length) {
                        $(this).find('input.multi').MultiFile({
                            max: 3,
                            accept: 'png|jpg|jpeg',
                            max_size: 2048
                        });
                    }
                    // JsSearchBox();
                },
                hide: function(deleteElement) {
                    if (confirm('Are you sure you want to delete this element?')) {
                        $(this).slideUp(deleteElement);
                        $(this).remove();

                        var inputs = $(".amount");
                        var subTotal = 0;
                        for (var i = 0; i < inputs.length; i++) {
                            subTotal = parseFloat(subTotal) + parseFloat($(inputs[i]).html());
                        }
                        $('.subTotal').html(subTotal.toFixed(2));
                        $('.totalAmount').html(subTotal.toFixed(2));
                    }
                },

                ready: function(setIndexes) {
                    // $dragAndDrop.on('drop', setIndexes);
                },
                isFirstItemUndeletable: true
            });
        }
    </script>
    <script>
        var invoice_id = '{{ $repair_order_request->id }}';

        $(document).on('change', '.product_type', function() {
            var iteams_id = $(this).val();

            var url = $(this).data('url');
            var el = $(this);

            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    'product_id': iteams_id
                },
                cache: false,
                success: function(data) {
                    var item = JSON.parse(data);
                    $(el.parent().parent().find('.quantity')).val(1);
                    if (item.product != null) {
                        $(el.parent().parent().find('.price')).val(item.product.sale_price);
                        $(el.parent().parent().parent().find('.pro_description')).val(item.product.description);

                    } else {
                        $(el.parent().parent().find('.price')).val(0);
                        $(el.parent().parent().parent().find('.pro_description')).val('');
                    }

                    var taxes = '';
                    var tax = [];
                    var totalItemTaxRate = 0;

                    if (item.taxes == 0) {
                        taxes += '-';
                    } else {
                        for (var i = 0; i < item.taxes.length; i++) {
                            taxes += '<span class="badge bg-primary p-2 px-3 mt-1 me-1">' +
                                item.taxes[i].name + ' ' + '(' + item.taxes[i].rate + '%)' +
                                '</span>';
                            tax.push(item.taxes[i].id);
                            totalItemTaxRate += parseFloat(item.taxes[i].rate);
                        }
                    }

                    var itemTaxPrice = 0;
                    if (item.product != null) {
                        var itemTaxPrice = parseFloat((totalItemTaxRate / 100) * (item.product.sale_price * 1));
                    }
                    $(el.parent().parent().find('.itemTaxPrice')).val(itemTaxPrice.toFixed(2));
                    $(el.parent().parent().find('.itemTaxRate')).val(totalItemTaxRate.toFixed(2));
                    $(el.parent().parent().find('.taxes')).html(taxes);
                    $(el.parent().parent().find('.tax')).val(tax);
                    $(el.parent().parent().find('.unit')).html(item.unit);
                    $(el.parent().parent().find('.discount')).val(0);
                    $(el.parent().parent().find('.amount')).html(item.totalAmount);

                    var inputs = $(".amount");
                    var subTotal = 0;
                    for (var i = 0; i < inputs.length; i++) {
                        subTotal = parseFloat(subTotal) + parseFloat($(inputs[i]).html());
                    }

                    var totalItemPrice = 0;
                    var priceInput = $('.price');
                    for (var j = 0; j < priceInput.length; j++) {
                        totalItemPrice += parseFloat(priceInput[j].value);
                    }

                    var totalItemTaxPrice = 0;
                    var itemTaxPriceInput = $('.itemTaxPrice');
                    for (var j = 0; j < itemTaxPriceInput.length; j++) {
                        totalItemTaxPrice += parseFloat(itemTaxPriceInput[j].value);
                        if (item.product != null) {
                            $(el.parent().parent().find('.amount')).html(parseFloat(item.totalAmount) +
                                parseFloat(itemTaxPriceInput[j].value));
                        }
                    }

                    var totalItemDiscountPrice = 0;
                    var itemDiscountPriceInput = $('.discount');

                    for (var k = 0; k < itemDiscountPriceInput.length; k++) {
                        totalItemDiscountPrice += parseFloat(itemDiscountPriceInput[k].value);
                    }

                    $('.subTotal').html(totalItemPrice.toFixed(2));
                    $('.totalTax').html(totalItemTaxPrice.toFixed(2));
                    $('.totalAmount').html((parseFloat(totalItemPrice) - parseFloat(totalItemDiscountPrice) +
                        parseFloat(totalItemTaxPrice)).toFixed(2));
                },
            });
        });
        $(document).on('click', '[data-repeater-create]', function() {
            $('.item :selected').each(function() {
                var id = $(this).val();
                $(".item option[value=" + id + "]").addClass("d-none");
            });
        })
    </script>
    <Script>
        $(document).on('keyup', '.quantity', function() {
            var quntityTotalTaxPrice = 0;

            var el = $(this).parent().parent().parent().parent();

            var quantity = $(this).val();
            var price = $(el.find('.price')).val();
            var discount = $(el.find('.discount')).val();
            if (discount.length <= 0) {
                discount = 0;
            }

            var totalItemPrice = (quantity * price) - discount;

            var amount = (totalItemPrice);


            var totalItemTaxRate = $(el.find('.itemTaxRate')).val();
            var itemTaxPrice = parseFloat((totalItemTaxRate / 100) * (totalItemPrice));
            $(el.find('.itemTaxPrice')).val(itemTaxPrice.toFixed(2));

            $(el.find('.amount')).html(parseFloat(itemTaxPrice) + parseFloat(amount));

            var totalItemTaxPrice = 0;
            var itemTaxPriceInput = $('.itemTaxPrice');
            for (var j = 0; j < itemTaxPriceInput.length; j++) {
                totalItemTaxPrice += parseFloat(itemTaxPriceInput[j].value);
            }


            var totalItemPrice = 0;
            var inputs_quantity = $(".quantity");

            var priceInput = $('.price');
            for (var j = 0; j < priceInput.length; j++) {
                totalItemPrice += (parseFloat(priceInput[j].value) * parseFloat(inputs_quantity[j].value));
            }

            var inputs = $(".amount");

            var subTotal = 0;
            for (var i = 0; i < inputs.length; i++) {
                subTotal = parseFloat(subTotal) + parseFloat($(inputs[i]).html());
            }

            $('.subTotal').html(totalItemPrice.toFixed(2));
            $('.totalTax').html(totalItemTaxPrice.toFixed(2));

            $('.totalAmount').html((parseFloat(subTotal)).toFixed(2));
        })

        $(document).on('keyup change', '.price', function() {
            var el = $(this).parent().parent().parent().parent();
            var price = $(this).val();
            var quantity = $(el.find('.quantity')).val();
            if (quantity.length <= 0) {
                quantity = 1;
            }
            var discount = $(el.find('.discount')).val();
            if (discount.length <= 0) {
                discount = 0;
            }
            var totalItemPrice = (quantity * price) - discount;

            var amount = (totalItemPrice);

            var totalItemTaxRate = $(el.find('.itemTaxRate')).val();
            var itemTaxPrice = parseFloat((totalItemTaxRate / 100) * (totalItemPrice));
            $(el.find('.itemTaxPrice')).val(itemTaxPrice.toFixed(2));

            $(el.find('.amount')).html(parseFloat(itemTaxPrice) + parseFloat(amount));

            var totalItemTaxPrice = 0;
            var itemTaxPriceInput = $('.itemTaxPrice');
            for (var j = 0; j < itemTaxPriceInput.length; j++) {
                totalItemTaxPrice += parseFloat(itemTaxPriceInput[j].value);
            }


            var totalItemPrice = 0;
            var inputs_quantity = $(".quantity");
            var priceInput = $('.price');
            for (var j = 0; j < priceInput.length; j++) {
                if (inputs_quantity[j].value <= 0) {
                    inputs_quantity[j].value = 1;
                }
                totalItemPrice += (parseFloat(priceInput[j].value) * parseFloat(inputs_quantity[j].value));
            }

            var inputs = $(".amount");

            var subTotal = 0;
            for (var i = 0; i < inputs.length; i++) {
                subTotal = parseFloat(subTotal) + parseFloat($(inputs[i]).html());
            }

            $('.subTotal').html(totalItemPrice.toFixed(2));
            $('.totalTax').html(totalItemTaxPrice.toFixed(2));

            $('.totalAmount').html((parseFloat(subTotal)).toFixed(2));
        })

        $(document).on('keyup change', '.discount', function() {
            var el = $(this).parent().parent().parent();
            var discount = $(this).val();
            if (discount.length <= 0) {
                discount = 0;
            }

            var price = $(el.find('.price')).val();
            var quantity = $(el.find('.quantity')).val();
            var totalItemPrice = (quantity * price) - discount;


            var amount = (totalItemPrice);


            var totalItemTaxRate = $(el.find('.itemTaxRate')).val();
            var itemTaxPrice = parseFloat((totalItemTaxRate / 100) * (totalItemPrice));
            $(el.find('.itemTaxPrice')).val(itemTaxPrice.toFixed(2));

            $(el.find('.amount')).html(parseFloat(itemTaxPrice) + parseFloat(amount));

            var totalItemTaxPrice = 0;
            var itemTaxPriceInput = $('.itemTaxPrice');
            for (var j = 0; j < itemTaxPriceInput.length; j++) {
                totalItemTaxPrice += parseFloat(itemTaxPriceInput[j].value);
            }


            var totalItemPrice = 0;
            var inputs_quantity = $(".quantity");

            var priceInput = $('.price');
            for (var j = 0; j < priceInput.length; j++) {
                totalItemPrice += (parseFloat(priceInput[j].value) * parseFloat(inputs_quantity[j].value));
            }

            var inputs = $(".amount");

            var subTotal = 0;
            for (var i = 0; i < inputs.length; i++) {
                subTotal = parseFloat(subTotal) + parseFloat($(inputs[i]).html());
            }


            var totalItemDiscountPrice = 0;
            var itemDiscountPriceInput = $('.discount');

            for (var k = 0; k < itemDiscountPriceInput.length; k++) {
                if (itemDiscountPriceInput[k].value == '') {
                    itemDiscountPriceInput[k].value = parseFloat(0);
                }
                totalItemDiscountPrice += parseFloat(itemDiscountPriceInput[k].value);
            }


            $('.subTotal').html(totalItemPrice.toFixed(2));
            $('.totalTax').html(totalItemTaxPrice.toFixed(2));

            $('.totalAmount').html((parseFloat(subTotal)).toFixed(2));
            $('.totalDiscount').html(totalItemDiscountPrice.toFixed(2));
        })
    </Script>
    @endpush
