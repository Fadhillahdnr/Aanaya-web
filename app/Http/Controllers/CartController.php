<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);

        return view('products.cart', compact('cart'));
    }

    public function add(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $cart = session()->get('cart', []);

        if(isset($cart[$id])) {
            $cart[$id]['quantity']++;
        } else {
            $cart[$id] = [
                'name' => $product->name,
                'price' => $product->price,
                'image' => $product->image,
                'quantity' => 1
            ];
        }

        session()->put('cart', $cart);

        return redirect()->back()->with('success', 'Product added to cart');
    }

    public function remove($id)
    {
        $cart = session()->get('cart', []);

        if(isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }

        return redirect()->back();
    }

    public function updateQuantity(Request $request, $id)
    {
        $quantity = $request->input('quantity', 1);
        $cart = session()->get('cart', []);

        if(isset($cart[$id])) {
            if($quantity <= 0) {
                unset($cart[$id]);
            } else {
                $cart[$id]['quantity'] = $quantity;
            }
            session()->put('cart', $cart);
        }

        if($request->expectsJson()) {
            $subtotal = $cart[$id]['price'] * $quantity ?? 0;
            $cartTotal = 0;
            foreach($cart as $item) {
                $cartTotal += $item['price'] * $item['quantity'];
            }
            return response()->json([
                'success' => true,
                'subtotal' => $subtotal,
                'total' => $cartTotal
            ]);
        }

        return redirect()->back();
    }
}