<div class="modal-body">
    <div class="row">
        <div class="col-form-label">
            <div class="invoice" id="printableArea">
                <div class="invoice-print">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="invoice-number d-flex justify-content-between align-items-center">
                                <img src="{{ get_file(sidebar_logo()) }}" width="170px;">
                                <a class="btn btn-sm btn-primary text-white non-printable" data-bs-toggle="tooltip"
                                    data-bs-toggle="bottom" title="{{ __('Download') }}" onclick="saveAsPDF()">
                                    <span class="ti ti-download"></span>
                                </a>
                            </div>
                            <div class="invoice-title"></div>
                            <hr>
                            <div class="row text-sm">
                                <div class="col-md-12">
                                    <address>
                                        <h6>
                                            {{ __('Name') }} :
                                            <small class="text-dark">{{ $swotanalysismodel->name }}</small>
                                        </h6></br>
                                        <h6>
                                            {{ __('Description') }} :
                                            <small class="text-dark">{!! $swotanalysismodel->dsescription !!}</small>
                                        </h6></br>
                                        <h6>
                                            {{ __('Strengths') }} :
                                            <small class="text-dark">{!! $swotanalysismodel->strengths !!}</small>
                                        </h6></br>

                                        <h6>
                                            {{ __('Weaknesses') }} :
                                            <small class="text-dark">{!! $swotanalysismodel->weaknesses !!}</small>
                                        </h6></br>

                                        <h6>
                                            {{ __('Opportunities') }} :
                                            <small class="text-dark">{!! $swotanalysismodel->opportunities !!}</small>
                                        </h6></br>

                                        <h6>
                                            {{ __('Threats') }} :
                                            <small class="text-dark">{!! $swotanalysismodel->threats !!}</small>
                                        </h6></br>

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
<script src="{{ asset('packages/workdo/SWOTAnalysisModel/src/Resources/assets/js/html2pdf.bundle.min.js') }}"></script>

<script>
     var filename = $('#filename').val();
        function saveAsPDF() {
        var downloadButton = document.querySelector('.non-printable');
        downloadButton.style.display = 'none';
        var element = document.getElementById('printableArea');
        var opt = {
            margin: 0.3,
            filename: 'SWOTAnalysisModel',
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
            downloadButton.style.display = '    block';
        });
    }
</script>
