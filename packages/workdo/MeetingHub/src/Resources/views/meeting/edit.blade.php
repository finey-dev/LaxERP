{{ Form::model($meeting, ['route' => ['meetings.update', $meeting->id], 'method' => 'PUT','class'=>'needs-validation','novalidate']) }}

<div class="modal-body">
    <div class="row">
        <input type="hidden" value="{{$meeting->id}}" id="meeting_id"  name="meeting_id">
        <div class="form-group col-md-12">
            {{ Form::label('module', __('Module'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::select('module', $module_calllogfiled, $meeting->sub_module, ['class' => 'form-control selectmodule', 'required' => 'required']) }}
        </div>
        <div class="form-group col-md-12" id="getfields">
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('meeting_type', __('Meeting Type'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::select('meeting_type', $meetingtypes, null, ['class' => 'form-control ', 'placeholder' => __('Select Meeting Type'), 'required' => 'required']) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('location', __('Location'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::text('location', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Meeting Location')]) }}
        </div>
        <div class="form-group col-md-12">
            {{ Form::label('subject', __('Subject'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::text('subject', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Meeting Subject')]) }}
        </div>
        <div class="form-group col-md-12">
            {{ Form::label('description', __('Description'), ['class' => 'form-label']) }}
            {{ Form::textarea('description', null, ['class' => 'form-control', 'rows' => 3, 'id' => 'description', 'placeholder' => __('Enter Meeting Description')]) }}
        </div>
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{ __('Cancel') }}" class="btn  btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Update') }}" class="btn  btn-primary">
</div>
{{ Form::close() }}
<script>
    $(document).ready(function() {
        choices();
        var submoduleId = $('.selectmodule').val();
        var id=$('#meeting_id').val();
        $.ajax({
            url: '{{ route('meetinghub.getcondition') }}',
            type: 'POST',
            data: {
                "submodule_id": submoduleId,
                "id": id,
                "_token": "{{ csrf_token() }}",
            },
            success: function(data) {
                $('#getfields').empty();
                $('#getfields').html(data.html);
            }
        });
    });
</script>
<script>
     $(document).on('change', '.selectmodule', function() {
        var submoduleId = $(this).val();
        $.ajax({
            url: '{{ route('meetinghub.getcondition') }}',
            type: 'POST',
            data: {
                "submodule_id": submoduleId,
                "_token": "{{ csrf_token() }}",
            },
            success: function(data) {
                $('#getfields').empty();
                $('#getfields').html(data.html);
            }
        });
    });
</script>
