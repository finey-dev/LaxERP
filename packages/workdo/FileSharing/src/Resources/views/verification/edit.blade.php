{{ Form::model($verification, ['route' => ['file-verification.update', $verification->id], 'method' => 'PUT', 'enctype' => 'multipart/form-data', 'class' => 'needs-validation', 'novalidate']) }}
<div class="modal-body">
    <div class="row">
        <div class="col-12 field" data-name="verification-attachments">
            <div class="form-group">
                {{ Form::label('attachment', __('Attachment'), ['class' => 'form-label']) }}<x-required></x-required>
                <input type="file" name="attachment" class="form-control"
                    onchange="document.getElementById('blah').src = window.URL.createObjectURL(this.files[0])">
                <p class="text-xs text-danger mt-1">
                    <strong>{{ __('Note : ') }}</strong>{{ __('e.g., Driving license, Passport, etc...') }}
                </p>
                <img id="blah" class="mt-2"
                    src="{{ check_file($verification->attachment) ? get_file($verification->attachment) : 'packages/workdo/FileSharing/src/Resources/assets/img/gallery.png' }}"
                    width="20%" height="20%" />
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{ __('Cancel') }}" class="btn  btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Update') }}" class="btn  btn-primary" id="submit">
</div>
{{ Form::close() }}
