<x-app-layout>

<div class="checkout-page">

    <div class="checkout-bg-glow glow-1"></div>
    <div class="checkout-bg-glow glow-2"></div>

    <div class="checkout-container fade-in-up">
        
        <div class="checkout-card">

            <div class="checkout-header">
                <h1>Dream Checkout ✨</h1>
                <p>Complete your shipping details to process the order</p>
            </div>

            <form
                id="checkoutForm"
                action="{{ route('checkout.process') }}"
                method="POST"
                class="modern-form">

                @csrf

                <div class="form-grid">
                    <div class="form-group">
                        <label for="name">
                            <i class="fas fa-user"></i> Full Name
                        </label>
                        <div class="input-wrapper">
                            <input type="text" id="name" name="name" placeholder="Enter your full name" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email">
                            <i class="fas fa-envelope"></i> Email Address
                        </label>
                        <div class="input-wrapper">
                            <input type="email" id="email" name="email" placeholder="name@example.com" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="phone">
                            <i class="fas fa-phone"></i> Phone Number
                        </label>
                        <div class="input-wrapper">
                            <input type="text" id="phone" name="phone" placeholder="e.g. 08123456789" required>
                        </div>
                    </div>
                </div>

                <div class="form-group mt-4">
                    <label for="address">
                        <i class="fas fa-map-marker-alt"></i> Complete Shipping Address
                    </label>
                    <div class="input-wrapper">
                        <textarea id="address" name="address" rows="4" placeholder="Street name, building number, district, city, postal code..." required></textarea>
                    </div>
                </div>

                <button type="submit" class="checkout-submit-btn">
                    <span>✨ Complete Checkout</span>
                    <i class="fas fa-paper-plane"></i>
                </button>

            </form>

        </div>

    </div>

</div>

<div class="checkout-popup" id="checkoutPopup">
    <div class="checkout-popup-backdrop"></div>
    <div class="checkout-popup-card">
        <div class="popup-icon-container">
            <div class="popup-icon">
                <i class="fas fa-check"></i>
            </div>
            <div class="popup-pulse"></div>
        </div>

        <h2>Checkout Success ✨</h2>
        <p>Your order is compiled beautifully. Redirecting you safely to WhatsApp...</p>
        
        <div class="popup-loader">
            <div class="loader-bar"></div>
        </div>
    </div>
</div>

<script>

document
.getElementById('checkoutForm')
.addEventListener('submit', async function(e){

    e.preventDefault();

    const form = this;

    const submitButton =
        form.querySelector(
            '.checkout-submit-btn'
        );

    submitButton.disabled = true;

    submitButton.innerHTML =
        `
        <span>Processing...</span>
        <i class="fas fa-spinner fa-spin"></i>
        `;

    try{

        /*
        ==========================================
        KIRIM DATA KE LARAVEL
        ==========================================
        */

        const response =
            await fetch(
                form.action,
                {
                    method: 'POST',

                    body: new FormData(form),

                    headers: {

                        'X-CSRF-TOKEN':
                            document
                            .querySelector(
                                'meta[name="csrf-token"]'
                            )
                            .getAttribute(
                                'content'
                            ),

                        'Accept':
                            'application/json'
                    }
                }
            );

        const result =
            await response.json();

        /*
        ==========================================
        JIKA GAGAL
        ==========================================
        */

        if(!result.success){

            throw new Error(
                result.message ||
                'Checkout gagal'
            );

        }

        /*
        ==========================================
        SHOW POPUP
        ==========================================
        */

        const popup =
            document.getElementById(
                'checkoutPopup'
            );

        popup.classList.add(
            'active'
        );

        document.body.style.overflow =
            'hidden';

        /*
        ==========================================
        REDIRECT WHATSAPP
        ==========================================
        */

        setTimeout(() => {

            window.location.href =
                result.whatsapp_url;

        },1800);

    }
    catch(error){

        console.error(error);

        alert(
            error.message ||
            'Terjadi kesalahan saat checkout'
        );

        submitButton.disabled =
            false;

        submitButton.innerHTML =
            `
            <span>
                ✨ Complete Checkout
            </span>
            <i class="fas fa-paper-plane"></i>
            `;

    }

});

</script>

</x-app-layout>