@php
    $company_settings = getCompanyAllSetting();
@endphp

<h5 class="h4 d-inline-block font-weight-400 mb-4">{{ __('Sales Items') }}</h5>
<div class="card repeater" @if ($acction == 'edit') data-value='{!! json_encode($invoice->items) !!}' @endif>
    <div class="item-section py-4">
        <div class="row justify-content-between align-items-center">
            <div class="col-md-12 d-flex align-items-center justify-content-md-end px-5">
                <a href="#" data-repeater-create="" class="btn btn-primary tax_get mr-2" data-toggle="modal"
                    data-target="#add-bank">
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
                        <th>{{ __('Price') }} </th>
                        <th>{{ __('Quantity') }} </th>
                        <th>{{ __('Discount') }}</th>
                        <th width="200px">{{ __('Tax') }} (%)</th>
                        <th class="text-end">{{ __('Amount') }} <br><small
                                class="text-danger font-weight-bold">{{ __('After discount & tax') }}</small></th>
                        <th></th>
                    </tr>
                </thead>

                <tbody class="ui-sortable" data-repeater-item>
                    <tr>
                        {{ Form::hidden('id', null, ['class' => 'form-control id']) }}
                        <td width="25%" class="form-group pt-0">
                            {{ Form::hidden('id', null, ['class' => 'form-control id']) }}
                            {{ Form::select('product_id', $items, null, ['class' => 'form-control item js-searchBox', 'required' => 'required', 'data-url' => route('invoice.product')]) }}
                        </td>
                        <td>
                            <div class="form-group price-input input-group search-form mb-0" style="width: 160px">
                                {{ Form::text('price', '', ['class' => 'form-control price', 'required' => 'required', 'placeholder' => __('Price'), 'required' => 'required']) }}
                                <span
                                    class="input-group-text bg-transparent">{{ isset($company_settings['defult_currancy_symbol']) ? $company_settings['defult_currancy_symbol'] : '' }}</span>
                            </div>
                        </td>

                        <td>
                            <div class="form-group price-input input-group search-form mb-0" style="width: 160px">
                                {{ Form::text('quantity', '', ['class' => 'form-control quantity', 'required' => 'required', 'placeholder' => __('Qty'), 'required' => 'required']) }}
                                <span class="unit input-group-text bg-transparent"></span>
                            </div>
                        </td>

                        <td>
                            <div class="form-group price-input input-group search-form mb-0" style="width: 160px">
                                {{ Form::text('discount', '', ['class' => 'form-control discount', 'required' => 'required', 'placeholder' => __('Discount')]) }}
                                <span
                                    class="input-group-text bg-transparent">{{ isset($company_settings['defult_currancy_symbol']) ? $company_settings['defult_currancy_symbol'] : '' }}</span>
                            </div>
                        </td>
                        <td>
                            <div class="form-group mb-0">
                                <div class="input-group colorpickerinput">
                                    <div class="taxes"></div>
                                    {{ Form::hidden('tax', '', ['class' => 'form-control tax text-dark']) }}
                                    {{ Form::hidden('itemTaxPrice', '', ['class' => 'form-control itemTaxPrice']) }}
                                    {{ Form::hidden('itemTaxRate', '', ['class' => 'form-control itemTaxRate']) }}
                                </div>
                            </div>
                        </td>
                        <td class="text-end amount">{{ __('0.00') }}</td>
                        <td>
                            <div class="action-btn ms-2 float-end mb-3" data-repeater-delete>
                                <a href="#!"
                                    class="mx-3 btn btn-sm align-items-center d-inline-flex m-2 p-2 bg-danger">
                                      <i class="ti ti-trash text-white" data-bs-toggle="tooltip"
                                          data-bs-original-title="{{ __('Delete') }}" ></i>
                                </a>
                           </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div class="form-group mb-0">
                                {{ Form::textarea('description', null, ['class' => 'form-control pro_description', 'rows' => '3', 'placeholder' => __('Enter Description')]) }}
                            </div>
                        </td>
                        <td colspan="3"></td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td></td>
                        <td></td>
                        <td><strong>{{ __('Sub Total') }}
                                ({{ isset($company_settings['defult_currancy_symbol']) ? $company_settings['defult_currancy_symbol'] : '' }})</strong>
                        </td>
                        <td class="text-end subTotal">{{ __('0.00') }}</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td></td>
                        <td></td>
                        <td><strong>{{ __('Discount') }}
                                ({{ isset($company_settings['defult_currancy_symbol']) ? $company_settings['defult_currancy_symbol'] : '' }})</strong>
                        </td>
                        <td class="text-end totalDiscount">{{ __('0.00') }}</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td></td>
                        <td></td>
                        <td><strong>{{ __('Tax') }}
                                ({{ isset($company_settings['defult_currancy_symbol']) ? $company_settings['defult_currancy_symbol'] : '' }})</strong>
                        </td>
                        <td class="text-end totalTax">{{ __('0.00') }}</td>
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
                        <td class="text-end totalAmount blue-text">{{ __('0.00') }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<script>
    var selector = "body";
    if ($(selector + " .repeater").length) {
        var $dragAndDrop = $("body .repeater tbody").sortable({
            handle: '.sort-handler'
        });
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
                // for item SearchBox ( this function is  custom Js )
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
                }
            },
            ready: function(setIndexes) {
                $dragAndDrop.on('drop', setIndexes);
            },
            isFirstItemUndeletable: true
        });
        var value = $(selector + " .repeater").attr('data-value');
        if (typeof value != 'undefined' && value.length != 0) {
            value = JSON.parse(value);
            $repeater.setList(value);
        }
    }
