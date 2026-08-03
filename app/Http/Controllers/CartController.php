<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        $productIds = collect($cart)
            ->map(fn ($item, $key) => $item['product_id'] ?? (is_numeric($key) ? $key : null))
            ->filter()
            ->unique();
        $variantIds = collect($cart)->pluck('variant_id')->filter()->unique();

        $products = Product::with('activeVariants')->whereIn('id', $productIds)->get()->keyBy('id');
        $variants = ProductVariant::with('product')->whereIn('id', $variantIds)->get()->keyBy('id');

        foreach ($cart as $key => &$item) {
            $productId = $item['product_id'] ?? (is_numeric($key) ? (int) $key : null);
            $product = $products->get((int) $productId);
            $variant = ! empty($item['variant_id']) ? $variants->get((int) $item['variant_id']) : null;

            $item['product_id'] = $productId;

            if ($variant && $variant->product_id === $product?->id) {
                $item['name'] = $product->name;
                $item['variant_name'] = $variant->name;
                $item['variant_label'] = $product->variant_label;
                $item['price'] = (int) round((float) $variant->effective_price);
                $item['image'] = $variant->display_image;
                $item['stock'] = $variant->stock;
                $item['available'] = (bool) ($product->is_active && $variant->is_active && $variant->stock > 0);
            } else {
                $item['stock'] = $product?->stock ?? 0;
                $item['available'] = (bool) ($product?->is_active && ! $product->has_variants && $product->stock > 0);
                if ($product) {
                    $item['name'] = $product->name;
                    $item['price'] = (int) round((float) $product->price);
                    $item['image'] = $product->image;
                }
            }
        }
        unset($item);

        session()->put('cart', $cart);

        return view('products.cart', compact('cart'));
    }

    public function add(Request $request, $id)
    {
        $validated = $request->validate([
            'variant_id' => ['nullable', 'integer'],
        ]);

        $product = Product::with('activeVariants')
            ->whereKey($id)
            ->where('is_active', true)
            ->firstOrFail();

        $variant = null;
        if ($product->has_variants) {
            if (empty($validated['variant_id'])) {
                return back()->withErrors(['cart' => "Pilih {$product->variant_label} terlebih dahulu."]);
            }

            $variant = $product->activeVariants->firstWhere('id', (int) $validated['variant_id']);
            if (! $variant) {
                return back()->withErrors(['cart' => 'Pilihan product tidak tersedia.']);
            }
        }

        $stock = $variant?->stock ?? $product->stock;
        if ($stock < 1) {
            return back()->withErrors(['cart' => 'Pilihan product sedang kehabisan stok.']);
        }

        $key = $variant ? "p{$product->id}-v{$variant->id}" : "p{$product->id}";
        $cart = session()->get('cart', []);

        if (isset($cart[$key])) {
            if ($cart[$key]['quantity'] >= $stock) {
                return back()->withErrors([
                    'cart' => "Stok {$product->name}".($variant ? " - {$variant->name}" : '')." hanya tersedia {$stock}.",
                ]);
            }

            $cart[$key]['quantity']++;
        } else {
            $cart[$key] = [
                'product_id' => $product->id,
                'variant_id' => $variant?->id,
                'variant_label' => $variant ? $product->variant_label : null,
                'variant_name' => $variant?->name,
                'variant_sku' => $variant?->sku,
                'name' => $product->name,
                'price' => (int) round((float) ($variant?->effective_price ?? $product->price)),
                'image' => $variant?->display_image ?? $product->image,
                'quantity' => 1,
                'stock' => $stock,
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
            throw ValidationException::withMessages(['cart' => 'Produk tidak ditemukan di keranjang.']);
        }

        $item = $cart[$id];
        $productId = $item['product_id'] ?? (is_numeric($id) ? (int) $id : null);
        $product = Product::with('activeVariants')->whereKey($productId)->where('is_active', true)->first();
        $variant = ! empty($item['variant_id'])
            ? ProductVariant::whereKey($item['variant_id'])->where('is_active', true)->first()
            : null;
        $stock = $variant?->stock ?? $product?->stock ?? 0;
        $validSelection = $product && ($variant
            ? $variant->product_id === $product->id
            : ! $product->has_variants);

        if (! $validSelection || $stock < $quantity) {
            throw ValidationException::withMessages([
                'quantity' => $validSelection
                    ? "Stok {$product->name} hanya tersedia {$stock}."
                    : 'Produk atau pilihan variant sudah tidak tersedia.',
            ]);
        }

        $price = (int) round((float) ($variant?->effective_price ?? $product->price));
        $cart[$id]['quantity'] = $quantity;
        $cart[$id]['price'] = $price;
        $cart[$id]['stock'] = $stock;
        $cart[$id]['available'] = true;
        session()->put('cart', $cart);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'subtotal' => $price * $quantity,
                'total' => collect($cart)->sum(fn ($cartItem) => $cartItem['price'] * $cartItem['quantity']),
                'quantity' => $quantity,
                'stock' => $stock,
            ]);
        }

        return redirect()->back();
    }
}
