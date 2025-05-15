{{ Form::model($service_agreements, ['route' => ['service-agreements.update', $service_agreements->id], 'method' => 'PUT', 'class' => 'needs-validation', 'novalidate']) }}

<div class="modal-body">
    <div class="row">
        <div class="form-group col-md-12">
            {{ Form::label('customer_name', __('Customer Name'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::text('customer_name', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Customer Name')]) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('start_date', __('Start Date'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::date('start_date', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Start Date')]) }}

        </div>
        <div class="form-group col-md-6">
            {{ Form::label('end_date', __('End Date'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::date('end_date', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter End Date')]) }}
        </div>
        <div class="form-group col-md-12 page_content">
            {{ Form::label('agreement_details', __('Agreement Details'), ['class' => 'form-label']) }}
            {!! Form::textarea('agreement_details', null, [
                'class' => 'summernote form-control',
                'rows' => '5',
                'id' => 'menubar_page_content',
            ]) !!}
        </div>
    </div>
</div>

<div class="modal-footer">
    <input type="button" value="{{ __('Cancel') }}" class="btn  btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Update') }}" class="btn  btn-primary">
</div>
{{ Form::close() }}
