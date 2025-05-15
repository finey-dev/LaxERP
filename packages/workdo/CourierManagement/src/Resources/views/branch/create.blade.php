<style>
    .pac-container {
        z-index: 9999 !important;
    }
</style>
@php
    $company_settings = getCompanyAllSetting();
    $is_enable = isset($company_settings['courier_setting_is_enable'])
        ? $company_settings['courier_setting_is_enable']
        : 'off';
    $api = isset($company_settings['courier_setting_api_key']) ? $company_settings['courier_setting_api_key'] : '';
@endphp
{{ Form::open(['route' => 'courier.branch.store', 'method' => 'post','class'=>'needs-validation','novalidate']) }}
<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('name', __('Branch Name'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::text('branch_name', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Branch Name')]) }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('name', __('Branch Location'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::text('branch_location', null, ['class' => 'form-control', 'id' => 'branch-location', 'required' => 'required', 'placeholder' => __('Enter Branch Location')]) }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('name', __('City'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::text('city', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter City Name')]) }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('name', __('State'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::text('state', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter State Name')]) }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('name', __('Country'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::text('country', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Country Name')]) }}
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
    {{ Form::submit(__('Create'), ['class' => 'btn  btn-primary']) }}
</div>
{{ Form::close() }}

@if ($is_enable == 'on' && !empty($api))
    <script
        src="https://maps.googleapis.com/maps/api/js?key={{ $api }}&callback=initAutocomplete&libraries=places&v=weekly"
        defer></script>
    <script type="text/javascript">
        function addaddress(address) {
            var html = `
        <div class="form-group col-md-12 adress_div">
            <textarea  class="form-control"  name="addresses[]"  readonly>${address}</textarea>
            <button type="button" class="btn btn-sm btn-danger delete_address">
                <i class="ti ti-trash text-white py-1"></i>
            </button>
        </div>

        `;

            $('#add_addresses').append(html);
        }

        $(document).on("click", ".delete_address", function() {
            $(this).parent('.adress_div').remove();
        });

        let location_data = '';
        let autocomplete;

        function initAutocomplete() {
            address1Field = document.querySelector("#branch-location");
            autocomplete = new google.maps.places.Autocomplete(address1Field, {
                componentRestrictions: {
                    country: ["us", "ca", "in"]
                },
                fields: ["address_components", "geometry"],
                types: ["address"],
            });
            //address1Field.focus();

            autocomplete = new google.maps.places.Autocomplete(address1Fields, {
                componentRestrictions: {
                    country: ["us", "ca", "in"]
                },
                fields: ["address_components", "geometry"],
                types: ["address"],
            });
            address1Fields.focus();
            autocomplete.addListener("place_changed", function() {
                fillInAddress();
            });
        }

        function fillInAddress() {
            const place = autocomplete.getPlace();
            for (const component of place.address_components) {
                const componentType = component.types[0];
            }
            location_data = address1Field.value;
            addaddress(location_data);
        }
        window.initAutocomplete = initAutocomplete;
    </script>
@endif
