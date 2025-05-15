@extends('procurement::layouts.master')
@section('page-title')
    {{ $rfx->title }}
@endsection

@section('content')
    <div class="job-wrapper">
        <div class="job-content">
            <nav class="navbar">
                <div class="container">
                    <a class="navbar-brand" href="#">
                        <img src="{{ !empty(get_file(company_setting('logo_light', $rfx->created_by, $rfx->workspace))) ? get_file('uploads/logo/logo_light.png') : 'WorkDo' }}"
                            alt="logo" style="width: 90px">
                    </a>
                    <li class="dropdown dash-h-item drp-language">
                        <div class="dropdown global-icon" data-toggle="tooltip"
                            data-original-titla="{{ __('Choose Language') }}">
                            <a class="nav-link px-0 d-flex align-items-center btn bg-white px-3 py-2" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-offset="0,10">
                                <span>{{ \Str::upper($currantLang) }}</span>
                                <i class="ti ti-chevron-down drp-arrow nocolor"></i>
                            </a>
                            <div class="dropdown-menu  dropdown-menu-right" aria-labelledby="dropdownMenuButton"
                                style="min-width: auto">
                                @foreach ($languages as $key => $language)
                                    <a class="dropdown-item @if ($key == $currantLang) text-danger @endif"
                                        href="{{ route('rfx.apply', [$rfx->code, $key]) }}">{{ $language }}</a>
                                @endforeach
                            </div>
                        </div>
                    </li>
                </div>
            </nav>
            <section class="job-banner">
                <div class="job-banner-bg">
                    <img src="{{ asset('packages/workdo/Procurement/src/Resources/assets/image/banner.png') }}"
                        alt="">
                </div>
                <div class="container">
                    <div class="job-banner-content text-center text-white">
                        <h1 class="text-white mb-3">
                            {{ __(' We help') }} <br> {{ __('businesses grow') }}
                        </h1>
                        <p>{{ __('Work there. Find the dream RFx you’ve always wanted..') }}</p>
                        </p>
                    </div>
                </div>
            </section>
            {{ Form::open(['route' => ['rfx.apply.data', $rfx->code], 'method' => 'post', 'enctype' => 'multipart/form-data', 'class' => 'needs-validation', 'novalidate']) }}
            <section class="apply-job-section">
                <div class="container">
                    <div class="apply-job-wrapper card mb-0">
                        <div class="section-title text-center">
                            <h2 class="h1 mb-3"> {{ $rfx->title }}</h2>
                            <div class="d-flex flex-wrap justify-content-center gap-1 mb-3">
                                @foreach (explode(',', $rfx->skill) as $skill)
                                    <span class="badge bg-primary p-2 px-3">{{ $skill }}</span>
                                @endforeach
                            </div>
                            @if (!empty($rfx->location))
                                <p> <i class="ti ti-map-pin ms-1"></i> {{ $rfx->location }}</p>
                            @endif
                        </div>
                        <div class="apply-job-form">
                            <h2 class="mb-4">{{ __('Apply for this RFx') }}</h2>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        {{ Form::label('name', __('Name'), ['class' => 'form-label']) }}<x-required></x-required>
                                        {{ Form::text('name', null, ['class' => 'form-control name', 'placeholder= "Enter Name"', 'required' => 'required']) }}
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        {{ Form::label('email', __('Email'), ['class' => 'form-label']) }}<x-required></x-required>
                                        {{ Form::text('email', null, ['class' => 'form-control', 'placeholder= "Enter Email"', 'required' => 'required']) }}
                                    </div>
                                </div>
                                <x-mobile divClass="col-sm-6" name="phone" required="true"></x-mobile>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        {!! Form::label('bid_amount', __('Bid Amount'), ['class' => 'form-label']) !!}<x-required></x-required>
                                        {!! Form::number('bid_amount', old('bid_amount'), [
                                            'class' => 'form-control w-100',
                                            'placeholder= "Enter Amount"',
                                            'required' => 'required',
                                        ]) !!}
                                    </div>
                                </div>
                                @if (!empty($rfx->applicant) && in_array('gender', explode(',', $rfx->applicant)))
                                    <div class="form-group col-sm-6 ">
                                        {!! Form::label('gender', __('Gender'), ['class' => 'form-label']) !!}<x-required></x-required>
                                        <div class="card mb-0">
                                            <div class="d-flex radio-check  p-2">
                                                <div class="custom-control custom-radio custom-control-inline">
                                                    <input type="radio" id="g_male" value="Male" name="gender"
                                                        class="custom-control-input" required>
                                                    <label class="custom-control-label"
                                                        for="g_male">{{ __('Male') }}</label>
                                                </div>
                                                <div class="custom-control custom-radio custom-control-inline">
                                                    <input type="radio" id="g_female" value="Female" name="gender"
                                                        class="custom-control-input" required>
                                                    <label class="custom-control-label"
                                                        for="g_female">{{ __('Female') }}</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <div class="col-sm-6">
                                    @if (!empty($rfx->applicant) && in_array('dob', explode(',', $rfx->applicant)))
                                        <div class="form-group">
                                            {!! Form::label('dob', __('Date of Birth'), ['class' => 'form-label']) !!}<x-required></x-required>
                                            {!! Form::date('dob', old('dob'), [
                                                'class' => 'form-control datepicker w-100',
                                                'required' => 'required',
                                                'max' => date('Y-m-d'),
                                            ]) !!}
                                        </div>
                                    @endif
                                </div>


                                @if (!empty($rfx->applicant) && in_array('country', explode(',', $rfx->applicant)))
                                    <div class="form-group col-sm-6 ">
                                        {{ Form::label('country', __('Country'), ['class' => 'form-label']) }}<x-required></x-required>
                                        {{ Form::text('country', null, ['class' => 'form-control', 'placeholder= "Enter Country"', 'required' => 'required']) }}
                                    </div>
                                    <div class="form-group col-sm-6 country">
                                        {{ Form::label('state', __('State'), ['class' => 'form-label']) }}<x-required></x-required>
                                        {{ Form::text('state', null, ['class' => 'form-control', 'placeholder= "Enter State"', 'required' => 'required']) }}
                                    </div>
                                    <div class="form-group col-sm-6 country">
                                        {{ Form::label('city', __('City'), ['class' => 'form-label']) }}<x-required></x-required>
                                        {{ Form::text('city', null, ['class' => 'form-control', 'placeholder= "Enter City"', 'required' => 'required']) }}
                                    </div>
                                @endif

                                @if (!empty($rfx->visibility) && in_array('profile', explode(',', $rfx->visibility)))
                                    <div class="form-group col-sm-6 ">
                                        {{ Form::label('profile', __('Profile'), ['class' => 'form-label']) }}<x-required></x-required>
                                        <input type="file" class="form-control h-auto" name="profile" id="profile"
                                            data-filename="profile_create"
                                            onchange="document.getElementById('blah').src = window.URL.createObjectURL(this.files[0])"
                                            required>
                                        <img id="blah" src="" class="mt-3" width="25%"
                                            style="max-height: 100px; max-width: 100px" />
                                        <p class="profile_create"></p>
                                    </div>
                                @endif

                                @if (!empty($rfx->visibility) && in_array('proposal', explode(',', $rfx->visibility)))
                                    <div class="form-group col-sm-6 ">
                                        {{ Form::label('proposal', __('Proposal'), ['class' => 'form-label']) }}<x-required></x-required>
                                        <input type="file" class="form-control h-auto" name="proposal" id="proposal"
                                            data-filename="proposal_create"
                                            onchange="document.getElementById('blah1').src = window.URL.createObjectURL(this.files[0])"
                                            required>
                                        <img id="blah1" class="mt-3" src="" width="25%"
                                            style="max-height: 100px; max-width: 100px" />
                                        <p class="proposal_create"></p>
                                    </div>
                                @endif

                                @if (!empty($rfx->visibility) && in_array('letter', explode(',', $rfx->visibility)))
                                    <div class="form-group col-md-12 ">
                                        {{ Form::label('cover_letter', __('Cover Letter'), ['class' => 'form-label']) }}<x-required></x-required>
                                        {{ Form::textarea('cover_letter', null, ['class' => 'form-control', 'rows' => '3', 'required' => 'required']) }}
                                    </div>
                                @endif

                                @foreach ($questions as $question)
                                    <div class="form-group col-md-12  question question_{{ $question->id }}">
                                        {{ Form::label($question->question, $question->question, ['class' => 'form-label']) }}
                                        <input type="text" class="form-control"
                                            name="question[{{ $question->question }}]"
                                            {{ $question->is_required == 'yes' ? 'required' : '' }}>
                                    </div>
                                @endforeach

                                <!-- Billing Type Dropdown -->
                                <div class="form-group col-md-6">
                                    <label class="require form-label">{{ __('Billing Type') }}</label>
                                    {{ Form::text('billing_type', $rfx->billing_type, ['class' => 'form-control billing_type', 'required' => 'required', 'readonly' => 'readonly']) }}
                                    <p class="text-danger d-none" id="billing_validation">
                                        {{ __('Billing Type field is required.') }}</p>
                                </div>

                                <!-- Items Section -->
                                <div id="items_section" class="{{ $rfx->billing_type != 'items' ? 'd-none' : '' }}">
                                    <h3 class="mb-3">{{ __('Items') }}</h3>
                                    @foreach ($rfxItemData as $rfxItem)
                                        <div class="item-row row">
                                            <input type="hidden" name="items[{{ $loop->index }}][id]"
                                                value="{{ $rfxItem->id }}">
                                            <div class="col-md-2 col-sm-6">
                                                <div class="form-group">
                                                    <label for="Item Type"
                                                        class="form-label">{{ __('Item Type') }}</label>
                                                    <input type="text" name="items[{{ $loop->index }}][product_type]"
                                                        class="form-control"
                                                        value="{{ isset($rfxItem->product_type) ? $rfxItem->product_type : '' }}"
                                                        readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-2 col-sm-6">
                                                <div class="form-group">
                                                    <label for="Items" class="form-label">{{ __('Items') }}</label>
                                                    <input type="hidden" name="items[{{ $loop->index }}][product_id]"
                                                        class="form-control"
                                                        value="{{ isset($rfxItem->product_id) ? $rfxItem->product_id : 0 }}"
                                                        readonly>
                                                    <input type="text" name="items[{{ $loop->index }}][product_name]"
                                                        class="form-control"
                                                        value="{{ isset($rfxItem->product_id) ? $rfxItem->product()->name : '' }}"
                                                        readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-2 col-sm-6">
                                                <div class="form-group">
                                                    <label for="Price" class="form-label">{{ __('Price') }}</label>
                                                    <input type="number" name="items[{{ $loop->index }}][price]"
                                                        class="form-control price-input" step="0.01"
                                                        value="{{ $rfxItem->product_price }}"
                                                        {{ $rfx->billing_type == 'items' ? 'required' : '' }}>
                                                </div>
                                            </div>
                                            <div class="col-md-2 col-sm-6">
                                                <div class="form-group">
                                                    <label for="Discount" class="form-label">{{ __('Discount') }}</label>
                                                    <input type="number" name="items[{{ $loop->index }}][discount]"
                                                        class="form-control discount-input" step="0.01"
                                                        value="{{ $rfxItem->product_discount }}">
                                                </div>

                                            </div>
                                            <div class="col-md-2 col-sm-6">
                                                <div class="form-group">
                                                    <label for="Tax" class="form-label">{{ __('Tax') }}</label>
                                                    <input type="number" name="items[{{ $loop->index }}][tax]"
                                                        class="form-control tax-input" step="0.01"
                                                        value="{{ $rfxItem->product_tax }}">
                                                </div>

                                            </div>
                                            <div class="col-md-2 col-sm-6">
                                                <div class="form-group">
                                                    <label for="Amount" class="form-label">{{ __('Amount') }}</label>
                                                    <input type="number" name="items[{{ $loop->index }}][amount]"
                                                        class="form-control amount-input" step="0.01" readonly
                                                        required>
                                                </div>

                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <input type="text" class="form-control"
                                                        name="items[{{ $loop->index }}][product_description]"
                                                        value="{{ $rfxItem->product_description }}" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                    <!-- Add your item rows here -->
                                </div>

                                <!-- RFx Section -->
                                <div id="rfx_section" class="{{ $rfx->billing_type != 'rfx' ? 'd-none' : '' }}">
                                    <h3 class="mb-3">{{ __('RFx') }}</h3>
                                    @foreach ($rfxItemData as $rfxItem)
                                        <div class="rfx-row row mt-2">
                                            <input type="hidden" name="rfx[{{ $loop->index }}][id]"
                                                value="{{ $rfxItem->id }}">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="name" class="form-label">{{ __('RFx') }}</label>
                                                    <input type="text" name="rfx[{{ $loop->index }}][task]"
                                                        class="form-control" value="{{ $rfxItem->rfx_task }}" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="name" class="form-label">{{ __('Price') }}</label>
                                                    <input type="number" name="rfx[{{ $loop->index }}][price]"
                                                    class="form-control price-input" step="0.01" value=""
                                                    {{ $rfx->billing_type == 'rfx' ? 'required' : '' }}>
                                                </div>

                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label for="name" class="form-label">{{ __('Discount') }}</label>
                                                    <input type="number" name="rfx[{{ $loop->index }}][discount]"
                                                    class="form-control discount-input" step="0.01" value="0">
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label for="name" class="form-label">{{ __('Tax') }}</label>
                                                    <input type="number" name="rfx[{{ $loop->index }}][tax]"
                                                    class="form-control tax-input" step="0.01" value="0">
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label for="name" class="form-label">{{ __('Amount') }}</label>
                                                    <input type="number" name="rfx[{{ $loop->index }}][amount]"
                                                    class="form-control amount-input" step="0.01" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="name" class="form-label">{{ __('Description') }}</label>
                                                    <input type="text" name="rfx[{{ $loop->index }}][description]"
                                                    class="form-control" value="{{ $rfxItem->rfx_description }}"
                                                    readonly>
                                                </div>

                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Bid Total -->
                                <div class="form-group col-md-6">
                                    <label class="form-label">{{ __('Bid Total') }}</label>
                                    <input type="number" class="form-control" id="bid_total" name="bid_total" readonly>
                                </div>
                                @if (!empty($rfx->visibility) && in_array('terms', explode(',', $rfx->visibility)))
                                    <div class="form-group col-md-12 ">
                                        <div class="form-check custom-checkbox">
                                            <input type="checkbox" class="form-check-input" id="termsCheckbox"
                                                name="terms_condition_check" required>
                                            <label class="form-check-label" for="termsCheckbox">{{ __('I Accept the') }}
                                                <a href="{{ route('rfx.terms.and.conditions', [$rfx->code, $currantLang]) }}"
                                                    target="_blank">{{ __('terms and conditions') }}</a></label>
                                        </div>

                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">{{ __('Submit your application') }}</button>
                        </div>
                    </div>
                </div>
            </section>


            {{ Form::close() }}
        </div>
    </div>
