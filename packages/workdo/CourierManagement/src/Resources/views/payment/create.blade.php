<form action="{{ route('make.paymnent', ['trackingId' => encrypt($courierDetails->tracking_id), 'courierPackageId' => encrypt($courierDetails->courier_package_id) ]) }}" method="post" enctype="multipart/form-data" class="needs-validation novalidate">
    @csrf
    <div class="modal-body">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="tracking_id" class="form-label">{{ __('Tracking Id') }}</label>
                    <input type="text" name="tracking_id" value="{{ $courierDetails->tracking_id }}"
                        class="form-control" disabled>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="payment_date" class="form-label">{{ __('Date') }}</label><x-required></x-required>
                    <input type="date" class="form-control" name="payment_date" required="required">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="amount" class="form-label">{{ __('Amount') }}</label>
                    <input type="number" name="pay_amount" class="form-control" value="{{ $courierDetails->price }}" disabled>
                    <input type="hidden" name="pay_amount" value="{{ $courierDetails->price }}">

                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label for="tracking_id" class="form-label">{{ __('Description') }}</label><x-required></x-required>
                    <textarea name="description" id="" cols="50" rows="3" class="form-control" required="required" placeholder="Enter Desctiption"></textarea>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label for="payment_receipt" class="form-label">{{ __('Payment Receipt') }}</label><x-required></x-required>
                    <input type="file" name="payment_receipt" class="form-control"
                        onchange="previewImage(this, 'payment_receipt_preview', 'payment_receipt_container')"
                        data-filename="payment_receipt" required="required">
                </div>
            </div>
            <div class="col-md-12" id="payment_receipt_container" style="display: none;">
                <div class="form-group">
                    <div class="img">
                        <a href="" target="_blank">
                            <img id="payment_receipt_preview" src="" class="img-thumbnail" height="100px"
                                width="100px" alt="">
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
        <button class="btn btn-primary" type="submit">{{ __('Create') }}</button>
    </div>
</form>
<script>
    function previewImage(input, imageId, containerId) {
        var preview = document.getElementById(imageId);
        var container = document.getElementById(containerId);

        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                container.style.display = 'block'; // Show the container when an image is selected
            };
            reader.readAsDataURL(input.files[0]);
        } else {
            preview.src = "";
            container.style.display = 'none'; // Hide the container when no image is selected
        }
    }
</script>
