<div class="container">
    <div class="row">
        <div class="col-lg-12 col-12">
            <div class="vendor-shedule">
                <h4 class="mb-3">{{ __('Choose Time Slot') }}</h4>
                <p class="text-danger d-none slot_text">{{ __('You have to select one slot') }}</p>
                <div class="TimeSlot">
                    <div class="row">
                        @if (!empty($slot))
                            @foreach ($slot as $time)
                                <div class="col-sm-3 col-6">
                                    <div class="single time-slot {{ $time['start_time'] . ':00' == (isset($booking->start_time) ? $booking->start_time : '') && $time['end_time'] . ':00' == (isset($booking->end_time) ? $booking->end_time : '') ? 'selected active' : '' }}"
                                        @if (!$time['available']) style="color: red; pointer-events: none;" @endif>
                                        <p class="time-slot-text time" data-start="{{ $time['start_time'] }}"
                                            data-end="{{ $time['end_time'] }}">
                                            {{ $time['start_time'] }} - {{ $time['end_time'] }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                            {{ Form::hidden('start_time', isset($booking->start_time) ? $booking->start_time : null, ['class' => 'start-time-input']) }}
                            {{ Form::hidden('end_time', isset($booking->end_time) ? $booking->end_time : null, ['class' => 'end-time-input']) }}
                        @else
                            <p>{{ __('No available time slots for the selected date. Please choose another date.') }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-12 col-12 mt-2">
            <div class="service-details rounded-1 p-3 mb-3">
                <div class="row d-flex align-items-center gap-2">
                    <div class="col-auto">
                        <h6>{{ __('Estimate time') }}</h6>
                        <p class="m-0">{{ $service->time }}</p>
                    </div>
                    <div class="col-auto">
                        <h6>{{ __('Price') }}</h6>
                        <p class="m-0">{{ currency_format_with_sym($price) ?? '-' }}</p>
                    </div>
                    <div class="col-auto">
                        <h6>{{ __('Tax Price') }}</h6>
                        @if (!empty($service->item) && !empty($service->item->tax_id))
                            @php
                                $totalTaxRate = 0;
                                $totalTaxPrice = 0;
                                $totalPrice = 0;
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
                                    $totalPrice = $totalTaxPrice + $person * $service->item->sale_price;
                                @endphp
                            @endforeach
                            <p class="m-0">
                                {{ currency_format_with_sym($totalTaxPrice, $service->created_by, $service->workspace) }}
                            </p>
                        @else
                            <p class="m-0">{{ currency_format_with_sym(0, $service->created_by, $service->workspace) }}</p>
                            @php
                                $totalPrice = $person * $service->item->sale_price;
                            @endphp
                        @endif
                    </div>
                    <div class="col-auto">
                        <h6>{{ __('Total Price') }}</h6>
                        <p class="m-0">{{ currency_format_with_sym($totalPrice) ?? '-' }}</p>
                    </div>
                </div>
            </div>
            <div class="row walk_in">
                <div class="form-group col-md-6 col-12 mb-3">
                    {{ Form::label('type', __('Type'), ['class' => 'col-form-label']) }}
                    {{ Form::select('type', $type, '', ['class' => 'form-control type']) }}
                </div>
                <div class="form-group col-md-6 col-12 mb-3">
                    {{ Form::label('name', __('Name'), ['class' => 'col-form-label']) }}
                    {{ Form::text('name', isset($booking->name) ? $booking->name : '', ['class' => 'form-control', 'placeholder' => 'Enter Name']) }}
                </div>
                <div class="form-group col-md-6 col-12 mb-3">
                    {{ Form::label('number', __('Mobile Number'), ['class' => 'col-form-label']) }}
                    {{ Form::text('number', isset($booking->number) ? $booking->number : '', ['class' => 'form-control ', 'placeholder' => 'Enter Mobile Number']) }}
                </div>
                <div class="form-group col-md-6 col-12 mb-3">
                    {{ Form::label('email', __('Email'), ['class' => 'col-form-label']) }}
                    {{ Form::email('email', isset($booking->email) ? $booking->email : '', ['class' => 'form-control ', 'placeholder' => 'Enter Email']) }}
                </div>
            </div>
            <div class="row client d-none">
                @if (\Auth::user()->type == 'company')
                <div class="form-group col-md-6 col-12 mb-3">
                        {{ Form::label('type', __('Type'), ['class' => 'col-form-label']) }}
                        {{ Form::select('type', $type, 'client', ['class' => 'form-control type', 'readonly' => 'readonly']) }}
                    </div>
                    <div class="form-group col-md-6 col-12 mb-3">
                        {{ Form::label('client_name', __('Name'), ['class' => 'col-form-label']) }}
                        {{ Form::select('client_name', $client, isset($booking->client_id) ? $booking->client_id : \Auth::user()->id, ['class' => 'form-control client_name', 'placeholder' => 'select name']) }}
                    </div>
                    <div class="form-group col-md-6 col-12 mb-3">
                        {{ Form::label('client_number', __('Mobile Number'), ['class' => 'col-form-label']) }}
                        {{ Form::text('client_number', '', ['class' => 'form-control client_number', 'placeholder' => 'Enter Mobile Number', 'disabled' => 'disabled']) }}
                    </div>
                    <div class="form-group col-md-6 col-12 mb-3">
                        {{ Form::label('client_email', __('Email'), ['class' => 'col-form-label']) }}
                        {{ Form::email('client_email', '', ['class' => 'form-control client_email', 'placeholder' => 'Enter Email', 'disabled' => 'disabled']) }}
                    </div>
                    {{ Form::hidden('client_id', isset($booking->client_id) ? $booking->client_id : '', ['class' => 'client_id']) }}
                @else
                <div class="form-group col-md-6 col-12 mb-3">
                        {{ Form::label('type', __('Type'), ['class' => 'col-form-label']) }}
                        {{ Form::text('type', \Auth::user()->type , ['class' => 'form-control type', 'readonly' => 'readonly']) }}
                    </div>
                    <div class="form-group col-md-6 col-12 mb-3">
                        {{ Form::label('client_name', __('Name'), ['class' => 'col-form-label']) }}
                        {{ Form::text('client_name', \Auth::user()->id , ['class' => 'form-control', 'readonly' => 'readonly']) }}
                    </div>
                    <div class="form-group col-md-6 col-12 mb-3">
                        {{ Form::label('client_number', __('Mobile Number'), ['class' => 'col-form-label']) }}
                        {{ Form::text('client_number', \Auth::user()->mobile_no, ['class' => 'form-control',  'readonly' => 'readonly']) }}
                    </div>
                    <div class="form-group col-md-6 col-12 mb-3">
                        {{ Form::label('client_email', __('Email'), ['class' => 'col-form-label']) }}
                        {{ Form::text('client_email', \Auth::user()->email, ['class' => 'form-control',  'readonly' => 'readonly']) }}
                    </div>
                    {{ Form::hidden('client_id', \Auth::user()->id, ['class' => 'client_id']) }}
                @endif
            </div>
        </div>
    </div>
    <div class="text-end mt-3">
        <button class="btn btn-warning time" id="submitBtn" type="submit">{{ __('Book Now') }}</button>
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
        client_id = $('.client_id').val();
        if (client_id != 0) {
            $('.client').removeClass('d-none');
            $('.walk_in').addClass('d-none');
            $('.client_name').prop('required', true);
            userDetail(client_id);
        } else {
            $('input[name="name"]').prop('required', true);
            $('input[name="email"]').prop('required', true);
            $('input[name="number"]').prop('required', true);
        }
    });
