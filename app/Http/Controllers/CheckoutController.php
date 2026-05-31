<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);

        if(empty($cart)) {
            return redirect('/cart');
        }

        return view('products.checkout', compact('cart'));
    }

    public function process(Request $request)
    {
        $cart = session()->get('cart', []);

        if(empty($cart)){
            return response()->json([
                'success' => false
            ]);
        }

        $total = 0;

        foreach($cart as $item){

            $total +=
                $item['price']
                *
                $item['quantity'];

        }

        $order = Order::create([

            'user_id' => Auth::id(),

            'name' => $request->name,

            'email' => $request->email,

            'phone' => $request->phone,

            'address' => $request->address,

            'total_price' => $total,

            'status' => 'pending'

        ]);

        $message =
            "✨ NEW DREAM ORDER ✨\n\n";

        $message .=
            "👤 Name: {$request->name}\n";

        $message .=
            "📧 Email: {$request->email}\n";

        $message .=
            "📱 Phone: {$request->phone}\n\n";

        $message .=
            "📍 Address:\n{$request->address}\n\n";

        $message .=
            "🛍 Products:\n";

        foreach($cart as $productId => $item){

            OrderItem::create([

                'order_id' => $order->id,

                'product_id' => $productId,

                'price' => $item['price'],

                'quantity' => $item['quantity'],

                'subtotal' =>
                    $item['price']
                    *
                    $item['quantity']

            ]);

            $message .=
                "- {$item['name']} x {$item['quantity']}\n";
        }

        $message .=
            "\n💰 Total: Rp "
            .
            number_format(
                $total,
                0,
                ',',
                '.'
            );

        session()->forget('cart');

        $waUrl =
            'https://wa.me/6289646363117?text='
            .
            urlencode($message);

        return response()->json([

            'success' => true,

            'order_id' => $order->id,

            'whatsapp_url' => $waUrl

        ]);
    }
}