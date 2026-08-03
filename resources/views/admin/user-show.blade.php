@extends('admin.layouts.admin')

@section('content')
<div class="admin-user-detail-page">
    <nav class="admin-user-detail-breadcrumb" aria-label="Breadcrumb">
        <a href="/admin/users"><i class="fas fa-arrow-left" aria-hidden="true"></i> User Management</a>
        <span aria-hidden="true">/</span>
        <span aria-current="page">User Detail</span>
    </nav>

    <section class="admin-user-detail-hero" aria-labelledby="user-detail-name">
        <div class="admin-user-detail-identity">
            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }} profile photo" class="admin-user-detail-avatar">
            <div>
                <div class="admin-user-detail-label">USER PROFILE</div>
                <h1 id="user-detail-name">{{ $user->name }}</h1>
                <div class="admin-user-detail-meta">
                    <span class="admin-user-detail-role admin-user-detail-role--{{ $user->role }}">{{ ucfirst($user->role) }}</span>
                    <span><i class="fas fa-calendar" aria-hidden="true"></i> Joined {{ $user->created_at->format('d M Y') }}</span>
                </div>
            </div>
        </div>

        <a href="/admin/users/{{ $user->id }}/edit" class="admin-user-detail-edit">
            <i class="fas fa-pen" aria-hidden="true"></i>
            Edit User
        </a>
    </section>

    <section class="admin-user-detail-stats" aria-label="User order summary">
        <article>
            <span>Total Orders</span>
            <strong>{{ $user->orders_count }}</strong>
            <i class="fas fa-bag-shopping" aria-hidden="true"></i>
        </article>
        <article>
            <span>Active Orders</span>
            <strong>{{ $orderStats['active'] }}</strong>
            <i class="fas fa-clock" aria-hidden="true"></i>
        </article>
        <article>
            <span>Completed</span>
            <strong>{{ $orderStats['completed'] }}</strong>
            <i class="fas fa-circle-check" aria-hidden="true"></i>
        </article>
        <article>
            <span>Total Spent</span>
            <strong class="admin-user-detail-money">Rp {{ number_format($orderStats['total_spent'], 0, ',', '.') }}</strong>
            <i class="fas fa-wallet" aria-hidden="true"></i>
        </article>
    </section>

    <div class="admin-user-detail-layout">
        <section class="admin-user-detail-card" aria-labelledby="personal-information-title">
            <div class="admin-user-detail-card-heading">
                <div class="admin-user-detail-card-icon"><i class="fas fa-address-card" aria-hidden="true"></i></div>
                <div>
                    <h2 id="personal-information-title">Personal Information</h2>
                    <p>Contact and personal details provided by this user.</p>
                </div>
            </div>

            <dl class="admin-user-detail-info-grid">
                <div><dt>Email</dt><dd><a href="mailto:{{ $user->email }}">{{ $user->email }}</a></dd></div>
                <div><dt>Phone</dt><dd>{{ $user->phone ?: 'Not provided' }}</dd></div>
                <div><dt>Gender</dt><dd>{{ $user->gender ? str($user->gender)->replace('_', ' ')->title() : 'Not provided' }}</dd></div>
                <div><dt>Date of Birth</dt><dd>{{ $user->date_of_birth?->format('d M Y') ?: 'Not provided' }}</dd></div>
                <div class="admin-user-detail-address"><dt>Address</dt><dd>{{ $user->address ?: 'Not provided' }}</dd></div>
            </dl>
        </section>

        <aside class="admin-user-detail-card admin-user-detail-account" aria-labelledby="account-status-title">
            <div class="admin-user-detail-card-heading">
                <div class="admin-user-detail-card-icon"><i class="fas fa-shield-heart" aria-hidden="true"></i></div>
                <div>
                    <h2 id="account-status-title">Account Status</h2>
                    <p>Identity and profile readiness.</p>
                </div>
            </div>

            <ul class="admin-user-detail-checks">
                <li>
                    <span><i class="fas {{ $user->email_verified_at ? 'fa-circle-check is-complete' : 'fa-circle-exclamation is-pending' }}" aria-hidden="true"></i> Email verification</span>
                    <strong>{{ $user->email_verified_at ? 'Verified' : 'Unverified' }}</strong>
                </li>
                <li>
                    <span><i class="fas {{ $user->google_id ? 'fa-circle-check is-complete' : 'fa-circle-minus is-neutral' }}" aria-hidden="true"></i> Google account</span>
                    <strong>{{ $user->google_id ? 'Connected' : 'Not connected' }}</strong>
                </li>
                <li>
                    <span><i class="fas {{ $user->phone && $user->address ? 'fa-circle-check is-complete' : 'fa-circle-exclamation is-pending' }}" aria-hidden="true"></i> Checkout profile</span>
                    <strong>{{ $user->phone && $user->address ? 'Ready' : 'Incomplete' }}</strong>
                </li>
            </ul>
        </aside>
    </div>

    <section class="admin-user-detail-card admin-user-detail-orders" aria-labelledby="recent-orders-title">
        <div class="admin-user-detail-card-heading admin-user-detail-orders-heading">
            <div>
                <h2 id="recent-orders-title">Recent Orders</h2>
                <p>The latest orders placed by this user.</p>
            </div>
            @if($user->orders_count > 8)
                <a href="/admin/orders">View all orders</a>
            @endif
        </div>

        @if($user->orders->isEmpty())
            <div class="admin-user-detail-empty">
                <i class="fas fa-bag-shopping" aria-hidden="true"></i>
                <h3>No orders yet</h3>
                <p>This user has not placed an order.</p>
            </div>
        @else
            <div class="admin-user-detail-table-wrap">
                <table class="admin-user-detail-table">
                    <thead><tr><th>Order</th><th>Date</th><th>Items</th><th>Total</th><th>Status</th><th><span class="sr-only">Action</span></th></tr></thead>
                    <tbody>
                        @foreach($user->orders as $order)
                            <tr>
                                <td data-label="Order"><strong>#{{ $order->id }}</strong></td>
                                <td data-label="Date">{{ $order->created_at->format('d M Y') }}</td>
                                <td data-label="Items">{{ $order->items_count }}</td>
                                <td data-label="Total">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                                <td data-label="Status"><span class="admin-user-order-status admin-user-order-status--{{ $order->status }}">{{ ucfirst($order->status) }}</span></td>
                                <td data-label="Action"><a href="{{ route('admin.orders.show', $order) }}" aria-label="View order number {{ $order->id }}">View order <i class="fas fa-arrow-right" aria-hidden="true"></i></a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
</div>
@endsection
