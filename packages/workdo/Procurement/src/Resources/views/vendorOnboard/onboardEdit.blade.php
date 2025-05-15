{{ Form::model($vendorOnBoard, ['route' => ['vendor.on.board.update', $vendorOnBoard->id], 'method' => 'post','class'=>'needs-validation','novalidate']) }}
<div class="modal-body">
    <div class="row">
        <div class="form-group col-md-12">
            {!! Form::label('joining_date', __('Joining Date'), ['class' => 'form-label']) !!}<x-required></x-required>
            {!! Form::date('joining_date', null, ['class' => 'form-control ','autocomplete'=>'off', 'required' => 'required']) !!}
        </div>

        <div class="form-group col-md-6">
            {!! Form::label('days_of_week', __('Days Of Week'), ['class' => 'form-label']) !!}
            {!! Form::text('days_of_week', null, ['class' => 'form-control ','autocomplete'=>'off','placeholder'=>'Enter Days Of Week']) !!}
        </div>
        <div class="form-group col-md-6">
            {!! Form::label('budget', __('Budget'), ['class' => 'form-label']) !!}
            {!! Form::number('budget', null, ['class' => 'form-control ','autocomplete'=>'off','min'=>'0','placeholder'=>'Enter Budget']) !!}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('budget_type', __('Budget Type'), ['class' => 'form-label']) }}
            {{ Form::select('budget_type', $budget_type, null, ['class' => 'form-control select']) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('budget_duration', __('Budget Duration'), ['class' => 'form-label']) }}
            {{ Form::select('budget_duration', $budget_duration, null, ['class' => 'form-control select']) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('rfx_type', __('RFx Type'), ['class' => 'form-label']) }}
            {{ Form::select('rfx_type', $rfx_type, null, ['class' => 'form-control select']) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('status', __('Status'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::select('status', $status, null, ['class' => 'form-control select', 'required' => 'required']) }}
        </div>
    </div>
</div>

<div class="modal-footer">
    <input type="button" value="{{ __('Cancel') }}" class="btn  btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Update') }}" class="btn btn-primary">
</div>

{{ Form::close() }}
