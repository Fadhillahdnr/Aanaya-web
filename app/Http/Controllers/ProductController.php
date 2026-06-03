<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Cloudinary\Cloudinary;

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
            'name'  => 'required',
            'image' => 'required|image|mimes:jpg,jpeg,png,webp',
            'price' => 'required|numeric',
            'stock' => 'required|numeric',
        ]);

        $cloudinary = app(Cloudinary::class);

        $upload = $cloudinary
            ->uploadApi()
            ->upload(
                $request->file('image')->getRealPath(),
                [
                    'folder' => 'products'
                ]
            );

        Product::create([
            'name' => $request->name,

            'slug' => Str::slug($request->name),

            'image' => $upload['secure_url'],

            'image_public_id' => $upload['public_id'],

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
            'name'  => 'required',
            'price' => 'required|numeric',
            'stock' => 'required|numeric',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp',
        ]);

        $data = [
            'name' => $request->name,

            'slug' => Str::slug($request->name),

            'description' => $request->description,

            'price' => $request->price,

            'stock' => $request->stock,

            'category' => $request->category,
        ];

        if ($request->hasFile('image')) {

            $cloudinary = app(Cloudinary::class);

            /*
            |--------------------------------------------------------------------------
            | DELETE OLD IMAGE
            |--------------------------------------------------------------------------
            */

            if (!empty($product->image_public_id)) {

                try {

                    $cloudinary
                        ->uploadApi()
                        ->destroy(
                            $product->image_public_id
                        );

                } catch (\Exception $e) {

                    \Log::warning(
                        'Cloudinary delete failed: '
                        . $e->getMessage()
                    );
                }
            }

            /*
            |--------------------------------------------------------------------------
            | UPLOAD NEW IMAGE
            |--------------------------------------------------------------------------
            */

            $upload = $cloudinary
                ->uploadApi()
                ->upload(
                    $request->file('image')->getRealPath(),
                    [
                        'folder' => 'products'
                    ]
                );

            $data['image'] = $upload['secure_url'];

            $data['image_public_id'] =
                $upload['public_id'];
        }

        $product->update($data);

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

        if (!empty($product->image_public_id)) {

            try {

                $cloudinary = app(Cloudinary::class);

                $cloudinary
                    ->uploadApi()
                    ->destroy(
                        $product->image_public_id
                    );

            } catch (\Exception $e) {

                \Log::warning(
                    'Cloudinary delete failed: '
                    . $e->getMessage()
                );
            }
        }

        $product->delete();

        return redirect('/admin/products')
            ->with('success', 'Product berhasil dihapus!');
    }

    /*
    |--------------------------------------------------------------------------
    | USER PRODUCT PAGE
    |--------------------------------------------------------------------------
    */

    public function userIndex()
    {
        $products = Product::latest()->get();

        return view(
            'user.products',
            compact('products')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | PRODUCT DETAIL
    |--------------------------------------------------------------------------
    */

    public function show($slug)
    {
        $product = Product::where(
            'slug',
            $slug
        )->firstOrFail();

        return view(
            'user.product-show',
            compact('product')
        );
    }
}