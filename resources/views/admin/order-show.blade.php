@extends('admin.layouts.admin')

@section('content')

<div class="order-show-page">

    <!-- =========================================
         PAGE HEADER
    ========================================= -->
    <div class="order-show-top">

        <div class="order-show-heading">

            <span class="order-show-badge">

                <i class="fas fa-receipt"></i>

                ORDER MANAGEMENT

            </span>

            <h1>

                Order #{{ $order->id }}

            </h1>

            <p class="order-show-subtitle">

                View customer details, purchased products,
                and manage order fulfillment status.

            </p>

        </div>

        <div class="order-show-status-badge {{ strtolower($order->status) }}">

            {{ ucfirst($order->status) }}

        </div>

    </div>

    <!-- =========================================
         CUSTOMER INFORMATION
    ========================================= -->
    <div class="order-show-card">

        <div class="order-show-card-header">

            <h2>

                <i class="fas fa-user-circle"></i>

                Customer Information

            </h2>

            <p>
                Buyer profile and shipping details.
            </p>

        </div>

        <div class="order-show-customer-grid">

            <div class="order-show-info-box">

                <span>Name</span>

                <h4>{{ $order->name }}</h4>

            </div>

            <div class="order-show-info-box">

                <span>Email</span>

                <h4>{{ $order->email }}</h4>

            </div>

            <div class="order-show-info-box">

                <span>Phone</span>

                <h4>{{ $order->phone }}</h4>

            </div>

            <div class="order-show-info-box order-show-address">

                <span>Address</span>

                <h4>{{ $order->address }}</h4>

            </div>

        </div>

    </div>

    <!-- =========================================
         PRODUCTS
    ========================================= -->
    <div class="order-show-card">

        <div class="order-show-card-header">

            <h2>

                <i class="fas fa-shopping-bag"></i>

                Ordered Products

            </h2>

            <p>
                Products included in this order.
            </p>

        </div>

        <div class="order-show-table-wrapper">

            <table class="order-show-table">

                <thead>

                    <tr>

                        <th>Product</th>

                        <th>Quantity</th>

                        <th>Price</th>

                        <th>Subtotal</th>

                    </tr>

                </thead>

                <tbody>

                    @foreach($order->items as $item)

                    <tr>

                        <td data-label="Product">

                            <div class="order-show-product">

                                <div class="order-show-product-icon">

                                    <i class="fas fa-box"></i>

                                </div>

                                <span>
                                    {{ $item->product?->name ?? 'Product no longer available' }}
                                    @if($item->variant_name)
                                        <small>{{ $item->variant_label ?? 'Option' }}: {{ $item->variant_name }}{{ $item->variant_sku ? ' · '.$item->variant_sku : '' }}</small>
                                    @endif
                                </span>

                            </div>

                        </td>

                        <td data-label="Quantity">

                            {{ $item->quantity }}

                        </td>

                        <td data-label="Price">

                            Rp {{ number_format($item->price) }}

                        </td>

                        <td data-label="Subtotal">

                            <strong>

                                Rp {{ number_format($item->subtotal) }}

                            </strong>

                        </td>

                    </tr>

                    @endforeach

                </tbody>

            </table>

        </div>

    </div>

    <!-- =========================================
         ORDER STATUS
    ========================================= -->
    <div class="order-show-card">

        <div class="order-show-card-header">

            <h2>

                <i class="fas fa-truck"></i>

                Update Order Status

            </h2>

            <p>

                Change the order progress and
                fulfillment stage.

            </p>

        </div>

        <form
            method="POST"
            action="{{ route('admin.orders.status',$order->id) }}"
            class="order-show-form">

            @csrf
            @method('PUT')

            <div class="order-show-group">

                <label>

                    Order Status

                </label>

                <select
                    name="status"
                    class="order-show-select">

                    <option
                        value="pending"
                        {{ $order->status == 'pending' ? 'selected' : '' }}>

                        Pending

                    </option>

                    <option
                        value="processing"
                        {{ $order->status == 'processing' ? 'selected' : '' }}>

                        Processing

                    </option>

                    <option
                        value="shipped"
                        {{ $order->status == 'shipped' ? 'selected' : '' }}>

                        Shipped

                    </option>

                    <option
                        value="completed"
                        {{ $order->status == 'completed' ? 'selected' : '' }}>

                        Completed

                    </option>

                    <option
                        value="cancelled"
                        {{ $order->status == 'cancelled' ? 'selected' : '' }}>

                        Cancelled

                    </option>

                </select>

            </div>

            <button
                type="submit"
                class="order-show-btn">

                <i class="fas fa-save"></i>

                Update Status

            </button>

        </form>

    </div>

</div>

@endsection
