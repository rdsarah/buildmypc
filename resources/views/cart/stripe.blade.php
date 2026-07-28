<script src="https://js.stripe.com/v3/"></script>
<script>
    const stripe = Stripe("{{ config('services.stripe.key') }}");
    const checkoutButton = document.getElementById('checkout-button');

    checkoutButton.addEventListener('click', function () {
        checkoutButton.disabled = true;
        checkoutButton.innerText = 'Processing...';

        fetch("{{ route('checkout.session') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            }
        })
        .then(async (response) => {
            const text = await response.text();
            
            try {
                const data = JSON.parse(text);
                if (!response.ok || data.error) {
                    throw new Error(data.error || "Server Error");
                }
                return data;
            } catch (e) {
                // If response is not JSON (e.g. HTML Error page)
                console.error("Server HTML Response:", text);
                throw new Error("Server returned 500 HTML Error instead of JSON. Check the browser console or Laravel logs.");
            }
        })
        .then(session => {
            return stripe.redirectToCheckout({ sessionId: session.id });
        })
        .catch(error => {
            console.error("Checkout Error:", error);
            alert("Error: " + error.message);
            checkoutButton.disabled = false;
            checkoutButton.innerText = 'Pay with Card';
        });
    });
</script>