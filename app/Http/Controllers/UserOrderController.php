<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserOrderController extends Controller
{
    public function index(Request $request): View
    {
        $orders = $request->user()
            ->orders()
            ->withCount('items')
            ->latest()
            ->paginate(10);

        return view('user.orders.index', compact('orders'));
    }

    public function show(Request $request, Order $order): View
    {
        abort_unless($order->user_id === $request->user()->id, 404);

        $order->load('items.product');

        return view('user.orders.show', compact('order'));
    }

    public function status(Request $request, Order $order): JsonResponse
    {
        abort_unless($order->user_id === $request->user()->id, 404);

        return response()->json([
            'status' => $order->status,
            'updated_at' => $order->updated_at->toIso8601String(),
        ]);
    }
}
