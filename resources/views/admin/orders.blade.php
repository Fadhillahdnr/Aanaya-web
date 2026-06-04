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

<div class="orders-kpi-grid">

    <div class="orders-kpi-card">

        <span>Total Revenue</span>

        <h2>
            Rp {{ number_format($totalRevenue,0,',','.') }}
        </h2>

    </div>

    <div class="orders-kpi-card">

        <span>Total Orders</span>

        <h2>
            {{ number_format($totalOrders) }}
        </h2>

    </div>

    <div class="orders-kpi-card">

        <span>Average Order</span>

        <h2>
            Rp {{ number_format($averageOrderValue,0,',','.') }}
        </h2>

    </div>

</div>

<div class="orders-chart-card">

    <div class="orders-chart-header">

        <h3>
            Sales Revenue
        </h3>

    </div>
    
    <div class="orders-chart-container">

        <canvas id="salesChart"></canvas>

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

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

const salesData = @json($monthlySales);

const labels = salesData.map(item => {

    const months = [
        '',
        'Jan',
        'Feb',
        'Mar',
        'Apr',
        'May',
        'Jun',
        'Jul',
        'Aug',
        'Sep',
        'Oct',
        'Nov',
        'Dec'
    ];

    return months[item.month];

});

const revenue = salesData.map(item => item.revenue);

new Chart(
    document.getElementById('salesChart'),
    {
        type:'line',

        data:{
            labels:labels,

            datasets:[{

                label:'Revenue',

                data:revenue,

                tension:.4,

                fill:true,

                borderWidth:3,

                borderColor:'#ff4f95',

                backgroundColor:'rgba(255,79,149,.1)'

            }]
        },

        options:{
            responsive:true,
            maintainAspectRatio:false
        }
    }
);

</script>

@endsection
