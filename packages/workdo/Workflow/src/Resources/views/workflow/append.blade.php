@php
    $json_data_array = isset($workflow->do_this_data) ? $workflow->do_this_data : '';
    $do_this_data = json_decode($json_data_array, true);
@endphp

{{-- Webhook URL --}}
    @if (in_array('Send Webhook URL', $Workflowdothis))
        <hr>
        <h4>{{ __('Webhook URL') }}</h4>
        <div class="row">
            <div class="form-group col-md-6 mt-2">
                {{ Form::label('', __('Enter Webhook'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::text('webhook_url', isset($do_this_data['webhook']) ? $do_this_data['webhook']['webhook_url'] : '', ['class' => 'form-control', 'placeholder' => __('Enter Webhook Url Here'), 'required' => 'required']) }}
            </div>
            <div class="form-group col-md-6 mt-2">
                {{ Form::label('', __('Method'), ['class' => 'form-label']) }}
                {{ Form::select('method', $methods, !empty($do_this_data['webhook']['method']) ? $do_this_data['webhook']['method'] : '', ['class' => 'form-control select']) }}
            </div>
        </div>
    @endif

{{-- email notification --}}
    @if (in_array('Send Email Notification', $Workflowdothis))
        <hr>
        @php
            $email_type_custom = false;
            $email_type_staff = false;
            $email = isset($do_this_data['email']) ? $do_this_data['email']['email_address'] : null;

            if (isset($do_this_data['email']['email_type']) && $do_this_data['email']['email_type'] == 'staff') {
                $email_type_staff = true;
            } else {
                $email_type_custom = true;
            }
        @endphp

        <h4>{{ __('Email') }}</h4>
        <div class="form-group col-md-12 mt-2">
            <div class="row">
                <label class="form-label col-md-2" for="example3cols3Input">{{ __('Select Type') }}</label>
                <div class="col-md-2">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="email_type" value="custom" id="email_type_custom"
                            {{ $email_type_custom ? "checked='checked'" : '' }}>
                        <label class="form-check-label pointer" for="email_type_custom">
                            {{ __('Custom') }}
                        </label>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="email_type" value="staff" id="email_type_staff"
                            {{ $email_type_staff ? "checked='checked'" : '' }}>
                        <label class="form-check-label pointer" for="email_type_staff">
                            {{ __('Staff') }}
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="form-group col-md-6 {{ $email_type_staff == false ? 'd-none' : '' }}" id="emailDropdown">
                {{ Form::label('', __('Select'), ['class' => 'form-label']) }}
                {{ Form::select('email_staff', $staff, $email_type_staff ? $email : null, ['class' => 'form-select select_person_email', 'data-toggle' => 'select', 'id' => 'select']) }}
            </div>

            <div class="form-group col-md-6">
                {{ Form::label('', __('Email Address'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::text('email_address', $email, ['class' => 'form-control person_email', 'required' => 'required', 'disabled' => $email_type_staff, 'id' => 'emailAddressField','placeholder'=>__('Enter Email Address')]) }}
            </div>
        </div>
    @endif

{{-- Twilio Notification --}}
    @if (in_array('Send Twilio Notification', $Workflowdothis))
        <hr>
        @php
            $twilio_type_custom = false;
            $twilio_type_staff = false;
            $twilio_mobile = isset($do_this_data['twilio']) ? $do_this_data['twilio']['twilio_number'] : null;

            if (isset($do_this_data['twilio']['twilio_type']) && $do_this_data['twilio']['twilio_type'] == 'staff') {
                $twilio_type_staff = true;
            } else {
                $twilio_type_custom = true;
            }
        @endphp
        <h4>{{ __('Twilio') }}</h4>
        <div class="form-group col-md-12 mt-2">
            <div class="row">
                <label class="form-label col-md-2" for="example3cols3Input">{{ __('Select Type') }}</label>
                <div class="col-md-2">
                    <div class="form-check">
                        <input class="form-check-input " type="radio" name="twilio_type" value="custom"
                            id="twilio_type_custom" {{ $twilio_type_custom ? "checked='checked'" : '' }}>
                        <label class="form-check-label pointer" for="twilio_type_custom">
                            {{ __('Custom') }}
                        </label>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="twilio_type" value="staff" id="twilio_type_staff"
                            {{ $twilio_type_staff ? "checked='checked'" : '' }}>
                        <label class="form-check-label pointer" for="twilio_type_staff" checked>
                            {{ __('Staff') }}
                        </label>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-6 {{ $twilio_type_staff == false ? 'd-none' : '' }}" id="twilioDropdown">
                {{ Form::label('', __('Select'), ['class' => 'form-label']) }}
                {{ Form::select('twilio_staff', $staff_mobile, $twilio_type_staff ? $staff_mobile : null, ['class' => 'form-select select_person_mobile', 'data-toggle' => 'select', 'id' => 'select']) }}
            </div>
            <x-mobile divClass="col-md-6" name="twilio_number" class="form-control" label=" {{ __('Send Twilio SMS to this Number') }}"         placeholder="{{ __('Enter number') }}" value="{{ $twilio_mobile }}" disabled="{{ $twilio_type_staff ? 'disabled' : '' }}" id="twilioField" required></x-mobile>
        </div>
    @endif

{{-- Slack Notification --}}
    @if (in_array('Send Slack Notification', $Workflowdothis))
        <hr>
        <h4>{{ __('Slack') }}</h4>
        <div class="row">
            <div class="form-group col-md-6 mt-2">
                {{ Form::label('', __('Enter Slack Webhook URL'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::text('slack_url', isset($do_this_data['slack']) ? $do_this_data['slack']['slack_url'] : '', ['class' => 'form-control', 'placeholder' => __('Enter Webhook URL'), 'required' => 'required']) }}
            </div>
        </div>
    @endif

{{-- Telegram Notification --}}
    @if (in_array('Send Telegram Notification', $Workflowdothis))
        <hr>
        <h4>{{ __('Telegram') }}</h4>
        <div class="row">
            <div class="form-group col-md-6 mt-2">
                {{ Form::label('', __('Telegram Access Token'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::text('telegram_access', isset($do_this_data['telegram']) ? $do_this_data['telegram']['telegram_access'] : '', ['class' => 'form-control', 'placeholder' => __('Telegram Access Token'), 'required' => 'required']) }}
            </div>
            <div class="form-group col-md-6 mt-2">
                {{ Form::label('', __('Telegram ChatID'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::text('telegram_chat', isset($do_this_data['telegram']) ? $do_this_data['telegram']['telegram_chat'] : '', ['class' => 'form-control', 'placeholder' => __('Telegram ChatID'), 'required' => 'required']) }}
            </div>
        </div>
    @endif

{{-- summer note --}}
    @if (in_array('Send Slack Notification', $Workflowdothis) ||
            in_array('Send Telegram Notification', $Workflowdothis) ||
            in_array('Send Twilio Notification', $Workflowdothis) ||
            in_array('Send Email Notification', $Workflowdothis))
        <hr>
        <div class="form-group col-md-12 mt-2">
            <div class="form-group">
                {{ Form::label('message', __('Send Message'), ['class' => 'form-label']) }}
                @if (!empty($workflow))
                    <textarea class="form-control summernote" name="message" id="notification-msg" rows="3">{!! $workflow->message !!}</textarea>
                @else
                    <textarea class="form-control summernote" name="message" id="notification-msg" rows="3"></textarea>
                @endif
            </div>
        </div>
        <script>
            $(document).ready(function() {
                summernote();
                var $emailAddressField = $('#emailAddressField');

                $('input[type=radio][name=email_type]').change(function() {
                    if (this.value === 'staff') {
                        $('#emailDropdown').removeClass('d-none');
                        if ($(this).val() !== '') {
                            $emailAddressField.prop('disabled', true);
                        } else {
                            $emailAddressField.prop('disabled', false);
                        }
                    } else if (this.value === 'custom') {
                        $('#emailDropdown').addClass('d-none');
                        $('.person_email').val('');
                        if ($(this).val() == '') {
                            $emailAddressField.prop('disabled', true);
                        } else {
                            $emailAddressField.prop('disabled', false);
                        }
                    }
                });
            });

            $(document).ready(function() {
                var $twilioField = $('#twilioField');

                $('input[type=radio][name=twilio_type]').change(function() {
                    if (this.value === 'staff') {
                        $('#twilioDropdown').removeClass('d-none');
                        if ($(this).val() !== '') {
                            $twilioField.prop('disabled', true);
                        } else {
                            $twilioField.prop('disabled', false);
                        }
                    } else if (this.value === 'custom') {
                        $('#twilioDropdown').addClass('d-none');
                        $('.twilio_number').val('');
                        if ($(this).val() == '') {
                            $twilioField.prop('disabled', true);
                        } else {
                            $twilioField.prop('disabled', false);
                        }
                    }
                });
            });

            $(document).on("change", ".select_person_email", function() {
                $(".person_email").val($(this).val());
            });

            $(document).on("change", ".select_person_mobile", function() {
                $(".twilio_number").val($(this).val());
            });
        </script>
    @endif
