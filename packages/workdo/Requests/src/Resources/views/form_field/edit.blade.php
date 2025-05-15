

{{ Form::model($FormField,['route' => ['requests-formfield.update' ,$FormField->id], 'method' => 'PUT','class'=>'needs-validation','novalidate']) }}
<div class="modal-body">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                {{Form::label('name',__('Question name'),['class'=>'form-label']) }}<x-required></x-required>
                {{Form::text('name',null,array('class'=>'form-control','placeholder'=>__('Enter Question'),'required'=>'required'))}}
                @error('name')
                <small class="invalid-name" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </small>
                @enderror
            </div>
        </div>
            <div class="col-md-6">
                <div class="form-group">
                    {{Form::label('type',__('Type'),['class'=>'form-label']) }}<x-required></x-required>
                    {{Form::select('type',$RequestFormField,null,array('class'=>'form-control ','required'=>'required'))}}
                </div>
            </div>
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn  btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Update')}}" class="btn  btn-primary">
</div>
{{Form::close()}}
