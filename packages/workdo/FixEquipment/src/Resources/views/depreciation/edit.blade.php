{{ Form::open(['route' => ['fix.equipment.depreciation.update', $depreciation->id], 'class'=>'needs-validation','novalidate','method' => 'post']) }}
<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('Depreciation Title', __('Depreciation Title'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::text('depreciation_title', $depreciation->title, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Depreciation name')]) }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('Depreciation Rate', __('Depreciation Rate'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::number('depteciation_rate', $depreciation->rate, ['class' => 'form-control', 'placeholder' => __('Enter Depteciation Rate')]) }}
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
    {{ Form::submit(__('Update'), ['class' => 'btn  btn-primary']) }}
</div>
{{ Form::close() }}
