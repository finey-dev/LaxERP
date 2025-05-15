
<div id="quotation-sidenav" class="card">
    <div class="card-header p-3">
        <h5>{{ __('Quotation Print Settings') }}</h5>
        <small class="text-muted">{{ __('Edit your Company Quotation details') }}</small>
    </div>
        <div class="company-setting">
            <form id="setting-form" method="post" action="{{ route('quotation.template.setting') }}"
                enctype ="multipart/form-data">
                @csrf
                <div class="card-body border-bottom border-1 p-3 pb-0">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                {{ Form::label('quotation_prefix', __('Prefix'), ['class' => 'form-label']) }}
                                {{ Form::text('quotation_prefix', !empty($settings['quotation_prefix']) ? $settings['quotation_prefix'] : '#QUO', ['class' => 'form-control', 'placeholder' => __('Enter Quotation Prefix') ]) }}
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                {{ Form::label('quotation_starting_number', __('Starting Number'), ['class' => 'form-label']) }}
                                {{ Form::number('quotation_starting_number', !empty($settings['quotation_starting_number']) ? $settings['quotation_starting_number'] : 1, ['class' => 'form-control', 'placeholder' => __('Enter Bill Starting Number') ]) }}
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                {{ Form::label('quotation_footer_title', __('Footer Title'), ['class' => 'form-label']) }}
                                {{ Form::text('quotation_footer_title', !empty($settings['quotation_footer_title']) ? $settings['quotation_footer_title'] : '', ['class' => 'form-control', 'placeholder' => __('Enter Footer Title') ]) }}
                            </div>
                        </div>
                        <div class="col-xxl-8">
                            <div class="form-group">
                                {{ Form::label('quotation_footer_notes', __('Footer Notes'), ['class' => 'form-label']) }}
                                {{ Form::textarea('quotation_footer_notes', !empty($settings['quotation_footer_notes']) ? $settings['quotation_footer_notes'] : '', ['class' => 'form-control', 'rows' => '2', 'placeholder' => __('Enter Bill Footer Notes') ]) }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="row row-gap">
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body p-2">
                                    <div class="form-group d-flex align-items-center justify-content-between mb-0">
                                        {{ Form::label('quotation_shipping_display', __('Shipping Display?'), ['class' => 'form-label mb-0']) }}
                                        <div class="text-end form-check form-switch d-inline-block">
                                            <input type="checkbox" class="form-check-input"
                                            name="quotation_shipping_display" id="quotation_shipping_display"
                                            {{ (isset($settings['quotation_shipping_display']) ? $settings['quotation_shipping_display'] : 'off') == 'on' ? 'checked' : '' }}>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-body p-2">
                                    <div class="form-group d-flex align-items-center justify-content-between mb-0">
                                        {{ Form::label('quotation_qr_display', __('QR Display?'), ['class' => 'form-label mb-0']) }}
                                        <div class="text-end form-check form-switch d-inline-block">
                                            <input type="checkbox" class="form-check-input"
                                            name="quotation_qr_display" id="quotation_qr_display"
                                            {{ (isset($settings['quotation_qr_display']) ? $settings['quotation_qr_display'] : 'off') == 'on' ? 'checked' : '' }}>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-body p-3">
                                    <div class="form-group d-flex flex-wrap align-items-center gap-2 mb-0">
                                        {{ Form::label('quotation_template', __('Quotation Template'), ['class' => 'form-label mb-0']) }}
                                        {{ Form::select('quotation_template', Workdo\Quotation\Entities\Quotation::templateData()['templates'], !empty($settings['quotation_template']) ? $settings['quotation_template'] : null, ['class' => 'form-control flex-1', 'required' => 'required']) }}
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header p-2">
                                    <h6 class="form-label mb-0">{{ __('Color Input') }}</h6>
                                </div>
                                <div class="card-body p-2">
                                    @foreach (Workdo\Quotation\Entities\Quotation::templateData()['colors'] as $key => $color)
                                        <label class="colorinput">
                                            <input name="quotation_color" type="radio" value="{{ $color }}"
                                                class="colorinput-input"
                                                {{ !empty($settings['quotation_color']) && $settings['quotation_color'] == $color ? 'checked' : '' }}>
                                            <span class="colorinput-color rounded-circle"
                                                style="background: #{{ $color }}"></span>
                                        </label>
                                @endforeach
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header p-2">
                                    <h3 class="h6 mb-0">{{ __('Logo') }}</h3>
                                </div>
                                <div class="card-body setting-card setting-logo-box p-3">
                                    <div class="logo-content img-fluid logo-set-bg  text-center">
                                        <img alt="image"
                                            src="{{ isset($settings['quotation_logo']) ? get_file($settings['quotation_logo']) : get_file('uploads/logo/logo_dark.png') }}"
                                            id="quotation_logo">
                                    </div>
                                    <div class="choose-files text-center  mt-3">
                                        <label for="quotation_logo1">
                                            <div class="bg-primary"> <i
                                                    class="ti ti-upload px-1"></i>{{ __('Choose file here') }}</div>
                                            <input type="file" class="form-control file" name="quotation_logo"
                                                id="quotation_logo1" data-filename="quotation_logo1"
                                                onchange="document.getElementById('quotation_logo').src = window.URL.createObjectURL(this.files[0])">
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group pt-4 mb-0 text-left">
                                <input type="submit" value="{{ __('Save Changes') }}"
                                    class="btn btn-print-quotation  btn-primary">
                            </div>
                        </div>
                        <div class="col-md-8">
                            @if (!empty($settings['quotation_template']) && !empty($settings['quotation_color']))
                                <iframe id="quotation_frame" class="w-100 h-100 rounded-1" frameborder="0"
                                    src="{{ route('quotation.preview', [$settings['quotation_template'], $settings['quotation_color']]) }}"></iframe>
                            @else
                                <iframe id="quotation_frame" class="w-100 h-100 rounded-1" frameborder="0"
                                    src="{{ route('quotation.preview', ['template1', 'fffff']) }}"></iframe>
                            @endif
                        </div>
                    </div>
                </div>
            </form>
        </div>
</div>
<script>
    $(document).on("change", "select[name='quotation_template'], input[name='quotation_color']", function() {
        var template = $("select[name='quotation_template']").val();
        var color = $("input[name='quotation_color']:checked").val();
        $('#quotation_frame').attr('src', '{{ url('/quotation/preview') }}/' + template + '/' + color);
    });
</script>
