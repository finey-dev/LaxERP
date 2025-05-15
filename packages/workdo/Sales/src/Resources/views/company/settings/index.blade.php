@php
    $company_setting = getCompanyAllSetting();
@endphp
<div id="sales-print-sidenav" class="card">
    <div class="card-header p-3">
        <h5>{{__('Quote Print Settings')}}</h5>
        <small class="text-muted">{{__('')}}</small>
    </div>
        <div class="company-setting">
            <form id="setting-form" method="post" action="{{route('quote.template.setting')}}" enctype="multipart/form-data">
                @csrf
                <div class="card-body border-bottom border-1 p-3 pb-0">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                {{Form::label('quote_prefix',__('Prefix'),array('class'=>'form-label')) }}
                                {{Form::text('quote_prefix',!empty($company_setting['quote_prefix']) ? $company_setting['quote_prefix'] :'#QUO',array('class'=>'form-control', 'placeholder' => 'Enter Quote Prefix'))}}
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                {{Form::label('quote_footer_title',__('Footer Title'),array('class'=>'form-label')) }}
                                {{Form::text('quote_footer_title',!empty($company_setting['quote_footer_title']) ? $company_setting['quote_footer_title'] :'',array('class'=>'form-control', 'placeholder' => 'Enter Footer Title'))}}
                            </div>
                        </div>
                        <div class="col-xxl-8">
                            <div class="form-group">
                                {{Form::label('quote_footer_notes',__('Footer Notes'),array('class'=>'form-label')) }}
                                {{Form::textarea('quote_footer_notes',!empty($company_setting['quote_footer_notes']) ? $company_setting['quote_footer_notes'] : '',array('class'=>'form-control','rows'=>'3' ,'placeholder' => 'Enter Quote Footer Notes'))}}
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
                                        {{Form::label('quote_shipping_display',__('Shipping Display?'),array('class'=>'form-label mb-0')) }}
                                        <div class=" form-switch form-switch-left">
                                            <input type="checkbox" class="form-check-input" name="quote_shipping_display" id="quote_shipping_display"
                                            {{(isset($company_setting['quote_shipping_display']) ? $company_setting['quote_shipping_display'] : 'off') == 'on' ? 'checked' : ''}} >
                                            <label class="form-check-label" for="quote_shipping_display"></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-body p-2">
                                    <div class="form-group d-flex align-items-center justify-content-between mb-0">
                                        {{ Form::label('quote_qr_display', __('QR Display?'), ['class' => 'form-label mb-0']) }}
                                        <div class="text-end form-check form-switch d-inline-block">
                                            <input type="checkbox" class="form-check-input"
                                            name="quote_qr_display" id="quote_qr_display"
                                            {{ (isset($company_setting['quote_qr_display']) ? $company_setting['quote_qr_display'] : 'off') == 'on' ? 'checked' : '' }}>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-body p-2">
                                    <div class="form-group d-flex flex-wrap align-items-center gap-2 mb-0">
                                        {{Form::label('quote_template',__('Template'),array('class'=>'form-label mb-0')) }}
                                        {{ Form::select('quote_template',Workdo\Sales\Entities\SalesUtility::templateData()['templates'],!empty($company_setting['quote_template']) ? $company_setting['quote_template'] : null, array('class' => 'form-control flex-1','required'=>'required')) }}
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header p-2">
                                    <h6 class="form-label mb-0">{{ __('Color Input') }}</h6>
                                </div>
                                <div class="card-body p-2">
                                    @foreach( Workdo\Sales\Entities\SalesUtility::templateData()['colors'] as $key => $color)
                                        <label class="colorinput">
                                            <input name="quote_color" type="radio" value="{{$color}}" class="colorinput-input" {{(!empty($company_setting['quote_color']) && $company_setting['quote_color'] == $color) ? 'checked' : ''}}>
                                            <span class="colorinput-color rounded-circle" style="background: #{{$color}}"></span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header p-2">
                                    <h3 class="h6 mb-0">{{__('Logo')}}</h3>
                                </div>
                                <div class="card-body setting-card setting-logo-box p-3">
                                        <div class="logo-content img-fluid logo-set-bg  text-center">
                                            <img alt="image" src="{{ isset($company_setting['quote_logo']) ? get_file($company_setting['quote_logo']) : get_file('uploads/logo/logo_dark.png') }}"
                                             id="quote_logo">
                                        </div>
                                        <div class="choose-files text-center  mt-3">
                                            <label for="quote_logo1">
                                                <div class="bg-primary"> <i class="ti ti-upload px-1"></i>{{__('Choose file here')}}</div>
                                                <input type="file" class="form-control file" name="quote_logo" id="quote_logo1" data-filename="quote_logo1" onchange="document.getElementById('quote_logo').src = window.URL.createObjectURL(this.files[0])">
                                            </label>
                                        </div>
                                </div>
                            </div>
                            <div class="form-group pt-4 mb-0 text-left">
                                <input type="submit" value="{{__('Save Changes')}}" class="btn btn-print-invoice  btn-primary">
                            </div>
                        </div>
                        <div class="col-md-8">
                            @if(!empty( $company_setting['quote_template']) && !empty($company_setting['quote_color']))
                            <iframe id="quote_frame" class="w-100 h-100 rounded-1" frameborder="0" src="{{route('quote.preview',[$company_setting['quote_template'], $company_setting['quote_color']])}}"></iframe>
                            @else
                            <iframe id="quote_frame" class="w-100 h-100 rounded-1" frameborder="0" src="{{route('quote.preview',['template1','fffff'])}}"></iframe>
                            @endif
                        </div>
                    </div>
                </div>
            </form>
        </div>
