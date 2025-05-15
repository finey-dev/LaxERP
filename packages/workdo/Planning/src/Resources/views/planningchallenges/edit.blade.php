{{ Form::model($Challenge, ['route' => ['planningchallenges.update', $Challenge->id], 'method' => 'PUT', 'enctype' => 'multipart/form-data', 'class' => 'needs-validation', 'novalidate']) }}
<div class="modal-body">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                {{Form::label('challenge_name',__('Challenge Name'),['class'=>'form-label']) }}<x-required></x-required>
                {{Form::text('challenge_name', $Challenge->name, array('class'=>'form-control','placeholder'=>__('Enter Challenge Name'),'required'=>'required'))}}
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('category', __('Category'),['class'=>'form-label']) }}<x-required></x-required>
                {{ Form::select('category', $PlanningCategories, null, array('class' => 'form-control','required'=>'required')) }}
            </div>
        </div>
        <div class="form-group col-md-6">
            <label for="datetime" class="form-label">{{ __('End Date/Time') }}</label><x-required></x-required>
            <input class="form-control" placeholder="{{ __('Select Date/Time') }}"
                required="required" name="end_date" type="datetime-local" value="{{ isset($Challenge) ? date('Y-m-d\TH:i', strtotime($Challenge->end_date)) : '' }}">
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('position', __('Position'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::select('position', ['Ongoing' => 'Ongoing', 'On Hold' => 'On Hold', 'Finished' => 'Finished'], null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => 'Select Position']) }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('explantion', __('Explanation'),['class'=>'form-label']) }}<x-required></x-required>
                {{ Form::textarea('explantion', null, array('class' => 'form-control','required'=>'required','placeholder'=>'Add Explanation','rows'=>3)) }}
            </div>
        </div>

        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('notes', __('Notes'),['class'=>'form-label']) }}<x-required></x-required>
                {{ Form::textarea('notes', null, array('class' => 'form-control','required'=>'required','placeholder'=>'Add Notes','rows'=>3)) }}
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
    <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
</div>

{{ Form::close() }}
