<x-app-layout>

<div class="checkout-page">

    <div class="checkout-card">

        <h1>
            Dream Checkout ✨
        </h1>

        <form id="checkoutForm">

            @csrf

            <div class="form-group">

                <label>Name</label>

                <input type="text"
                       name="name"
                       required>

            </div>

            <div class="form-group">

                <label>Email</label>

                <input type="email"
                       name="email"
                       required>

            </div>

            <div class="form-group">

                <label>Phone</label>

                <input type="text"
                       name="phone"
                       required>

            </div>

            <div class="form-group">

                <label>Address</label>

                <textarea name="address"
                          rows="5"
                          required></textarea>

            </div>

            <button type="submit"
                    class="checkout-submit-btn">

                ✨ Complete Checkout

            </button>

        </form>

    </div>

</div>

<!-- SUCCESS POPUP -->
<div class="checkout-popup" id="checkoutPopup">

    <div class="checkout-popup-card">

        <div class="popup-icon">
            <i class="fas fa-check"></i>
        </div>

        <h2>
            Checkout Success ✨
        </h2>

        <p>
            Redirecting to WhatsApp...
        </p>

    </div>

</div>

<script>

document
    .getElementById('checkoutForm')
    .addEventListener('submit', function(e){

    e.preventDefault();

    const form = this;

    const name =
        form.querySelector('[name="name"]').value;

    const email =
        form.querySelector('[name="email"]').value;

    const phone =
        form.querySelector('[name="phone"]').value;

    const address =
        form.querySelector('[name="address"]').value;

    // =========================
    // CART DATA
    // =========================

    const cartItems = @json(session('cart'));

    let message =
`✨ *NEW DREAM ORDER* ✨

👤 Name: ${name}
📧 Email: ${email}
📱 Phone: ${phone}

📍 Address:
${address}

🛍️ *Products:*`;

    let total = 0;

    Object.values(cartItems).forEach(item => {

        const subtotal =
            item.price * item.quantity;

        total += subtotal;

        message += `

• ${item.name}
Qty: ${item.quantity}
Price: Rp ${subtotal.toLocaleString('id-ID')}`;
    });

    message += `

💰 *Total:* Rp ${total.toLocaleString('id-ID')}

✨ Thank you`;

    // =========================
    // SHOW POPUP
    // =========================

    const popup =
        document.getElementById('checkoutPopup');

    popup.classList.add('active');

    // =========================
    // REDIRECT WA
    // =========================

    setTimeout(() => {

        const whatsappNumber =
            '6289646363117';

        const waUrl =
            `https://wa.me/${whatsappNumber}?text=${encodeURIComponent(message)}`;

        window.location.href = waUrl;

    }, 1800);

});

</script>

</x-app-layout>