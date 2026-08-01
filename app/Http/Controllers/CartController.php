<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);

        $products = Product::query()
            ->whereIn('id', array_keys($cart))
            ->get()
            ->keyBy('id');

        foreach ($cart as $id => &$item) {
            $product = $products->get((int) $id);
            $item['stock'] = $product?->stock ?? 0;
            $item['available'] = (bool) ($product?->is_active && $product->stock > 0);
        }
        unset($item);

        session()->put('cart', $cart);

        return view('products.cart', compact('cart'));
    }

    public function add(Request $request, $id)
    {
        $product = Product::query()
            ->whereKey($id)
            ->where('is_active', true)
            ->firstOrFail();

        if ($product->stock < 1) {
            return back()->withErrors(['cart' => 'Produk sedang kehabisan stok.']);
        }

        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            if ($cart[$id]['quantity'] >= $product->stock) {
                return back()->withErrors([
                    'cart' => "Stok {$product->name} hanya tersedia {$product->stock}.",
                ]);
            }

            $cart[$id]['quantity']++;
        } else {
            $cart[$id] = [
                'name' => $product->name,
                'price' => $product->price,
                'image' => $product->image,
                'quantity' => 1,
                'stock' => $product->stock,
                'available' => true,
            ];
        }

        session()->put('cart', $cart);

        return redirect()->back()->with('success', 'Product added to cart');
    }

    public function remove($id)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }

        return redirect()->back();
    }

    public function updateQuantity(Request $request, $id)
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);
        $quantity = (int) $validated['quantity'];
        $cart = session()->get('cart', []);

        if (! isset($cart[$id])) {
            throw ValidationException::withMessages([
                'cart' => 'Produk tidak ditemukan di keranjang.',
            ]);
        }

        $product = Product::query()
            ->whereKey($id)
            ->where('is_active', true)
            ->first();

        if (! $product || $product->stock < $quantity) {
            throw ValidationException::withMessages([
                'quantity' => $product
                    ? "Stok {$product->name} hanya tersedia {$product->stock}."
                    : 'Produk sudah tidak tersedia.',
            ]);
        }

        $cart[$id]['quantity'] = $quantity;
        $cart[$id]['stock'] = $product->stock;
        $cart[$id]['available'] = true;
        session()->put('cart', $cart);

        if ($request->expectsJson()) {
            $subtotal = $cart[$id]['price'] * $quantity;
            $cartTotal = 0;
            foreach ($cart as $item) {
                $cartTotal += $item['price'] * $item['quantity'];
            }

            return response()->json([
                'success' => true,
                'subtotal' => $subtotal,
                'total' => $cartTotal,
                'quantity' => $quantity,
                'stock' => $product->stock,
            ]);
        }

        return redirect()->back();
    }
}
