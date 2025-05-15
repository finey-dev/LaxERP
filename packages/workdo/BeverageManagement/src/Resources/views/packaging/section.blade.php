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
                JsSearchBox();
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
                    $('.final_amount').val(subTotal.toFixed(2));
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
    $(document).on('change', '.raw_material_id', function() {

        var raw_material_id = $(this).val();

        var url = $(this).data('url');
        var el = $(this);
        $.ajax({
            url: url,
            type: 'POST',

            data: {
                raw_material_id: raw_material_id,
                "_token": "{{ csrf_token() }}",
            },
            success: function(data) {
                console.log(data);
                var item = data;

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
                        taxes += '<span class="badge bg-primary p-2 px-3  mt-1 me-1">' +
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
                $(el.parent().parent().find('.unit')).val(item.unit);
                $(el.parent().parent().find('.discount')).val(0);
                $(el.parent().parent().find('.amount')).html(item.totalAmount);
                $(el.parent().parent().find('.total_amount')).val(item.totalAmount);

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
                        $(el.parent().parent().find('.total_amount')).val(parseFloat(item.totalAmount) +
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
                $('.final_amount').val((parseFloat(totalItemPrice) - parseFloat(totalItemDiscountPrice) +
                    parseFloat(totalItemTaxPrice)).toFixed(2));

            }
        });
    });

    $(document).on('click', '[data-repeater-create]', function() {
        $('.raw_material_id :selected').each(function() {
            var id = $(this).val();
            if (id) {
                $(".raw_material_id option[value='" + id + "']").addClass('d-none');
            }
        });
    });
</script>
@if ($action == 'edit')
<script>
    $(document).ready(function() {

        var value = $(selector + " .repeater").attr('data-value');
        if (typeof value != 'undefined' && value.length != 0) {
            value = JSON.parse(value);
            $repeater.setList(value);
            for (var i = 0; i < value.length; i++) {
                var raw_material_id = value[i].raw_material_id;
                var tr = $('#sortable-table tbody').find('tr').filter(function() {
                    return $(this).find('.raw_material_id').val() == raw_material_id;
                });
                changeItem(raw_material_id, tr.find('.raw_material_id'));

            }
        }
    });
</script>

<script>
    var packaging_id = '{{ $packaging_id }}';

    function changeItem(raw_material_id, element) {
        var url = element.data('url');
        var el = element;
        $.ajax({
            url: "{{ route('raw.material') }}",
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                'raw_material_id': raw_material_id
            },
            cache: false,
            success: function(data) {
                var item = data;
                console.log({
                    item
                });

                $.ajax({
                    url: "{{ route('package.raw.material.items') }}",
                    type: 'GET',
                    data: {
                        _token: "{{ csrf_token() }}",
                        'packaging_id': packaging_id,
                        'raw_material_id': raw_material_id,
                    },
                    cache: false,
                    success: function(data) {
                        var invoiceItems = JSON.parse(data);
                        console.log(invoiceItems);

                        if (invoiceItems != null) {
                            var amount = (invoiceItems.price * invoiceItems.quantity);

                            $(el.parent().parent().find('.quantity')).val(invoiceItems
                                .quantity);
                            $(el.parent().parent().find('.price')).val(invoiceItems.price);
                            $(el.parent().parent().find('.packaging_item_id')).val(invoiceItems
                                .id);
                        } else {
                            $(el.parent().parent().find('.quantity')).val(1);
                            $(el.parent().parent().find('.price')).val(item.product.sale_price);
                        }


                        var taxes = '';
                        var tax = [];

                        var totalItemTaxRate = 0;
                        for (var i = 0; i < item.taxes.length; i++) {
                            taxes +=
                                '<span class="badge bg-primary p-2 px-3  mt-1 mr-1">' +
                                item.taxes[i].name + ' ' + '(' + item.taxes[i].rate + '%)' +
                                '</span>';
                            tax.push(item.taxes[i].id);
                            totalItemTaxRate += parseFloat(item.taxes[i].rate);
                        }

                        if (invoiceItems != null) {
                            var itemTaxPrice = parseFloat((totalItemTaxRate / 100) * (
                                invoiceItems.price * invoiceItems.quantity));
                        } else {
                            var itemTaxPrice = parseFloat((totalItemTaxRate / 100) * (item
                                .product.sale_price * 1));
                        }

                        $(el.parent().parent().find('.itemTaxPrice')).val(itemTaxPrice.toFixed(
                            2));
                        $(el.parent().parent().find('.itemTaxRate')).val(totalItemTaxRate
                            .toFixed(2));
                        $(el.parent().parent().find('.taxes')).html(taxes);
                        $(el.parent().parent().find('.tax')).val(tax);
                        $(el.parent().parent().find('.unit')).html(item.unit);

                        $(".quantity").trigger('change');
                    }
                });
            },
        });
    }
    $(document).on('click', '[data-repeater-delete]', function() {
        var el = $(this).parent().parent();
        var packaging_item_id = $(this).closest('tr').find('.packaging_item_id').val();
        if (packaging_item_id) {
            $.ajax({
                url: "{{ route('package.raw.material.delete') }}",
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    'packaging_item_id': packaging_item_id
                },
                cache: false,
                success: function(data) {
                    if (data.success) {
                        toastrs('Success', data.success, 'success');
                    } else {
                        toastrs('Error', data.error, 'error');
                    }
                },
                error: function(data) {
                    toastrs('Error', "{{ __('something went wrong please try again') }}", 'error');
                },
            });
        }

    });
