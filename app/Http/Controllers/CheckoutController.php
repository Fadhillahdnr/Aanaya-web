<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
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
            $productIds = collect($cart)
                ->map(fn ($item, $key) => $item['product_id'] ?? (is_numeric($key) ? $key : null))
                ->filter()
                ->unique();
            $variantIds = collect($cart)->pluck('variant_id')->filter()->unique();

            $products = Product::with('activeVariants')
                ->whereIn('id', $productIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');
            $variants = ProductVariant::query()
                ->whereIn('id', $variantIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $items = [];
            $total = 0;

            foreach ($cart as $cartKey => $cartItem) {
                $productId = $cartItem['product_id'] ?? (is_numeric($cartKey) ? (int) $cartKey : null);
                $product = $products->get((int) $productId);
                $variant = ! empty($cartItem['variant_id'])
                    ? $variants->get((int) $cartItem['variant_id'])
                    : null;
                $quantity = max(1, (int) ($cartItem['quantity'] ?? 1));

                if (! $product || ! $product->is_active) {
                    throw ValidationException::withMessages([
                        'cart' => 'Salah satu produk sudah tidak tersedia.',
                    ]);
                }

                if ($variant) {
                    if ($variant->product_id !== $product->id || ! $variant->is_active) {
                        throw ValidationException::withMessages([
                            'cart' => "Pilihan untuk {$product->name} sudah tidak tersedia.",
                        ]);
                    }

                    $stock = $variant->stock;
                    $price = (int) round((float) ($variant->price ?? $product->price));
                } else {
                    if ($product->has_variants) {
                        throw ValidationException::withMessages([
                            'cart' => "Pilih {$product->variant_label} untuk {$product->name}.",
                        ]);
                    }

                    $stock = $product->stock;
                    $price = (int) round((float) $product->price);
                }

                if ($stock < $quantity) {
                    throw ValidationException::withMessages([
                        'cart' => "Stok {$product->name}".($variant ? " - {$variant->name}" : '').' tidak mencukupi.',
                    ]);
                }

                $subtotal = $price * $quantity;
                $total += $subtotal;

                $items[] = compact('product', 'variant', 'quantity', 'price', 'subtotal');
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
                    'product_variant_id' => $item['variant']?->id,
                    'variant_label' => $item['variant'] ? $item['product']->variant_label : null,
                    'variant_name' => $item['variant']?->name,
                    'variant_sku' => $item['variant']?->sku,
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'subtotal' => $item['subtotal'],
                ]);

                if ($item['variant']) {
                    $item['variant']->stock -= $item['quantity'];
                    $item['variant']->save();
                } else {
                    // save() deliberately runs model events so cached public product
                    // data is invalidated immediately after a successful checkout.
                    $item['product']->stock -= $item['quantity'];
                    $item['product']->save();
                }
            }

            collect($items)
                ->filter(fn ($item) => $item['variant'])
                ->pluck('product')
                ->unique('id')
                ->each(function ($product) {
                    $product->stock = (int) $product->variants()->where('is_active', true)->sum('stock');
                    $product->save();
                });

            return [$order, $items, $total];
        });

        $message = "✨ NEW DREAM ORDER ✨\n\n";
        $message .= "👤 Name: {$validated['name']}\n";
        $message .= "📧 Email: {$validated['email']}\n";
        $message .= "📱 Phone: {$validated['phone']}\n\n";
        $message .= "📍 Address:\n{$validated['address']}\n\n";
        $message .= "🛍 Products:\n";

        foreach ($items as $item) {
            $variantText = $item['variant'] ? " ({$item['product']->variant_label}: {$item['variant']->name})" : '';
            $message .= "- {$item['product']->name}{$variantText} x {$item['quantity']}\n";
        }

        $message .= "\n💰 Total: Rp ".number_format($total, 0, ',', '.');

        session()->forget('cart');

        $waUrl = 'https://wa.me/6289646363117?text='.urlencode($message);

        return response()->json([

            'success' => true,

            'order_id' => $order->id,

            'whatsapp_url' => $waUrl,

            'order_url' => route('orders.show', $order),
        ]);
    }
}
