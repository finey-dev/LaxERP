<div class="modal-body">
    <div class="row">
        <div class="col-form-label">
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
                                    <div class="text-md-end mt-3">
                                        <a class="btn btn-sm btn-primary text-white" data-bs-toggle="tooltip" data-bs-toggle="bottom"
                                            title="{{ __('Download') }}" onclick="saveAsPDF()"><span class="ti ti-download"></span></a>
                                    </div>
                                </div>
                            </div>
                            <div class="invoice-title">
                            </div>
                            <hr>
                            <div class="row text-sm">
                                <div class="col-md-12">
                                    <address>
                                        <h6>
                                            {{ __('Name') }} :
                                            <small class="text-dark">{{ $Charters->charter_name }}</small>
                                        </h6></br>
                                        <h6>
                                            {{ __('Description') }} :
                                            <small class="text-dark">{!! $Charters->dsescription !!}</small>
                                        </h6></br>
                                        <h6>
                                            {{ __('Organizational effects') }} :
                                            <small class="text-dark">{!! $Charters->organisational_effects !!}</small>
                                        </h6></br>

                                        <h6>
                                            {{ __('Goal Description') }} :
                                            <small class="text-dark">{!! $Charters->goal_description !!}</small>
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
<script src="{{ asset('js/html2pdf.bundle.min.js') }}"></script>

<script>
    var filename = $('#filename').val();
    function saveAsPDF() {
        var downloadButton = document.querySelector('.non-printable');
        downloadButton.style.display = 'none';
        var element = document.getElementById('printableArea');
        var opt = {
            margin: 0.3,
            filename: 'Charter',
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
