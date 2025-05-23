{{Form::model($salesdocumenttype, array('route' => array('salesdocument_type.update', $salesdocumenttype->id), 'class'=>'needs-validation','novalidate','method' => 'PUT')) }}
<div class="modal-body">
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                {{Form::label('name',__('Name'),['class'=>'form-label'])}} <x-required></x-required>
                {{Form::text('name',null,array('class'=>'form-control','placeholder'=>__('Enter Document Type'),'required'=>'required'))}}
                @error('name')
                <span class="invalid-name" role="alert">
                        <strong class="text-danger">{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn  btn-light"
        data-bs-dismiss="modal">{{__('Cancel')}}</button>
        {{Form::submit(__('update'),array('class'=>'btn  btn-primary '))}}{{Form::close()}}
</div>
{{Form::close()}}
