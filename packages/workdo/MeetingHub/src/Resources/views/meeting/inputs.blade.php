<input type="hidden" value="{{ isset($meeting->id) ? $meeting->id : '' }}" class="meeting_id" name="meeting_id">
@if ($modelName == 'Lead')

    <div class="col-md-12">
        {{ Form::label('leads', __('Lead'), ['class' => 'form-label']) }}<x-required></x-required>
        <select name="leads[]" class="form-control" id="leads" required>
            <option value="">{{ __('Select Lead') }}</option>
            @foreach ($leads as $key => $value)
                <option value="{{ $key }}"
                    @if (isset($meeting)) @if (!empty($meeting->lead_id) && $meeting->lead_id == $key) selected @endif @endif >{{ $value }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-12" id="usersListLead">

    </div>
@elseif ($modelName == 'Client' || $modelName == 'Vendor' || $modelName == 'Employee' || $modelName == 'Journalist')
    @php
        $user_ids = explode(',', isset($meeting->user_id) ? $meeting->user_id : 0);
    @endphp
    <div class="col-md-12" id="users_selections">
        {{ Form::label('users', __('Users'), ['class' => 'form-label']) }}<x-required></x-required>
        <select name="users[]" class="form-control choices" id="choices-multiple" required multiple="">
            <option value="">{{ __('Select User') }}</option>
            @foreach ($users as $key => $user)
                @php
                    $selected = isset($meeting->user_id) && in_array($key, $user_ids ?? []) ? 'selected' : '';
                @endphp
                <option value="{{ $key }}" {{ $selected }}>{{ $user }}</option>
            @endforeach
        </select>
    </div>
@elseif ($modelName == 'Teacher')
    @php
        $user_ids = explode(',', isset($meeting->user_id) ? $meeting->user_id : 0);
    @endphp
    <div class="col-md-12" id="users_selections">
        {{ Form::label('users', __('Users'), ['class' => 'form-label']) }}<x-required></x-required>
        <select name="users[]" class="form-control choices" id="choices-multiple" required multiple="">
            <option value="">{{ __('Select Teacher') }}</option>
            @foreach ($users as $key => $user)
                @php
                    $selected = isset($meeting->user_id) && in_array($key, $user_ids ?? []) ? 'selected' : '';
                @endphp
                <option value="{{ $key }}" {{ $selected }}>{{ $user }}</option>
            @endforeach
        </select>
    </div>
@elseif ($modelName == 'Parent')
    @php
        $user_ids = explode(',', isset($meeting->user_id) ? $meeting->user_id : 0);
    @endphp
    <div class="col-md-12" id="users_selections">
        {{ Form::label('users', __('Users'), ['class' => 'form-label']) }}<x-required></x-required>
        <select name="users[]" class="form-control choices" id="choices-multiple" required multiple="">
            <option value="">{{ __('Select Parents') }}</option>
            @foreach ($users as $key => $user)
                @php
                    $selected = isset($meeting->user_id) && in_array($key, $user_ids ?? []) ? 'selected' : '';
                @endphp
                <option value="{{ $key }}" {{ $selected }}>{{ $user }}</option>
            @endforeach
        </select>
    </div>
@elseif ($modelName == 'Advocate')
    @php
        $user_ids = explode(',', isset($meeting->user_id) ? $meeting->user_id : 0);
    @endphp
    <div class="col-md-12" id="users_selections">
        {{ Form::label('users', __('Users'), ['class' => 'form-label']) }}<x-required></x-required>
        <select name="users[]" class="form-control choices" id="choices-multiple" required multiple="">
            <option value="">{{ __('Select Advocate') }}</option>
            @foreach ($users as $key => $user)
                @php
                    $selected = isset($meeting->user_id) && in_array($key, $user_ids ?? []) ? 'selected' : '';
                @endphp
                <option value="{{ $key }}" {{ $selected }}>{{ $user }}</option>
            @endforeach
        </select>
    </div>
@elseif ($modelName == 'Agriculture User')
    @php
        $user_ids = explode(',', isset($meeting->user_id) ? $meeting->user_id : 0);
    @endphp
    <div class="col-md-12" id="users_selections">
        {{ Form::label('users', __('Users'), ['class' => 'form-label']) }}<x-required></x-required>
        <select name="users[]" class="form-control choices" id="choices-multiple" required multiple="">
            <option value="">{{ __('Select Farmer') }}</option>
            @foreach ($users as $key => $user)
                @php
                    $selected = isset($meeting->user_id) && in_array($key, $user_ids ?? []) ? 'selected' : '';
                @endphp
                <option value="{{ $key }}" {{ $selected }}>{{ $user }}</option>
            @endforeach
        </select>
    </div>
@elseif ($modelName == 'Agent')
    @php
        $user_ids = explode(',', isset($meeting->user_id) ? $meeting->user_id : 0);
    @endphp
    <div class="col-md-12" id="users_selections">
        {{ Form::label('users', __('Users'), ['class' => 'form-label']) }}<x-required></x-required>
        <select name="users[]" class="form-control choices" id="choices-multiple" required multiple="">
            <option value="">{{ __('Select Agent') }}</option>
            @foreach ($users as $key => $user)
                @php
                    $selected = isset($meeting->user_id) && in_array($key, $user_ids ?? []) ? 'selected' : '';
                @endphp
                <option value="{{ $key }}" {{ $selected }}>{{ $user }}</option>
            @endforeach
        </select>
    </div>
@elseif ($modelName == 'Tenants')
    @php
        $user_ids = explode(',', isset($meeting->user_id) ? $meeting->user_id : 0);
    @endphp
    <div class="col-md-12" id="users_selections">
        {{ Form::label('users', __('Users'), ['class' => 'form-label']) }}<x-required></x-required>
        <select name="users[]" class="form-control choices" id="choices-multiple" required multiple="">
            <option value="">{{ __('Select Tenants') }}</option>
            @foreach ($users as $key => $user)
                @php
                    $selected = isset($meeting->user_id) && in_array($key, $user_ids ?? []) ? 'selected' : '';
                @endphp
                <option value="{{ $key }}" {{ $selected }}>{{ $user }}</option>
            @endforeach
        </select>
    </div>
@elseif ($modelName == 'Doctor')
    @php
        $user_ids = explode(',', isset($meeting->user_id) ? $meeting->user_id : 0);
    @endphp
    <div class="col-md-12" id="users_selections">
        {{ Form::label('users', __('Users'), ['class' => 'form-label']) }}<x-required></x-required>
        <select name="users[]" class="form-control choices" id="choices-multiple" required multiple="">
            <option value="">{{ __('Select Doctor') }}</option>
            @foreach ($users as $key => $user)
                @php
                    $selected = isset($meeting->user_id) && in_array($key, $user_ids ?? []) ? 'selected' : '';
                @endphp
                <option value="{{ $key }}" {{ $selected }}>{{ $user }}</option>
            @endforeach
        </select>
    </div>
@endif

<script>
    $(document).ready(function() {
        choices();
        var selectedLead = $('#leads').val();
        var meetingId = $('.meeting_id').val();
        if (selectedLead) {
            fetchUsers(selectedLead, '#usersListLead', 'lead', meetingId);
        } else {
            $('#usersListLead').html('');
        }
    });
</script>
<script>
    function fetchUsers(selectedValue, targetId, dropdownType, meetingId = null) {
        $.ajax({
            url: '{{ route('meetinghub.updateUsersSelect') }}',
            method: 'POST',
            data: {
                "selectvalue": selectedValue,
                "meetingId": meetingId,
                "_token": "{{ csrf_token() }}",
                "dropdownType": dropdownType,
            },
            success: function(data) {
                $(targetId).html(data.html);
            },
            error: function() {
                console.error('Failed to fetch users.');
            }
        });
    }
    $('#leads').change(function() {
        var selectedLead = $(this).val();
        if (selectedLead) {
            fetchUsers(selectedLead, '#usersListLead', 'lead');
        } else {
            $('#usersListLead').html('');
        }
    });
</script>
