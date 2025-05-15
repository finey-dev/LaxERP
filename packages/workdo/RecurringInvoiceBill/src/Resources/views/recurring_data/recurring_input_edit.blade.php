@php
    if(!empty($recurring_invoice->cycles) && $recurring_invoice->cycles == "9999"){
        $cycyles = null;
    }else {
        $cycyles = !empty($recurring_invoice->cycles) ? $recurring_invoice->cycles : '' ;
    }
    if(!empty($recurring_invoice->recurring_type) && $recurring_invoice->recurring_type == "invoice"){
        $Recurring = __('Recurring Invoice?');
    }else {
        $Recurring = __('Recurring Bill?');
    }
@endphp
<div class="col-md-12 form-group">
    {{ Form::label('recurring_duration', __($Recurring), ['class' => 'form-label']) }}
    {{ Form::select('recurring_duration', $recuuring_type, $recurring_invoice->recurring_duration ?? 'no', ['class' => 'form-control recurring_duration', 'required' => 'required']) }}
</div>
<div class="col-md-6 form-group count " style="display: none;">
    {{ Form::number('count',$recurring_invoice->count ?? '0' , ['class' => 'form-control count-number']) }}
</div>
<div class="col-md-6 form-group day_type" style="display: none;">
    {{ Form::select('day_type', $day_type, $recurring_invoice->day_type ?? '' , ['class' => 'form-control day_type']) }}
</div>

<div class="form-group recurring-cycles" style="display: none;">
    <div class="input-group mb-3">
        {!! Form::number('cycles', $cycyles ?? '0', ['class' => 'form-control cycles', 'id' => 'cycles']) !!}
        @if(!empty($recurring_invoice->cycles))
          <input type="hidden" class="cycle-data" value ={{$recurring_invoice->cycles}}>
        @endif
        <div class="input-group-text gap-2">
            {!! Form::checkbox('unlimited_cycles', '1', false, [
                'id' => 'unlimited_cycles',
                'aria-invalid' => 'false',
                'class' => 'form-check-input',
            ]) !!}
            {!! Form::label('unlimited_cycles', 'Infinity', ['class' => 'ml-6']) !!}
        </div>
    </div>
    <p class="text-danger" id="cycles_validation"></p>
</div>


<script>
    $(document).ready(function(){

        $('.cycles').on('keyup',function(){
            var cycles_data = $(this).val();
            if(cycles_data == -1){
           $('#cycles_validation').text('The value must be greater than or equal to 0');
        }
        });
            var cycles_data = $('.cycle-data').val();
            if(cycles_data == '9999'){
                $('.cycles').prop('disabled', true);
                $('#unlimited_cycles').prop('checked', true);
            }

        var value = $('.recurring_duration').val();
        if(value == 'custom'){
            $('.count').show();
            $('.day_type').show();
            $('.recurring-cycles').show();
            $('.count-number').prop('required',true);
        }else if(value == 'no'){
            $('.recurring-cycles').hide();
            $('.count').hide();
            $('.day_type').hide();
            $('.count-number').prop('required',false);

        }
        else{
            $('.count').hide();
            $('.recurring-cycles').show();
            $('.day_type').hide();
            $('.count-number').prop('required',false);

        }
        $('.recurring_duration').on('click',function(){
            var recurring_duration = $(this).val();
            if(recurring_duration == 'no'){
                $('.recurring-cycles').hide();
                $('.count').hide();
                $('.day_type').hide();
                $('.count-number').prop('required',false);

            }else if(recurring_duration == 'custom')
            {
                $('.count').show();
                $('.day_type').show();
                $('.recurring-cycles').show();
                $('.count-number').prop('required',true);

            }
            else{
                $('.count').hide();
                $('.recurring-cycles').show();
                $('.day_type').hide();
                $('.count-number').prop('required',false);

             }
        });
        $('#unlimited_cycles').on('click',function(){
            if($(this).is(':checked')) {
                $('.cycles').prop('disabled', true);
            }else{
                $('.cycles').prop('disabled', false);

            }
        });
    });
</script>