</script>
<script>
    $(document).on('click', '.type', function() {
        type = $(this).val();
        if (type == 'client' || type == 'tenant') {
            $('.client').removeClass('d-none');
            $('.walk_in').addClass('d-none');
            $('.client_name').prop('required', true);
            $('input[name="name"]').prop('required', false);
            $('input[name="email"]').prop('required', false);
            $('input[name="number"]').prop('required', false);
            users(type);
        } else {
            $('.client').addClass('d-none');
            $('.walk_in').removeClass('d-none');
            $('.client_name').prop('required', false);
            $('input[name="name"]').prop('required', true);
            $('input[name="email"]').prop('required', true);
            $('input[name="number"]').prop('required', true);
        }
    });
</script>

<script>
    $(document).on('click', '.client_name', function() {
        user_id = $(this).val();
        userDetail(user_id)
    });

    function userDetail(user_id) {
        $.ajax({
            url: '{{ route('users.detail') }}',
            type: 'POST',
            data: {
                "_token": "{{ csrf_token() }}",
                user_id: user_id,
            },
            success: function(data) {
                $('.client_email').val(data.email);
                $('.client_number').val(data.mobile_no);
            }
        });
    }

    function users(type) {
        $.ajax({
            url: '{{ route('users') }}',
            type: 'POST',
            data: {
                "_token": "{{ csrf_token() }}",
                type: type,
            },
            success: function(data) {

                $('.client_name').empty();
                $('.client_name').append('<option value=""> {{ __('select name') }} </option>');
                $.each(data, function(key, value) {
                    $('.client_name').append('<option value="' + key + '">' + value +
                        '</option>');
                });
            }
        });
    }
</script>
