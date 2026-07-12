<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect('/cart');
        }

        return view('products.checkout', compact('cart'));
    }

    public function process(Request $request)
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return response()->json([
                'success' => false,
                'message' => 'Keranjang belanja kosong.',
            ], 422);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'address' => ['required', 'string', 'min:10', 'max:1000'],
        ]);

        [$order, $items, $total] = DB::transaction(function () use ($cart, $validated) {
            $products = Product::query()
                ->whereIn('id', array_keys($cart))
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $items = [];
            $total = 0;

            foreach ($cart as $productId => $cartItem) {
                $product = $products->get((int) $productId);
                $quantity = max(1, (int) ($cartItem['quantity'] ?? 1));

                if (! $product || ! $product->is_active) {
                    throw ValidationException::withMessages([
                        'cart' => 'Salah satu produk sudah tidak tersedia.',
                    ]);
                }

                if ($product->stock < $quantity) {
                    throw ValidationException::withMessages([
                        'cart' => "Stok {$product->name} tidak mencukupi.",
                    ]);
                }

                $price = (int) round((float) $product->price);
                $subtotal = $price * $quantity;
                $total += $subtotal;

                $items[] = compact('product', 'quantity', 'price', 'subtotal');
            }

            $order = Order::create([
                'user_id' => Auth::id(),
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'address' => $validated['address'],
                'total_price' => $total,
                'status' => 'pending',
            ]);

            foreach ($items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product']->id,
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'subtotal' => $item['subtotal'],
                ]);

                $item['product']->decrement('stock', $item['quantity']);
            }

            return [$order, $items, $total];
        });

        $message = "✨ NEW DREAM ORDER ✨\n\n";
        $message .= "👤 Name: {$validated['name']}\n";
        $message .= "📧 Email: {$validated['email']}\n";
        $message .= "📱 Phone: {$validated['phone']}\n\n";
        $message .= "📍 Address:\n{$validated['address']}\n\n";
        $message .= "🛍 Products:\n";

        foreach ($items as $item) {
            $message .= "- {$item['product']->name} x {$item['quantity']}\n";
        }

        $message .= "\n💰 Total: Rp ".number_format($total, 0, ',', '.');

        session()->forget('cart');

        $waUrl = 'https://wa.me/6289646363117?text='.urlencode($message);

        return response()->json([

            'success' => true,

            'order_id' => $order->id,

            'whatsapp_url' => $waUrl,
        ]);
    }
}
