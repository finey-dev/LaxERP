{{ Form::open(['route' => 'machine-repair.store', 'method' => 'post', 'id' => 'machine','enctype' => 'multipart/form-data','class'=>'needs-validation','novalidate']) }}
<div class="modal-body">
    <div class="row">
        <div class="form-group col-md-12">
            {!! Form::label('', __('Machine Name'), ['class' => 'form-label']) !!}<x-required></x-required>
            {!! Form::text('name', null, [
                'class' => 'form-control',
                'placeholder' => 'Enter Machine Name',
                'required' => true,
            ]) !!}
        </div>
        <div class="form-group col-md-6">
            {!! Form::label('', __('Manufacturer'), ['class' => 'form-label']) !!}<x-required></x-required>
            {!! Form::text('manufacturer', null, [
                'class' => 'form-control',
                'placeholder' => 'Enter Manufacturer',
                'required' => true,
            ]) !!}
        </div>
        <div class="form-group col-md-6">
            {!! Form::label('', __('Model'), ['class' => 'form-label']) !!}<x-required></x-required>
            {!! Form::text('model', null, [
                'class' => 'form-control',
                'placeholder' => 'Enter Model',
                'required' => true,
            ]) !!}
        </div>
        <div class="form-group col-md-6">
            {!! Form::label('', __('Installation Date'), ['class' => 'form-label']) !!}<x-required></x-required>
            {!! Form::date('installation_date', null, [
                'class' => 'form-control',
                'required' => true,
            ]) !!}
        </div>
        {{-- <div class="form-group col-md-6">
            {!! Form::label('', __('Last Maintenance Date'), ['class' => 'form-label']) !!}
            {!! Form::date('last_maintenance_date', null, [
                'class' => 'form-control',
                'required' => true,
            ]) !!}
        </div> --}}
        <div class="form-group col-md-6">
            {{ Form::label('status', __('Status'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::select('status', $status, null, ['class' => 'form-control', 'required' => 'required']) }}
        </div>
        <div class="form-group col-md-12">
            {!! Form::label('', __('Description'), ['class' => 'form-label']) !!}
            {!! Form::textarea('description', null, [
                'class' => 'form-control',
                'placeholder' => 'Enter Description',
                'rows' => '3',
                'cols' => '50',
                'id' => 'machine-desc',
            ]) !!}
        </div>
        @if (module_is_active('CustomField') && !$customFields->isEmpty())
        <div class="form-group col-md-6">
            <div class="tab-pane fade show" id="tab-2" role="tabpanel">
                @include('custom-field::formBuilder')
            </div>
        </div>
    @endif
        <div class="modal-footer pb-0">
            <input type="button" value="Cancel" class="btn btn-light" data-bs-dismiss="modal">
            <input type="submit" value="Create" class="btn btn-primary bg-primary">
        </div>
    </div>
</div>
{!! Form::close() !!}
