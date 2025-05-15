@permission('letter offer manage')
    <div id="recruitment-print-settings" class="card">
        <div class="card-header p-3">
            <h5>{{ __('Recruitment Print Settings') }}</h5>
            <small class="text-muted">{{ __('Edit your Company Job details') }}</small>
        </div>
        <div class="company-setting">
            <form id="setting-form" method="post" action="{{ route('job.template.setting') }}" enctype ="multipart/form-data">
                @csrf
                <div class="card-body p-3">
                    <div class="row row-gap">
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body p-2">
                                    <div class="form-group d-flex flex-wrap align-items-center gap-2 mb-0">
                                        {{ Form::label('job_template', __('Template'), ['class' => 'form-label mb-0']) }}
                                        {{ Form::select('job_template', Workdo\Recruitment\Entities\JobCandidate::templateData()['templates'], !empty($settings['job_template']) ? $settings['job_template'] : null, ['class' => 'form-control flex-1', 'required' => 'required']) }}
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header p-2">
                                    <h6 class="form-label mb-0">{{ __('Color Input') }}</h6>
                                </div>
                                <div class="card-body p-2">
                                    @foreach (Workdo\Recruitment\Entities\JobCandidate::templateData()['colors'] as $key => $color)
                                        <label class="colorinput">
                                            <input name="job_color" type="radio" value="{{ $color }}"
                                                class="colorinput-input"
                                                {{ !empty($settings['job_color']) && $settings['job_color'] == $color ? 'checked' : '' }}>
                                            <span class="colorinput-color rounded-circle"
                                                style="background: #{{ $color }}"></span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header p-2">
                                    <h3 class="h6 mb-0">{{ __('Job Image') }}</h3>
                                </div>
                                <div class="card-body setting-card setting-logo-box p-3">
                                    <div class="logo-content img-fluid logo-set-bg  text-center">
                                        <img alt="image" src="{{ isset($settings['job_logo']) ? get_file($settings['job_logo']) : get_file('uploads/logo/logo_dark.png') }}" id="blah7">
                                    </div>
                                    <div class="choose-files text-center  mt-3">
                                        <label for="job_logo">
                                            <div class="bg-primary"> <i class="ti ti-upload px-1"></i>Choose file here</div>
                                            <input type="file" class="form-control file" name="job_logo" id="job_logo"
                                                data-filename="job_logo_update"
                                                onchange="document.getElementById('blah7').src = window.URL.createObjectURL(this.files[0])">
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group pt-4 mb-0 text-left">
                                <input type="submit" value="{{ __('Save Changes') }}"
                                    class="btn btn-print-invoice  btn-primary">
                            </div>
                        </div>
                        <div class="col-md-8">
                            @if (!empty($settings['job_template']) && !empty($settings['job_color']))
                                <iframe id="job_frame" class="w-100 h-100 rounded-1" frameborder="0"
                                    src="{{ route('job.preview', [$settings['job_template'], $settings['job_color']]) }}"></iframe>
                            @else
                                <iframe id="job_frame" class="w-100 h-100 rounded-1" frameborder="0"
                                    src="{{ route('job.preview', ['template1', 'fffff']) }}"></iframe>
                            @endif
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endpermission
<link rel="stylesheet" href="{{ asset('packages/workdo/Recruitment/src/Resources/assets/css/custom.css') }}">
<script src="{{ asset('public/js/custom.js') }}"></script>
<script>
    $(document).on("change", "select[name='job_template'], input[name='job_color']", function() {
        var template = $("select[name='job_template']").val();
        var color = $("input[name='job_color']:checked").val();
        $('#job_frame').attr('src', '{{ url('/job/preview') }}/' + template + '/' + color);
    });
</script>