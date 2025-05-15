{{ Form::open(['route' => ['fix.equipment.location.update', $location->id],'class'=>'needs-validation','novalidate', 'method' => 'post', 'enctype' => 'multipart/form-data']) }}
<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('Location Name', __('Location Name'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::text('location_name', $location->location_name, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Location name')]) }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('Address', __('Address'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::textarea('address', $location->address, ['class' => 'form-control', 'required' => 'required','placeholder' => __('Enter Address'), 'rows' => '3']) }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('Attachment', __('Attachment'), ['class' => 'form-label']) }}
                <input type="file" class="form-control file" name="attachment" id="attachment"
                    data-filename="attachment_update"
                    onchange="document.getElementById('attach').src = window.URL.createObjectURL(this.files[0])"><br>
                <div class="img">
                    <a href="{{ !empty($location->attachment) ? get_file($location->attachment) : asset('packages/workdo/FixEquipment/src/Resources/assets/images/defualt.png') }}">
                        <img id="attach" src="{{ !empty($location->attachment) ? get_file($location->attachment) : asset('packages/workdo/FixEquipment/src/Resources/assets/images/defualt.png') }}" alt="location" width="35%">
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('description', __('Description'), ['class' => 'form-label']) }}
                {{ Form::textarea('description', $location->location_description, ['class' => 'form-control', 'placeholder' => __('Enter Description'), 'rows' => '3']) }}
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
    {{ Form::submit(__('Update'), ['class' => 'btn  btn-primary']) }}
</div>
{{ Form::close() }}