</div>



<div id="salesorder-print-sidenav" class="card">
    <div class="card-header p-3">
        <h5>{{__('Sales Order Print Settings')}}</h5>
        <small class="text-muted">{{__('')}}</small>
    </div>
        <div class="company-setting">
            <form id="setting-form" method="post" action="{{route('salesorder.template.setting')}}" enctype="multipart/form-data">
                @csrf
                <div class="card-body border-bottom border-1 p-3 pb-0">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                {{Form::label('salesorder_prefix',__('Prefix'),array('class'=>'form-label')) }}
                                {{Form::text('salesorder_prefix',!empty($company_setting['salesorder_prefix']) ? $company_setting['salesorder_prefix'] :'#SLO',array('class'=>'form-control', 'placeholder' => 'Enter Quote Prefix'))}}
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                {{Form::label('salesorder_footer_title',__('Footer Title'),array('class'=>'form-label')) }}
                                {{Form::text('salesorder_footer_title',!empty($company_setting['salesorder_footer_title']) ? $company_setting['salesorder_footer_title'] :'',array('class'=>'form-control', 'placeholder' => 'Enter Footer Title'))}}
                            </div>
                        </div>
                        <div class="col-xxl-8">
                            <div class="form-group">
                                {{Form::label('salesorder_footer_notes',__('Footer Notes'),array('class'=>'form-label')) }}
                                {{Form::textarea('salesorder_footer_notes',!empty($company_setting['salesorder_footer_notes']) ? $company_setting['salesorder_footer_notes'] : '',array('class'=>'form-control','rows'=>'3' ,'placeholder' => 'Enter Quote Footer Notes'))}}
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
                                        {{Form::label('salesorder_shipping_display',__('Shipping Display?'),array('class'=>'form-label mb-0')) }}
                                        <div class=" form-switch form-switch-left">
                                            <input type="checkbox" class="form-check-input" name="salesorder_shipping_display" id="salesorder_shipping_display"
                                            {{(isset($company_setting['salesorder_shipping_display']) ? $company_setting['salesorder_shipping_display'] : 'off') == 'on' ? 'checked' : ''}}>
                                            <label class="form-check-label" for="salesorder_shipping_display"></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-body p-2">
                                    <div class="form-group d-flex align-items-center justify-content-between mb-0">
                                        {{ Form::label('salesorder_qr_display', __('QR Display?'), ['class' => 'form-label mb-0']) }}
                                        <div class="text-end form-check form-switch d-inline-block">
                                            <input type="checkbox" class="form-check-input"
                                            name="salesorder_qr_display" id="salesorder_qr_display"
                                            {{ (isset($company_setting['salesorder_qr_display']) ? $company_setting['salesorder_qr_display'] : 'off') == 'on' ? 'checked' : '' }}>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-body p-2">
                                    <div class="form-group d-flex flex-wrap align-items-center gap-2 mb-0">
                                        {{Form::label('salesorder_template',__('Template'),array('class'=>'form-label mb-0')) }}
                                        {{ Form::select('salesorder_template',Workdo\Sales\Entities\SalesUtility::templateData()['templates'],!empty($company_setting['salesorder_template']) ? $company_setting['salesorder_template'] : null, array('class' => 'form-control flex-1','required'=>'required')) }}
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header p-2">
                                    <h6 class="form-label mb-0">{{ __('Color Input') }}</h6>
                                </div>
                                <div class="card-body p-2">
                                    @foreach( Workdo\Sales\Entities\SalesUtility::templateData()['colors'] as $key => $color)
                                        <label class="colorinput">
                                            <input name="salesorder_color" type="radio" value="{{$color}}" class="colorinput-input" {{(!empty($company_setting['salesorder_color']) && $company_setting['salesorder_color'] == $color) ? 'checked' : ''}}>
                                            <span class="colorinput-color rounded-circle" style="background: #{{$color}}"></span>
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
                                            src="{{ isset($company_setting['salesorder_logo']) ? get_file($company_setting['salesorder_logo']) : get_file('uploads/logo/logo_dark.png') }}"
                                            id="salesorder_logo">
                                    </div>
                                    <div class="choose-files text-center  mt-3">
                                        <label for="salesorder_logo1">
                                            <div class="bg-primary"> <i
                                                    class="ti ti-upload px-1"></i>{{ __('Choose file here') }}
                                            </div>
                                            <input type="file" class="form-control file" name="salesorder_logo"
                                                id="salesorder_logo1" data-filename="salesorder_logo1"
                                                onchange="document.getElementById('salesorder_logo').src = window.URL.createObjectURL(this.files[0])">
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group pt-4 mb-0 text-left">
                                <input type="submit" value="{{__('Save Changes')}}" class="btn btn-print-invoice  btn-primary">
                            </div>
                        </div>
                        <div class="col-md-8">
                            @if(!empty( $company_setting['salesorder_template']) && !empty($company_setting['salesorder_color']))
                            <iframe id="salesorder_frame" class="w-100 h-100 rounded-1" frameborder="0" src="{{route('salesorder.preview',[$company_setting['salesorder_template'], $company_setting['salesorder_color']])}}"></iframe>
                            @else
                            <iframe id="salesorder_frame" class="w-100 h-100 rounded-1" frameborder="0" src="{{route('salesorder.preview',['template1','fffff'])}}"></iframe>
                            @endif
                        </div>
                    </div>
                </div>
            </form>
        </div>
