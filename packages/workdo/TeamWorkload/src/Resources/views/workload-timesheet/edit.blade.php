{{Form::model($timesheet, array('route' => array('workload-timesheet.update', $timesheet->id), 'method' => 'PUT', 'class'=>'needs-validation', 'novalidate')) }}

<div class="modal-body">
    <div class="row">

        @if(\Auth::user()->type == 'company')
        <div class="col-6">
            <div class="form-group">
                {{Form::label('user_id',__('User'),['class'=>'col-form-label']) }}<x-required></x-required>
                {!! Form::select('user_id', $user, null,array('class' => 'form-control user','placeholder' => 'Select User','required'=>'required')) !!}
            </div>
        </div>
        @endif

         <div @if(\Auth::user()->type == 'company')class="col-6" @else class="col-4" @endif>
            <div class="form-group">
                {{Form::label('date',__('Date'),['class'=>'col-form-label']) }}<x-required></x-required>
                {!!Form::date('date', null,array('class' => 'form-control date','placeholder' => 'Date','required'=>'required')) !!}
            </div>
        </div>
        <div class="col-4">
            <div class="form-group">
                {{ Form::label('hours',__('Hours'),['class'=>'col-form-label']) }}<x-required></x-required>
                {{ Form::select('hours',[$hours], null, array('class' => 'form-control hours','required'=>'required')) }}
            </div>
        </div>
        <div class="col-4">
            <div class="form-group">
                {{ Form::label('minutes',__('Minutes'),['class'=>'col-form-label']) }}<x-required></x-required>
                {{ Form::select('minutes',[$minutes],null, array('class' => 'form-control minutes','required'=>'required')) }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('notes', __('Notes'),['class'=>'col-form-label']) }}
                {{ Form::textarea('notes', null, array('class' => 'form-control')) }}
            </div>
        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{__('Close')}}</button>
    <button type="submit" class="btn  btn-primary">{{__('Update')}}</button>

</div>
{{ Form::close() }}
<script>


    $(document).on('change', 'select[name=user_id]', function() {

        var user_id = $(this).val();
        var date = $('#date').val();

        $.ajax({
            url: '{{ route('workload-timesheet.totalhours') }}',
            type: 'GET',
            headers: {
                'X-CSRF-TOKEN': jQuery('#token').val()
            },
            data: {
                'user_id': user_id,
                'date' : date,
            },
            cache: false,
            success: function(data) {
                    $('#hours').empty();
                    for(var i = 0; i <= 12; i++){
                        $('#hours').append('<option>'+i+'</option>');
                    }
                    $('#minutes').empty();
                    for(var j = 0; j < 60; j+=15){
                        $('#minutes').append('<option>'+j+'</option>');
                    }
            }
        });
    });

    </script>
