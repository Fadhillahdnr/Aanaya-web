<x-app-layout>

<div class="user-product-detail-page">

    <!-- BG -->
    <div class="user-products-bg glow-1"></div>
    <div class="user-products-bg glow-2"></div>

    <section class="user-product-detail-container">

        <!-- IMAGE -->
        <div class="user-product-detail-image-wrap">

            <img
                src="{{ asset('uploads/products/' . $product->image) }}"
                alt="{{ $product->name }}"
                class="user-product-detail-image">

            <div class="user-product-detail-overlay"></div>

        </div>

        <!-- CONTENT -->
        <div class="user-product-detail-content">

            <span class="user-product-detail-badge">
                ✨ DREAM PRODUCT
            </span>

            <h1>
                {{ $product->name }}
            </h1>

            <div class="user-product-detail-price">

                Rp {{ number_format($product->price, 0, ',', '.') }}

            </div>

            <div class="user-product-detail-meta">

                <span>

                    <i class="fas fa-layer-group"></i>

                    {{ $product->category ?? 'Dreamy Collection' }}

                </span>

                <span>

                    <i class="fas fa-box"></i>

                    {{ $product->stock }} Stock

                </span>

            </div>

            <p>
                {{ $product->description }}
            </p>

            <!-- BUTTON -->
            <div class="user-product-detail-actions">

                <form action="{{ route('cart.add', $product->id) }}" method="POST" class="inline-form">
                    @csrf
                    <button type="submit" class="user-cart-btn-lg">
                        <i class="fas fa-cart-plus"></i>
                        Add To Cart
                    </button>
                </form>

                <a href="{{ route('cart.index') }}" class="user-checkout-btn">
                    <i class="fas fa-bag-shopping"></i>
                    View Cart
                </a>

            </div>

        </div>

    </section>

</div>

<!-- TOAST -->
<div id="cart-toast" class="cart-toast">
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