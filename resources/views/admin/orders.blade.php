@extends('admin.layouts.admin')

@section('content')

<div class="orders-page">

<!-- =========================
     HERO SECTION
========================== -->
<div class="orders-top">

    <div class="orders-heading">

        <div class="orders-badge">
            <i class="fas fa-shopping-bag"></i>
            Order Management
        </div>

        <h1>Customer Orders ✨</h1>

        <p>
            Monitor customer purchases, track order progress,
            and manage every transaction from one beautiful dashboard.
        </p>

    </div>

</div>

<!-- =========================
     STATS
========================== -->
<div class="orders-stats">

    <div class="stat-card">
        <h3>Pending Orders</h3>
        <div class="value">{{ $pending }}</div>
    </div>

    <div class="stat-card">
        <h3>Processing</h3>
        <div class="value">{{ $processing }}</div>
    </div>

    <div class="stat-card">
        <h3>Completed</h3>
        <div class="value">{{ $completed }}</div>
    </div>

    <div class="stat-card">
        <h3>Cancelled</h3>
        <div class="value">{{ $cancelled }}</div>
    </div>

</div>

<!-- =========================
     TABLE
========================== -->
<div class="orders-table-card">

    @if($orders->count())

        <table class="orders-table">

            <thead>
                <tr>
                    <th>#</th>
                    <th>Customer</th>
                    <th>Phone</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th width="120">Action</th>
                </tr>
            </thead>

            <tbody>

                @foreach($orders as $order)

                <tr>

                    <td>
                        #{{ $order->id }}
                    </td>

                    <td>
                        <strong>{{ $order->name }}</strong>
                        <br>
                        <small>{{ $order->email }}</small>
                    </td>

                    <td>
                        {{ $order->phone }}
                    </td>

                    <td>
                        Rp {{ number_format($order->total_price,0,',','.') }}
                    </td>

                    <td>

                        <span class="status {{ $order->status }}">
                            {{ ucfirst($order->status) }}
                        </span>

                    </td>

                    <td>
                        {{ $order->created_at->format('d M Y') }}
                    </td>

                    <td>

                        <a
                            href="{{ route('admin.orders.show',$order->id) }}"
                            class="order-view-btn">

                            <i class="fas fa-eye"></i>
                            View

                        </a>

                    </td>

                </tr>

                @endforeach

            </tbody>

        </table>

    @else

        <div class="orders-empty">

            <i class="fas fa-box-open"></i>

            <h3>No Orders Yet</h3>

            <p>
                Customer orders will appear here once checkout is completed.
            </p>

        </div>

    @endif

</div>

<!-- =========================
     PAGINATION
========================== -->

@if($orders->hasPages())

    <div class="orders-pagination">
        {{ $orders->links() }}
    </div>

@endif

</div>

@endsection
