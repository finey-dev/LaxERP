{{ Form::model($book, ['route' => ['book.update', $book->id], 'method' => 'put','enctype' => 'multipart/form-data', 'class' => 'needs-validation', 'novalidate']) }}
<div class="modal-body">
    <div class="row">
        <div class="form-group col-md-12">
            {{ Form::label('title', __('Title'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::text('title', null, ['class' => 'form-control', 'required' => 'required', 'id' => 'title', 'placeholder' => 'Enter title']) }}
        </div>

        <div class="form-group col-md-12">
            {{ Form::label('user_id', __('Users'), ['class' => 'form-label']) }}<x-required></x-required>
            <select class=" multi-select choices" id="user_id" name="user_id[]" multiple="multiple"
                data-placeholder="{{ __('Select Users ...') }}" required="required">
                @foreach ($users as $user)
                    <option value="{{ $user->id }}" {{ in_array($user->id, $selectedUserIds) ? 'selected' : '' }}>
                        {{ $user->name }} - {{ $user->email }}
                    </option>
                @endforeach
            </select>
            <p class="text-danger d-none" id="user_validation">{{ __('Users filed is required.') }}</p>
        </div>
        <div class="form-group col-md-12">
            {{ Form::label('description', __('Description'), ['class' => 'form-label']) }}
            {{ Form::textarea('description', null, ['class' => 'form-control', 'rows' => '3', 'id' => 'description', 'placeholder' => 'Enter description']) }}
        </div>
        @if (module_is_active('CustomField') && !$customFields->isEmpty())
        <div class="form-group col-md-12">
            <div class="tab-pane fade show" id="tab-2" role="tabpanel">
                @include('custom-field::formBuilder', ['fildedata' => $book->customField])
            </div>
        </div>
    @endif
    </div>
</div>

<div class="modal-footer">
    <input type="button" value="{{ __('Cancel') }}" class="btn  btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Update') }}" class="btn  btn-primary">
</div>

{{ Form::close() }}
