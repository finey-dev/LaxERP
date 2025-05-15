{{ Form::open(['route' => ['business-process-mapping.store'], 'method' => 'post', 'enctype' => 'multipart/form-data','class'=>'needs-validation','novalidate']) }}
<div class="modal-body">
    <div class="row">
        <div class="form-group col-md-12">
            {{ Form::label('title', __('Title'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::text('title', '', ['class' => 'form-control title', 'required' => 'required', 'placeholder' => __('Enter Title')]) }}
        </div>
        <div class="form-group col-md-12">
            {{ Form::label('description', __('Description'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::textarea('description', '', ['class' => 'form-control description', 'rows' => '3', 'placeholder' => __('Enter description'), 'required' => 'required']) }}
        </div>
        <div class="form-group col-md-12">
            {{ Form::label('related_to', __('Related To'), ['class' => 'form-label']) }}<x-required></x-required>
            <select class="form-control select related To" required="required" id="related-to" name="related_to">
                <option value="0">{{ __('Select Related') }}</option>
                @foreach ($relateds as $related)
                    <option value="{{ $related->id }}">{{ $related->related }} </option>
                @endforeach
            </select>
        </div>
        <div id="value_id_name">
            <select class="form-control choices" name="value[]" id="values_name" placeholder="{{ __('Select Item') }}"
                multiple>
                <option value="">{{ __('Select related first') }}</option>
            </select>
        </div>
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{ __('Cancel') }}" class="btn  btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Create') }}" class="btn  btn-primary">
</div>

{{ Form::close() }}

<script>
    $(document).on("change", "#related-to", function() {
        var relatedId = $(this).val();
        $.ajax({
            url: '{{ route('mapping.relateds.get') }}',
            type: 'POST',
            data: {
                "related_id": relatedId,
                "_token": "{{ csrf_token() }}",
            },
            success: function(data) {
                if (data != 'Other') {

                    $('#value_id_name').empty();
                    var option =
                        '<select class="form-control choices" name="value[]" id="values_name" placeholder="{{ __('Select Item') }}"  multiple>';
                    option += '<option value="" disabled>{{ __('Select Item') }}</option>';

                    $.each(data, function(key, value) {
                        option += '<option value="' + key + '">' + value + '</option>';
                    });
                    option += '</select>';

                    $("#value_id_name").append(option);
                    var multipleCancelButton = new Choices('#values_name', {
                        removeItemButton: true,
                    });
                } else {
                    $('#value_id_name').empty();
                    var text =
                        '<input class="form-control" name="value" id="values_name" placeholder="{{ __('Enter Other') }}">';
                    $("#value_id_name").append(text);

                }
            },
        });
    });
</script>
