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
                            <input type="text" id="name" name="name" value="{{ old('name', auth()->user()->name) }}" autocomplete="name" placeholder="Enter your full name" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email">
                            <i class="fas fa-envelope"></i> Email Address
                        </label>
                        <div class="input-wrapper">
                            <input type="email" id="email" name="email" value="{{ old('email', auth()->user()->email) }}" autocomplete="email" placeholder="name@example.com" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="phone">
                            <i class="fas fa-phone"></i> Phone Number
                        </label>
                        <div class="input-wrapper">
                            <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" inputmode="tel" autocomplete="tel" placeholder="e.g. 08123456789" required>
                        </div>
                    </div>
                </div>

                <div class="form-group mt-4">
                    <label for="address">
                        <i class="fas fa-map-marker-alt"></i> Complete Shipping Address
                    </label>
                    <div class="input-wrapper">
                        <textarea id="address" name="address" rows="4" autocomplete="street-address" placeholder="Street name, building number, district, city, postal code..." required>{{ old('address') }}</textarea>
                    </div>
                </div>

                <section class="checkout-order-summary" aria-labelledby="checkout-summary-title">
                    <div class="checkout-order-summary-heading">
                        <h2 id="checkout-summary-title">Order Summary</h2>
                        <a href="{{ route('cart.index') }}">Edit cart</a>
                    </div>

                    @php
                        $checkoutTotal = 0;
                    @endphp
                    @foreach($cart as $item)
                        @php
                            $lineTotal = $item['price'] * $item['quantity'];
                            $checkoutTotal += $lineTotal;
                        @endphp
                        <div class="checkout-summary-item">
                            <span>{{ $item['name'] }} <small>× {{ $item['quantity'] }}</small></span>
                            <strong>Rp {{ number_format($lineTotal, 0, ',', '.') }}</strong>
                        </div>
                    @endforeach
                    <div class="checkout-summary-total">
                        <span>Total</span>
                        <strong>Rp {{ number_format($checkoutTotal, 0, ',', '.') }}</strong>
                    </div>
                </section>

                <button type="submit" class="checkout-submit-btn">
                    <span>✨ Complete Checkout</span>
                    <i class="fas fa-paper-plane"></i>
                </button>

            </form>

        </div>

    </div>

</div>

<div class="checkout-popup" id="checkoutPopup" role="dialog" aria-modal="true" aria-labelledby="checkoutSuccessTitle" aria-describedby="checkoutSuccessMessage">
    <div class="checkout-popup-backdrop"></div>
    <div class="checkout-popup-card">
        <div class="popup-icon-container">
            <div class="popup-icon">
                <i class="fas fa-check"></i>
            </div>
            <div class="popup-pulse"></div>
        </div>

        <h2 id="checkoutSuccessTitle">Checkout Success ✨</h2>
        <p id="checkoutSuccessMessage">WhatsApp opened in a new tab. Redirecting to your order details...</p>

        <a href="#" class="checkout-whatsapp-fallback" data-whatsapp-fallback target="_blank" rel="noopener" hidden>
            Open WhatsApp
        </a>
        
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

    const whatsappWindow = window.open('', '_blank');
    if (whatsappWindow) {
        whatsappWindow.opener = null;
        whatsappWindow.document.title = 'Opening WhatsApp…';
        whatsappWindow.document.body.textContent = 'Preparing your Aanaya order for WhatsApp…';
    }

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

        const contentType = response.headers.get('content-type') || '';
        const result = contentType.includes('application/json')
            ? await response.json()
            : { success: false, message: 'Server mengembalikan respons yang tidak valid.' };

        /*
        ==========================================
        JIKA GAGAL
        ==========================================
        */

        if(!response.ok || !result.success){

            const validationMessage = result.errors
                ? Object.values(result.errors).flat()[0]
                : null;

            throw new Error(
                validationMessage || result.message ||
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

        const whatsappFallback = popup.querySelector('[data-whatsapp-fallback]');

        if (whatsappWindow && !whatsappWindow.closed) {
            whatsappWindow.location.replace(result.whatsapp_url);
        } else {
            whatsappFallback.href = result.whatsapp_url;
            whatsappFallback.hidden = false;
            document.getElementById('checkoutSuccessMessage').textContent =
                'Your browser blocked the WhatsApp tab. Use the button below, then view your order details.';
        }

        setTimeout(() => {
            popup.classList.remove('active');
            document.body.style.overflow = '';
            window.location.href = result.order_url;
        },1800);

    }
    catch(error){

        if (whatsappWindow && !whatsappWindow.closed) {
            whatsappWindow.close();
        }

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