</script>

@if ($acction == 'edit')
    <script>
        $(document).ready(function() {

            var value = $(selector + " .repeater").attr('data-value');
            var type = '{{ $type }}';
            if (typeof value != 'undefined' && value.length != 0) {
                value = JSON.parse(value);
                $repeater.setList(value);
                for (var i = 0; i < value.length; i++) {
                    var tr = $('#sortable-table .id[value="' + value[i].id + '"]').parent();
                    tr.find('.item').val(value[i].product_id);
                    var element = tr.find('.product_type');
                    var product_id = value[i].product_id;
                    changeItem(tr.find('.item'));
                }
            }
            const elementsToRemove = document.querySelectorAll('.bs-pass-para.repeater-action-btn');
            if (elementsToRemove.length > 0) {
                elementsToRemove[0].remove();
            }

        });
    </script>
    <script>
        var invoice_id = '{{ $invoice->id }}';

        function changeItem(element) {

            var iteams_id = element.val();

            var url = element.data('url');
            var el = element;

            $.ajax({
                url: '{{ route('invoice.items') }}',
                type: 'GET',
                headers: {
                    'X-CSRF-TOKEN': jQuery('#token').val()
                },
                data: {
                    'invoice_id': invoice_id,
                    'product_id': iteams_id,
                },

                cache: false,
                success: function(data) {
                    var item = JSON.parse(data);
                    var invoiceItems = item.items;

                    if (invoiceItems != null) {
                        var amount = (invoiceItems.price * invoiceItems.quantity);

                        $(el.parent().parent().find('.quantity')).val(invoiceItems
                            .quantity);
                        $(el.parent().parent().find('.price')).val(invoiceItems.price);
                        $(el.parent().parent().find('.discount')).val(invoiceItems
                            .discount);
                    } else {
                        $(el.parent().parent().find('.quantity')).val(1);
                        $(el.parent().parent().find('.price')).val(item.product.sale_price);
                        $(el.parent().parent().find('.discount')).val(0);
                    }


                    var taxes = '';
                    var tax = [];

                    var totalItemTaxRate = 0;
                    for (var i = 0; i < item.taxes.length; i++) {
                        taxes +=
                            '<span class="badge bg-primary p-2 px-3 mt-1 mr-1">' +
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

                    $(".discount").trigger('change');
                }
            });
        }
        $(document).on('click', '[data-repeater-create]', function() {
            $('.item :selected').each(function() {
                var id = $(this).val();
                $(".item option[value=" + id + "]").addClass("d-none");
            });
        })
    </script>
    <script>
        $(document).on('click', '[data-repeater-create]', function() {
            $('.item :selected').each(function() {
                var id = $(this).val();
                if (id != '') {
                    $(".item option[value=" + id + "]").addClass("d-none");
                }
            });
        })

        $(".tax_get").click(function() {
            myFunction();

        });
        $(".get_tax").change(function() {
            myFunction();
        });

        function myFunction() {
            var tax_id = $('.get_tax').val();

            if (tax_id != "") {
                $.ajax({
                    url: '{{ route('get.taxes') }}',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': jQuery('#token').val()
                    },
                    data: {
                        'tax_id': tax_id,
                    },
                    cache: false,
                    success: function(data) {
                        var obj = jQuery.parseJSON(data);


                        var taxes = '';
                        var tax = [];
                        $.each(obj, function() {

                            taxes += '<span class="badge bg-primary p-2 px-3 mt-1 mr-1">' +
                                this.name + ' ' + '(' + this.rate + '%)' +
                                '</span>';
                            tax.push(this.id);

                        });

                        $('.taxes').html(taxes);
                    },
                });
            } else {
                $('.taxes').html("");
            }
        }
    </script>
@endif
