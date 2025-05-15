
{{ Form::model($contract, array('route' => array('contract.update', $contract->id), 'method' => 'PUT','enctype'=>'multipart/form-data','class'=>'needs-validation','novalidate')) }}
<div class="modal-body">
    <div class="text-end">
        @if (module_is_active('AIAssistant'))
            @include('aiassistant::ai.generate_ai_btn',['template_module' => 'contract','module'=>'Contract'])
        @endif
    </div>
    <div class="row">
        <div class="col-md-6 form-group">
            {{ Form::label('subject', __('Subject'),['class'=>'form-label']) }}<x-required></x-required>
            {{ Form::text('subject', null, array('class' => 'form-control','required'=>'required','placeholder'=>__('Enter Subject'))) }}
        </div>
        @if (\Auth::user()->type == 'company')
            <div class="col-md-6 form-group">
                {{ Form::label('user_id', __('User'),['class'=>'form-label']) }}<x-required></x-required>
                {{ Form::select('user_id',$user,null, array('class' => 'form-control','id'=>'user_id','placeholder' => __('Select User'),'required'=>'required')) }}
            </div>
        @else
            {{ Form::hidden('user_id',\Auth::user()->id,null) }}
        @endif
        @if(module_is_active('Taskly'))
            <div class="col-md-6 form-group">
                {{ Form::label('project_id', __('Project'),['class'=>'form-label']) }}
                {{ Form::select('project_id',$project,null, array('class' => 'form-control','id'=>'project_id','placeholder' => __('Select Project'))) }}
            </div>
        @endif
        <div class="col-md-6 form-group">
            {{ Form::label('value', __('Value'),['class'=>'form-label']) }}<x-required></x-required>
            {{ Form::number('value', $renewContract->value ?? $contract->value, array('class' => 'form-control','required'=>'required','min' => '1','placeholder'=>__('Enter Amount'))) }}
        </div>
        <div class="col-md-4 form-group">
            {{ Form::label('type', __('Type'),['class'=>'form-label']) }}<x-required></x-required>
            {{ Form::select('type', $contractType,null, array('class' => 'form-control','required'=>'required')) }}
        </div>
        <div class="col-md-4">
            <div class="form-group">
                {{Form::label('start_date',__('Start Date'),['class'=>'form-label']) }}<x-required></x-required>
                {!!Form::date('start_date', $renewContract->start_date ?? $contract->start_date ,array('class' => 'form-control','placeholder' => __('Start Date'),'required'=>'required')) !!}
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                {{Form::label('end_date',__('End Date'),['class'=>'form-label']) }}<x-required></x-required>
                {!!Form::date('end_date', $renewContract->end_date ?? $contract->end_date ,array('class' => 'form-control','required'=>'required','placeholder' => __('End Date'))) !!}
            </div>
        </div>
        <div class="col-md-12 form-group">
            {{ Form::label('notes', __('Description'),['class'=>'form-label']) }}
            {{ Form::textarea('notes', null, array('class' => 'form-control','placeholder'=>__('Enter Description'),'rows' => '3')) }}
        </div>
        @if(module_is_active('CustomField') && !$customFields->isEmpty())
            <div class="col-md-12">
                <div class="tab-pane fade show" id="tab-2" role="tabpanel">
                    @include('customfield::formBuilder',['fildedata' => $contract->customField])
                </div>
            </div>
        @endif
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{__('Cancel')}}</button>
    <button type="submit" class="btn  btn-primary">{{__('Update')}}</button>
</div>

{{ Form::close() }}

<script>
    @if(module_is_active('Taskly'))
    $(document).on('change', 'select[name=user_id]', function() {
        var user_id = $(this).val();
    getproject(user_id);
    });

    function getproject(did) {
    $.ajax({
        url: '{{ route('getproject') }}',
        type: 'POST',
        data: {
            "user_id": did,
            "_token": "{{ csrf_token() }}",
        },
        success: function(data) {
            $('#project_id').empty();
            $('#project_id').append(
                '<option value="">{{ __('Select Project') }}</option>');
            $.each(data, function(key, value) {
                $('#project_id').append('<option value="' + key + '">' + value +
                    '</option>');
            });
        }
    });
    }
    @endif
</script>
