@php
    $company_setting = getCompanyAllSetting();
@endphp

    <div class="card" id="couriermanagement">
        {{ Form::open(array('route' => 'courier.setting.store','method' => 'post', 'enctype' => 'multipart/form-data')) }}
        <div class="card-header p-3">
            <div class="row align-items-center">
                <div class="col-sm-10 col-9">
                    <h5 class="">{{ __('Courier Settings') }}</h5>
                    <small><b class="text-danger">{{ __('Note: ') }}</b>{{ __('You can use this key for location suggestion in Courier module.') }}</small>
                </div>
                <div class="col-sm-2 col-3 text-end">
                    <div class="form-check form-switch custom-switch-v1 float-end">
                        <input type="checkbox" name="courier_setting_is_enable" class="form-check-input input-primary" id="courier_setting_is_enable" {{ (isset($company_setting['courier_setting_is_enable']) && $company_setting['courier_setting_is_enable'] =='on') ?' checked ':'' }} >
                        <label class="form-check-label" for="courier_setting_is_enable"></label>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body p-3 pb-0">
            <div class="row">
                <div class="form-group col-12">
                    <label class="form-label">{{ __('API Key') }}</label> <br>
                    <input class="form-control courier_setting_api_key" placeholder="{{ __('API Key') }}" name="courier_setting_api_key" type="text"
                        value="{{ isset($company_setting['courier_setting_api_key']) ? $company_setting['courier_setting_api_key'] :'' }}"
                        {{ (isset($company_setting['courier_setting_is_enable']) ? $company_setting['courier_setting_is_enable'] : 'off') == 'on' ? '' : ' disabled' }}>
                </div>
            </div>
        </div>
        <div class="card-footer text-end p-3">
            <button class="btn-submit btn btn-primary" type="submit">
                {{__('Save Changes')}}
            </button>
        </div>
        {{Form::close()}}
    </div>
    <script>
        $(document).on('click', '#courier_setting_is_enable', function() {
            if ($('#courier_setting_is_enable').prop('checked')) {
                $(".courier_setting_api_key").removeAttr("disabled");
            } else {
                $('.courier_setting_api_key').attr("disabled", "disabled");
            }
        });
    </script>

