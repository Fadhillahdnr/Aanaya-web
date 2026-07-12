<x-app-layout>

<div class="user-product-detail-page">

    <div class="user-products-bg glow-1"></div>
    <div class="user-products-bg glow-2"></div>

    <section class="user-product-detail-container fade-in-up">

        <div class="user-product-detail-image-wrap">
            <img 
                src="{{ $product->image }}" 
                alt="{{ $product->name }}" 
                class="user-product-detail-image"
                loading="lazy"
                onerror="this.src='https://placehold.co/600x600?text=No+Image'">
            
            <div class="user-product-detail-overlay-light"></div>
        </div>

        <div class="user-product-detail-content">

            <span class="user-product-detail-badge">
                <i class="fas fa-sparkles"></i> DREAM PRODUCT
            </span>

            <h1>
                {{ $product->name }}
            </h1>

            <div class="user-product-detail-price">
                <span>Rp {{ number_format($product->price, 0, ',', '.') }}</span>
            </div>

            <div class="user-product-detail-meta">
                <span class="meta-item">
                    <div class="meta-icon">
                        <i class="fas fa-layer-group"></i>
                    </div>
                    {{ $product->category ?? 'Dreamy Collection' }}
                </span>

                <span class="meta-item">
                    <div class="meta-icon">
                        <i class="fas fa-box"></i>
                    </div>
                    {{ $product->stock }} Stock In
                </span>
            </div>

            <p class="user-product-description">
                {{ $product->description }}
            </p>

            <div class="user-product-detail-actions">
                <form action="{{ route('cart.add', $product->id) }}" method="POST" class="inline-form">
                    @csrf
                    <button type="submit" class="user-cart-btn-lg">
                        <i class="fas fa-cart-plus"></i>
                        <span>Add To Cart</span>
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

<div id="cart-toast" class="cart-toast product-detail-cart-toast" role="status" aria-live="polite">
    <div class="toast-icon">
        <i class="fas fa-circle-check"></i>
    </div>
    <div class="toast-content">
        <strong>Success!</strong>
        <span>Product successfully added to cart ✨</span>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const forms = document.querySelectorAll('.inline-form');
    const toast = document.getElementById('cart-toast');

    forms.forEach(form => {
        form.addEventListener('submit', () => {
            // Set flag before page reloads
            localStorage.setItem('showCartToast', 'true');
        });
    });

    // Check flag on page load
    if(localStorage.getItem('showCartToast') === 'true'){
        // Show toast with slight delay for smooth UI
        setTimeout(() => {
            toast.classList.add('show');
        }, 300);

        // Hide toast after 3.5 seconds
        setTimeout(() => {
            toast.classList.remove('show');
            localStorage.removeItem('showCartToast');
        }, 3800);
    }
});
</script>

</x-app-layout>
