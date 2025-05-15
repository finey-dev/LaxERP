@extends('layouts.main')
@section('page-title')
    {{ __('Diagnosis Create') }}
@endsection
@section('page-breadcrumb')
    {{ __('Diagnosis Create') }}  {{--Machine Repair Invoice--}}
@endsection

@push('scripts')
    <script src="{{ asset('js/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('js/jquery.repeater.min.js') }}"></script>
    <script>
        $(document).on('change', '#customer', function() {
            $('#customer_detail').removeClass('d-none');
            $('#customer_detail').addClass('d-block');
            $('#customer-box').addClass('d-none');
            var id = $(this).val();
            var url = $(this).data('url');
            $.ajax({
                url: url,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': jQuery('#token').val()
                },
                data: {
                    'id': id
                },
                cache: false,
                success: function(data) {
                    if (data != '') {
                        $('#customer_detail').html(data);
                    } else {
                        $('#customer-box').removeClass('d-none');
                        $('#customer_detail').removeClass('d-block');
                        $('#customer_detail').addClass('d-none');
                    }

                },

            });
        });

        $(document).on('click', '#remove', function() {
            $('#customer-box').removeClass('d-none');
            $('#customer_detail').removeClass('d-block');
            $('#customer_detail').addClass('d-none');
        })
    </script>
        <Script>
            $(document).on('keyup change', '.service_charge', function () {
                var service_charge = $(this).val();
                var service = parseFloat(service_charge);
                var inputs = $(".amount");

                var subTotal = 0;
                for (var i = 0; i < inputs.length; i++) {
                    subTotal = parseFloat(subTotal) + parseFloat($(inputs[i]).html());
                }
                subTotal = subTotal + service;
                $('.totalServiceCharge').html(service.toFixed(2));

                $('.totalAmount').html((parseFloat(subTotal)).toFixed(2));
            })

            $(document).on('keyup', '.quantity', function () {
                var quntityTotalTaxPrice = 0;

                var el = $(this).parent().parent().parent().parent();

                var quantity = $(this).val();
                var price = $(el.find('.price')).val();
                var discount = $(el.find('.discount')).val();
                var service_charge = $('.service_charge').val();
                var service = parseFloat(service_charge);
                if(discount.length <= 0)
                {
                    discount = 0 ;
                }

                var totalItemPrice = (quantity * price) - discount;

                var amount = (totalItemPrice);


                var totalItemTaxRate = $(el.find('.itemTaxRate')).val();
                var itemTaxPrice = parseFloat((totalItemTaxRate / 100) * (totalItemPrice));
                $(el.find('.itemTaxPrice')).val(itemTaxPrice.toFixed(2));

                $(el.find('.amount')).html(parseFloat(itemTaxPrice)+parseFloat(amount));

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
                subTotal = parseFloat(subTotal) + service;

                $('.subTotal').html(totalItemPrice.toFixed(2));
                $('.totalTax').html(totalItemTaxPrice.toFixed(2));
                $('.totalServiceCharge').html(service.toFixed(2));

                $('.totalAmount').html((parseFloat(subTotal)).toFixed(2));
            })

            $(document).on('keyup change', '.price', function () {
                var el = $(this).parent().parent().parent().parent();
                var price = $(this).val();
                var quantity = $(el.find('.quantity')).val();
                var service_charge = $('.service_charge').val();
                var service = parseFloat(service_charge);
                if(quantity.length <= 0)
                {
                    quantity = 1 ;
                }
                var discount = $(el.find('.discount')).val();
                if(discount.length <= 0)
                {
                    discount = 0 ;
                }
                var totalItemPrice = (quantity * price)-discount;

                var amount = (totalItemPrice);

                var totalItemTaxRate = $(el.find('.itemTaxRate')).val();
                var itemTaxPrice = parseFloat((totalItemTaxRate / 100) * (totalItemPrice));
                $(el.find('.itemTaxPrice')).val(itemTaxPrice.toFixed(2));

                $(el.find('.amount')).html(parseFloat(itemTaxPrice)+parseFloat(amount));

                var totalItemTaxPrice = 0;
                var itemTaxPriceInput = $('.itemTaxPrice');
                for (var j = 0; j < itemTaxPriceInput.length; j++) {
                    totalItemTaxPrice += parseFloat(itemTaxPriceInput[j].value);
                }


                var totalItemPrice = 0;
                var inputs_quantity = $(".quantity");
                var priceInput = $('.price');
                for (var j = 0; j < priceInput.length; j++) {
                    if(inputs_quantity[j].value <= 0)
                    {
                        inputs_quantity[j].value = 1 ;
                    }
                    totalItemPrice += (parseFloat(priceInput[j].value) * parseFloat(inputs_quantity[j].value));
                }

                var inputs = $(".amount");

                var subTotal = 0;
                for (var i = 0; i < inputs.length; i++) {
                    subTotal = parseFloat(subTotal) + parseFloat($(inputs[i]).html());
                }
                subTotal = parseFloat(subTotal) + service;

                $('.subTotal').html(totalItemPrice.toFixed(2));
                $('.totalTax').html(totalItemTaxPrice.toFixed(2));
                $('.totalServiceCharge').html(service.toFixed(2));

                $('.totalAmount').html((parseFloat(subTotal)).toFixed(2));
            })

            $(document).on('keyup change', '.discount', function () {
                var el = $(this).parent().parent().parent();
                var discount = $(this).val();
                if(discount.length <= 0)
                {
                    discount = 0 ;
                }

                var price = $(el.find('.price')).val();
                var quantity = $(el.find('.quantity')).val();
                var service_charge = $('.service_charge').val();
                var service = parseFloat(service_charge);
                var totalItemPrice = (quantity * price) - discount;


                var amount = (totalItemPrice);


                var totalItemTaxRate = $(el.find('.itemTaxRate')).val();
                var itemTaxPrice = parseFloat((totalItemTaxRate / 100) * (totalItemPrice));
                $(el.find('.itemTaxPrice')).val(itemTaxPrice.toFixed(2));

                $(el.find('.amount')).html(parseFloat(itemTaxPrice)+parseFloat(amount));

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
                subTotal = parseFloat(subTotal) + service;


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
                $('.totalServiceCharge').html(service.toFixed(2));

                $('.totalAmount').html((parseFloat(subTotal)).toFixed(2));
                $('.totalDiscount').html(totalItemDiscountPrice.toFixed(2));
            })
    </Script>

    {{-- @if (module_is_active('Account')) --}}
        <script>
            $(document).on('change', '.item', function() {
                items($(this));
            });

            function items(data)
            {
                var in_type = $('#invoice_type').val();
                if (in_type == 'product') {
                    var iteams_id = data.val();
                    var url = data.data('url');
                    var el = data;
                    $.ajax({
                        url: url,
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': jQuery('#token').val()
                        },
                        data: {
                            'product_id': iteams_id
                        },
                        cache: false,
                        success: function(data) {
                            var item = JSON.parse(data);
                            $(el.parent().parent().find('.quantity')).val(1);
                            if(item.product != null)
                            {
                                $(el.parent().parent().find('.price')).val(item.product.sale_price);
                                $(el.parent().parent().parent().find('.pro_description')).val(item.product.description);

                            }
                            else
                            {
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
                                    taxes += '<span class="badge bg-primary p-2 px-3 me-1">' +
                                    taxes += '<span class="badge bg-primary p-2 px-3 mt-1 me-1">' +
                                        item.taxes[i].name + ' ' + '(' + item.taxes[i].rate + '%)' +
                                        '</span>';
                                    tax.push(item.taxes[i].id);
                                    totalItemTaxRate += parseFloat(item.taxes[i].rate);
                                }
                            }
                            var itemTaxPrice = 0;
                            if(item.product != null)
                            {
                                var itemTaxPrice = parseFloat((totalItemTaxRate / 100) * (item.product
                                .sale_price * 1));
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
                                if(item.product != null)
                                {
                                    $(el.parent().parent().find('.amount')).html(parseFloat(item.totalAmount)+parseFloat(itemTaxPriceInput[j].value));
                                }
                            }



                            var totalItemDiscountPrice = 0;
                            var itemDiscountPriceInput = $('.discount');

                            var service_charge = $('.service_charge').val();
                            var service = parseFloat(service_charge);
                            for (var k = 0; k < itemDiscountPriceInput.length; k++) {

                                totalItemDiscountPrice += parseFloat(itemDiscountPriceInput[k].value);
                            }

                            $('.subTotal').html(totalItemPrice.toFixed(2));
                            $('.totalTax').html(totalItemTaxPrice.toFixed(2));
                            $('.totalServiceCharge').html(service.toFixed(2));
                            $('.totalAmount').html((parseFloat(totalItemPrice) - parseFloat(totalItemDiscountPrice) + parseFloat(totalItemTaxPrice) + service).toFixed(2));

                        },
                    });
                }
            }
        </script>
    {{-- @endif --}}

    {{-- @if (module_is_active('Account')) --}}
        <script>
            $(document).ready(function() {
                SectionGet('product');
            });
        </script>
    {{-- @endif --}}
    <script>
        $(document).on('click', '[data-repeater-delete]', function () {
            $(".price").change();
            $(".discount").change();
        });
    </script>
    <script>
        function SectionGet(type = 'product', project_id = "0",title = 'Project') {
            $.ajax({
                type: 'post',
                url: "{{ route('machine.invoice.section.type') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    type: type,
                    project_id: project_id,
                    acction: 'create',
                },
                beforeSend: function() {
                    $("#loader").removeClass('d-none');
                },
                success: function(response) {
                    if (response != false) {
                        $('.section_div').html(response.html);
                        $("#loader").addClass('d-none');
                        $('.pro_name').text(title)
                        // for item SearchBox ( this function is  custom Js )
                        JsSearchBox();
                    } else {
                        $('.section_div').html('');
                        toastrs('Error', 'Something went wrong please try again !', 'error');
                    }
                },
            });
        }
    </script>
