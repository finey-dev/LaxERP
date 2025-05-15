{{ Form::model($formBuilder, array('route' => array('form_builder.update', $formBuilder->id), 'method' => 'PUT','class'=>'needs-validation','novalidate')) }}
<div class="modal-body">
    <div class="row">
        <div class="col-12 form-group">
            {{ Form::label('name', __('Name'),['class'=>'form-label']) }} <x-required></x-required>
            {{ Form::text('name', null, array('class' => 'form-control','placeholder' => __('Enter Name') ,'required' => 'required')) }}
        </div>
        <div class="col-12 form-group">
            {{ Form::label('active', __('Active'),['class'=>'form-label']) }} <x-required></x-required>
            <div class="d-flex radio-check">
                <div class="custom-control custom-radio custom-control-inline">
                    <input type="radio" id="on" value="1" name="is_active" class="form-check-input pointer" {{($formBuilder->is_active == 1) ? 'checked' : ''}}>
                    <label class="custom-control-label form-check-labe" for="on">{{__('On')}}</label>
                </div>
                <div class="custom-control custom-radio custom-control-inline" style="margin-left: 10px;">
                    <input type="radio" id="off" value="0" name="is_active" class="form-check-input pointer" {{($formBuilder->is_active == 0) ? 'checked' : ''}}>
                    <label class="custom-control-label form-check-labe" for="off">{{__('Off')}}</label>
                </div>
            </div>
            <p class="text-danger d-none" id="is_active_validation">{{__('The active filed is required.')}}</p>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{__('Cancel')}}</button>
    <button type="submit" class="btn  btn-primary" id="submit">{{__('Update')}}</button>
</div>

{{ Form::close() }}

<script>
    $(function(){
        $("#submit").click(function() {
            var is_active = $("input[name='is_active']:checked").val();
            if (is_active && (is_active == 0 || is_active == 1)) {
                $('#is_active_validation').addClass('d-none');
            } else {
                $('#is_active_validation').removeClass('d-none');
                return false;
            }
        });
    });

</script>
