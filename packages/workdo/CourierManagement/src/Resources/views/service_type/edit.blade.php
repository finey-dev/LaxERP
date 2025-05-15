<style>
    .pac-container {
        z-index: 9999 !important;
    }
</style>
{{ Form::open(['route' => ['courier.servicetype.update', ['servicetypeId' => $servicetypeData['id']]], 'method' => 'post','class'=>'needs-validation','novalidate']) }}
<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('name', __('Service Type'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::text('service_type', $servicetypeData['service_type'], ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Service Type')]) }}
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
    {{ Form::submit(__('Update'), ['class' => 'btn  btn-primary']) }}
</div>
{{ Form::close() }}

