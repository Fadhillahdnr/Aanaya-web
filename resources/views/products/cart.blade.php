<x-app-layout>

<div class="cart-page">

    <h1>Your Dream Cart ✨</h1>

    @php
        $total = 0;
    @endphp

    @foreach($cart as $id => $item)

        @php
            $subtotal = $item['price'] * $item['quantity'];
            $total += $subtotal;
        @endphp

        <div class="cart-card">

            <img
                src="{{ asset('uploads/products/' . $item['image']) }}">

            <div class="cart-info">

                <h2>{{ $item['name'] }}</h2>

                <div class="cart-qty-control">
                    <button class="qty-btn qty-minus" data-id="{{ $id }}" data-price="{{ $item['price'] }}">
                        <i class="fas fa-minus"></i>
                    </button>
                    <input type="number" class="qty-input" data-id="{{ $id }}" data-price="{{ $item['price'] }}" value="{{ $item['quantity'] }}" min="1">
                    <button class="qty-btn qty-plus" data-id="{{ $id }}" data-price="{{ $item['price'] }}">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>

                <div class="cart-price" data-subtotal="{{ $id }}">
                    Rp {{ number_format($subtotal) }}
                </div>

            </div>

            <form action="/cart/remove/{{ $id }}"
                  method="POST">

                @csrf
                @method('DELETE')

                <button class="remove-btn">
                    Remove
                </button>

            </form>

        </div>

    @endforeach

    <div class="cart-total">

        <h2>
            Total: Rp {{ number_format($total) }}
        </h2>

        <a href="/checkout"
           class="checkout-btn">

            Checkout Now

        </a>

    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const qtyBtns = document.querySelectorAll('.qty-btn');
        const qtyInputs = document.querySelectorAll('.qty-input');

        // Plus and Minus buttons
        qtyBtns.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const id = this.dataset.id;
                const input = document.querySelector(`.qty-input[data-id="${id}"]`);
                let quantity = parseInt(input.value);

                if(this.classList.contains('qty-plus')) {
                    quantity++;
                } else if(this.classList.contains('qty-minus') && quantity > 1) {
                    quantity--;
                }

                input.value = quantity;
                updateQuantity(id, quantity);
            });
        });

        // Direct input changes
        qtyInputs.forEach(input => {
            input.addEventListener('change', function() {
                const id = this.dataset.id;
                let quantity = parseInt(this.value);

                if(quantity < 1) {
                    quantity = 1;
                    this.value = 1;
                }

                updateQuantity(id, quantity);
            });
        });

        function updateQuantity(id, quantity) {
            fetch(`/cart/update/${id}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ quantity: quantity })
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    // Update subtotal
                    const priceElement = document.querySelector(`[data-subtotal="${id}"]`);
                    priceElement.textContent = 'Rp ' + data.subtotal.toLocaleString('id-ID');

                    // Update total
                    const totalElement = document.querySelector('.cart-total h2');
                    totalElement.textContent = 'Total: Rp ' + data.total.toLocaleString('id-ID');
                }
            })
            .catch(error => console.error('Error:', error));
        }
    });
</script>

</x-app-layout>