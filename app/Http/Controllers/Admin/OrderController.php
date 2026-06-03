<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

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

        $cancelled = Order::where('status','cancelled')->count();

        return view(
            'admin.orders',
            compact(
                'orders',
                'pending',
                'processing',
                'completed',
                'cancelled'
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
    ){

        $order->update([
            'status' =>
                $request->status
        ]);

        return back()->with(
            'success',
            'Status updated'
        );
    }
}