<div class="modal-body">
    <div class="row">
        <div class="form-label">


            <div class="invoice" id="printableArea">
                <div class="invoice-print">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="invoice-number">
                                        <img src="{{ get_file(sidebar_logo()) }}" width="170px;">
                                    </div>
                                </div>
                                <div class="col-md-6 non-printable">
                                    <div class="mt-3 text-md-end">
                                        <a class="text-white btn btn-sm btn-primary" data-bs-toggle="tooltip" data-bs-toggle="bottom"
                                            title="{{ __('Download') }}" onclick="saveAsPDF()"><span class="ti ti-download"></span></a>
                                    </div>
                                </div>
                            </div>


                            <div class="invoice-title">
                            </div>
                            <hr>
                            <div class="text-sm row">
                                <div class="col-md-12">
                                    <address>
                                        <h6>
                                            {{ __('Name') }} :
                                            <small class="text-dark">{{ $MarketingPlan->name }}</small>
                                        </h6></br>
                                        <h6>
                                            {{ __('Description') }} :
                                            <small class="text-dark">{!! !empty($MarketingPlan->description) ? $MarketingPlan->description : '-'   !!}</small>
                                        </h6></br>

                                        <h6>
                                            {{ __('Business Summary') }} :
                                            <small class="text-dark">{!! !empty($MarketingPlan->business_summary) ? $MarketingPlan->business_summary : '-' !!}</small>
                                        </h6></br>
                                        <h6>
                                            {{ __('Company Description') }} :
                                            <small class="text-dark">{!! !empty($MarketingPlan->company_description) ? $MarketingPlan->company_description : '-' !!}</small>
                                        </h6></br>
                                        <h6>
                                            {{ __('Team') }} :
                                            <small class="text-dark">{!! !empty($MarketingPlan->team) ? $MarketingPlan->team : '-' !!}</small>
                                        </h6></br>
                                        <h6>
                                            {{ __('Business initiative') }} :
                                            <small class="text-dark">{!! !empty($MarketingPlan->business_initiative) ? $MarketingPlan->business_initiative : '-' !!}</small>
                                        </h6></br>
                                        <h6>
                                            {{ __('Target Market') }} :
                                            <small class="text-dark">{!! !empty($MarketingPlan->target_market) ? $MarketingPlan->target_market : '-' !!}</small>
                                        </h6></br>
                                        <h6>
                                            {{ __('Marketing Channels') }} :
                                            <small class="text-dark">{!! !empty($MarketingPlan->marketing_channels) ? $MarketingPlan->marketing_channels : '-' !!}</small>
                                        </h6></br>
                                        <h6>
                                            {{ __('Budget') }} :
                                            <small class="text-dark">{!! !empty($MarketingPlan->budget) ? $MarketingPlan->budget : '-' !!}</small>
                                        </h6>
                                    </address>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div>
</div>
<script src="{{ asset('packages/workdo/MarketingPlan/src/Resources/assets/js/html2pdf.bundle.min.js') }}"></script>

<script>
     var filename = $('#filename').val();
    function saveAsPDF() {
        var downloadButton = document.querySelector('.non-printable');
        downloadButton.style.display = 'none';
        var element = document.getElementById('printableArea');
        var opt = {
            margin: 0.3,
            filename: 'Marketing Plan',
            image: {
                type: 'jpeg',
                quality: 1
            },
            html2canvas: {
                scale: 4,
                dpi: 72,
                letterRendering: true
            },
            jsPDF: {
                unit: 'in',
                format: 'A4'
            }
        };
        html2pdf().set(opt).from(element).save().then(function() {
            downloadButton.style.display = 'block';
        });
    }
</script>
