@php
$admin_settings = getAdminAllSetting($workspace->created_by, $workspace->id);
$company_settings = getCompanyAllSetting($workspace->created_by, $workspace->id);
$check = false;

if (
    (isset($company_settings['stripe_is_on']) ? $company_settings['stripe_is_on'] : 'off') == 'on' &&
    !empty($company_settings['stripe_key']) &&
    !empty($company_settings['stripe_secret']) ||
    (isset($company_settings['paypal_payment_is_on']) ? $company_settings['paypal_payment_is_on'] : 'off') == 'on' &&
    !empty($company_settings['company_paypal_client_id']) &&
    !empty($company_settings['company_paypal_secret_key'])
    ) {
    $check = true;
}
@endphp

<div class="container">
    <div class="card">
        <div class="card-body">
            {{ Form::open(array('route' => array('facilities.store', $slug), 'id'=>'payment_form', 'enctype' => 'multipart/form-data')) }}
            <div class="row">
                <div class="col-lg-6 col-12">
                    <div class="vendor-shedule">
                        <h4 class="mb-3">{{__('Choose Time Slot')}}</h4>
                        <p class="text-danger d-none slot_text">{{ __('You have to select one slot') }}</p>
                        <div class="TimeSlot">
                            <div class="row">
                                @if (!empty($slot))
                                @foreach ($slot as $time)
                                <div class="col-sm-3 col-6">
                                    <div class="single time-slot" @if (!$time['available']) style="color: red; pointer-events: none;" @endif>
                                        <p class="time-slot-text time" data-start="{{ $time['start_time'] }}" data-end="{{ $time['end_time'] }}">
                                            {{ $time['start_time'] }} - {{ $time['end_time'] }}
                                        </p>
                                    </div>
                                </div>
                                    @endforeach
                                    {{ Form::hidden('start_time', null, ['class' => 'start-time-input']) }}
                                    {{ Form::hidden('end_time', null, ['class' => 'end-time-input']) }}
                                    @else
                                    <p>{{ __('No available time slots for the selected date. Please choose another date.') }}</p>
                                    @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-12">
                    <div class="service-details rounded-1 p-3 mb-3">
                        <div class="row d-flex align-items-center gap-2">
                            <div class="col-auto">
                                <h6>{{__('Estimate time')}}</h6>
                                <p class="m-0">{{$service->time}}</p>
                            </div>
                            <div class="col-auto">
                                <h6>{{__('Price')}}</h6>
                                <p class="m-0">{{currency_format_with_sym($price , $workspace->created_by , $workspace->id)}}</p>
                            </div>
                            <div class="col-auto">
                                <h6>{{__('Tax Price')}}</h6>
                                @if (!empty($service->item) && !empty($service->item->tax_id))
                                    <table>
                                        @php
                                            $totalTaxRate = 0;
                                            $totalTaxPrice = 0;
                                            $totalPrice = $person * $service->item->sale_price;
                                            $taxes = \App\Models\Invoice::tax($service->item->tax_id);
                                        @endphp
                                        @foreach ($taxes as $tax)
                                            @php
                                                $taxPrice = \App\Models\Invoice::taxRate(
                                                    $tax->rate,
                                                    $service->item->sale_price,
                                                    $person,
                                                    $service->item->discount,
                                                );
                                                $totalTaxPrice += $taxPrice;
                                                $totalPrice = $totalTaxPrice + $totalPrice;
                                            @endphp
                                        @endforeach
                                        <p class="m-0">{{currency_format_with_sym($totalTaxPrice , $workspace->created_by , $workspace->id)}}</p>
                                </table>
                            @else
                            @php
                                $totalPrice = ($person * $service->item->sale_price);
                            @endphp
                                <p class="m-0">{{currency_format_with_sym(0 , $workspace->created_by , $workspace->id)}}</p>
                            @endif
                            </div>
                            {{ Form::hidden('price', $totalPrice ?? 0, ['class' => 'price']) }}

                            <div class="col-auto">
                                <h6>{{__('Total Price')}}</h6>
                                <p class="m-0">{{currency_format_with_sym($totalPrice , $workspace->created_by , $workspace->id)}}</p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6 col-12 mb-3">
                            {{ Form::label('name', __('Name'),['class'=>'col-form-label']) }}
                            {{ Form::text('name', '', ['class' => 'form-control name','placeholder'=> 'Enter Name' ,'required'=>'required']) }}
                            <span class="text-danger d-none name_text">{{ __('This field is required') }}</span>
                        </div>

                        <div class="form-group col-md-6 col-12 mb-3">
                            {{ Form::label('service', __('Service'),['class'=>'col-form-label']) }}
                            {{ Form::text('service', !empty($service->item) ? $service->item->name : '', ['class' => 'form-control','required' => 'required','readonly'=>'readonly']) }}
                        </div>
                        <div class="form-group col-md-6 col-12 mb-3">
                            {{ Form::label('date', __('Date'), ['class' => 'col-form-label']) }}
                            {{ Form::date('date', $formattedSelectedDate, ['class' => 'form-control ','required' => 'required','readonly'=>'readonly']) }}
                        </div>
                        <div class="form-group col-md-6 col-12 mb-3">
                            {{ Form::label('number', __('Mobile Number'), ['class' => 'col-form-label']) }}
                            {{ Form::text('number', '', ['class' => 'form-control number','placeholder'=> 'Enter Mobile Number' , 'required' => 'required']) }}
                            <span class="text-danger d-none number_text">{{ __('This field is required') }}</span>
                        </div>
                        <div class="form-group col-md-6 col-12 mb-3">
                            {{ Form::label('email', __('Email'), ['class' => 'col-form-label']) }}
                            {{ Form::email('email', '', ['class' => 'form-control ','placeholder'=> 'Enter Email' , 'required' => 'required']) }}
                        </div>

                        <div class="form-group col-md-6 col-12 mb-3">
                            {{ Form::label('gender', __('Gender'),['class'=>'col-form-label']) }}
                            {{ Form::text('gender', $gender, ['class' => 'form-control','required' => 'required','readonly'=>'readonly']) }}
                        </div>

                        <div class="form-group col-md-6">
                            <label class="require form-label">{{ __('Select Payment Option') }}</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_option" id="online_payment" value="Online" required>
                                <label class="form-check-label" for="online_payment">
                                    {{ __('Online') }}
                                </label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_option" id="offline_payment" value="Offline" required>
                                <label class="form-check-label" for="offline_payment">
                                    {{ __('Offline') }}
                                </label>
                            </div>
                        </div>

                        <div id="offlineSection" style="display:none;">
                            @if($check == true)
                                <div class="row justify-content-between">
                                    <div class="col-md-6 col-12">
                                        <b><h5>{{__('Payment Method')}}</h5><b><br>
                                        @stack('facilities_payment')
                                    </div>
                                </div>
                            @else
                                <p>{{ __('Please payment setting save.')}}</p>
                            @endif
                        </div>
                        <input type="hidden" name="service_id" value="{{$service->id}}">
                        <input type="hidden" name="person" value="{{$person}}">
                    </div>
                </div>
            </div>
            <div class="text-center mt-3">
                {{ Form::submit('Book Now', ['id' => 'submitBtn', 'class' => 'btn btn-primary submit']) }}
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $(".single").click(function() {
            $(".single").removeClass("selected");
            $(this).addClass("selected");
        });
    });
