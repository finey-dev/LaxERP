{{ Form::model($formField, array('route' => array('form.bind.store', $form->id),'class'=>'needs-validation','novalidate')) }}
<div class="modal-body">
    <div class="row">
        <div class="col-12 pb-3">
            <span class="text-xs"><b>{{__('It will auto convert from response on selected module based on below setting. It will not convert old response.')}}</b></span>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('active', __('Active'),['class'=>'form-label']) }} <x-required></x-required>
                <div class="d-flex radio-check">
                    <div class="custom-control custom-radio custom-control-inline ">
                        <input type="radio" id="on" value="1" name="is_lead_active" class="form-check-input lead_radio pointer" {{($form->is_lead_active == 1) ? 'checked' : ''}}>
                        <label class="form-check-labe" for="on">{{__('On')}}</label>
                    </div>
                    <div class="custom-control custom-radio custom-control-inline" style="margin-left: 10px;">
                        <input type="radio" id="off" value="0" name="is_lead_active" class="form-check-input lead_radio pointer" {{($form->is_lead_active == 0) ? 'checked' : ''}}>
                        <label class="form-check-labe" for="off">{{__('Off')}}</label>
                    </div>
                </div>
                <p class="text-danger d-none" id="is_lead_active_validation">{{__('The active filed is required.')}}</p>
            </div>
        </div>
        <div class="form-group select_module d-none">
            {{ Form::label('module', __('Module'), ['class' => 'form-label']) }} <x-required></x-required>
            {{ Form::select('module', $formBuilderModule, null, ['class' => 'form-control text-capitalize module', 'required' => 'required']) }}
        </div>
    </div>
    <div id="relatedfields">

    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{__('Cancel')}}</button>
    <button type="submit" class="btn  btn-primary" id="submit">{{__('Convert')}}</button>
</div>
{{ Form::close() }}


<script>
    $(function(){
        $("#submit").click(function() {
            var is_lead_active = $("input[name='is_lead_active']:checked").val();
            if (is_lead_active && (is_lead_active == 0 || is_lead_active == 1)) {
                $('#is_lead_active_validation').addClass('d-none');
            } else {
                $('#is_lead_active_validation').removeClass('d-none');
                return false;
            }
        });
    });
    $(document).ready(function () {
        var lead_active = {{$form->is_lead_active}};
        if (lead_active == 1) {
            $('.module').trigger("change");
            $('.select_module').removeClass('d-none');
        }
    });
    $('.lead_radio').on('click', function () {
        var inputValue = $(this).attr("value");
        if (inputValue == 1) {
            $('.module').trigger("change");
            $('.select_module').removeClass('d-none');
        } else {
            $('.select_module').addClass('d-none');
            $('#relatedfields').html('');
        }
        $('.lead_radio').removeAttr('checked');
        $(this).prop("checked", true);
    })
    $(document).on("change", ".module", function() {
        var form_id = {{$form->id}};
        var module_id = $(this).val();
        if (module_id != 0) {
            $.ajax({
                url: '{{ route('form.builder.modules') }}',
                type: 'POST',
                data: {
                    module: module_id,
                    form_id: form_id
                },
                beforeSend: function () {
                    $(".loader-wrapper").removeClass('d-none');
                },
                success: function(data) {
                    $('#relatedfields').html(data.html);
                    $(".loader-wrapper").addClass('d-none');
                    choices();
                }
            });
        } else {
            $('#relatedfields').html('')
        }
    });
</script>
