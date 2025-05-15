{{-- email notification --}}
<div class="row col-md-12">
    @if (in_array('Email', $notification))
        <div class="col-md-6 mt-1">
            <div class="form-group ">
                {{ Form::label('', __('Email Address'), ['class' => 'form-label']) }}
                {{ Form::text('email_address', $useremail, ['class' => 'form-control person_email', 'required' => 'required', 'id' => 'emailAddressField']) }}
            </div>
        </div>
    @endif

{{-- sms Notification --}}
    @if (in_array('SMS', $notification))
        <x-mobile divClass="col-md-6 mt-1" name="sms_mobile_no" class="form-control" label=" {{ __('Send SMS to this Number') }}"  placeholder="{{ __('Enter number') }}" value="{{ $user_contact_number }}" required></x-mobile>
    @endif
</div>
<div class="row col-md-12">
{{-- slack Notification --}}
@if (in_array('Slack', $notification))
    <div class="col-md-6 mt-1">
        <div class="form-group ">
            {{ Form::label('', __('Enter Slack Webhook URL'), ['class' => 'form-label']) }}
            {{ Form::text('slack_url', null, ['class' => 'form-control ', 'required' => 'required']) }}
        </div>
    </div>
@endif


{{-- twilio Notification --}}
@if (in_array('Twilio', $notification))
    <x-mobile divClass="col-md-6 mt-1" name="twillo_mobile_no" class="form-control" label=" {{ __('Send Twilio SMS to this Number') }}"  placeholder="{{ __('Enter number') }}" value="{{ $user_contact_number }}" required></x-mobile>
@endif
</div>

{{-- Telegram Notification --}}
@if (in_array('Telegram', $notification))
    <h4>{{ __('Telegram') }}</h4>
    <div class="row col-md-12">
        <div class="form-group col-md-6 mt-2">
            {{ Form::label('', __('Telegram Access Token'), ['class' => 'form-label']) }}
            {{ Form::text('telegram_access', null, ['class' => 'form-control', 'placeholder' => __(''), 'required' => 'required']) }}
        </div>
        <div class="form-group col-md-6 mt-2">
            {{ Form::label('', __('Telegram ChatID'), ['class' => 'form-label']) }}
            {{ Form::text('telegram_chat', null, ['class' => 'form-control', 'placeholder' => __(''), 'required' => 'required']) }}
        </div>
    </div>
@endif

<div class="row col-md-12">

{{-- Whatsapp Notification --}}
    @if (in_array('Whatsapp', $notification))
        <x-mobile divClass="col-md-6 mt-1" name="whatsapp_mobile_no" class="form-control" label=" {{ __('Send Twilio Whatsapp Message to this Number') }}"  placeholder="{{ __('Enter number') }}" value="{{ $user_contact_number }}" required></x-mobile>
    @endif

{{-- WhatsappApi Notification --}}
    @if (in_array('WhatsAppAPI', $notification))
        <x-mobile divClass="col-md-6 mt-1" name="whatsappapi_mobile_no" class="form-control" label=" {{ __('Send WhatsappApi Message to this Number') }}"  placeholder="{{ __('Enter number') }}" value="{{ $user_contact_number }}" required></x-mobile>
    @endif
</div>

@if(!empty($notification))
<div class="form-group col-md-12 mt-2">
    <div class="form-group">
        {{ Form::label('message', __('Send Message'), ['class' => 'form-label']) }}
        <textarea class="form-control summernote {{ !empty($errors->first('course_description')) ? 'is-invalid' : '' }}" required name="course_description" id="exampleFormControlTextarea2" rows="3" ></textarea>
    </div>
</div>
@endif

<link href="{{  asset('assets/js/plugins/summernote-0.8.18-dist/summernote-lite.min.css')  }}" rel="stylesheet">
<script src="{{ asset('assets/js/plugins/summernote-0.8.18-dist/summernote-lite.min.js') }}"></script>
<script src="{{ asset('js/custom.js') }}"></script>
