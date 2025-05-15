<form action="{{ route('update.paymentdetails',['trackingId' => encrypt($courierDetails->tracking_id)]) }}" method="post" enctype="multipart/form-data">
    @csrf
    <div class="modal-body">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="tracking_id">{{ __('Tracking Id') }}</label>
                    <input type="text" name="tracking_id" value="{{ $courierDetails->tracking_id }}"
                        class="form-control" disabled>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="payment_date">{{ __('Date') }}</label><x-required></x-required>
                    <input type="date" class="form-control" name="payment_date"
                        value="{{ $courierDetails->payment_date }}" required="required">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="amount">{{ __('Amount') }}</label>
                    <input type="number" name="pay_amount" class="form-control" value="{{ $courierDetails->price }}" required>

                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label for="tracking_id">{{ __('Description') }}</label><x-required></x-required>
                    <textarea name="description"  rows="3" class="form-control" required="required">{{ $courierDetails->description }}</textarea>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label for="payment_receipt">{{ __('Payment Receipt') }}</label><x-required></x-required>
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

            <div class="col-md-12" id="edit_img_preview">
                <div class="form-group">
                    <div class="img">
                        <a href="" target="_blank">
                            <img src="{{ get_file($courierDetails->payment_receipt) }}" alt="" height="100px" width="100px">
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
        <button class="btn btn-primary" type="submit">{{ __('Update') }}</button>
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
            $("#edit_img_preview").hide();
        } else {
            preview.src = "";
            container.style.display = 'none'; // Hide the container when no image is selected
        }
    }
</script>
