{{ Form::model($visitor_document, ['route' => ['visitors-documents.update', $visitor_document->id], 'class' => 'needs-validation', 'novalidate', 'method' => 'PUT']) }}
<div class="modal-body">
    <div class="row">
        <div class="form-group col-md-12">
            {{ Form::label('visitor', __('Visitor'), ['class' => 'form-label']) }}
            {{ Form::select('visitor_id', $visitors, null, ['class' => 'form-control font-style']) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('document_type', __('Document Type'), ['class' => 'form-label']) }}
            {{ Form::select('document_type', $document, null, ['class' => 'form-control font-style']) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('document_number', __('Document Number'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::number('document_number', null, ['class' => 'form-control visitor-data font-style', 'required' => 'required','placeholder'=> __('Enter Document Number')]) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('status', __('Status'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::select('status', ['' => 'Select Status', 0 => 'Pending', 1 => 'verified'], null, array('class' => 'form-control select','required'=>'required')) }}

        </div>
        <div class="form-group col-md-6">
            {{ Form::label('date', __('Verification Date'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::datetimeLocal('date', null, ['class' => 'form-control visitor-data font-style', 'required' => 'required']) }}
        </div>

    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{ __('Cancel') }}" class="btn  btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Update') }}" class="btn  btn-primary">
</div>
{{ Form::close() }}