</script>

<script>
    $(document).ready(function() {

        $(document).ready(function() {
            $('.time-slot').on('click', function() {

                $('.time-slot').removeClass('active');

                $(this).addClass('active');

                var startTime = $(this).find('.time-slot-text').data('start');
                var endTime = $(this).find('.time-slot-text').data('end');

                $('.start-time-input').val(startTime);
                $('.end-time-input').val(endTime);
            });
        });
    });
</script>
<script>
    $(document).ready(function() {
        $('input[name="payment_option"]').change(function() {

            if ($(this).val() == 'Offline') {
                $('#offlineSection').hide();
            } else {
                $('#offlineSection').show();
            }
        });
    });

    $('body').on('click', '.time, .submit' , function(event) {

        if ($('#online_payment').prop('checked')) {
            $('.payment_method').prop('required',true);
        }
        else {
            $('.payment_method').prop('required',false);
        }

        var start_time = $('.start-time-input').val();
        var end_time = $('.end-time-input').val();
        var name = $('.name').val();
        var number = $('.number').val();

        if (start_time == '' || end_time == '') {
            $('.slot_text').removeClass('d-none');
            event.preventDefault();
        }
        else if (name.trim() == '') {
            $('.name_text').removeClass('d-none');
            $('.slot_text').addClass('d-none');
            event.preventDefault();
        }
        else if (number.trim() == '') {
            $('.number_text').removeClass('d-none');
            $('.name_text').addClass('d-none');
            event.preventDefault();
        }
        else {
            $('.slot_text').addClass('d-none');
        }
    });
</script>
