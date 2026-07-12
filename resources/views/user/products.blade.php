<x-app-layout>

<div class="user-products-page">

    <!-- BACKGROUND -->
    <div class="user-products-bg glow-1"></div>
    <div class="user-products-bg glow-2"></div>

    <!-- HERO -->
    <section class="user-products-hero">

        <span class="user-products-badge">
            <span class="badge-dot"></span>
            AANAYA COLLECTION
        </span>

        <h1>
            Magical <span>Products</span>
        </h1>

        <p>
            Discover aesthetic products, dreamy accessories,
            and magical collections crafted beautifully.
        </p>

        <div class="user-products-hero-actions">

            <a href="/cart" class="user-checkout-btn">

                <i class="fas fa-shopping-cart"></i>

                <span>View Cart</span>

            </a>

        </div>

    </section>


    <!-- PRODUCTS GRID -->
    <section class="user-products-grid">

        @forelse($products as $product)

            <div class="user-product-card">

                <!-- IMAGE -->
                <div class="user-product-image-wrap">

                    <img
                        src="{{ $product->image }}"
                        alt="{{ $product->name }}"
                        class="user-product-image"
                        loading="lazy"
                        onerror="this.src='https://placehold.co/600x600?text=No+Image'">

                    <div class="user-product-overlay"></div>

                    <!-- CATEGORY -->
                    <span class="user-product-category">

                        {{ $product->category ?? 'Dreamy' }}

                    </span>

                </div>

                <!-- BODY -->
                <div class="user-product-body">

                    <h2>
                        {{ $product->name }}
                    </h2>

                    <p>
                        {{ Str::limit($product->description, 90) }}
                    </p>

                    <!-- PRICE -->
                    <div class="user-product-price">

                        Rp {{ number_format($product->price, 0, ',', '.') }}

                    </div>

                    <!-- STOCK -->
                    <div class="user-product-stock">

                        <i class="fas fa-box"></i>

                        Stock:
                        {{ $product->stock }}

                    </div>

                    <!-- BUTTONS -->
                    <div class="user-product-actions">

                        <a
                            href="{{ route('merchandise.show', $product->slug) }}"
                            class="user-detail-btn">

                            <i class="fas fa-eye"></i>

                            Detail Product

                        </a>

                        <form action="{{ route('cart.add', $product->id) }}" method="POST" class="inline-form">
                            @csrf
                            <button type="submit" class="user-cart-btn">
                                <i class="fas fa-cart-plus"></i>
                                Add Cart
                            </button>
                        </form>

                    </div>

                </div>

            </div>

        @empty

            <div class="empty-products">

                <i class="fas fa-box-open"></i>

                <h2>No Products Yet</h2>

                <p>
                    Dreamy products will appear here soon ✨
                </p>

            </div>

        @endforelse

    </section>

</div>

<!-- TOAST -->
<div id="cart-toast" class="cart-toast products-cart-toast" role="status" aria-live="polite">
    <i class="fas fa-circle-check"></i>

    <span>
        Product successfully added to cart ✨
    </span>
</div>

<script>

document.addEventListener('DOMContentLoaded', () => {

    const forms = document.querySelectorAll('.inline-form');

    const toast = document.getElementById('cart-toast');

    forms.forEach(form => {

        form.addEventListener('submit', () => {

            localStorage.setItem('showCartToast', 'true');

        });

    });

    if(localStorage.getItem('showCartToast') === 'true'){

        toast.classList.add('show');

        setTimeout(() => {

            toast.classList.remove('show');

            localStorage.removeItem('showCartToast');

        }, 3000);

    }

});

</script>

</x-app-layout>
