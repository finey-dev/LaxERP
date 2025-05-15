{{ Form::model($response_data, ['route' => ['request.bind.store', $form->id], 'method' => 'POST']) }}
{{ method_field('POST') }}
<input type="hidden" value="{{ $form->id }}" name="request_id">

<div class="modal-body">
    <div class="row">
        <div class="col-12 pb-3">
            <span class="form-check-label"><b>{{ __('It will auto convert from response to '. $form->module_type.' based on below setting. It will not convert old response.') }}</b></span>
        </div>
    </div>
    <div class="row">
        <div class="col-4">
            <div class="form-group">
                {{ Form::label('active', __('Active'), ['class' => 'col-form-label']) }}
            </div>
        </div>
        <div class="col-8 pt-1">
            <div class="d-flex radio-check">
                <div class="custom-control custom-radio custom-control-inline">
                    {{ Form::radio('is_converted', 1, $form->is_converted == 1, ['id' => 'on', 'class' => 'form-check-input lead_radio pointer']) }}
                    {{ Form::label('on', __('On'), ['class' => 'form-check-label']) }}
                </div>
                <div class="custom-control custom-radio custom-control-inline" style="margin-left: 10px;">
                    {{ Form::radio('is_converted', 0, $form->is_converted == 0, ['id' => 'off', 'class' => 'form-check-input lead_radio pointer']) }}
                    {{ Form::label('off', __('Off'), ['class' => 'form-check-label']) }}
                </div>
            </div>
        </div>
    </div>
    <div class="row px-2 select_module @if($form->is_converted == 0) d-none @endif">
        <div class="col-4">
            <div class="form-group">
                {{ Form::label('subject_id', __('Subject'), ['class' => 'col-form-label']) }}
            </div>
        </div>
        <div class="col-8">
            <div class="form-group">
                {{ Form::select('subject_id', $fields, $response_data->subject_id ?? null, ['class' => 'form-control', 'required' => 'required']) }}
            </div>
        </div>
        <div class="col-4">
            <div class="form-group">
                {{ Form::label('name_id', __('Name'), ['class' => 'col-form-label']) }}
            </div>
        </div>
        <div class="col-8">
            <div class="form-group">
                {{ Form::select('name_id', $fields, $response_data->name_id ?? null, ['class' => 'form-control', 'required' => 'required']) }}
            </div>
        </div>
        <div class="col-4">
            <div class="form-group">
                {{ Form::label('email_id', __('Email'), ['class' => 'col-form-label']) }}
            </div>
        </div>
        <div class="col-8">
            <div class="form-group">
                {{ Form::select('email_id', $fields,$response_data->email_id ?? null, ['class' => 'form-control', 'required' => 'required']) }}
            </div>
        </div>
        <div class="col-4">
            <div class="form-group">
                {{ Form::label('user_id', __('User'), ['class' => 'col-form-label']) }}
            </div>
        </div>
        <div class="col-8">
            <div class="form-group">
                {{ Form::select('user_id', $users,$response_data->user_id ?? null, ['class' => 'form-control', 'required' => 'required']) }}
                @if(count($users) == 0)
                    <div class="text-muted text-xs">
                        {{ __('Please create new users') }} <a href="{{ route('users.index') }}">{{ __('here.') }}</a>
                    </div>
                @endif
            </div>
        </div>
        <div class="col-4">
            <div class="form-group">
                {{ Form::label('title_id', __('Title'), ['class' => 'col-form-label']) }}
            </div>
        </div>
        <div class="col-8">
            <div class="form-group">
                {{ Form::select('title_id', $fields, $response_data->title_id ?? null, ['class' => 'form-control', 'required' => 'required']) }}
            </div>
        </div>
        <div class="col-4">
            <div class="form-group">
                {{ Form::label('description_id', __('Description'), ['class' => 'col-form-label']) }}
            </div>
        </div>
        <div class="col-8">
            <div class="form-group">
                {{ Form::select('description_id', $fields, $response_data->description_id ?? null, ['class' => 'form-control', 'required' => 'required']) }}
            </div>
        </div>
        <div class="col-4">
            <div class="form-group">
                {{ Form::label('pipeline_id', __('Pipeline'), ['class' => 'col-form-label']) }}
            </div>
        </div>
        <div class="col-8">
            <div class="form-group">
                {{ Form::select('pipeline_id', $pipelines,$response_data->pipeline_id ?? null, ['class' => 'form-control', 'required' => 'required']) }}
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
    <button type="submit" class="btn btn-primary">{{ __('Create') }}</button>
</div>
{{ Form::close() }}


<script>
    $(document).on('click', function () {
        $('.lead_radio').on('click', function () {
            var inputValue = $(this).attr("value");
            if (inputValue == 1) {
                $('.select_module').removeClass('d-none');
            } else {
                $('.select_module').addClass('d-none');
            }
            $('.lead_radio').removeAttr('checked');
            $(this).prop("checked", true);
        })
    });

</script>
