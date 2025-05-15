{{ Form::model($meeting, ['route' => ['meetinghub.meeting.minute.update', $meeting->id], 'method' => 'PUT','class'=>'needs-validation','novalidate']) }}
<div class="modal-body">
    <div class="row">
        <div class="form-group col-md-12">
            @foreach ($meeting_users as $user)
                <label class="px-1">
                    <input type="radio" name="contact_user" class="form-check-input checkusers mr-2"
                        value="{{ $user->id }}">
                        {{ $user->name }}

                </label>
            @endforeach
        </div>
    </div>
    <div class="row">
        <div class="form-group col-md-12">
            {!! Form::label('log_type', __('Log Type'), ['class' => 'form-label']) !!}
            <div class="d-flex radio-check">
                <div class="custom-control custom-radio custom-control-inline">
                    <input type="radio" id="enable_call" value="Call" name="log_type" class="form-check-input"
                        checked="checked" onclick="getSelectedLogType()">
                    <label class="form-check-label " for="log_call">{{ __('Call') }}</label>
                </div>
                @if (module_is_active('Twilio'))
                    <div class="custom-control custom-radio ms-1 custom-control-inline">
                        <input type="radio" id="enable_sms" value="SMS" name="log_type" class="form-check-input"
                            onclick="getSelectedLogType()">
                        <label class="form-check-label " for="log_sms">{{ __('SMS') }}</label>
                    </div>
                @endif
            </div>
        </div>

        <div class="form-group col-md-6">
            <label for="datetime" class="form-label">{{ __('Start Time') }}</label><x-required></x-required>
            <input class="form-control datetimepicker" value="{{ date('Y-m-d h:i') }}"
                placeholder="{{ __('Select Date/Time') }}" required="required" name="call_start_time"
                type="datetime-local" id="call_start_time">
        </div>
        <div class="form-group col-md-6">
            <label for="datetime" class="form-label">{{ __('End Time') }}</label><x-required></x-required>
            <input class="form-control datetimepicker" value="{{ date('Y-m-d h:i') }}"
                placeholder="{{ __('Select Date/Time') }}" required="required" name="call_end_time"
                type="datetime-local" id="call_end_time">
        </div>

        <div class="form-group col-md-6">
            {{ Form::label('assign_user', __('User'), ['class' => 'form-label']) }}<x-required></x-required>
            <select class="form-select form-control" id="assign_user" name="assign_user"
                data-placeholder="{{ __('assign_user') }}" required>
                <option value="">{{ __('Select') }}</option>
                @foreach ($users as $key => $user)
                    <option value="{{ $key }}">{{ $user }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-md-6">
            <label for="duration" class="form-label">{{ __('Duration') }}</label>
            <input type="text" id="duration" name="duration" class="form-control" readonly="readonly" disabled>
        </div>
        <div id="log_call_content">
            <div class="col-md-9">
                <div class="row">
                    <x-mobile divClass="col-8" class="form-control getnumber" name="phone_call" id="phone_no_call" label="{{__('Phone')}}" placeholder="{{__('Enter phone Number')}}" required></x-mobile>
                    <div class="col-4">
                        <a href="tel:" title="{{ __('Calling') }}" target="_blank"
                            class="btn btn-sm btn-primary call-support-button" id="call-support-link" style="margin-top: 28px;">
                            <span><i class="ti ti-phone-call" style="padding-right: 3px;"></i></span>{{__('Call')}}
                        </a>
                    </div>
                </div>
            </div>

        </div>
        <div id="log_sms_content" style="display: none">
            <x-mobile divClass="form-group col-md-12" class="form-control getnumber" name="phone_sms" id="phone_no_sms" label="{{__('Phone')}}" placeholder="{{__('Enter phone Number')}}"></x-mobile>
            <div class="form-group col-md-12">
                {{ Form::label('message', __('Write Your SMS Here'), ['class' => 'form-label']) }}
                {{ Form::textarea('message', null, ['class' => 'form-control', 'rows' => 3, 'id' => 'message', 'placeholder' => __('Enter Your SMS')]) }}

                <div class="row" style="padding-top: 13px;">
                    <div class="col-lg-12">
                        <div class="text-right text-end float-end p-2">
                            <button type="button" class="btn btn-sm btn-primary" id="sms-send-btn"><span><i
                                        class="fa fa-envelope" style="padding-right: 3px;"></i></span>{{__('Send')}}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('status', __('Status'), ['class' => 'form-label']) }}
            {{ Form::select('status', $status, isset($_GET['status']) ? $_GET['status'] : '', ['class' => 'form-control']) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('priority', __('Priority'), ['class' => 'form-label']) }}<x-required></x-required>
            <select name="priority" id="priority" class="form-control" required>
                <option value="">{{ __('Select Priority') }}</option>
                <option value="High">{{ __('High') }}</option>
                <option value="Medium">{{ __('Medium') }}</option>
                <option value="Low">{{ __('Low') }}</option>
            </select>
        </div>
        <div class="form-group col-md-12">
            {{ Form::label('note', __('Note'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::textarea('note', null, ['class' => 'form-control', 'rows' => 3, 'id' => 'note', 'required' => 'required', 'placeholder' => __('Note...')]) }}
        </div>
        <div class="form-group col-md-6">
            <div class="form-group">
                {!! Form::label('completed', __('Completed'), ['class' => 'form-label']) !!}
                <div class="d-flex radio-check">
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" id="completed_yes" value="Yes" name="completed"
                            class="form-check-input" checked="checked">
                        <label class="form-check-label " for="yes">{{ __('Yes') }}</label>
                    </div>
                    <div class="custom-control custom-radio ms-1 custom-control-inline">
                        <input type="radio" id="completed_no" value="No" name="completed"
                            class="form-check-input">
                        <label class="form-check-label " for="no">{{ __('No') }}</label>
                    </div>
                </div>
            </div>
        </div>
        <div class=" form-group col-md-6">
            <div class="form-group">
                {!! Form::label('important', __('Important'), ['class' => 'form-label']) !!}
                <div class="d-flex radio-check">
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" id="important_yes" value="Yes" name="important"
                            class="form-check-input" checked="checked">
                        <label class="form-check-label " for="yes">{{ __('Yes') }}</label>
                    </div>
                    <div class="custom-control custom-radio ms-1 custom-control-inline">
                        <input type="radio" id="important_no" value="No" name="important"
                            class="form-check-input">
                        <label class="form-check-label " for="no">{{ __('No') }}</label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <div class="text-end">
        <input type="button" value="{{ __('Cancel') }}" class="btn  btn-light me-1" data-bs-dismiss="modal">
        <input type="submit" value="{{ __('Create') }}" class="btn  btn-primary">
    </div>
</div>
{{ Form::close() }}
<script>
    function getSelectedLogType() {
        var checked = $("input[type=radio][name='log_type']:checked");
        var id = $(checked).attr("id");
        if (id == 'enable_call') {
            $('#log_call_content').show();
            $('#log_sms_content').hide();
            $('#phone_no_sms').removeAttr('required');
            $('#phone_no_sms').removeAttr('pattern');
            $('#phone_no_call').attr('required','required');
            $('#phone_no_call').attr('pattern', '^\\+\\d{1,3}\\d{9,13}$');
        } else if (id == 'enable_sms') {
            $('#log_sms_content').show();
            $('#log_call_content').hide();
            $('#phone_no_sms').attr('required','required');
            $('#phone_no_sms').attr('pattern', '^\\+\\d{1,3}\\d{9,13}$');
            $('#phone_no_call').removeAttr('required');
            $('#phone_no_call').removeAttr('pattern');
        }
    }
</script>
<script>
    $(document).ready(function() {
        $('#phone_no_sms').removeAttr('required');
        $('#phone_no_sms').removeAttr('pattern');
    });
</script>
<script>
    $(document).on('change', '.checkusers', function() {
        var userId = $(this).val();
        $.ajax({
            url: '{{ route('meetinghub.meeting.minute.getNumber') }}',
            type: 'POST',
            data: {
                "user_id": userId,
                "_token": "{{ csrf_token() }}",
            },
            success: function(data) {
                $('.getnumber').val(data.mobile_no);

                // Update the phone_no field based on the log type
                if ($('#enable_call').is(':checked')) {
                    $('#phone_no_call').val(data.mobile_no);
                } else if ($('#enable_sms').is(':checked')) {
                    $('#phone_no_sms').val(data.mobile_no);
                }
            }
        });
    });
</script>
<script>
    $(document).ready(function() {
        $("#sms-send-btn").click(function() {

            if ($('#enable_call').is(':checked')) {
                var phoneNumber = $("#phone_no_call").val();
            } else if ($('#enable_sms').is(':checked')) {
                var phoneNumber = $("#phone_no_sms").val();
            }
            var message = $("#message").val();
            if (phoneNumber && message) {
                $.ajax({
                    url: '{{ route('meetinghub.sendsms') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        phone: phoneNumber,
                        message: message,
                    },
                    cache: false,
                    success: function(data) {
                        if (data.error) {
                            toastrs('Error', data.error, 'error');
                        } else {
                            toastrs('Success', data.msg, 'success');
                        }
                    },
                    error: function(data) {
                        toastrs('Error',
                            '{{ __('something went wrong please try again') }}',
                            'error');
                    },
                });
            } else {
                toastrs('Error', '{{ __('Please enter a phone number and message.') }}', 'error');
            }
        });
    });
</script>
<script>
    $(document).ready(function() {
        var callSupportLink = $('#call-support-link');

        function updateCallSupportLink(selectedValue) {
            callSupportLink.attr('href', 'tel:' + selectedValue);
        }

        $('.checkusers').change(function() {
            var selectedValue = $('input[name="contact_user"]:checked').val();
            updateCallSupportLink(selectedValue);
        });

        $('#phone_no_call').change(function() {
            var selectedValue = $(this).val();
            updateCallSupportLink(selectedValue);
        });


    });
</script>
<script>
    $(document).ready(function() {
        $('#call_end_time').on('change', function() {
            var startTime = $('#call_start_time').val();
            var endTime = $('#call_end_time').val();

            $.ajax({
                url: '{{ route('meetinghub.calculateduration') }}',
                type: 'POST',
                data: {
                    start_time: startTime,
                    end_time: endTime,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    $('#duration').val(response);
                },
            });
        });
    });
</script>
