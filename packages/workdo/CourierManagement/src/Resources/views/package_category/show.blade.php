<div class="row p-3">
    <div class="col-12">
        <div class="row">
            <div class="col-md-6">
                <label class="form-label" for="branch_name" class="form-label">{{ __('Branch Name : ') }}</label><br>
            </div>
            <div class="col-md-6">
                {{ $servicetypeData->service_type }}
            </div>
        </div>
        <hr class="my-3">
        <div class="row">
            <div class="col-md-6">
                <label class="form-label" for="branch_location"
                    class="form-label">{{ __('Branch Location : ') }}</label><br>
            </div>
            <div class="col-md-6">
                {{ $brachData->branch_location }}
            </div>
        </div>
        <hr class="my-3">
        <div class="row">
            <div class="col-md-6">
                <label class="form-label" for="city" class="form-label">{{ __('City : ') }}</label><br>
            </div>
            <div class="col-md-6">
                {{ $brachData->city }}
            </div>
        </div>
        <hr class="my-3">
        <div class="row">
            <div class="col-md-6">
                <label class="form-label" for="state" class="form-label">{{ __('State : ') }}</label><br>
            </div>
            <div class="col-md-6">
                {{ $brachData->state }}
            </div>
        </div>
        <hr class="my-3">
        <div class="row">
            <div class="col-md-6">
                <label class="form-label" for="country" class="form-label">{{ __('Country : ') }}</label><br>
            </div>
            <div class="col-md-6">
                {{ $brachData->country }}
            </div>
        </div>

    </div>
    <hr class="my-3">
</div>
