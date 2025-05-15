{{ Form::model($business, ['route' => ['business-process-mapping.update', $business->id], 'method' => 'put','class'=>'needs-validation','novalidate']) }}
<div class="modal-body">
    <div class="row">
        <div class="form-group col-md-12">
            {{ Form::label('title', __('Title'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::text('title', null, ['class' => 'form-control title', 'required' => 'required', 'id' => 'title', 'placeholder' => 'Enter Title']) }}
        </div>
        <div class="form-group col-md-12">
            {{ Form::label('description', __('Description'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::textarea('description', null, ['class' => 'form-control description', 'rows' => '3', 'id' => 'description', 'placeholder' => 'Enter description','required' => 'required']) }}
        </div>
        <div class="form-group col-md-12">
            {{ Form::label('related_to', __('Related To'), ['class' => 'form-label']) }}<x-required></x-required>
            <select class="form-control select related To" required="required" id="related-to" name="related_to">
                <option value="0">{{ __('Related To') }}</option>
                @foreach ($relateds as $key => $related)
                    <option value="{{ $key }}" @if ($key == $business->related_to) selected @endif>
                        {{ $related }}</option>
                @endforeach
            </select>
        </div>
        <div id="value_id_name">
            @if ($related_name->related == 'Other')
                {!! Form::text('value', $business->related_assign, [
                    'class' => 'form-control',
                    'id' => 'values_name',
                    'placeholder' => __('Enter Other'),
                ]) !!}
            @else
                {!! Form::select('value[]', $value, explode(',', $business->related_assign), [
                    'class' => 'form-control choices',
                    'multiple',
                    'id' => 'values_name',
                ]) !!}
            @endif
        </div>
    </div>
</div>

<div class="modal-footer">
    <input type="button" value="{{ __('Cancel') }}" class="btn  btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Update') }}" class="btn  btn-primary">
</div>

{{ Form::close() }}

<script>
    $(document).on("change", "#related-to", function() {
        var relatedId = $(this).val();
        $.ajax({
            url: '{{ route('mapping.relateds.update') }}',
            type: 'POST',
            data: {
                "related_id": relatedId,
                "_token": "{{ csrf_token() }}",
            },
            success: function(data) {
                $('#value_id_name').empty();

                if (data != 'Other') {
                    var option =
                        '<select class="form-control choices" name="value[]" id="values_name" placeholder="{{ __('Select Item') }}"  multiple>';
                    option += '<option value="" disabled>{{ __('Select Item') }}</option>';

                    for (var i = 0; i < data.length; i++) {
                        option += '<option value="' + data[i].id + '">' + data[i].text +
                            '</option>';
                    }

                    option += '</select';

                    $("#value_id_name").append(option);
                    var multipleCancelButton = new Choices('#values_name', {
                        removeItemButton: true,
                    });
                } else if (data === 'Other') {
                    var text =
                        '<input class="form-control" name="value" id="values_name" placeholder="{{ __('Enter Other') }}">';
                    $("#value_id_name").append(text);
                } else {
                    var text =
                        '<input class="form-control" name="value" id="values_name" placeholder="{{ __('Enter Other') }}" value="' +
                        data + '">';
                    $("#value_id_name").append(text);
                }
            },
        });
    });
</script>
