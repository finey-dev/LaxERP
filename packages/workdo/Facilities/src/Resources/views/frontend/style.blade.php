@push('scripts')
@if(isset($stripe_session) && isset($service))

    <script src="https://js.stripe.com/v3/"></script>
    <script>
    var stripe = Stripe("{{ company_setting('stripe_key', $service->created_by, $service->wokspace) }}");
    stripe.redirectToCheckout({
        sessionId: '{{ $stripe_session->id }}',
    }).then((result) => {
    });
    </script>
@endif
