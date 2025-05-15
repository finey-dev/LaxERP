{{ Form::open(['route' => ['contracts.renewcontract.store',$contract->id], 'method' => 'post','class'=>'needs-validation','novalidate']) }}

<div class="modal-body">
    <div class="col-12">
        <div class="form-group">
            {{Form::label('start_date',__('Start Date'),['class'=>'form-label']) }}<x-required></x-required>
            {!!Form::date('start_date', $renewContract->start_date ?? $contract->start_date ,array('class' => 'form-control','placeholder' => __('Start Date'),'required'=>'required')) !!}

        </div>
    </div>
    <div class="col-12">
        <div class="form-group">
            {{Form::label('end_date',__('End Date'),['class'=>'form-label']) }}<x-required></x-required>
            {!!Form::date('end_date', $renewContract->end_date ?? $contract->end_date ,array('class' => 'form-control','required'=>'required','placeholder' => __('End Date'))) !!}

        </div>
    </div>
    <div class="col-md-12 form-group">
        {{ Form::label('value', __('Value'),['class'=>'form-label']) }}<x-required></x-required>
        {{ Form::number('value',  $renewContract->value ?? $contract->value , array('class' => 'form-control','required'=>'required','min' => '1','placeholder'=>__('Enter Amount'))) }}
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
    {{ Form::submit(__('Create'), ['class' => 'btn btn-primary']) }}
</div>
{{ Form::close() }}
