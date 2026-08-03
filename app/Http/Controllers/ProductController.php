<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\MediaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

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
            'uploaded_media.product_images' => ['required', 'array', 'min:1', 'max:8'],
            'uploaded_media.product_images.*' => ['required', 'string', 'distinct', 'exists:media,id'],
            'price' => 'required|numeric',
            'stock' => ['required', 'integer', 'min:0'],
            'has_variants' => ['nullable', 'boolean'],
            'variant_label' => ['nullable', 'required_if:has_variants,1', 'string', 'max:60'],
            'variants' => ['nullable', 'required_if:has_variants,1', 'array', 'min:1', 'max:30'],
            'variants.*.name' => ['required_with:variants', 'string', 'max:100', 'distinct'],
            'variants.*.sku' => ['nullable', 'string', 'max:100', 'distinct'],
            'variants.*.price' => ['nullable', 'numeric', 'min:0'],
            'variants.*.stock' => ['required_with:variants', 'integer', 'min:0'],
            'variants.*.is_active' => ['nullable', 'boolean'],
            'uploaded_media.variants.*.image' => ['nullable', 'string', 'exists:media,id'],
        ]);

        $this->validateActiveVariants($request);

        $mediaItems = $mediaService->fromRequestMany($request, 'product_images', true);

        if (! $mediaItems->contains('media_type', 'image')) {
            throw ValidationException::withMessages([
                'product_images' => 'Product harus memiliki minimal satu foto sebagai cover.',
            ]);
        }

        $product = DB::transaction(function () use ($request, $mediaItems, $mediaService) {
            $primaryImage = $mediaItems->firstWhere('media_type', 'image');

            $product = Product::create([
                'name' => $request->name,

                'slug' => Str::slug($request->name),

                'image' => $primaryImage->secure_url,

                'image_public_id' => $primaryImage->public_id,

                'description' => $request->description,

                'price' => $request->price,

                'stock' => $request->boolean('has_variants') ? 0 : $request->stock,

                'category' => $request->category,

                'variant_label' => $request->boolean('has_variants') ? $request->variant_label : null,

                'is_active' => true,
            ]);

            $mediaItems->each(fn ($media, $index) => $mediaService->claim($media, $product, 'product_images', $index));

            if ($request->boolean('has_variants')) {
                $this->syncVariants($request, $product, $mediaService);
            }

            return $product;
        });

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
        $product = Product::with(['galleryMedia', 'variants'])->findOrFail($id);

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
            'stock' => ['required', 'integer', 'min:0'],
            'uploaded_media.product_images' => ['nullable', 'array', 'max:8'],
            'uploaded_media.product_images.*' => ['required', 'string', 'distinct', 'exists:media,id'],
            'delete_media_ids' => ['nullable', 'array'],
            'delete_media_ids.*' => ['required', 'string', 'distinct', 'exists:media,id'],
            'has_variants' => ['nullable', 'boolean'],
            'variant_label' => ['nullable', 'required_if:has_variants,1', 'string', 'max:60'],
            'variants' => ['nullable', 'required_if:has_variants,1', 'array', 'min:1', 'max:30'],
            'variants.*.id' => ['nullable', 'integer', 'exists:product_variants,id'],
            'variants.*.name' => ['required_with:variants', 'string', 'max:100', 'distinct'],
            'variants.*.sku' => ['nullable', 'string', 'max:100', 'distinct'],
            'variants.*.price' => ['nullable', 'numeric', 'min:0'],
            'variants.*.stock' => ['required_with:variants', 'integer', 'min:0'],
            'variants.*.is_active' => ['nullable', 'boolean'],
            'uploaded_media.variants.*.image' => ['nullable', 'string', 'exists:media,id'],
        ]);

        $this->validateActiveVariants($request);

        $newMedia = $mediaService->fromRequestMany($request, 'product_images');
        $deleteIds = collect($request->input('delete_media_ids', []))->unique()->values();
        $existingMedia = $product->galleryMedia;
        $deletableMedia = $existingMedia->whereIn('id', $deleteIds);
        $hasUntrackedLegacyImage = $product->image
            && ! $existingMedia->contains(fn ($media) => $media->secure_url === $product->image);

        if (($existingMedia->count() - $deletableMedia->count() + $newMedia->count()) < 1 && ! $hasUntrackedLegacyImage) {
            throw ValidationException::withMessages([
                'product_images' => 'Product harus memiliki minimal satu media.',
            ]);
        }

        $remainingImageCount = $existingMedia
            ->where('media_type', 'image')
            ->whereNotIn('id', $deleteIds)
            ->count() + $newMedia->where('media_type', 'image')->count();

        if ($remainingImageCount < 1 && ! $hasUntrackedLegacyImage) {
            throw ValidationException::withMessages([
                'product_images' => 'Product harus memiliki minimal satu foto sebagai cover.',
            ]);
        }

        $retainedCount = $existingMedia->count() - $deletableMedia->count() + ($hasUntrackedLegacyImage ? 1 : 0);
        if (($retainedCount + $newMedia->count()) > 8) {
            throw ValidationException::withMessages([
                'product_images' => 'Maksimal delapan foto untuk setiap product.',
            ]);
        }

        $data = [
            'name' => $request->name,

            'slug' => Str::slug($request->name),

            'description' => $request->description,

            'price' => $request->price,

            'stock' => $request->stock,

            'category' => $request->category,

            'variant_label' => $request->boolean('has_variants') ? $request->variant_label : null,
        ];

        DB::transaction(function () use ($request, $product, $data, $deletableMedia, $newMedia, $mediaService) {
            $deletableMedia->each->delete();

            $nextSortOrder = (int) ($product->galleryMedia()->max('sort_order') ?? -1) + 1;
            $newMedia->each(function ($media, $index) use ($mediaService, $product, $nextSortOrder) {
                $mediaService->claim($media, $product, 'product_images', $nextSortOrder + $index);
            });

            $primaryMedia = $product->galleryMedia()->where('media_type', 'image')->first();
            if ($primaryMedia) {
                $data['image'] = $primaryMedia->secure_url;
                $data['image_public_id'] = $primaryMedia->public_id;
            }

            $product->update($data);

            if ($request->boolean('has_variants')) {
                $this->syncVariants($request, $product, $mediaService);
            } else {
                $product->variants()->update(['is_active' => false]);
                $product->update(['stock' => (int) $request->stock]);
            }

            $deletableMedia->each(fn ($media) => $mediaService->queueDelete($media->public_id, $media->resource_type));
        });

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
        $product = Product::with(['galleryMedia', 'variants'])->findOrFail($id);

        $product->galleryMedia
            ->unique('public_id')
            ->each(fn ($media) => $mediaService->queueDelete($media->public_id, $media->resource_type));
        if ($product->image_public_id && ! $product->galleryMedia->contains('public_id', $product->image_public_id)) {
            $mediaService->queueDelete($product->image_public_id);
        }
        $product->variants
            ->pluck('image_public_id')
            ->filter()
            ->unique()
            ->each(fn ($publicId) => $mediaService->queueDelete($publicId));
        $product->variants->each->delete();
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
        $products = Product::with('activeVariants')->where('is_active', true)->latest()->paginate(12);

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
        $product = Product::with(['galleryMedia', 'activeVariants'])->where(
            'slug',
            $slug
        )->where('is_active', true)->firstOrFail();

        return view(
            'user.product-show',
            compact('product')
        );
    }

    private function syncVariants(Request $request, Product $product, MediaService $mediaService): void
    {
        $submittedIds = collect();

        foreach (array_values($request->input('variants', [])) as $index => $variantData) {
            $variantId = $variantData['id'] ?? null;
            $variant = $variantId
                ? $product->variants()->whereKey($variantId)->first()
                : new ProductVariant(['product_id' => $product->id]);

            if (! $variant) {
                throw ValidationException::withMessages([
                    "variants.{$index}.id" => 'Variant tidak termasuk dalam product ini.',
                ]);
            }

            $variant->fill([
                'name' => $variantData['name'],
                'sku' => ($variantData['sku'] ?? '') ?: null,
                'price' => ($variantData['price'] ?? '') !== '' ? $variantData['price'] : null,
                'stock' => (int) $variantData['stock'],
                'is_active' => (bool) ($variantData['is_active'] ?? false),
                'sort_order' => $index,
            ]);

            $mediaId = data_get($request->input('uploaded_media', []), "variants.{$index}.image");
            $newImage = $mediaId ? $mediaService->readyOwnedMedia((string) $mediaId) : null;
            $oldPublicId = $variant->image_public_id;

            if ($newImage) {
                $variant->image = $newImage->secure_url;
                $variant->image_public_id = $newImage->public_id;
            }

            $variant->save();
            $submittedIds->push($variant->id);

            if ($newImage) {
                $mediaService->claim($newImage, $variant, 'product_variant_image');
                if ($oldPublicId && $oldPublicId !== $newImage->public_id) {
                    $mediaService->queueDelete($oldPublicId);
                }
            }
        }

        $product->variants()->whereNotIn('id', $submittedIds)->update(['is_active' => false]);
        $product->update([
            'stock' => (int) $product->variants()->where('is_active', true)->sum('stock'),
        ]);
    }

    private function validateActiveVariants(Request $request): void
    {
        if ($request->boolean('has_variants') && ! collect($request->input('variants', []))->contains(
            fn ($variant) => (bool) ($variant['is_active'] ?? false)
        )) {
            throw ValidationException::withMessages([
                'variants' => 'Tambahkan minimal satu variant yang aktif.',
            ]);
        }
    }
}
