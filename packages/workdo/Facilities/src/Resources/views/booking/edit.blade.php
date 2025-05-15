{{ Form::open(array('route' => array('facility-booking.update',$booking->id),'method' => 'PUT', 'class'=>'needs-validation','novalidate')) }}
    <div class="modal-body">
        <div class="row">
            <div class="form-group col-md-6 col-12 mb-3">
                {{ Form::label('service', __('Service'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::select('service', $service, $booking->service, ['class' => 'form-control', 'id' => 'service', 'required' => 'required']) }}
            </div>
            <div class="form-group col-md-6 col-12 mb-3">
                {{ Form::label('date', __('Date'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::date('date', $booking->date, ['class' => 'form-control ', 'placeholder' => 'Enter Date', 'id' => 'date', 'required' => 'required','min' => date('Y-m-d')]) }}
            </div>
            <div class="form-group col-md-6 col-12 mb-3">
                {{ Form::label('person', __('Person'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::number('person', $booking->person, ['class' => 'form-control', 'placeholder' => 'Enter Total Person', 'id' => 'person', 'required' => 'required']) }}
            </div>
            <div class="form-group col-md-6 col-12 mb-3">
                <label class= "form-label">{{ __('Gender') }}</label><x-required></x-required>
                <select class="form-select" id="gender" name="gender" required>
                    <option selected>{{ __('Select Gender') }}</option>
                    <option {{$booking->gender == 'Male' ? 'selected' : ''}}>{{ __('Male') }}</option>
                    <option {{$booking->gender == 'Female' ? 'selected' : ''}}>{{ __('Female') }}</option>
                </select>
            </div>
            <input type="hidden" name="booking_id" value="{{$booking->id}}" id="booking_id">

            <div class="text-end mt-3">
                <button class="btn btn-warning" id="submitForm" type="button">{{ __('Choose Slot') }}</button>
            </div>
            <section class="VendorDetails mt-2" id="append_div">
            </section>
        </div>
    </div>
{{ Form::close() }}
