@extends('layouts.main')
@section('page-title')
    {{ __('Edit RFx') }}
@endsection
@push('css')
    <link href="{{ asset('packages/workdo/Procurement/src/Resources/assets/css/bootstrap-tagsinput.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('packages/workdo/Procurement/src/Resources/assets/css/custom.css') }}">
    <link href="{{ asset('assets/js/plugins/summernote-0.8.18-dist/summernote-lite.min.css') }}" rel="stylesheet">
@endpush
@section('page-action')
    <div class="">
        @if (module_is_active('AIAssistant'))
            @include('aiassistant::ai.generate_ai_btn', [
                'template_module' => 'rfx',
                'module' => 'Procurement',
            ])
        @endif
        <ul class="nav nav-pills nav-fill cust-nav information-tab mt-2" id="pills-tab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="details" data-bs-toggle="pill" data-bs-target="#details-tab"
                    type="button">{{ __('Details') }}</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="purchase" data-bs-toggle="pill" data-bs-target="#purchase-tab"
                    type="button">{{ __('Purchase') }}</button>
            </li>
        </ul>
    </div>
@endsection
@push('scripts')
    <script src="{{ asset('assets/js/plugins/summernote-0.8.18-dist/summernote-lite.min.js') }}"></script>
    <script src="{{ asset('packages/workdo/Procurement/src/Resources/assets/js/editorplaceholder.js') }}"></script>
    <script src="{{ asset('packages/workdo/Procurement/src/Resources/assets/js/bootstrap-tagsinput.min.js') }}"></script>
    <script src="{{ asset('js/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('js/jquery.repeater.min.js') }}"></script>
    <script src="{{ asset('js/jquery-searchbox.js') }}"></script>
    <script>
        var e = $('[data-toggle="tags"]');
        e.length && e.each(function() {
            $(this).tagsinput({
                tagClass: "badge badge-primary",
            })
        });


        $("#submit").click(function() {
            var skill = $('.skill_data').val();

            if (skill == '') {
                $('#skill_validation').removeClass('d-none')
                return false;
            } else {
                $('#skill_validation').addClass('d-none')
            }

            var description = $('textarea[name="description"]').val();
            if (!isNaN(description)) {
                $('#description_val').removeClass('d-none')
                return false;
            } else {
                $('#description_val').addClass('d-none')
            }

            var requirement = $('textarea[name="requirement"]').val();
            if (!isNaN(requirement)) {
                $('#req_val').removeClass('d-none')
                return false;
            } else {
                $('#req_val').addClass('d-none')
            }

            var checkbox = $('#check-terms');
            var termsDiv = $('#termsandcondition');
            var TermsAndCondition = $('textarea[name="terms_and_conditions"]').val();
            if (checkbox.is(':checked')) {
                if (!isNaN(TermsAndCondition)) {
                    $('#terms_val').removeClass('d-none')
                    return false;
                } else {
                    $('#terms_val').addClass('d-none')
                }
            }

            return true;
        });
    </script>

    <script>
        $(document).ready(function() {
            var checkbox = $('#check-terms');
            var termsDiv = $('#termsandcondition');

            checkbox.change(function() {
                if (checkbox.is(':checked')) {
                    termsDiv.show();
                } else {
                    termsDiv.hide();
                }
            });

            if (!checkbox.is(':checked')) {
                termsDiv.hide();
            }
        });
    </script>


    <script>
        function changetab(tabname) {
            var someTabTriggerEl = document.querySelector('button[data-bs-target="' + tabname + '"]');
            var actTab = new bootstrap.Tab(someTabTriggerEl);
            actTab.show();
        }

        $("#nextButton").click(function() {
            var allFieldsFilled = true;

            // Check required fields for personal details
            if ($('#title').val().trim() === '') {
                $('#title_validation').removeClass('d-none');
                allFieldsFilled = false;
            } else {
                $('#title_validation').addClass('d-none');
            }


            if ($('#location').val().trim() === '') {
                $('#location_validation').removeClass('d-none');
                allFieldsFilled = false;
            } else {
                $('#location_validation').addClass('d-none');
            }

            if ($('#category').val().trim() === '') {
                $('#category_validation').removeClass('d-none');
                allFieldsFilled = false;
            } else {
                $('#category_validation').addClass('d-none');
            }

            if ($('#status').val().trim() === '') {
                $('#status_validation').removeClass('d-none');
                allFieldsFilled = false;
            } else {
                $('#status_validation').addClass('d-none');
            }

            // Check required fields for location details
            if ($('#budget_from').val().trim() === '') {
                $('#budget_from_validation').removeClass('d-none');
                allFieldsFilled = false;
            } else {
                $('#budget_from_validation').addClass('d-none');
            }

            if ($('#position').val().trim() === '') {
                $('#position_validation').removeClass('d-none');
                allFieldsFilled = false;
            } else {
                $('#position_validation').addClass('d-none');
            }

            if ($('#budget_to').val().trim() === '') {
                $('#budget_to_validation').removeClass('d-none');
                allFieldsFilled = false;
            } else {
                $('#budget_to_validation').addClass('d-none');
            }

            if ($('#start_date').val().trim() === '') {
                $('#start_date_validation').removeClass('d-none');
                allFieldsFilled = false;
            } else {
                $('#start_date_validation').addClass('d-none');
            }

            if ($('#end_date').val().trim() === '') {
                $('#end_date_validation').removeClass('d-none');
                allFieldsFilled = false;
            } else {
                $('#end_date_validation').addClass('d-none');
            }

            if ($('#rfx_type').val().trim() === '') {
                $('#rfx_type_validation').removeClass('d-none');
                allFieldsFilled = false;
            } else {
                $('#rfx_type_validation').addClass('d-none');
            }
            if ($('#budget_from').val().trim() === '-1') {
                $('#budget_from_validation').text('The budget cannot be -1. Please enter a valid budget.');
                $('#budget_from_validation').removeClass('d-none');
                allFieldsFilled = false;
            }  else {
                $('#budget_from_validation').addClass('d-none');
            }

            if ($('#position').val().trim() === '-1') {
                $('#position_validation').text('The position cannot be allowed in -1.');
                $('#position_validation').removeClass('d-none');
                allFieldsFilled = false;
            } else {
                $('#position_validation').addClass('d-none');
            }

            if ($('#budget_to').val().trim() === '-1') {
                $('#budget_to_validation').text('The budget cannot be -1. Please enter a valid budget.');
                $('#budget_to_validation').removeClass('d-none');
                allFieldsFilled = false;
            } else {
                $('#budget_to_validation').addClass('d-none');
            }

            if (!allFieldsFilled) {
                return false; // Prevents the button from proceeding to the next tab
            } else {
                // Proceed to the next tab
                changetab('#purchase-tab'); // Check if this function is correctly defined and functioning
            }
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const billingTypeSelect = document.getElementById('billing_type');
            const itemTab = document.getElementById('item_tab');
            const rfxTab = document.getElementById('rfx_tab');
            const billingValidation = document.getElementById('billing_validation');

            billingTypeSelect.addEventListener('change', function() {
                const selectedValue = billingTypeSelect.value;

                // Hide validation message
                billingValidation.classList.add('d-none');

                if (selectedValue === 'items') {
                    itemTab.classList.remove('d-none');
                    rfxTab.classList.add('d-none');
                } else if (selectedValue === 'rfx') {
                    rfxTab.classList.remove('d-none');
                    itemTab.classList.add('d-none');
                } else {
                    itemTab.classList.add('d-none');
                    rfxTab.classList.add('d-none');
                }
            });

            // Initial check
            billingTypeSelect.dispatchEvent(new Event('change'));
        });
    </script>
    {{-- Purchase Tab script --}}
    <script>
        var selector = "body";
        if ($(selector + " .item-repeater").length) {
            var $dragAndDrop = $("body .item-repeater tbody").sortable({
                handle: '.sort-handler'
            });
            var $repeater = $(selector + ' .item-repeater').repeater({
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

                        $(this).slideUp(deleteElement);
                        $(this).remove();

                        var inputs = $(".amount");
                        var subTotal = 0;
                        for (var i = 0; i < inputs.length; i++) {
                            subTotal = parseFloat(subTotal) + parseFloat($(inputs[i]).html());
                        }
                        $('.subTotal').html(subTotal.toFixed(2));
                        $('.totalAmount').html(subTotal.toFixed(2));

                },
                ready: function(setIndexes) {
                    $dragAndDrop.on('drop', setIndexes);
                },
                isFirstItemUndeletable: true
            });
            var value = $(selector + " .item-repeater").attr('data-value');
            if (typeof value != 'undefined' && value.length != 0) {
                value = JSON.parse(value);
                $repeater.setList(value);
            }

        }


        $(document).on('change', '.item', function() {
            changeItem($(this));
        });

        function changeItem(element) {
            var rfx_id='{{$rfx->id}}';
            var iteams_id = element.val();

            var url = element.data('url');
            var el = element;
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

                    $.ajax({
                        url: '{{ route('rfx.items') }}',
                        type: 'post',
                        headers: {
                            'X-CSRF-TOKEN': jQuery('#token').val()
                        },
                        data: {
                            'rfx_id': rfx_id,
                            'product_id': iteams_id,
                        },
                        cache: false,
                        success: function(data) {
                            var purchaseItems = JSON.parse(data);
                            if (purchaseItems != null) {
                                var amount = (purchaseItems.product_price * purchaseItems.product_quantity);
                                $(el.parent().parent().parent().find('.quantity')).val(purchaseItems
                                    .product_quantity);
                                $(el.parent().parent().parent().find('.price')).val(purchaseItems
                                    .product_price);
                                $(el.parent().parent().parent().find('.discount')).val(purchaseItems
                                    .product_discount);
                                $('.pro_description').text(purchaseItems.product_description);

                            } else {
                                $(el.parent().parent().parent().find('.quantity')).val(1);
                                $(el.parent().parent().parent().find('.discount')).val(0);
                                if (item.product != null) {
                                    $(el.parent().parent().parent().find('.price')).val(item.product
                                        .purchase_price);
                                    $(el.parent().parent().parent().find('.pro_description')).val(
                                        item.product.description);
                                } else {
                                    $(el.parent().parent().parent().find('.price')).val(0);
                                    $(el.parent().parent().parent().find('.pro_description')).val(
                                        '');
                                }
                            }

                            var taxes = '';
                            var tax = [];

                            var totalItemTaxRate = 0;
                            for (var i = 0; i < item.taxes.length; i++) {

                                taxes +=
                                    '<span class="badge bg-primary p-2 px-3 me-1">' +
                                    item.taxes[i].name + ' ' + '(' + item.taxes[i].rate + '%)' +
                                    '</span>';
                                tax.push(item.taxes[i].id);
                                totalItemTaxRate += parseFloat(item.taxes[i].rate);

                            }

                            var discount = $(el.parent().parent().parent().find('.discount')).val();
                            var itemTaxPrice = 0;
                            if (purchaseItems != null) {
                                var itemTaxPrice = parseFloat((totalItemTaxRate / 100)) *
                                    parseFloat((purchaseItems.product_price * purchaseItems.product_quantity) -
                                        discount);
                            } else if (item.product != null) {
                                var itemTaxPrice = parseFloat((totalItemTaxRate / 100)) *
                                    parseFloat((item.product.purchase_price * 1) - discount);
                            }


                            $(el.parent().parent().parent().find('.itemTaxPrice')).val(itemTaxPrice
                                .toFixed(2));
                            $(el.parent().parent().parent().find('.itemTaxRate')).val(
                                totalItemTaxRate.toFixed(2));
                            $(el.parent().parent().parent().find('.taxes')).html(taxes);
                            $(el.parent().parent().parent().find('.tax')).val(tax);
                            $(el.parent().parent().parent().find('.unit')).html(item.unit);

                            var inputs = $(".amount");
                            var subTotal = 0;
                            for (var i = 0; i < inputs.length; i++) {
                                subTotal = parseFloat(subTotal) + parseFloat($(inputs[i]).html());
                            }


                            var totalItemPrice = 0;
                            var inputs_quantity = $(".quantity");
                            var priceInput = $('.price');
                            for (var j = 0; j < priceInput.length; j++) {
                                totalItemPrice += (parseFloat(priceInput[j].value) * parseFloat(
                                    inputs_quantity[j].value));
                            }


                            var totalItemTaxPrice = 0;
                            var itemTaxPriceInput = $('.itemTaxPrice');
                            for (var j = 0; j < itemTaxPriceInput.length; j++) {
                                totalItemTaxPrice += parseFloat(itemTaxPriceInput[j].value);
                                if (purchaseItems != null) {
                                    $(el.parent().parent().parent().find('.amount')).html(
                                        parseFloat(amount) + parseFloat(itemTaxPrice) -
                                        parseFloat(discount));
                                } else {
                                    $(el.parent().parent().parent().find('.amount')).html(
                                        parseFloat(item.totalAmount) + parseFloat(itemTaxPrice));
                                }

                            }

                            var totalItemDiscountPrice = 0;
                            var itemDiscountPriceInput = $('.discount');

                            for (var k = 0; k < itemDiscountPriceInput.length; k++) {
                                totalItemDiscountPrice += parseFloat(itemDiscountPriceInput[k]
                                    .value);
                            }


                            $('.subTotal').html(totalItemPrice.toFixed(2));
                            $('.totalTax').html(totalItemTaxPrice.toFixed(2));
                            $('.totalAmount').html((parseFloat(totalItemPrice) - parseFloat(
                                    totalItemDiscountPrice) + parseFloat(totalItemTaxPrice))
                                .toFixed(2));
                            $('.totalDiscount').html(totalItemDiscountPrice.toFixed(2));


                        }
                    });


                },
            });
        }
    </script>
    <script>
        $(document).on('click', '[data-repeater-delete]', function() {
            $(".price").change();
            $(".discount").change();
        });
        // for item SearchBox ( this function is  custom Js )
        JsSearchBox();

        $(document).on('change', '.product_type', function() {
            ProductType($(this));
        });

        function ProductType(data, id = null, type = null) {
            var product_type = data.val();
            var selector = data;
            var itemSelect = selector.parent().parent().find('.product_id.item').attr('name');
            $.ajax({
                url: '{{ route('get.item') }}',
                type: 'POST',
                data: {
                    "product_type": product_type,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(data) {
                    selector.parent().parent().find('.product_id').empty();
                    var product_select = `<select class="form-control product_id item js-searchBox" name="${itemSelect}"
                                        placeholder="Select Item" data-url="{{ route('rfx.product') }}" required = 'required'>
                                        </select>`;
                    selector.parent().parent().find('.product_div').html(product_select);

                    selector.parent().parent().find('.product_id').append(
                        '<option> {{ __('Select Item') }} </option>');
                    $.each(data, function(key, value) {
                        var selected = (key == id) ? 'selected' : '';
                        selector.parent().parent().find('.product_id').append('<option value="' + key +
                            '" ' + selected + '>' + value + '</option>');
                    });
                    changeItem(selector.parent().parent().find('.product_id'));

                    // Initialize your searchBox here if needed
                    selector.parent().parent().find(".js-searchBox").searchBox({
                        elementWidth: '250'
                    });
                    selector.parent().parent().find('.unit.input-group-text').text("");
                }
            });
        }
    </script>
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

                ready: function(setIndexes) {
                    // $dragAndDrop.on('drop', setIndexes);
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
    <script>
        $(document).ready(function() {
            var value = $(selector + " .item-repeater").attr('data-value');

            var type = '{{ $rfx->billing_type }}';
            if (typeof value != 'undefined' && value.length != 0) {
                value = JSON.parse(value);
                $repeater.setList(value);
                for (var i = 0; i < value.length; i++) {
                    var tr = $('#sortable-table .id[value="' + value[i].id + '"]').parent();
                    tr.find('.item').val(value[i].product_id);

                    var element = tr.find('.product_type');
                    var product_id = value[i].product_id;
                    ProductType(element, product_id, 'edit');
                    changeItem(tr.find('.item'));

                }
            }
            const elementsToRemove = document.querySelectorAll('.bs-pass-para.repeater-action-btn');
            if (elementsToRemove.length > 0) {
                elementsToRemove[0].remove();
            }
        });
        $(document).ready(function() {
            var value = $(selector + " .repeater").attr('data-value');


            var type = '{{ $rfx->billing_type }}';
            if (typeof value != 'undefined' && value.length != 0) {
                value = JSON.parse(value);
                $repeater.setList(value);
                for (var i = 0; i < value.length; i++) {
                    var tr = $('#sortable-table .id[value="' + value[i].id + '"]').parent();
                    tr.find('.rfx-task').val(value[i].product_id);
                }
            }
            const elementsToRemove = document.querySelectorAll('.bs-pass-para.repeater-action-btn');
            if (elementsToRemove.length > 0) {
                elementsToRemove[0].remove();
            }
        });
    </script>
@endpush

@section('page-breadcrumb')
    {{ __('Manage RFx') }},
    {{ __('Edit RFx') }}
@endsection
@php
    $company_settings = getCompanyAllSetting();
@endphp
@section('content')
    <div class="row">
        {{ Form::model($rfx, ['route' => ['rfx.update', $rfx->id], 'method' => 'PUT']) }}
        <div class="tab-content" id="pills-tabContent">
            {{-- Detail Tab --}}
            <div class="tab-pane fade active show" id="details-tab" role="tabpanel" aria-labelledby="pills-user-tab-1">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-body">

                                        <h5 class="mb-0">{{ __('RFx Details') }}</h5>
                                        <hr>
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                {!! Form::label('title', __('RFx Title'), ['class' => 'col-form-label']) !!}
                                                {!! Form::text('title', old('title'), [
                                                    'class' => 'form-control',
                                                    'required' => 'required',
                                                    'placeholder' => __('Enter rfx title'),
                                                ]) !!}
                                                <p class="text-danger d-none" id="{{ 'title_validation' }}">
                                                    {{ __('This field is required.') }}</p>
                                            </div>


                                            <div class="form-group col-md-6">
                                                {!! Form::label('category', __('RFx Category'), ['class' => 'col-form-label']) !!}
                                                {{ Form::select('category', $categories, null, ['class' => 'form-control ', 'placeholder' =>__('Select RFx Category'), 'required' => 'required']) }}
                                                @if (empty($categories->count()))
                                                    <div class=" text-xs">
                                                        {{ __('Please add rfx category. ') }}<a
                                                            href="{{ route('rfx-category.index') }}"><b>{{ __('Add RFx Category') }}</b></a>
                                                    </div>
                                                @endif
                                                <p class="text-danger d-none" id="{{ 'category_validation' }}">
                                                    {{ __('This field is required.') }}</p>
                                            </div>

                                            <div class="form-group col-md-6">
                                                {{ Form::label('rfx_type', __('RFx Type'), ['class' => 'col-form-label']) }}
                                                {{ Form::select('rfx_type', $rfx_type, null, ['class' => 'form-control select']) }}
                                                <p class="text-danger d-none" id="{{ 'rfx_type_validation' }}">
                                                    {{ __('This field is required.') }}</p>
                                            </div>
                                            <div class="form-group col-md-6">
                                                {!! Form::label('status', __('Status'), ['class' => 'col-form-label']) !!}
                                                {{ Form::select('status', $status, null, ['class' => 'form-control ', 'placeholder' => __('Select Status'), 'required' => 'required']) }}
                                                <p class="text-danger d-none" id="{{ 'status_validation' }}">
                                                    {{ __('This field is required.') }}</p>
                                            </div>
                                            <div class="form-group col-md-6">
                                                {!! Form::label('location', __('Location'), ['class' => 'col-form-label']) !!}
                                                {!! Form::text('location', old('location'), [
                                                    'class' => 'form-control',
                                                    'required' => 'required',
                                                    'placeholder' => __('Enter rfx Location'),
                                                ]) !!}
                                                <p class="text-danger d-none" id="{{ 'location_validation' }}">
                                                    {{ __('This field is required.') }}</p>
                                            </div>

                                            <div class="form-group col-md-6">
                                                {!! Form::label('position', __('No. of Positions'), ['class' => 'col-form-label']) !!}
                                                {!! Form::number('position', old('positions'), [
                                                    'class' => 'form-control',
                                                    'required' => 'required',
                                                    'step' => '1',
                                                    'placeholder' => __('Enter Position'),
                                                ]) !!}
                                                <p class="text-danger d-none" id="{{ 'position_validation' }}">
                                                    {{ __('This field is required.') }}</p>
                                            </div>

                                            <div class="form-group col-md-6">
                                                {!! Form::label('budget_from', __('Budget From'), ['class' => 'col-form-label']) !!}
                                                {!! Form::number('budget_from', old('budget_from'), [
                                                    'class' => 'form-control',
                                                    'required' => 'required',
                                                    'step' => '1',
                                                    'placeholder' => __('Enter Amount'),
                                                ]) !!}
                                                <p class="text-danger d-none" id="{{ 'budget_from_validation' }}">
                                                    {{ __('This field is required.') }}</p>
                                            </div>

                                            <div class="form-group col-md-6">
                                                {!! Form::label('budget_to', __('Budget To'), ['class' => 'col-form-label']) !!}
                                                {!! Form::number('budget_to', old('budget_to'), [
                                                    'class' => 'form-control',
                                                    'required' => 'required',
                                                    'step' => '1',
                                                    'placeholder' => __('Enter Amount'),
                                                ]) !!}
                                                <p class="text-danger d-none" id="{{ 'budget_to_validation' }}">
                                                    {{ __('This field is required.') }}</p>
                                            </div>

                                            <div class="form-group col-md-6">
                                                {!! Form::label('start_date', __('Start Date'), ['class' => 'col-form-label']) !!}
                                                {!! Form::date('start_date', old('start_date'), [
                                                    'class' => 'form-control ',
                                                    'autocomplete' => 'off',
                                                    'required' => 'required',
                                                ]) !!}
                                                <p class="text-danger d-none" id="{{ 'start_date_validation' }}">
                                                    {{ __('This field is required.') }}</p>
                                            </div>

                                            <div class="form-group col-md-6">
                                                {!! Form::label('end_date', __('End Date'), ['class' => 'col-form-label']) !!}
                                                {!! Form::date('end_date', old('end_date'), [
                                                    'class' => 'form-control ',
                                                    'autocomplete' => 'off',
                                                    'required' => 'required',
                                                ]) !!}
                                                <p class="text-danger d-none" id="{{ 'end_date_validation' }}">
                                                    {{ __('This field is required.') }}</p>
                                            </div>

                                            <div class="form-group col-md-12">
                                                <label class="col-form-label" for="skill">{{ __('Skill Box') }}</label>
                                                <input type="text" class="form-control skill_data"
                                                    value="{{ $rfx->skill }}" data-toggle="tags" name="skill"
                                                    placeholder="{{__('Skill')}}" />
                                                <p class="text-danger d-none" id="{{ 'skill_validation' }}">
                                                    {{ __('This field is required.') }}</p>
                                            </div>
                                            <p class="text-danger d-none" id="skill_validation">
                                                {{ __('Skill field is required.') }}</p>

                                            <div class="row">
                                                <div class="col-6 form-group" id="users" style="display: none;">
                                                    {{ Form::label('user_id', __('Client'), ['class' => 'col-form-label']) }}
                                                    {{ Form::select('user_id', $users, null, ['class' => 'form-control select2']) }}
                                                    @if (empty($users->count()))
                                                        <div class="text-muted text-xs">
                                                            {{ __('Please create new client') }} <a
                                                                href="{{ route('users.index') }}">{{ __('here') }}</a>.
                                                        </div>
                                                    @endif
                                                    <p class="text-danger d-none" id="{{ 'user_id_validation' }}">
                                                        {{ __('This field is required.') }}</p>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row">

                                            <h5 class="mb-0">{{ __('RFx Checkboxes') }}</h5>
                                            <hr>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <h6>{{ __('Need to Ask ?') }}</h6>
                                                        <div class="my-4">
                                                            <div class="form-check custom-checkbox">
                                                                <input type="checkbox" class="form-check-input"
                                                                    name="applicant[]" value="gender" id="check-gender"
                                                                    {{ in_array('gender', $rfx->applicant) ? 'checked' : '' }}>
                                                                <label class="form-check-label"
                                                                    for="check-gender">{{ __('Gender') }}
                                                                </label>
                                                            </div>
                                                            <div class="form-check custom-checkbox">
                                                                <input type="checkbox" class="form-check-input"
                                                                    name="applicant[]" value="dob" id="check-dob"
                                                                    {{ in_array('dob', $rfx->applicant) ? 'checked' : '' }}>
                                                                <label class="form-check-label"
                                                                    for="check-dob">{{ __('Date Of Birth') }}</label>
                                                            </div>
                                                            <div class="form-check custom-checkbox">
                                                                <input type="checkbox" class="form-check-input"
                                                                    name="applicant[]" value="country" id="check-country"
                                                                    {{ in_array('country', $rfx->applicant) ? 'checked' : '' }}>
                                                                <label class="form-check-label"
                                                                    for="check-country">{{ __('Country') }}</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <h6>{{ __('Need to show Option ?') }}</h6>
                                                        <div class="my-4">
                                                            <div class="form-check custom-checkbox">
                                                                <input type="checkbox" class="form-check-input"
                                                                    name="visibility[]" value="profile"
                                                                    id="check-profile"
                                                                    {{ in_array('profile', $rfx->visibility) ? 'checked' : '' }}>
                                                                <label class="form-check-label"
                                                                    for="check-profile">{{ __('Profile Image') }}
                                                                </label>
                                                            </div>
                                                            <div class="form-check custom-checkbox">
                                                                <input type="checkbox" class="form-check-input"
                                                                    name="visibility[]" value="proposal"
                                                                    id="check-proposal"
                                                                    {{ in_array('proposal', $rfx->visibility) ? 'checked' : '' }}>
                                                                <label class="form-check-label"
                                                                    for="check-proposal">{{ __('Proposal') }}</label>
                                                            </div>
                                                            <div class="form-check custom-checkbox">
                                                                <input type="checkbox" class="form-check-input"
                                                                    name="visibility[]" value="letter" id="check-letter"
                                                                    {{ in_array('letter', $rfx->visibility) ? 'checked' : '' }}>
                                                                <label class="form-check-label"
                                                                    for="check-letter">{{ __('Cover Letter') }}</label>
                                                            </div>
                                                            <div class="form-check custom-checkbox">
                                                                <input type="checkbox" class="form-check-input"
                                                                    name="visibility[]" value="terms" id="check-terms"
                                                                    {{ in_array('terms', $rfx->visibility) ? 'checked' : '' }}>
                                                                <label class="form-check-label"
                                                                    for="check-terms">{{ __('Terms And Conditions') }}</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <h5 class="mb-0">{{ __('Questions Checkboxes') }}</h5>
                                                <hr>
                                                <div class="form-group col-md-12">
                                                    <h6>{{ __('Custom Questions') }}</h6>
                                                    <div class="my-4">
                                                        @foreach ($customQuestion as $question)
                                                            <div class="form-check custom-checkbox">
                                                                <input type="checkbox"
                                                                    class="form-check-input  @if ($question->is_required == 'yes') required-checkbox @endif"
                                                                    name="custom_question[]" value="{{ $question->id }}"
                                                                    @if ($question->is_required == 'yes') required @endif
                                                                    id="custom_question_{{ $question->id }}"
                                                                    {{ in_array($question->id, $rfx->custom_question) ? 'checked' : '' }}>
                                                                <label class="form-check-label"
                                                                    for="custom_question_{{ $question->id }}">{{ $question->question }}
                                                                    @if ($question->is_required == 'yes')
                                                                        <span class="text-danger">*</span>
                                                                    @endif
                                                                </label>
                                                            </div>
                                                        @endforeach
                                                        <p class="text-danger d-none" id="required_checkbox_validation">
                                                            {{ __('Please select the required question.') }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12" id="termsandcondition">
                                                <div class="form-group terms_val col-md-12">
                                                    {!! Form::label('terms_and_conditions', __('Terms And Conditions'), ['class' => 'col-form-label']) !!}
                                                    <textarea name="terms_and_conditions"
                                                        class="form-control summernote  {{ !empty($errors->first('terms_and_conditions')) ? 'is-invalid' : '' }}"
                                                        id="terms_and_conditions">{{ $rfx->terms_and_conditions }}</textarea>
                                                    <p class="text-danger d-none" id="terms_val">
                                                        {{ __('This field is required.') }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="form-group col-md-12">
                                            {!! Form::label('description', __('RFx Description'), ['class' => 'col-form-label']) !!}
                                            <textarea name="description"
                                                class="form-control dec_data summernote {{ !empty($errors->first('description')) ? 'is-invalid' : '' }}" required
                                                id="description">{{ $rfx->description }}</textarea>

                                            <p class="text-danger d-none" id="description_val">
                                                {{ __('This field is required.') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="form-group col-md-12">
                                            {!! Form::label('requirement', __('RFx Requirement'), ['class' => 'col-form-label']) !!}
                                            <textarea name="requirement"
                                                class="form-control req_data summernote  {{ !empty($errors->first('requirement')) ? 'is-invalid' : '' }}" required
                                                id="requirement">{{ $rfx->requirement }}</textarea>
                                            <p class="text-danger d-none" id="req_val">
                                                {{ __('This field is required.') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 text-end">
                            <button class="btn btn-primary d-inline-flex align-items-center" id="nextButton"
                                type="button">{{ __('Next') }}<i class="ti ti-chevron-right ms-2"></i></button>
                        </div>
                    </div>
                </div>
            </div>
            {{-- End Detail Tab --}}
            {{-- Purchase Tag --}}
            <div class="tab-pane fade" id="purchase-tab" role="tabpanel" aria-labelledby="pills-user-tab-2">
                <div class="row">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="mb-0">{{ __('Purchase Details') }}</h5>
                            <hr>
                            <div class="form-group col-md-6">
                                <label class="require form-label">{{ __('Billing Type') }}</label>
                                <select class="form-control" name="billing_type" required="" id="billing_type">
                                    <option value="">{{ __('Select Billing Type') }}</option>
                                    <option value="items" {{ $rfx->billing_type == 'items' ? 'selected' : '' }}>
                                        {{ __('Item') }}</option>
                                    <option value="rfx" {{ $rfx->billing_type == 'rfx' ? 'selected' : '' }}>
                                        {{ __('RFx') }}</option>
                                </select>
                                <p class="text-danger d-none" id="billing_validation">
                                    {{ __('Billing Type field is required.') }}</p>
                            </div>
                            <div id="item_tab" class="tab-content d-none">
                                <div class="col-12">
                                    <h5 class="h4 d-inline-block font-weight-400 mb-4">{{ __('Items') }}</h5>

                                    <div class="card item-repeater" data-value='{!! json_encode($rfxItemData) !!}'>
                                        <div class="item-section py-2">
                                            <div class="row justify-content-between align-items-center">
                                                <div
                                                    class="col-md-12 d-flex align-items-center justify-content-between justify-content-md-end">
                                                    <div class="all-button-box me-2">
                                                        <a href="#" data-repeater-create="" class="btn btn-primary"
                                                            data-bs-toggle="modal" data-target="#add-bank">
                                                            <i class="ti ti-plus"></i> {{ __('Add item') }}
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body table-border-style">
                                            <div class="table-responsive">
                                                <table class="table mb-0" data-repeater-list="items" id="sortable-table">
                                                    <thead>
                                                        <tr>
                                                            <th>{{ __('Item Type') }}</th>
                                                            <th>{{ __('Items') }}</th>
                                                            <th>{{ __('Quantity') }}</th>
                                                            <th>{{ __('Price') }} </th>
                                                            <th>{{ __('Discount') }}</th>
                                                            <th>{{ __('Tax') }} (%)</th>
                                                            <th class="text-end">{{ __('Amount') }} <br><small
                                                                    class="text-danger font-weight-bold">{{ __('After discount & tax') }}</small>
                                                            </th>
                                                            <th></th>
                                                        </tr>
                                                    </thead>
                                                        <tbody class="ui-sortable" data-repeater-item>
                                                            <tr >
                                                                {{ Form::hidden('id', null, ['class' => 'form-control id']) }}
                                                                <td class="form-group pt-0">
                                                                    {{ Form::select('product_type', $item_type, null, ['class' => 'form-control product_type ', 'placeholder' => '--']) }}
                                                                </td>
                                                                <td width="25%" class="form-group pt-0 product_div">
                                                                    <select name="product_id"
                                                                        class="form-control product_id item js-searchBox"
                                                                        data-url="{{ route('rfx.product') }}">
                                                                        <option>{{ '--' }}</option>
                                                                        @foreach ($items as $key => $item)
                                                                            <option value="{{ $key }}">
                                                                                {{ $item }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </td>
                                                                <td>
                                                                    <div class="form-group price-input input-group search-form"
                                                                        style="width: 160px">
                                                                        {{ Form::text('quantity', '', ['class' => 'form-control quantity', 'placeholder' => __('Qty'), 'readonly' => 'readonly']) }}
                                                                        <span
                                                                            class="unit input-group-text bg-transparent"></span>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="form-group price-input input-group search-form"
                                                                        style="width: 160px">
                                                                        {{ Form::text('price', '', ['class' => 'form-control price', 'placeholder' => __('Price'), 'readonly' => 'readonly']) }}
                                                                        <span
                                                                            class="input-group-text bg-transparent">{{ isset($company_settings['defult_currancy_symbol']) ? $company_settings['defult_currancy_symbol'] : '' }}</span>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="form-group price-input input-group search-form"
                                                                        style="width: 160px">
                                                                        {{ Form::text('discount', '', ['class' => 'form-control discount', 'placeholder' => __('Discount'), 'readonly' => 'readonly']) }}
                                                                        <span
                                                                            class="input-group-text bg-transparent">{{ isset($company_settings['defult_currancy_symbol']) ? $company_settings['defult_currancy_symbol'] : '' }}</span>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="form-group">
                                                                        <div class="input-group">
                                                                            <div class="taxes "></div>
                                                                            {{ Form::hidden('tax', null, ['class' => 'form-control tax']) }}
                                                                            {{ Form::hidden('itemTaxPrice', '', ['class' => 'form-control itemTaxPrice']) }}
                                                                            {{ Form::hidden('itemTaxRate', '', ['class' => 'form-control itemTaxRate']) }}
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td class="text-end amount">
                                                                    0.00
                                                                </td>
                                                                <td>
                                                                    <a href="#"
                                                                        class="bs-pass-para repeater-action-btn"
                                                                        data-repeater-delete>
                                                                        <div
                                                                            class="repeater-action-btn action-btn bg-danger ms-2">
                                                                            <i
                                                                                class="ti ti-trash text-white text-white"></i>
                                                                        </div>
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="2">
                                                                    <div class="form-group">
                                                                        {{ Form::textarea('product_description', null, ['class' => 'form-control pro_description', 'rows' => '1', 'placeholder' => __('Description')]) }}
                                                                    </div>
                                                                </td>
                                                                <td colspan="5"></td>
                                                            </tr>
                                                        </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="rfx_tab" class="tab-content d-none">
                                <div class="col-12">
                                    <h5 class="h4 d-inline-block font-weight-400 mb-4">{{ __('RFx`s') }}</h5>

                                    <div class="card repeater" data-value='{!! json_encode($rfxTaskData) !!}'>
                                        <div class="item-section py-2">
                                            <div class="row justify-content-between align-items-center">
                                                <div
                                                    class="col-md-12 d-flex align-items-center justify-content-between justify-content-md-end">
                                                    <div class="all-button-box me-2">
                                                        <a href="#" data-repeater-create=""
                                                            class="btn btn-primary">
                                                            <i class="ti ti-plus"></i> {{ __('Add RFx') }}
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body table-border-style">
                                            <div class="table-responsive">
                                                <table class="table mb-0 rfx-item" data-repeater-list="rfx"
                                                    id="sortable-table">
                                                    <thead>
                                                        <tr>
                                                            <th>{{ __('RFx') }}</th>
                                                            <th>{{ __('Description') }}</th>
                                                            <th>{{ __('Actions') }}</th>
                                                        </tr>
                                                    </thead>
                                                        <tbody class="ui-sortable" data-repeater-item>
                                                            <tr clas="hgh">
                                                                <td class="d-none">
                                                                    {{ Form::hidden('id', null, ['class' => 'form-control id']) }}
                                                                </td>
                                                                <td>
                                                                    <div class="form-group">
                                                                        <input type="text" name="rfx_task"
                                                                            class="form-control"
                                                                            placeholder="{{ __('RFx Task') }}">
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="form-group">
                                                                        <textarea name="rfx_description" class="form-control" rows="2"
                                                                            placeholder="{{ __('Description') }}"></textarea>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <a href="javascript:;" data-repeater-delete
                                                                        class="btn btn-sm btn-danger">
                                                                        <i class="ti ti-trash"></i>
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="col-md-12 text-end">
                            <div class="form-group">
                                <input type="submit" id="submit" value="{{ __('Update') }}"
                                    class="btn btn-primary">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- End Purchase Tab --}}
        </div>
        {{ Form::close() }}
    </div>
@endsection
