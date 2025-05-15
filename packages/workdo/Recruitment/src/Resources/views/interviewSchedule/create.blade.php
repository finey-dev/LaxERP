{{ Form::open(['url' => 'interview-schedule', 'method' => 'post', 'class' => 'needs-validation', 'novalidate']) }}
<div class="modal-body">
    <div class="text-end">
        @if (module_is_active('AIAssistant'))
            @include('aiassistant::ai.generate_ai_btn', [
                'template_module' => 'interview-schedule',
                'module' => 'Recruitment',
            ])
        @endif
    </div>
    <div class="row">
        <div class="form-group col-md-6">
            {{ Form::label('candidate', __('Job candidate'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::select('candidate', $candidates, null, ['class' => 'form-control ', 'required' => 'required']) }}
        </div>
        @if (module_is_active('Hrm'))
            <div class="form-group col-md-6">
                {{ Form::label('employee', __('Assign Employee'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::select('employee', $employees, null, ['class' => 'form-control ', 'required' => 'required']) }}
            </div>
        @endif
        <div class="form-group col-md-6">
            {{ Form::label('date', __('Interview Date'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::date('date', null, ['class' => 'form-control ', 'autocomplete' => 'off', 'required' => 'required', 'min' => date('Y-m-d')]) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('time', __('Interview Time'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::time('time', null, ['class' => 'form-control ', 'id' => 'clock_in', 'required' => 'required']) }}
        </div>
        <div class="d-flex gap-2 radio-check">
            @foreach ($meetings as $key => $meeting)
                @php
                    $alias_name = Module_Alias_Name($meeting);
                @endphp
                <div class="form-check form-check-inline form-group">
                    <input type="radio" id="{{ $key }}" name="meeting_type" value="{{ $alias_name }}"
                        class="form-check-input  pointer">
                    <label class="form check-label" for="{{ $key }}">{{ $alias_name }}</label>
                </div>
            @endforeach
        </div>
        @stack('meeting_time')
        <div class="form-group ">
            {{ Form::label('comment', __('Comment'), ['class' => 'form-label']) }}
            {{ Form::textarea('comment', null, ['class' => 'form-control', 'rows' => 3, 'placeholder' => __('Enter Comment')]) }}
        </div>
        @stack('calendar')

    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
    <input type="submit" value="{{ __('Create') }}" class="btn  btn-primary">
</div>
{{ Form::close() }}

@if ($candidate != 0)
    <script>
        $('select#candidate').val({{ $candidate }}).trigger('change');
    </script>
@endif

<script>
    $(document).ready(function() {
        function handleRadioChange() {
            if ($('input[name="meeting_type"]:checked').val() === 'ZoomMeeting') {
                $('.zoom_start_time').removeClass('d-none');
                $('.zoom_end_time').removeClass('d-none');
                $('.google_start_time').addClass('d-none');
                $('.google_end_time').addClass('d-none');
                $('.zoom_start_time').prop('required', true);
                $('.zoom_end_time').prop('required', true);
                $('.google_start_time').prop('required', false);
                $('.google_end_time').prop('required', false);
            } else if ($('input[name="meeting_type"]:checked').val() === 'Google Meet') {
                $('.google_start_time').removeClass('d-none');
                $('.google_end_time').removeClass('d-none');
                $('.zoom_start_time').addClass('d-none');
                $('.zoom_end_time').addClass('d-none');
                $('.google_start_time').prop('required', true);
                $('.google_end_time').prop('required', true);
                $('.zoom_start_time').prop('required', false);
                $('.zoom_end_time').prop('required', false);
            } else {
                $('.zoom_start_time').addClass('d-none');
                $('.zoom_end_time').addClass('d-none');
                $('.google_start_time').addClass('d-none');
                $('.google_end_time').addClass('d-none');
                $('.google_start_time').prop('required', false);
                $('.google_end_time').prop('required', false);
                $('.zoom_start_time').prop('required', false);
                $('.zoom_end_time').prop('required', false);
            }
        }

        $('input[name="meeting_type"]').change(handleRadioChange);

        // Trigger change event on page load for the initially checked radio button
        handleRadioChange();
    });
</script>
