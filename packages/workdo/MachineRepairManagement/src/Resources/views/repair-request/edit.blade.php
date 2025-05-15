{{ Form::model($repair_request, ['route' => ['machine-repair-request.update', $repair_request->id], 'id' => 'repair_request', 'method' => 'PUT','class'=>'needs-validation','novalidate']) }}
<div class="modal-body">
    <div class="row">
        <div class="form-group col-md-6">
            {{ Form::label('machine_id', __('Machine'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::select('machine_id', $machines, null, ['class' => 'form-control', 'required' => 'required']) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('customer_name', __('Customer Name'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::text('customer_name', null, ['class' => 'form-control', 'required' => 'required','placeholder'=>__('Enter Customer Name')]) }}
        </div>
        <div class="form-group col-md-6">
            {{Form::label('customer_email',__('Customer Email'),['class'=>'form-label'])}}<x-required></x-required>
            {{Form::email('customer_email',null,array('class'=>'form-control','placeholder'=>__('Enter Customer Email'),'required'=>'required'))}}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('priority_level', __('Priority Level'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::select('priority_level', $priority_level, null, ['class' => 'form-control', 'required' => 'required']) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('status', __('Status'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::select('status', $status, null, ['class' => 'form-control', 'required' => 'required']) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('staff_id', __('Staff'), ['class' => 'form-label']) }}
            {{ Form::select('staff_id', $staffs, null, ['class' => 'form-control']) }}
        </div>
        <div class="form-group col-md-12">
            {!! Form::label('', __('Description of Issue'), ['class' => 'form-label']) !!}<x-required></x-required>
            {!! Form::textarea('description_of_issue', null, [
                'class' => 'form-control',
                'placeholder' => 'Enter Description of Issue',
                'rows' => '3',
                'cols' => '50',
                'id' => 'machine-desc-issue',
                'required' => true,
            ]) !!}
        </div>
        <div class="modal-footer pb-0">
            <input type="button" value="Cancel" class="btn btn-light" data-bs-dismiss="modal">
            <input type="submit" value="Update" class="btn btn-primary bg-primary" id="submit-all">
        </div>
    </div>
</div>
{!! Form::close() !!}
