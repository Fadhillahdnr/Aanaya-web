<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $products = Product::latest()->get();

        return view('admin.products', compact('products'));
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE PAGE
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        return view('admin.product-create');
    }

    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $request->validate([

            'name' => 'required',
            'image' => 'required|image|mimes:jpg,jpeg,png,webp',
            'price' => 'required|numeric',
            'stock' => 'required|numeric',

        ]);

        /*
        |--------------------------------------------------------------------------
        | UPLOAD IMAGE
        |--------------------------------------------------------------------------
        */

        $imageName = time() . '.' .
            $request->image->extension();

        $request->image->move(
            public_path('uploads/products'),
            $imageName
        );

        /*
        |--------------------------------------------------------------------------
        | SAVE DATABASE
        |--------------------------------------------------------------------------
        */

        Product::create([

            'name' => $request->name,

            'slug' => Str::slug($request->name),

            'image' => $imageName,

            'description' => $request->description,

            'price' => $request->price,

            'stock' => $request->stock,

            'category' => $request->category,

            'is_active' => true,

        ]);

        return redirect('/admin/products')
            ->with('success', 'Product berhasil ditambahkan!');
    }

    /*
    |--------------------------------------------------------------------------
    | EDIT PAGE
    |--------------------------------------------------------------------------
    */

    public function edit($id)
    {
        $product = Product::findOrFail($id);

        return view(
            'admin.product-edit',
            compact('product')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([

            'name' => 'required',
            'price' => 'required|numeric',
            'stock' => 'required|numeric',

        ]);

        /*
        |--------------------------------------------------------------------------
        | IMAGE UPDATE
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('image')) {

            $imageName = time() . '.' .
                $request->image->extension();

            $request->image->move(
                public_path('uploads/products'),
                $imageName
            );

            $product->image = $imageName;
        }

        /*
        |--------------------------------------------------------------------------
        | UPDATE DATA
        |--------------------------------------------------------------------------
        */

        $product->update([

            'name' => $request->name,

            'slug' => Str::slug($request->name),

            'description' => $request->description,

            'price' => $request->price,

            'stock' => $request->stock,

            'category' => $request->category,

            'is_active' => true,

        ]);

        return redirect('/admin/products')
            ->with('success', 'Product berhasil diupdate!');
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */

    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        $product->delete();

        return redirect('/admin/products')
            ->with('success', 'Product berhasil dihapus!');
    }

    public function userIndex()
    {
        $products = Product::latest()->get();

        return view(
            'user.products',
            compact('products')
        );
    }

    public function show($slug)
    {
        $product = Product::where('slug', $slug)->firstOrFail();

        return view(
            'user.product-show',
            compact('product')
        );
    }
}