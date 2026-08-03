<x-app-layout>

<div class="cart-page">

    <div class="cart-bg-glow glow-1"></div>
    <div class="cart-bg-glow glow-2"></div>

    <div class="cart-container fade-in-up">
        
        <div class="cart-header">
            <h1>Your Dream Cart ✨</h1>
            <p>Review your beautiful items before checkout.</p>
        </div>

        @php
            $total = 0;
        @endphp

        <div class="cart-items-wrapper">
            @if($errors->has('cart'))
                <div class="direct-upload-status direct-upload-status--error">
                    {{ $errors->first('cart') }}
                </div>
            @endif

            @forelse($cart as $id => $item)

                @php
                    $subtotal = $item['price'] * $item['quantity'];
                    $total += $subtotal;
                @endphp

                <div class="cart-card">

                    <div class="cart-img-wrap">

                        <img
                            src="{{ $item['image'] }}"
                            alt="{{ $item['name'] }}"
                            loading="lazy"
                            onerror="this.src='https://placehold.co/300x300?text=No+Image'">

                    </div>

                    <div class="cart-info">
                        <h2>{{ $item['name'] }}</h2>
                        @if(! empty($item['variant_name']))
                            <p class="cart-variant">
                                {{ $item['variant_label'] ?? 'Option' }}: <strong>{{ $item['variant_name'] }}</strong>
                                @if(! empty($item['variant_sku']))
                                    <span>· {{ $item['variant_sku'] }}</span>
                                @endif
                            </p>
                        @endif
                        
                        <div class="cart-price" data-subtotal="{{ $id }}">
                            Rp {{ number_format($subtotal, 0, ',', '.') }}
                        </div>

                        <small>
                            {{ $item['available'] ? 'Stok tersedia: '.$item['stock'] : 'Produk tidak tersedia' }}
                        </small>

                        <div class="cart-actions-row">
                            <div class="cart-qty-control">
                                <button class="qty-btn qty-minus" data-id="{{ $id }}" data-price="{{ $item['price'] }}">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <input type="number" class="qty-input" data-id="{{ $id }}" data-price="{{ $item['price'] }}" value="{{ $item['quantity'] }}" min="1" max="{{ max(1, $item['stock']) }}" @disabled(! $item['available'])>
                                <button class="qty-btn qty-plus" data-id="{{ $id }}" data-price="{{ $item['price'] }}" @disabled(! $item['available'] || $item['quantity'] >= $item['stock'])>
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>

                            <form action="/cart/remove/{{ $id }}" method="POST" class="remove-form">
                                @csrf
                                @method('DELETE')
                                <button class="remove-btn">
                                    <i class="fas fa-trash-alt"></i> Remove
                                </button>
                            </form>
                        </div>
                    </div>

                </div>

            @empty
                <div class="empty-cart-state">
                    <i class="fas fa-box-open"></i>
                    <h2>Your cart is empty</h2>
                    <p>Looks like you haven't added any dreamy products yet.</p>
                    <a href="{{ route('merchandise') }}" class="continue-shopping-btn">Explore Products</a>
                </div>
            @endforelse
        </div>

        @if(count($cart) > 0)
        <div class="cart-summary-card">
            <div class="cart-total">
                <span>Grand Total</span>
                <h2>Rp {{ number_format($total, 0, ',', '.') }}</h2>
            </div>

            <a href="/checkout" class="checkout-btn" @if(collect($cart)->contains(fn ($item) => ! $item['available'] || $item['quantity'] > $item['stock'])) aria-disabled="true" onclick="return false" @endif>
                <span>Checkout Now</span>
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        @endif

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
            // Tambahkan loading state pada card (opsional)
            const card = document.querySelector(`.qty-input[data-id="${id}"]`).closest('.cart-card');
            card.style.opacity = '0.7';

            fetch(`/cart/update/${id}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ quantity: quantity })
            })
            .then(async response => {
                const data = await response.json();

                if (!response.ok) {
                    const message = data.errors
                        ? Object.values(data.errors).flat()[0]
                        : data.message;
                    throw new Error(message || 'Jumlah produk gagal diperbarui.');
                }

                return data;
            })
            .then(data => {
                if(data.success) {
                    // Update subtotal
                    const priceElement = document.querySelector(`[data-subtotal="${id}"]`);
                    // Gunakan format Indonesia
                    priceElement.textContent = 'Rp ' + data.subtotal.toLocaleString('id-ID');

                    // Update total
                    const totalElement = document.querySelector('.cart-total h2');
                    totalElement.textContent = 'Rp ' + data.total.toLocaleString('id-ID');
                }
            })
            .catch(error => {
                alert(error.message);
                window.location.reload();
            })
            .finally(() => {
                card.style.opacity = '1';
            });
        }
    });
</script>

</x-app-layout>
