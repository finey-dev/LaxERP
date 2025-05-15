<li class="nav-item" role="presentation">
    @if($recuuring_show->recurring_type == "invoice" )
    <button class="nav-link" id="invoice-recurring-tab" data-bs-toggle="pill" data-bs-target="#invoice-recurring"
        type="button">{{ __('Recurring Invoice') }}</button>
    @else
    <button class="nav-link" id="invoice-recurring-tab" data-bs-toggle="pill" data-bs-target="#invoice-recurring"
    type="button">{{ __('Recurring Bill') }}</button>
    @endif
</li>


