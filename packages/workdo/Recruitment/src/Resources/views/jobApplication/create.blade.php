{{ Form::open(['url' => 'job-application', 'method' => 'post', 'enctype' => 'multipart/form-data', 'class' => 'needs-validation', 'novalidate']) }}
<div class="modal-body">
    <div class="row">
        <div class="form-group col-md-6">
            {{ Form::label('job', __('Job'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::select('job', $jobs, null, ['class' => 'form-control ', 'id' => 'jobs', 'required' => 'required']) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('application_type', __('Application Type'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::select('application_type', $application_type, null, ['class' => 'form-control', 'id' => 'application_type', 'required' => 'required']) }}
        </div>

        <div class="form-group col-md-6 d-none job_candidate">
            {{ Form::label('job_candidate', __('Job Candidate'), ['class' => 'form-label']) }}
            {{ Form::select('job_candidate', $job_candidate, null, ['id' => 'job_candidate', 'class' => 'form-control choices', 'placeholder' => __('Select candidate'), 'searchEnabled' => 'true']) }}
            @if (empty($job_candidate->count()))
                <div class=" text-xs">
                    {{ __('Please add job candidate. ') }}<a
                        href="{{ route('job-candidates.index') }}"><b>{{ __('Add Job Candidate') }}</b></a>
                </div>
            @endif
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('name', __('Name'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::text('name', null, ['class' => 'form-control name', 'required' => 'required', 'placeholder' => __('Enter Name')]) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('email', __('Email'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::email('email', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Email')]) }}
        </div>
        <x-mobile divClass="col-md-6" name="phone" label="{{ __('Phone') }}"
            placeholder="{{ __('Enter Phone') }}" id="phone" required>
        </x-mobile>
        <div class="form-group col-md-6 dob d-none">
            {!! Form::label('dob', __('Date of Birth'), ['class' => 'form-label']) !!}
            {!! Form::date('dob', old('dob'), ['class' => 'form-control ', 'autocomplete' => 'off']) !!}
        </div>
        <div class="form-group col-md-6 gender d-none">
            {!! Form::label('gender', __('Gender'), ['class' => 'form-label']) !!}
            <div class="d-flex radio-check">
                <div class="form-check form-check-inline form-group">
                    <input type="radio" id="g_male" value="Male" name="gender" class="form-check-input">
                    <label class="form-check-label" for="g_male">{{ __('Male') }}</label>
                </div>
                <div class="form-check form-check-inline form-group">
                    <input type="radio" id="g_female" value="Female" name="gender" class="form-check-input">
                    <label class="form-check-label" for="g_female">{{ __('Female') }}</label>
                </div>
            </div>
        </div>
        <div class="form-group col-md-6 country d-none">
            {{ Form::label('country', __('Country'), ['class' => 'form-label']) }}
            {{ Form::text('country', null, ['class' => 'form-control', 'placeholder' => __('Enter  Country')]) }}
        </div>
        <div class="form-group col-md-6 country d-none">
            {{ Form::label('state', __('State'), ['class' => 'form-label']) }}
            {{ Form::text('state', null, ['class' => 'form-control', 'placeholder' => __('Enter State')]) }}
        </div>
        <div class="form-group col-md-6 country d-none">
            {{ Form::label('city', __('City'), ['class' => 'form-label']) }}
            {{ Form::text('city', null, ['class' => 'form-control', 'placeholder' => __('Enter City')]) }}
        </div>
        <div class="form-group col-md-12 letter d-none">
            {{ Form::label('cover_letter', __('Cover Letter'), ['class' => 'form-label']) }}
            {{ Form::textarea('cover_letter', null, ['class' => 'form-control', 'placeholder' => __('Enter Cover Letter') , 'rows' => 3]) }}
        </div>
        <div class="form-group col-md-6 profile d-none">
            {{ Form::label('profile', __('Profile'), ['class' => 'form-label']) }}
            <div class="choose-file">
                    <input type="file" class="form-control file" name="profile" id="profile"
                        onchange="document.getElementById('blah').src = window.URL.createObjectURL(this.files[0])">
                    <div class="">
                        <img src="" id="blah" width="15%" />
                    </div>
            </div>
        </div>
        <div class="form-group col-md-6 resume d-none">
            {{ Form::label('resume', __('CV / Resume'), ['class' => 'form-label']) }}
            <div class="choose-file">
                    <input type="file" class="form-control file" name="resume" id="resume"
                        onchange="document.getElementById('blah1').src = window.URL.createObjectURL(this.files[0])">
                    <div class="">
                        <img src="" id="blah1" width="15%" />
                    </div>
            </div>
        </div>
        @foreach ($questions as $question)
            <div class="form-group col-md-12  question question_{{ $question->id }} d-none">
                {{ Form::label($question->question, $question->question, ['class' => 'form-label']) }}@if ($question->is_required == 'yes')
                    <x-required></x-required>
                @endif
                <input type="text" class="form-control" name="question[{{ $question->question }}]"
                    {{ $question->is_required == 'yes' ? 'required' : '' }}>
            </div>
        @endforeach
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{ __('Cancel') }}" class="btn  btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Create') }}" class="btn  btn-primary">
</div>
{{ Form::close() }}

@if (!empty($jobId))
    <script>
        $(document).ready(function() {

            var id = $('#jobs').val();

            $.ajax({
                url: "{{ route('get.job.application') }}",
                type: 'POST',
                data: {
                    "id": id,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(data) {
                    var job = JSON.parse(data);
                    var applicant = job.applicant;
                    var visibility = job.visibility;
                    var question = job.custom_question;

                    (applicant.indexOf("gender") != -1) ? $('.gender').removeClass('d-none'): $(
                        '.gender').addClass('d-none');
                    (applicant.indexOf("dob") != -1) ? $('.dob').removeClass('d-none'): $('.dob')
                        .addClass('d-none');
                    (applicant.indexOf("country") != -1) ? $('.country').removeClass('d-none'): $(
                        '.country').addClass('d-none');

                    (visibility.indexOf("profile") != -1) ? $('.profile').removeClass('d-none'): $(
                        '.profile').addClass('d-none');
                    (visibility.indexOf("resume") != -1) ? $('.resume').removeClass('d-none'): $(
                        '.resume').addClass('d-none');
                    (visibility.indexOf("letter") != -1) ? $('.letter').removeClass('d-none'): $(
                        '.letter').addClass('d-none');

                    $('.question').addClass('d-none');

                    if (question.length > 0) {
                        question.forEach(function(id) {
                            $('.question_' + id).removeClass('d-none');
                        });
                    }
                }
            });
        });

        // Show/hide job candidate dropdown based on application type selection
        $(document).on('change', '#application_type', function() {
            var selectedValue = $(this).val();
            if (selectedValue === 'job_candidate') {
                $('.job_candidate').removeClass('d-none');
            } else {
                $('.job_candidate').addClass('d-none');
            }
        });

        $(document).on('change', '#job_candidate', function() {
            var candidateId = $(this).val();
            var baseUrl = "{{ url('/') }}";
            $.ajax({
                url: "{{ route('get.job.candidate') }}",
                type: 'POST',
                data: {
                    "id": candidateId,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(data) {
                    var job_candidate = JSON.parse(data);
                    var name = job_candidate.name;
                    var email = job_candidate.email;
                    var phone = job_candidate.phone;
                    var dob = job_candidate.dob;
                    var gender = job_candidate.gender;
                    var country = job_candidate.country;
                    var state = job_candidate.state;
                    var city = job_candidate.city;
                    var profile = job_candidate.profile;
                    var resume = job_candidate.resume;

                    // Set values and hide the fields
                    $('.name').val(name).closest('.form-group').addClass('d-none');
                    $('[name="email"]').val(email).closest('.form-group').addClass('d-none');
                    $('[name="phone"]').val(phone).closest('.form-group').addClass('d-none');
                    $('.dob input').val(dob).closest('.form-group').addClass('d-none');
                    $('.gender').closest('.form-group').addClass('d-none');
                    $('[name="country"]').val(country).closest('.form-group').addClass('d-none');
                    $('[name="state"]').val(state).closest('.form-group').addClass('d-none');
                    $('[name="city"]').val(city).closest('.form-group').addClass('d-none');
                    if (profile) {
                        $('.profile').val(profile).closest('.form-group').addClass('d-none');
                    }
                    if (resume) {
                        $('.resume').val(resume).closest('.form-group').addClass('d-none');
                    }
                }
            });
        });
    </script>
@endif
