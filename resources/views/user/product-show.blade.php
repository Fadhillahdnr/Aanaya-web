<x-app-layout>

@php
    $productMedia = $product->gallery_items
        ->merge($product->activeVariants->whereNotNull('image')->map(fn ($variant) => [
            'type' => 'image',
            'url' => $variant->image,
            'thumbnail' => $variant->image,
        ]))
        ->unique(fn ($item) => $item['type'].'|'.$item['url'])
        ->values();
@endphp

<div class="user-product-detail-page">

    <div class="user-products-bg glow-1"></div>
    <div class="user-products-bg glow-2"></div>

    <section class="user-product-detail-container fade-in-up">

        <div class="user-product-gallery" data-product-gallery data-image-count="{{ $productMedia->count() }}">
            <div class="user-product-detail-image-wrap" data-product-gallery-viewport>
                <div class="user-product-gallery-track" data-product-gallery-track>
                    @foreach($productMedia as $media)
                        <div class="user-product-gallery-slide" data-product-gallery-slide data-media-type="{{ $media['type'] }}" data-original-src="{{ $media['url'] }}" data-image-src="{{ $media['type'] === 'image' ? \App\Support\MediaUrl::image($media['url'], 1600, null, 'limit') : '' }}">
                            @if($media['type'] === 'video')
                                <div class="user-product-video-stage" data-product-video-stage>
                                    <img class="user-product-video-backdrop" src="{{ $media['thumbnail'] }}" alt="" aria-hidden="true">
                                    <video class="user-product-detail-image user-product-detail-video" controls playsinline preload="metadata" poster="{{ $media['thumbnail'] }}" data-product-video aria-label="{{ $product->name }} product video {{ $loop->iteration }}">
                                        <source src="{{ \App\Support\MediaUrl::video($media['url']) }}">
                                        Your browser does not support this product video.
                                    </video>
                                    <button type="button" class="user-product-video-play" data-product-video-play aria-label="Play {{ $product->name }} product video">
                                        <span><i class="fas fa-play" aria-hidden="true"></i></span>
                                        <small>Play video</small>
                                    </button>
                                </div>
                            @else
                                <button type="button" class="user-product-gallery-image-button" data-open-product-image aria-label="Open {{ $product->name }} photo {{ $loop->iteration }} of {{ $productMedia->count() }}">
                                    <x-media-image :src="$media['url']" :alt="$product->name.' photo '.$loop->iteration"
                                        :width="960" :height="960" crop="fill" sizes="(max-width: 800px) 94vw, 50vw"
                                        class="user-product-detail-image" :priority="$loop->first"
                                        onerror="this.src='https://placehold.co/600x600?text=No+Image'" />
                                </button>
                            @endif
                        </div>
                    @endforeach
                </div>

                @if($productMedia->count() > 1)
                    <button type="button" class="user-product-gallery-arrow user-product-gallery-arrow--previous" data-product-gallery-previous aria-label="Previous product photo"><i class="fas fa-chevron-left" aria-hidden="true"></i></button>
                    <button type="button" class="user-product-gallery-arrow user-product-gallery-arrow--next" data-product-gallery-next aria-label="Next product photo"><i class="fas fa-chevron-right" aria-hidden="true"></i></button>
                    <div class="user-product-gallery-counter" aria-live="polite"><span data-product-gallery-current>1</span> / {{ $productMedia->count() }}</div>
                @endif

                <div class="user-product-detail-overlay-light"></div>
                <span class="user-product-gallery-zoom-hint"><i class="fas fa-expand" aria-hidden="true"></i> View photo</span>
            </div>

            @if($productMedia->count() > 1)
                <div class="user-product-gallery-thumbnails" aria-label="Choose product media">
                    @foreach($productMedia as $media)
                        <button type="button" class="user-product-gallery-thumbnail {{ $loop->first ? 'is-active' : '' }}" data-product-gallery-thumbnail="{{ $loop->index }}" aria-label="Show product {{ $media['type'] }} {{ $loop->iteration }}" aria-current="{{ $loop->first ? 'true' : 'false' }}">
                            <x-media-image :src="$media['thumbnail']" alt="" :width="160" :height="160" crop="fill" />
                            @if($media['type'] === 'video')<i class="fas fa-play" aria-hidden="true"></i>@endif
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="user-product-detail-content">

            <span class="user-product-detail-badge">
                <i class="fas fa-sparkles"></i> DREAM PRODUCT
            </span>

            <h1>
                {{ $product->name }}
            </h1>

            <div class="user-product-detail-price">
                <span data-product-price>Rp {{ number_format($product->price, 0, ',', '.') }}</span>
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
                    <span data-product-stock>{{ $product->stock }} Stock In</span>
                </span>
            </div>

            <p class="user-product-description">
                {{ $product->description }}
            </p>

            @if($product->has_variants)
                <fieldset class="user-product-variant-selector" data-product-variant-selector>
                    <legend>Choose {{ $product->variant_label }}</legend>
                    <p>Select one option before adding this product to your cart.</p>
                    <div class="user-product-variant-options">
                        @foreach($product->activeVariants as $variant)
                            <label class="user-product-variant-option {{ $variant->stock < 1 ? 'is-sold-out' : '' }}">
                                <input type="radio" name="variant_choice" value="{{ $variant->id }}"
                                    data-variant-choice
                                    data-price="{{ (int) round((float) $variant->effective_price) }}"
                                    data-stock="{{ $variant->stock }}"
                                    data-image="{{ $variant->image }}"
                                    @disabled($variant->stock < 1)>
                                @if($variant->image)
                                    <x-media-image :src="$variant->image" alt="" :width="96" :height="96" crop="fill" />
                                @endif
                                <span><strong>{{ $variant->name }}</strong><small>{{ $variant->stock > 0 ? $variant->stock.' in stock' : 'Sold out' }}</small></span>
                                @if($variant->price !== null && (float) $variant->price !== (float) $product->price)
                                    <b>Rp {{ number_format($variant->price, 0, ',', '.') }}</b>
                                @endif
                            </label>
                        @endforeach
                    </div>
                </fieldset>
            @endif

            @if($errors->has('cart'))
                <div class="direct-upload-status direct-upload-status--error">
                    {{ $errors->first('cart') }}
                </div>
            @endif

            <div class="user-product-detail-actions">
                <form action="{{ route('cart.add', $product->id) }}" method="POST" class="inline-form">
                    @csrf
                    @if($product->has_variants)
                        <input type="hidden" name="variant_id" value="" data-selected-variant>
                    @endif
                    <button type="submit" class="user-cart-btn-lg" data-product-add-button @disabled($product->stock < 1 || $product->has_variants)>
                        <i class="fas fa-cart-plus"></i>
                        <span>{{ $product->stock < 1 ? 'Out of Stock' : ($product->has_variants ? 'Select an option' : 'Add To Cart') }}</span>
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

<dialog class="user-product-lightbox" data-product-lightbox aria-label="Product photo viewer">
    <div class="user-product-lightbox-shell">
        <button type="button" class="user-product-lightbox-close" data-product-lightbox-close aria-label="Close photo viewer"><i class="fas fa-times" aria-hidden="true"></i></button>
        <button type="button" class="user-product-lightbox-arrow user-product-lightbox-arrow--previous" data-product-lightbox-previous aria-label="Previous photo"><i class="fas fa-chevron-left" aria-hidden="true"></i></button>
        <img src="" alt="" data-product-lightbox-image>
        <button type="button" class="user-product-lightbox-arrow user-product-lightbox-arrow--next" data-product-lightbox-next aria-label="Next photo"><i class="fas fa-chevron-right" aria-hidden="true"></i></button>
        <div class="user-product-lightbox-counter" data-product-lightbox-counter></div>
    </div>
</dialog>

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
