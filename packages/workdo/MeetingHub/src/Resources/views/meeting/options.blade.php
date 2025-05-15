@php        
$user_ids= explode(',', isset($meeting->user_id) ? $meeting->user_id : 0 );
@endphp
<div class="form-group col-md-12" id="users_selections">
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

<script>
    $(document).ready(function() {
        choices();
    });
</script>