</script>
@endif
<Script>
    $(document).on('keyup change', '.quantity', function() {
        var quantity = $(this).val();
        if (quantity === '-1') {
            $(this).val('');
            alert('Please enter a valid quantity.');
        }
    });
    $(document).on('keyup change', '.quantity', function() {
        var quntityTotalTaxPrice = 0;

        var el = $(this).parent().parent().parent().parent();

        var quantity = $(this).val();
        var price = $(el.find('.price')).val();

        var totalItemPrice = (quantity * price);

        var amount = (totalItemPrice);

        var totalItemTaxRate = $(el.find('.itemTaxRate')).val();
        var itemTaxPrice = parseFloat((totalItemTaxRate / 100) * (totalItemPrice));
        $(el.find('.itemTaxPrice')).val(itemTaxPrice.toFixed(2));

        $(el.find('.amount')).html(parseFloat(itemTaxPrice.toFixed(2)) + parseFloat(amount));
        $(el.find('.total_amount')).val(parseFloat(itemTaxPrice.toFixed(2)) + parseFloat(amount));

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
        $('.final_amount').val((parseFloat(subTotal)).toFixed(2));
    });
</Script>
@php
$company_settings = getCompanyAllSetting();
@endphp
<h5 class="h4 d-inline-block font-weight-400 mb-4">{{ __('Raw Material') }}</h5>
<div class="card repeater" @if ($action=='edit' ) data-value='{!! json_encode($packaging_item_summary) !!}' @endif>
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
            <table class="table mb-0 table-custom-style" data-repeater-list="items" id="sortable-table">
                <thead>
                    <tr>
                        <th>{{ __('Item') }}</th>
                        <th>{{ __('Quantity') }}</th>
                        <th>{{ __('Unit') }}</th>
                        <th>{{ __('price') }}</th>
                        <th>{{ __('Tax') }} (%)</th>
                        <th class="text-end">{{ __('Amount') }} <br><small class="text-danger font-weight-bold">{{ __('After tax') }}</small></th>
                        <th></th>
                    </tr>
                </thead>

                <tbody class="ui-sortable" data-repeater-item>
                    <tr>
                        <td width="25%" class="form-group pt-0">
                            {{ Form::hidden('packaging_item_id', '', ['class' => 'form-control packaging_item_id']) }}

                            <select class="form-control raw_material_id" name="raw_material_id" id="raw_material_id" placeholder="Select Raw Material" data-url="{{ route('raw.material') }}" required>
                                <option value="">{{__('Select Raw Material')}}</option>
                                @foreach($raw_materials as $raw_material)
                                <option value="{{ $raw_material->id }}">{{ $raw_material->productService->name }} - ({{ $raw_material->productService->type }})</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <div class="form-group price-input input-group search-form">
                                {{ Form::number('quantity', '', ['class' => 'form-control quantity', 'required' => 'required', 'placeholder' => __('Quantity'), 'required' => 'required','min' => '1']) }}
                            </div>
                        </td>
                        <td>
                            <div class="form-group price-input input-group search-form">
                                {{ Form::text('unit', '', ['class' => 'form-control unit', 'required' => 'required', 'placeholder' => __('Unit'),'readonly' => 'readonly']) }}
                            </div>
                        </td>
                        <td>
                            <div class="form-group price-input input-group search-form">
                                {{ Form::text('price', '', ['class' => 'form-control price', 'required' => 'required', 'placeholder' => __('Price'), 'required' => 'required','readonly'=>'readonly']) }}
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
                        {{ Form::hidden('total_amount', '', ['class' => 'form-control total_amount']) }}
                        <td>
                            <div class="action-btn float-end" data-repeater-delete>
                                <a href="#!"
                                   class="mx-3 btn btn-sm bg-danger align-items-center show_confirm m-2" >
                                   <i class="ti ti-trash text-white" data-bs-toggle="tooltip"
                                    data-bs-original-title="{{ __('Delete') }}" ></i>
                                </a>
                            </div>
                 

                        </td>
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
                        {{ Form::hidden('final_amount', null, ['class' => 'form-control final_amount']) }}
                        <td class="blue-text"><strong>{{ __('Total Amount') }}
                                ({{ isset($company_settings['defult_currancy_symbol']) ? $company_settings['defult_currancy_symbol'] : '' }})</strong></td>
                        <td class="text-end totalAmount blue-text">0.00</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>