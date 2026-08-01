<x-app-layout>

<main class="user-orders-page">
    <div class="user-orders-glow user-orders-glow--one"></div>
    <div class="user-orders-glow user-orders-glow--two"></div>

    <section class="user-orders-hero">
        <span class="user-orders-eyebrow">
            <i class="fas fa-receipt" aria-hidden="true"></i>
            MY ORDERS
        </span>
        <h1>Track Your <span>Dream Orders</span></h1>
        <p>See every Aanaya purchase and follow its latest fulfillment status.</p>

        <a href="{{ route('merchandise') }}" class="user-orders-secondary-btn">
            <i class="fas fa-arrow-left" aria-hidden="true"></i>
            Continue Shopping
        </a>
    </section>

    <section class="user-orders-list" aria-label="Order history">
        @forelse($orders as $order)
            @php
                $statusMeta = match ($order->status) {
                    'processing' => ['Preparing order', 'fa-box-open'],
                    'shipped' => ['On the way', 'fa-truck-fast'],
                    'completed' => ['Completed', 'fa-circle-check'],
                    'cancelled' => ['Cancelled', 'fa-circle-xmark'],
                    default => ['Awaiting confirmation', 'fa-clock'],
                };
            @endphp

            <article class="user-order-card">
                <div class="user-order-card-main">
                    <div class="user-order-icon" aria-hidden="true">
                        <i class="fas {{ $statusMeta[1] }}"></i>
                    </div>

                    <div>
                        <div class="user-order-number">Order #{{ $order->id }}</div>
                        <p>{{ $order->items_count }} item · {{ $order->created_at->format('d M Y, H:i') }}</p>
                    </div>
                </div>

                <div class="user-order-card-status">
                    <span class="user-order-status user-order-status--{{ $order->status }}">
                        {{ $statusMeta[0] }}
                    </span>
                    <strong>Rp {{ number_format($order->total_price, 0, ',', '.') }}</strong>
                </div>

                <a href="{{ route('orders.show', $order) }}" class="user-orders-primary-btn">
                    View Order
                    <i class="fas fa-arrow-right" aria-hidden="true"></i>
                </a>
            </article>
        @empty
            <div class="user-orders-empty">
                <div class="user-orders-empty-icon"><i class="fas fa-bag-shopping" aria-hidden="true"></i></div>
                <h2>No orders yet</h2>
                <p>Your completed checkouts will appear here so you can track every update.</p>
                <a href="{{ route('merchandise') }}" class="user-orders-primary-btn">Explore Merchandise</a>
            </div>
        @endforelse
    </section>

    @if($orders->hasPages())
        <div class="media-pagination">{{ $orders->onEachSide(1)->links() }}</div>
    @endif
</main>

</x-app-layout>
