<div class="modal-body">
    <div class="row">
        <div class="col-lg-12 mt-2" id="printableArea">
            <div class="row">
                <div class="col-9">
                    <div class="invoice-number">
                        <img src="{{ get_file(sidebar_logo())}}"
                            width="120px;">
                    </div>
                </div>
                <div class="col-3">
                    <div class="non-printable text-md-end">
                        <a class="btn btn-sm btn-primary text-white" data-bs-toggle="tooltip"
                            data-bs-toggle="bottom" title="{{ __('Download') }}" onclick="saveAsPDF()" ><span
                                class="ti ti-download" ></span></a>
                    </div>
                </div>
            </div>
            <table class="table modal-table mt-3">
                <tbody>
                    <tr>
                        <th>{{__('Name')}}</th>
                        <td>{{ $facilitiesreceipt->client_id != 0 ? (isset($facilitiesreceipt->user) ? $facilitiesreceipt->user->name : '-') : (isset($facilitiesreceipt->name) ? $facilitiesreceipt->name : '-') }}</td>
                    </tr>
                    <tr>
                        <th>{{__('Service')}}</th>
                        <td>{{ isset($facilitiesreceipt->services) ? $facilitiesreceipt->services->name : '-'}}</td>
                    </tr>
                    <tr>
                        <th>{{__('Number')}}</th>
                        <td>{{ $facilitiesreceipt->client_id != 0 ? (isset($facilitiesreceipt->user) ? $facilitiesreceipt->user->mobile_no : '-') : (isset($facilitiesreceipt->number) ? $facilitiesreceipt->number : '-')}}</td>
                    </tr>
                    <tr>
                        <th >{{__('Gender')}}</th>
                        <td>{{ !empty($facilitiesreceipt->gender)?$facilitiesreceipt->gender:'-'}}</td>
                    </tr>
                    <tr>
                        <th>{{__('Start Time')}}</th>
                        <td>{{ !empty($facilitiesreceipt->start_time) ? $facilitiesreceipt->start_time :'-' }}</td>
                    </tr>
                    <tr>
                        <th>{{__('End Time')}}</th>
                        <td>{{ !empty($facilitiesreceipt->end_time) ? $facilitiesreceipt->end_time :'-' }}</td>
                    </tr>
                    <tr>
                        <th>{{__('Person')}}</th>
                        <td>{{ isset($facilitiesreceipt->booking) ? $facilitiesreceipt->booking->person : '-' }}</td>
                    </tr>
                    <tr>
                        <th>{{__('Price')}}</th>
                        <td> {{ currency_format_with_sym($facilitiesreceipt->price) ?? '-' }} </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="{{ asset('js/html2pdf.bundle.min.js') }}"></script>
<script>
    function saveAsPDF() {
        var downloadButton = document.querySelector('.non-printable');
        downloadButton.style.display = 'none';

        var element = document.getElementById('printableArea');
        var opt = {
            margin: 0.3,
            filename: 'Booking Receipt',
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
