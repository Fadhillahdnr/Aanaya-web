<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\MediaService;
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

    public function store(Request $request, MediaService $mediaService)
    {
        $request->validate([
            'name' => 'required',
            'uploaded_media.image' => 'required|string|exists:media,id',
            'price' => 'required|numeric',
            'stock' => 'required|numeric',
        ]);

        $media = $mediaService->fromRequest($request, 'image', true);

        $product = Product::create([
            'name' => $request->name,

            'slug' => Str::slug($request->name),

            'image' => $media->secure_url,

            'image_public_id' => $media->public_id,

            'description' => $request->description,

            'price' => $request->price,

            'stock' => $request->stock,

            'category' => $request->category,

            'is_active' => true,
        ]);

        $mediaService->claim($media, $product, 'image');

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

    public function update(Request $request, $id, MediaService $mediaService)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'stock' => 'required|numeric',
            'uploaded_media.image' => 'nullable|string|exists:media,id',
        ]);

        $data = [
            'name' => $request->name,

            'slug' => Str::slug($request->name),

            'description' => $request->description,

            'price' => $request->price,

            'stock' => $request->stock,

            'category' => $request->category,
        ];

        if ($media = $mediaService->fromRequest($request, 'image')) {
            $oldPublicId = $product->image_public_id;
            $data['image'] = $media->secure_url;
            $data['image_public_id'] = $media->public_id;
        }

        $product->update($data);

        if (isset($media)) {
            $mediaService->claim($media, $product, 'image');
            $mediaService->queueDelete($oldPublicId);
        }

        return redirect('/admin/products')
            ->with('success', 'Product berhasil diupdate!');
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */

    public function destroy($id, MediaService $mediaService)
    {
        $product = Product::findOrFail($id);

        $mediaService->queueDelete($product->image_public_id);
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
        $products = Product::where('is_active', true)->latest()->paginate(12);

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
