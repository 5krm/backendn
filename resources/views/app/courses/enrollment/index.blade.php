<x-layouts.app :user="$user">
    <div id="checkout"></div>
</x-layouts.app>

<script src="https://js.stripe.com/v3/"></script>
<script>
    const stripe = Stripe("{{ env('STRIPE_CLIENT') }}");
    async function init() {
        const checkout = await stripe.initEmbeddedCheckout({
            clientSecret: '{{ $clientSecret }}',
        });
        checkout.mount('#checkout');
    }
    init();
</script>
<style>
    html,
    body {
        background: #f6f9fd !important;
    }
</style>
