<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::latest()
            ->paginate(10);

        $pending =
            Order::where(
                'status',
                'pending'
            )->count();

        $processing =
            Order::where(
                'status',
                'processing'
            )->count();

        $completed =
            Order::where(
                'status',
                'completed'
            )->count();

        $cancelled = Order::where('status', 'cancelled')->count();

        $totalRevenue = Order::where('status', 'completed')
            ->sum('total_price');

        $totalOrders = Order::count();

        $averageOrderValue = $totalOrders
        ? $totalRevenue / $totalOrders
        : 0;

        $monthExpression = match (Order::query()->getConnection()->getDriverName()) {
            'pgsql' => 'EXTRACT(MONTH FROM created_at)',
            'sqlite' => "CAST(strftime('%m', created_at) AS INTEGER)",
            default => 'MONTH(created_at)',
        };

        $monthlySales = Order::selectRaw("{$monthExpression} as month, SUM(total_price) as revenue")
            ->whereYear('created_at', now()->year)
            ->groupByRaw($monthExpression)
            ->orderByRaw($monthExpression)
            ->get();

        return view(
            'admin.orders',
            compact(
                'orders',
                'pending',
                'processing',
                'completed',
                'cancelled',
                'totalRevenue',
                'totalOrders',
                'averageOrderValue',
                'monthlySales'
            )
        );
    }

    public function show(Order $order)
    {
        $order->load(
            'items.product'
        );

        return view(
            'admin.order-show',
            compact('order')
        );
    }

    public function updateStatus(
        Request $request,
        Order $order
    ) {
        $validated = $request->validate([
            'status' => ['required', Rule::in(Order::STATUSES)],
        ]);

        $order->update([
            'status' => $validated['status'],
        ]);

        return redirect()
            ->route('admin.orders')
            ->with(
                'success',
                'Status updated successfully.'
            );

    }
}
