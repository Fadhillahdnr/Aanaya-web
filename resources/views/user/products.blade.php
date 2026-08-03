<x-app-layout>

<div class="user-products-page">
    <div class="user-products-bg glow-1" aria-hidden="true"></div>
    <div class="user-products-bg glow-2" aria-hidden="true"></div>
    <div class="user-products-stars" aria-hidden="true"></div>

    <div class="user-products-shell">
        <section class="user-products-hero" aria-labelledby="products-title">
            <div class="user-products-hero-copy">
                <span class="user-products-badge">
                    <span class="badge-dot" aria-hidden="true"></span>
                    AANAYA COLLECTION
                </span>

                <h1 id="products-title">
                    Little pieces of the <span>Aanaya universe.</span>
                </h1>

                <p>
                    Discover dreamy merchandise and thoughtful keepsakes, made to bring
                    the feeling of Aanaya a little closer to you.
                </p>

                <div class="user-products-hero-actions">
                    <a href="{{ route('cart.index') }}" class="user-products-primary-action">
                        <i class="fas fa-bag-shopping" aria-hidden="true"></i>
                        <span>View Cart</span>
                    </a>

                    @auth
                        <a href="{{ route('orders.index') }}" class="user-products-secondary-action">
                            <i class="fas fa-receipt" aria-hidden="true"></i>
                            <span>Track My Orders</span>
                        </a>
                    @endauth
                </div>
            </div>

            <div class="user-products-hero-note" aria-label="Collection highlights">
                <span class="hero-note-icon" aria-hidden="true">
                    <i class="fas fa-wand-magic-sparkles"></i>
                </span>
                <div>
                    <span>Made with feeling</span>
                    <strong>Limited pieces, packed with care.</strong>
                </div>
            </div>
        </section>

        @if($errors->has('cart'))
            <div class="user-products-alert" role="alert">
                <i class="fas fa-circle-exclamation" aria-hidden="true"></i>
                <span>{{ $errors->first('cart') }}</span>
            </div>
        @endif

        <section class="user-products-collection" aria-labelledby="collection-title">
            <header class="user-products-section-heading">
                <div>
                    <span class="section-eyebrow">THE MERCH EDIT</span>
                    <h2 id="collection-title">Find your favorite piece</h2>
                </div>

                <p>
                    {{ $products->total() }} {{ Str::plural('piece', $products->total()) }} in this collection
                </p>
            </header>

            <div class="user-products-grid">
                @forelse($products as $product)
                    <article class="user-product-card">
                        <a
                            href="{{ route('merchandise.show', $product->slug) }}"
                            class="user-product-image-link"
                            aria-label="View {{ $product->name }} details"
                        >
                            <div class="user-product-image-wrap">
                                <x-media-image :src="$product->image" :alt="$product->name"
                                    :width="640" :height="640" crop="fill"
                                    sizes="(max-width: 639px) 94vw, (max-width: 1023px) 46vw, 31vw"
                                    class="user-product-image"
                                    onerror="this.src='https://placehold.co/600x600?text=No+Image'" />

                                <div class="user-product-overlay" aria-hidden="true"></div>

                                <span class="user-product-category">
                                    {{ $product->category ?? 'Dreamy Collection' }}
                                </span>

                                <span class="user-product-stock-badge {{ $product->stock < 1 ? 'is-empty' : ($product->stock <= 5 ? 'is-low' : '') }}">
                                    <span aria-hidden="true"></span>
                                    {{ $product->stock < 1 ? 'Sold out' : ($product->stock <= 5 ? 'Only '.$product->stock.' left' : 'In stock') }}
                                </span>
                            </div>
                        </a>

                        <div class="user-product-body">
                            <div class="user-product-copy">
                                <h3>
                                    <a href="{{ route('merchandise.show', $product->slug) }}">
                                        {{ $product->name }}
                                    </a>
                                </h3>

                                <p>{{ Str::limit($product->description, 92) }}</p>
                            </div>

                            <div class="user-product-price-row">
                                <span class="user-product-price-label">Price</span>
                                <strong class="user-product-price">
                                    Rp {{ number_format($product->price, 0, ',', '.') }}
                                </strong>
                            </div>

                            <div class="user-product-actions">
                                <a
                                    href="{{ route('merchandise.show', $product->slug) }}"
                                    class="user-detail-btn"
                                >
                                    <span>See details</span>
                                    <i class="fas fa-arrow-right" aria-hidden="true"></i>
                                </a>

                                @if($product->has_variants)
                                    <a href="{{ route('merchandise.show', $product->slug) }}" class="user-cart-btn">
                                        <i class="fas fa-sliders" aria-hidden="true"></i>
                                        <span>Choose {{ $product->variant_label }}</span>
                                    </a>
                                @else
                                    <form action="{{ route('cart.add', $product->id) }}" method="POST" class="inline-form">
                                        @csrf
                                        <button type="submit" class="user-cart-btn" @disabled($product->stock < 1)>
                                            <i class="fas {{ $product->stock > 0 ? 'fa-bag-shopping' : 'fa-ban' }}" aria-hidden="true"></i>
                                            <span>{{ $product->stock > 0 ? 'Add to cart' : 'Unavailable' }}</span>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="empty-products">
                        <span class="empty-products-icon" aria-hidden="true">
                            <i class="fas fa-box-open"></i>
                        </span>
                        <span class="section-eyebrow">COMING SOON</span>
                        <h2>The collection is being prepared</h2>
                        <p>New dreamy pieces will appear here soon.</p>
                    </div>
                @endforelse
            </div>
        </section>

        @if($products->hasPages())
            <nav class="media-pagination user-products-pagination" aria-label="Product pages">
                {{ $products->onEachSide(1)->links() }}
            </nav>
        @endif
    </div>
</div>

@if(session('success'))
    <div id="cart-toast" class="products-cart-toast" role="status" aria-live="polite">
        <span class="products-cart-toast-icon" aria-hidden="true">
            <i class="fas fa-check"></i>
        </span>
        <span>
            <strong>Added to your cart</strong>
            <small>{{ session('success') }}</small>
        </span>
        <button type="button" class="products-cart-toast-close" aria-label="Close notification">
            <i class="fas fa-xmark" aria-hidden="true"></i>
        </button>
    </div>

@endif

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const toast = document.getElementById('cart-toast');

        if (toast) {
            const closeButton = toast.querySelector('.products-cart-toast-close');
            const hideToast = () => toast.classList.remove('show');

            requestAnimationFrame(() => toast.classList.add('show'));

            const timeout = window.setTimeout(hideToast, 4000);
            closeButton?.addEventListener('click', () => {
                window.clearTimeout(timeout);
                hideToast();
            });
        }

        document.querySelectorAll('.user-product-actions .inline-form').forEach((form) => {
            form.addEventListener('submit', () => {
                const button = form.querySelector('.user-cart-btn');

                if (!button || button.disabled) return;

                button.disabled = true;
                button.setAttribute('aria-busy', 'true');
                button.querySelector('i')?.classList.replace('fa-bag-shopping', 'fa-spinner');
                button.querySelector('i')?.classList.add('fa-spin');

                const label = button.querySelector('span');
                if (label) label.textContent = 'Adding...';
            });
        });
    });
</script>

</x-app-layout>
