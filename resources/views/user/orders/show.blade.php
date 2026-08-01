<x-app-layout>

@php
    $steps = [
        'pending' => ['Order received', 'We received your order and are waiting for confirmation.', 'fa-receipt'],
        'processing' => ['Preparing order', 'Your Aanaya merchandise is being carefully prepared.', 'fa-box-open'],
        'shipped' => ['Order shipped', 'Your package is on its way to the delivery address.', 'fa-truck-fast'],
        'completed' => ['Order completed', 'Your order has reached the end of its journey.', 'fa-circle-check'],
    ];
    $statusOrder = array_keys($steps);
    $currentIndex = array_search($order->status, $statusOrder, true);
    $currentIndex = $currentIndex === false ? -1 : $currentIndex;
@endphp

<main
    class="user-orders-page"
    data-order-tracker
    data-current-status="{{ $order->status }}"
    data-status-url="{{ route('orders.status', $order) }}">
    <div class="user-orders-glow user-orders-glow--one"></div>

    <section class="user-order-detail-header">
        <a href="{{ route('orders.index') }}" class="user-orders-back-link">
            <i class="fas fa-arrow-left" aria-hidden="true"></i>
            My Orders
        </a>

        <div class="user-order-detail-title">
            <div>
                <span class="user-orders-eyebrow">ORDER DETAILS</span>
                <h1>Order #{{ $order->id }}</h1>
                <p>Placed on {{ $order->created_at->format('d F Y, H:i') }}</p>
            </div>
            <div class="user-order-detail-actions">
                <span class="user-order-status user-order-status--{{ $order->status }}">
                    {{ ucfirst($order->status) }}
                </span>
                <a
                    href="https://wa.me/6289646363117?text={{ urlencode('Halo Aanaya, saya ingin menanyakan pesanan #'.$order->id.'.') }}"
                    target="_blank"
                    rel="noopener"
                    class="user-orders-secondary-btn">
                    <i class="fab fa-whatsapp" aria-hidden="true"></i>
                    Contact Aanaya
                </a>
            </div>
        </div>
    </section>

    @if($order->status === 'cancelled')
        <div class="user-order-cancelled" role="status">
            <i class="fas fa-circle-xmark" aria-hidden="true"></i>
            <div>
                <strong>This order was cancelled</strong>
                <p>Please contact Aanaya through WhatsApp if you need more information.</p>
            </div>
        </div>
    @else
        <section class="user-order-panel" aria-labelledby="tracking-title">
            <div class="user-order-panel-heading">
                <div>
                    <span>LIVE PROGRESS</span>
                    <h2 id="tracking-title">Order Tracking</h2>
                </div>
                <p>Last updated {{ $order->updated_at->diffForHumans() }}</p>
            </div>

            <ol class="user-order-timeline">
                @foreach($steps as $status => [$label, $description, $icon])
                    @php
                        $stepIndex = array_search($status, $statusOrder, true);
                        $state = $stepIndex < $currentIndex ? 'complete' : ($stepIndex === $currentIndex ? 'active' : 'upcoming');
                    @endphp
                    <li class="user-order-step user-order-step--{{ $state }}" @if($state === 'active') aria-current="step" @endif>
                        <div class="user-order-step-marker"><i class="fas {{ $icon }}" aria-hidden="true"></i></div>
                        <div>
                            <strong>{{ $label }}</strong>
                            <p>{{ $description }}</p>
                            @if($state === 'active')<span>Current status</span>@endif
                        </div>
                    </li>
                @endforeach
            </ol>
        </section>
    @endif

    <div class="user-order-detail-grid">
        <section class="user-order-panel">
            <div class="user-order-panel-heading">
                <div><span>SUMMARY</span><h2>Ordered Items</h2></div>
            </div>

            <div class="user-order-items">
                @foreach($order->items as $item)
                    <div class="user-order-item">
                        @if($item->product?->image)
                            <x-media-image :src="$item->product->image" :alt="$item->product->name"
                                :width="160" :height="160" crop="fill" sizes="80px" />
                        @else
                            <div class="user-order-item-placeholder"><i class="fas fa-box" aria-hidden="true"></i></div>
                        @endif
                        <div class="user-order-item-copy">
                            <strong>{{ $item->product?->name ?? 'Product no longer available' }}</strong>
                            <span>{{ $item->quantity }} × Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                        </div>
                        <strong>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</strong>
                    </div>
                @endforeach
            </div>

            <div class="user-order-total">
                <span>Total</span>
                <strong>Rp {{ number_format($order->total_price, 0, ',', '.') }}</strong>
            </div>
        </section>

        <aside class="user-order-panel user-order-shipping">
            <div class="user-order-panel-heading">
                <div><span>DELIVERY</span><h2>Shipping Details</h2></div>
            </div>
            <dl>
                <div><dt>Name</dt><dd>{{ $order->name }}</dd></div>
                <div><dt>Phone</dt><dd>{{ $order->phone }}</dd></div>
                <div><dt>Email</dt><dd>{{ $order->email }}</dd></div>
                <div><dt>Address</dt><dd>{{ $order->address }}</dd></div>
            </dl>
        </aside>
    </div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const tracker = document.querySelector('[data-order-tracker]');
        if (!tracker) return;

        const checkStatus = async () => {
            if (document.hidden) return;

            try {
                const response = await fetch(tracker.dataset.statusUrl, {
                    headers: { 'Accept': 'application/json' },
                });
                if (!response.ok) return;

                const result = await response.json();
                if (result.status !== tracker.dataset.currentStatus) {
                    window.location.reload();
                }
            } catch (_) {
                // A temporary network failure should not interrupt the order page.
            }
        };

        window.setInterval(checkStatus, 30000);
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden) checkStatus();
        });
    });
</script>

</x-app-layout>
