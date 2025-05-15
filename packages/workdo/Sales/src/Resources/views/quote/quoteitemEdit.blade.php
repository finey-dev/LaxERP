{{Form::model($quoteItem,array('route' => array('quote.quoteitem.update', $quoteItem->id), 'method' => 'POST')) }}
    <div class="modal-body">
        <div class="text-end">
            @if (module_is_active('AIAssistant'))
                @include('aiassistant::ai.generate_ai_btn',['template_module' => 'quote item','module'=>'Sales'])
            @endif
        </div>
        <div class="row">
            <div class="form-group col-md-6">
                {{ Form::label('item', __('Item'),['class'=>'form-label']) }}
                {{ Form::select('item', $items,null, array('class' => 'form-control items','required'=>'required')) }}
            </div>
            <div class="form-group col-md-6">
                {{ Form::label('quantity', __('Quantity'),['class'=>'form-label']) }}
                {{ Form::number('quantity',null, array('class' => 'form-control quantity','required'=>'required','placeholder'=>'Enter Quantity')) }}
            </div>
            <div class="form-group col-md-6">
                {{ Form::label('price', __('Price'),['class'=>'form-label']) }}
                {{ Form::number('price',null, array('class' => 'form-control price','required'=>'required','stage'=>'0.01','placeholder'=>'Enter Price')) }}
            </div>
            <div class="form-group col-md-6">
                {{ Form::label('discount', __('Discount'),['class'=>'form-label']) }}
                {{ Form::number('discount',null, array('class' => 'form-control discount','placeholder'=>'Enter Discount')) }}
            </div>
            <div class="form-group col-md-12">
                {{ Form::label('tax', __('Tax'),['class'=>'form-label']) }}
                {{ Form::hidden('tax',null, array('class' => 'form-control taxId')) }}
                @if(module_is_active('ProductService'))
                    <div class="row">
                        <div class="col-md-12">
                            <div class="tax">
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            <div class="form-group col-md-12">
                {{ Form::label('description', __('Description'),['class'=>'form-label']) }}
                {!! Form::textarea('description', null, ['class'=>'form-control','rows'=>'3','placeholder'=> 'Enter Description']) !!}
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="modal-footer">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
            {{Form::submit(__('update'),array('class'=>'btn btn-primary'))}}
        </div>
    </div>
{{ Form::close() }}
<script>
    $('.items').val({{$quoteItem->item}}).trigger("change")
</script>