@endpush
@section('content')
    <div class="row">
        {{ Form::open(['url' => 'machine-repair-invoice', 'class' => 'w-100', 'enctype' => 'multipart/form-data']) }}
        <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
        {{-- @if (module_is_active('Account')) --}}
            <input type="hidden" name="invoice_type" id="invoice_type" value="product">
        {{-- @endif --}}
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                            <div>
                                @if(isset($repair_request))
                                    <div class="row">
                                        <div class="col-md-5 col-sm-6 col-12">
                                            <h6>{{ __('Request Number')}}</h6>
                                            <div class="request-to">
                                                <p>
                                                    <span>
                                                        {{ \Workdo\MachineRepairManagement\Entities\MachineRepairRequest::machineRepairNumberFormat($repair_request->id) }}
                                                        {{-- {{ $repair_request->id }} --}}
                                                    </span><br>
                                                </p>
                                                <input type="hidden" name="request_id" id="request_id" value="{{ $repair_request->id }}">
                                            </div>
                                        </div>
                                        <div class="col-md-5 col-sm-6 col-12">
                                            <h6>{{ __('Customer Details')}}</h6>
                                            <div class="customer-to">
                                                <p>
                                                    <span>{{ $repair_request->customer_name }}</span><br>
                                                    <span>{{ $repair_request->customer_email }}</span><br>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="col-md-8 col-sm-6 col-12 mt-2">
                                            <h6>{{ __('Machine Details') }}</h6>
                                            <dl class="row align-items-center">
                                                <dt class="col-sm-4" style="font-weight: 600;">{{ __('Name') }}</dt>
                                                <dd class="col-sm-8  ms-0" style="margin-bottom: 0px;"> : {{ !empty($machine_details->name) ? $machine_details->name : '' }}</dd>
                                                <dt class="col-sm-4" style="font-weight: 600;">{{ __('Model') }}</dt>
                                                <dd class="col-sm-8  ms-0" style="margin-bottom: 0px;"> : {{ !empty($machine_details->model) ? $machine_details->model : '' }}</dd>
                                                <dt class="col-sm-4" style="font-weight: 600;">{{ __('Manufacturer') }}</dt>
                                                <dd class="col-sm-8  ms-0" style="margin-bottom: 0px;"> : {{ !empty($machine_details->manufacturer) ? $machine_details->manufacturer : '' }}</dd>
                                            </dl>
                                        </div>
                                    </div>
                                @else
                                    <div class="row" id="customer-box">
                                        <div class="form-group col-md-6">
                                            {{ Form::label('request_id', __('Machine Repair Request'), ['class' => 'form-label']) }}
                                                <select class="form-control staff " name="request_id" id="customer" data-url="{{ route('machine.invoice.request') }}" tabindex="-1" required
                                                    aria-hidden="true">
                                                    <option value="">{{ __('Select Request') }}</option>
                                                    @foreach ($requests as $request)
                                                        <option value="{{ $request }}">
                                                            {{ \Workdo\MachineRepairManagement\Entities\MachineRepairRequest::machineRepairNumberFormat($request) }}
                                                            {{-- {{ $request->id }} --}}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            {{-- {{ Form::select('request_id', $requests, null, ['class' => 'form-control ', 'id' => 'customer', 'data-url' => route('machine.invoice.request'), 'required' => 'required', 'placeholder' => 'Select Request']) }} --}}
                                        </div>
                                    </div>
                                    <div id="customer_detail" class="d-none">
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {{ Form::label('issue_date', __('Issue Date'), ['class' => 'form-label']) }}
                                        <div class="form-icon-user">
                                            {{ Form::date('issue_date',date('Y-m-d'), ['class' => 'form-control ', 'required' => 'required', 'placeholder' => 'Select Issue Date']) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {{ Form::label('due_date', __('Due Date'), ['class' => 'form-label']) }}
                                        <div class="form-icon-user">
                                            {{ Form::date('due_date',date('Y-m-d'), ['class' => 'form-control ', 'required' => 'required', 'placeholder' => 'Select Due Date']) }}

                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {{ Form::label('invoice_number', __('Invoice Number'), ['class' => 'form-label']) }}
                                        <div class="form-icon-user">
                                            <input type="text" class="form-control" value="{{ $invoice_number }}"
                                                readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {{ Form::label('service_charge', __('Sevice Charge'), ['class' => 'form-label']) }}
                                        <div class="form-icon-user input-group">
                                            <input type="number" class="form-control service_charge" name="service_charge" id="service_charge" placeholder="{{__('Enter Service Charge')}}" value="0" required> {{-- value="{{ $service_charge }}" --}}
                                            <span class="input-group-text bg-transparent">{{ !empty(company_setting('defult_currancy_symbol')) ? company_setting('defult_currancy_symbol') : '' }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {{ Form::label('estimated_time', __('Estimated Repair Time'), ['class' => 'form-label']) }}
                                        <div class="form-icon-user">
                                            <input type="number" class="form-control" name="estimated_time" id="estimated_time" placeholder="{{__('Enter Estimated Repair Time')}}" required> {{-- value="{{ $estimated_time }}" --}}
                                            <small>{{ __('Note : Enter Estimated Repair Time In Hours.')}}</small>
                                        </div>
                                    </div>
                                </div>

                                {{--
                                {{--
                                @if(module_is_active('CustomField') && !$customFields->isEmpty())
                                    <div class="col-md-12">
                                        <div class="tab-pane fade show" id="tab-2" role="tabpanel">
                                            @include('custom-field::formBuilder')
                                        </div>
                                    </div>
                                @endif --}}
                                {{-- @stack('add_invoices_agent_filed') --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="loader" class="card card-flush">
            <div class="card-body">
                <div class="row">
                    <img class="loader" src="{{ asset('public/images/loader.gif') }}" alt="">
                </div>
            </div>
        </div>
        <div class="col-12 section_div">

        </div>
        <div class="modal-footer">
            <input type="button" value="{{ __('Cancel') }}" onclick="location.href = '{{ route('machine-repair-invoice.index') }}';"
                class="btn btn-light me-2">
            <input type="submit" id="submit" value="{{ __('Create') }}" class="btn  btn-primary ">
        </div>
        {{ Form::close() }}

    </div>
@endsection
@push('scripts')
    <script src="{{ asset('js/jquery-searchbox.js') }}"></script>

    <script>
         $("#submit").click(function() {
        var skill = $('.account_type').val();
        if (skill == '') {
            $('#account_validation').removeClass('d-none')
            return false;
        } else {
            $('#account_validation').addClass('d-none')
        }
    });
    </script>
@endpush