</div>


<div id="salesinvoice-print-sidenav" class="card">
    <div class="card-header p-3">
        <h5>{{__('Sales Invoice Print Settings')}}</h5>
        <small class="text-muted">{{__('')}}</small>
    </div>
    <div class="company-setting">
        <form id="setting-form" method="post" action="{{route('salesinvoice.template.setting')}}" enctype="multipart/form-data">
            @csrf
            <div class="card-body border-bottom border-1 p-3 pb-0">
                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-group">
                            {{Form::label('salesinvoice_prefix',__('Sales Invoice Prefix'),array('class'=>'form-label')) }}
                            {{Form::text('salesinvoice_prefix',!empty($company_setting['salesinvoice_prefix']) ? $company_setting['salesinvoice_prefix'] :'#INV',array('class'=>'form-control', 'placeholder' => 'Enter Invoice Prefix'))}}
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            {{Form::label('salesinvoice_footer_title',__('Footer Title'),array('class'=>'form-label')) }}
                            {{Form::text('salesinvoice_footer_title',!empty($company_setting['salesinvoice_footer_title']) ? $company_setting['salesinvoice_footer_title'] :'',array('class'=>'form-control', 'placeholder' => 'Enter Footer Title'))}}
                        </div>
                    </div>
                    <div class="col-xxl-8">
                        <div class="form-group">
                            {{Form::label('salesinvoice_footer_notes',__('Footer Notes'),array('class'=>'form-label')) }}
                            {{Form::textarea('salesinvoice_footer_notes',!empty($company_setting['salesinvoice_footer_notes']) ? $company_setting['salesinvoice_footer_notes'] : '',array('class'=>'form-control','rows'=>'3' ,'placeholder' => 'Enter Quote Footer Notes'))}}
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
                                    {{Form::label('salesinvoice_shipping_display',__('Shipping Display?'),array('class'=>'form-label mb-0')) }}
                                    <div class=" form-switch form-switch-left">
                                        <input type="checkbox" class="form-check-input" name="salesinvoice_shipping_display" id="salesinvoice_shipping_display"
                                        {{(isset($company_setting['salesinvoice_shipping_display']) ? $company_setting['salesinvoice_shipping_display'] : 'off') == 'on' ? 'checked' : ''}}>
                                        <label class="form-check-label" for="salesinvoice_shipping_display"></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body p-2">
                                <div class="form-group d-flex align-items-center justify-content-between mb-0">
                                    {{ Form::label('sales_invoice_qr_display', __('QR Display?'), ['class' => 'form-label mb-0']) }}
                                    <div class="text-end form-check form-switch d-inline-block">
                                        <input type="checkbox" class="form-check-input"
                                        name="sales_invoice_qr_display" id="sales_invoice_qr_display"
                                        {{ (isset($company_setting['sales_invoice_qr_display']) ? $company_setting['sales_invoice_qr_display'] : 'off') == 'on' ? 'checked' : '' }}>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body p-2">
                                <div class="form-group d-flex flex-wrap align-items-center gap-2 mb-0">
                                    {{Form::label('salesinvoice_template',__('Template'),array('class'=>'form-label mb-0')) }}
                                    {{ Form::select('salesinvoice_template',Workdo\Sales\Entities\SalesUtility::templateData()['templates'],!empty($company_setting['salesinvoice_template']) ? $company_setting['salesinvoice_template'] : null, array('class' => 'form-control flex-1','required'=>'required')) }}
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header p-2">
                                <h6 class="form-label mb-0">{{ __('Color Input') }}</h6>
                            </div>
                            <div class="card-body p-2">
                                @foreach( Workdo\Sales\Entities\SalesUtility::templateData()['colors'] as $key => $color)
                                    <label class="colorinput">
                                        <input name="salesinvoice_color" type="radio" value="{{$color}}" class="colorinput-input" {{(!empty($company_setting['salesinvoice_color']) && $company_setting['salesinvoice_color'] == $color) ? 'checked' : ''}}>
                                        <span class="colorinput-color rounded-circle" style="background: #{{$color}}"></span>
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
                                        src="{{ isset($company_setting['salesinvoice_logo']) ? get_file($company_setting['salesinvoice_logo']) : get_file('uploads/logo/logo_dark.png') }}"
                                        id="salesinvoice_logo">
                                </div>
                                <div class="choose-files text-center  mt-3">
                                    <label for="salesinvoice_logo1">
                                        <div class="bg-primary"> <i
                                                class="ti ti-upload px-1"></i>{{ __('Choose file here') }}
                                        </div>
                                        <input type="file" class="form-control file" name="salesinvoice_logo"
                                            id="salesinvoice_logo1" data-filename="salesinvoice_logo1"
                                            onchange="document.getElementById('salesinvoice_logo').src = window.URL.createObjectURL(this.files[0])">
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group pt-4 mb-0 text-left">
                            <input type="submit" value="{{__('Save Changes')}}" class="btn btn-print-invoice  btn-primary">
                        </div>
                    </div>
                    <div class="col-md-8">
                        @if(!empty( $company_setting['salesinvoice_template']) && !empty($company_setting['salesinvoice_color']))
                        <iframe id="salesinvoice_frame" class="w-100 h-100 rounded-1" frameborder="0" src="{{route('salesinvoice.preview',[$company_setting['salesinvoice_template'], $company_setting['salesinvoice_color']])}}"></iframe>
                        @else
                        <iframe id="salesinvoice_frame" class="w-100 h-100 rounded-1" frameborder="0" src="{{route('salesinvoice.preview',['template1','fffff'])}}"></iframe>
                        @endif
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>


<script>
    $(document).on("change", "select[name='quote_template'], input[name='quote_color']", function ()
    {
           var template = $("select[name='quote_template']").val();
           var color = $("input[name='quote_color']:checked").val();
           $('#quote_frame').attr('src', '{{url('/quote/preview')}}/' + template + '/' + color);
    });

       $(document).on("change", "select[name='salesorder_template'], input[name='salesorder_color']", function() {
           var template = $("select[name='salesorder_template']").val();
           var color = $("input[name='salesorder_color']:checked").val();
           $('#salesorder_frame').attr('src', '{{ url('/salesorder/preview') }}/' + template + '/' + color);
        });

        $(document).on("change", "select[name='salesinvoice_template'], input[name='salesinvoice_color']", function() {
           var template = $("select[name='salesinvoice_template']").val();
           var color = $("input[name='salesinvoice_color']:checked").val();
           $('#salesinvoice_frame').attr('src', '{{ url('/salesinvoice/preview') }}/' + template + '/' + color);
        });
</script>

