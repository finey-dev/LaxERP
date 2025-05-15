{{ Form::model($customQuestion, ['route' => ['custom-question.update', $customQuestion->id], 'method' => 'PUT', 'class' => 'needs-validation', 'novalidate']) }}
<div class="modal-body">
    <div class="row">
        <div class="form-group">
            {{ Form::label('question', __('Question'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::text('question', null, ['class' => 'form-control', 'placeholder' => __('Enter question'), 'required' => 'required']) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('is_required', __('Is Required'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::select('is_required', $is_required, null, ['class' => 'form-control  ', 'placeholder' => __('Select Is Required'), 'required' => 'required']) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('screening_type', __('Screening Type'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::select('screening_type', $screening_type, null, ['class' => 'form-control ', 'placeholder' => __('Select Screening Type'), 'required' => 'required']) }}
            @if (empty($screening_type->count()))
                <div class=" text-xs">
                    {{ __('Please add screening type. ') }}<a
                        href="{{ route('screening-type.index') }}" target="_blank"><b>{{ __('Add Screening Type') }}</b></a>
                </div>
            @endif
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('screen_indicator', __('Screen Indicator'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::select('screen_indicator', $screen_indicator, null, ['class' => 'form-control ', 'placeholder' => __('Select Screen Indicator'), 'required' => 'required']) }}
            @if (empty($screen_indicator->count()))
                <div class=" text-xs">
                    {{ __('Please add screen indicator. ') }}<a
                        href="{{ route('screen-indicator.index') }}" target="_blank"><b>{{ __('Add Screen Indicator') }}</b></a>
                </div>
            @endif
        </div>
        <div class="form-group col-md-6">
            <h6 class="form-label">{{ __('Rating') }}</h6>
            <fieldset id='demo1' class="rate">
                <input class="stars" type="radio" id="technical-5-1" name="rating" value="5"
                    {{ isset($customQuestion->rating) && $customQuestion->rating == 5 ? 'checked' : '' }} />
                <label class="full" for="technical-5-1" title="Awesome - 5 stars"></label>
                <input class="stars" type="radio" id="technical-4-1" name="rating" value="4"
                    {{ isset($customQuestion->rating) && $customQuestion->rating == 4 ? 'checked' : '' }} />
                <label class="full" for="technical-4-1" title="Pretty good - 4 stars"></label>
                <input class="stars" type="radio" id="technical-3-1" name="rating" value="3"
                    {{ isset($customQuestion->rating) && $customQuestion->rating == 3 ? 'checked' : '' }} />
                <label class="full" for="technical-3-1" title="Meh - 3 stars"></label>
                <input class="stars" type="radio" id="technical-2-1" name="rating" value="2"
                    {{ isset($customQuestion->rating) && $customQuestion->rating == 2 ? 'checked' : '' }} />
                <label class="full" for="technical-2-1" title="Kinda bad - 2 stars"></label>
                <input class="stars" type="radio" id="technical-1-1" name="rating" value="1"
                    {{ isset($customQuestion->rating) && $customQuestion->rating == 1 ? 'checked' : '' }} />
                <label class="full" for="technical-1-1" title="Sucks big time - 1 star"></label>
            </fieldset>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
    <input type="submit" value="{{ __('Update') }}" class="btn  btn-primary">

</div>
{{ Form::close() }}

<script type="text/javascript">
    $(document).on('change', '#screening_type', function() {
        var screening_type = $(this).val();
        getDepartment(screening_type);
    });

    function getDepartment(screening_type) {
        var data = {
            "screening_type": screening_type,
            "_token": "{{ csrf_token() }}",
        }

        $.ajax({
            url: '{{ route('screen.indicator') }}',
            method: 'POST',
            data: data,
            success: function(data) {
                $('#screen_indicator').empty();
                $('#screen_indicator').append(
                    '<option value="" disabled>{{ __('Select Screen Indicator') }}</option>');

                $.each(data, function(key, value) {
                    $('#screen_indicator').append('<option value="' + key + '">' + value +
                        '</option>');
                });
                $('#screen_indicator').val('');
            }
        });
    }
</script>