@endsection
@push('scripts')
    <script src="{{ asset('packages/workdo/Procurement/src/Resources/assets/js/moment.min.js') }}"></script>
    <script src="{{ asset('packages/workdo/Procurement/src/Resources/assets/js/site.core.js') }}"></script>
    <script src="{{ asset('packages/workdo/Procurement/src/Resources/assets/js/autosize.min.js') }}"></script>
    <script src="{{ asset('packages/workdo/Procurement/src/Resources/assets/js/site.js') }}"></script>
    <script src="{{ asset('packages/workdo/Procurement/src/Resources/assets/js/demo.js') }} "></script>
    <script src="{{ asset('packages/workdo/Procurement/src/Resources/assets/js/bootstrap-notify.min.js') }}"></script>
    <script src="{{ asset('packages/workdo/Procurement/src/Resources/assets/js/daterangepicker.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const bidTotalInput = document.getElementById('bid_total');

            function calculateAmount(inputRow) {
                const price = parseFloat(inputRow.querySelector('.price-input').value) || 0;
                const discount = parseFloat(inputRow.querySelector('.discount-input').value) || 0;
                const tax = parseFloat(inputRow.querySelector('.tax-input').value) || 0;

                const amount = (price - discount) * (1 + tax / 100);
                inputRow.querySelector('.amount-input').value = amount.toFixed(2);

                calculateBidTotal();
            }

            function calculateBidTotal() {
                let total = 0;
                const amountInputs = document.querySelectorAll('.amount-input');
                amountInputs.forEach(input => {
                    total += parseFloat(input.value) || 0;
                });
                bidTotalInput.value = total.toFixed(2);
            }

            // Add event listeners to price, discount, and tax inputs for recalculating the Amount and Bid Total
            const priceInputs = document.querySelectorAll('.price-input');
            const discountInputs = document.querySelectorAll('.discount-input');
            const taxInputs = document.querySelectorAll('.tax-input');

            if ($('.billing_type').val() == 'items') {
                priceInputs.forEach(input => {
                    input.addEventListener('input', function() {
                        calculateAmount(input.closest('.item-row'));
                    });
                });

                discountInputs.forEach(input => {
                    input.addEventListener('input', function() {
                        calculateAmount(input.closest('.item-row'));
                    });
                });

                taxInputs.forEach(input => {
                    input.addEventListener('input', function() {
                        calculateAmount(input.closest('.item-row'));
                    });
                });

                // Initial calculation of Amounts and Bid Total
                document.querySelectorAll('.item-row').forEach(row => {
                    calculateAmount(row);
                });

            } else {
                document.querySelectorAll('.rfx-row').forEach(row => {
                    calculateAmount(row);
                });
                priceInputs.forEach(input => {
                    input.addEventListener('input', function() {
                        calculateAmount(input.closest('.rfx-row'));
                    });
                });

                discountInputs.forEach(input => {
                    input.addEventListener('input', function() {
                        calculateAmount(input.closest('.rfx-row'));
                    });
                });

                taxInputs.forEach(input => {
                    input.addEventListener('input', function() {
                        calculateAmount(input.closest('.rfx-row'));
                    });
                });
            }


        });
    </script>
@endpush
