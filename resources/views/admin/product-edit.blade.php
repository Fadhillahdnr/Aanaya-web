@extends('admin.layouts.admin')

@section('content')
<div class="product-form-page">
    <div class="page-top">
        <div>
            <h1>Edit Product</h1>
            <p class="page-subtitle">Update product information and manage its photo gallery.</p>
        </div>
        <a href="/admin/products" class="back-btn"><i class="fas fa-arrow-left" aria-hidden="true"></i> Back</a>
    </div>

    <div class="product-form-card">
        @if ($errors->any())
            <div class="alert alert-danger mb-4" role="alert">
                <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        <form data-cloudinary-direct-upload action="/admin/products/{{ $product->id }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="form-grid">
                <div class="form-group">
                    <label for="name">Product Name</label>
                    <input id="name" type="text" name="name" value="{{ old('name', $product->name) }}" required>
                </div>
                <div class="form-group">
                    <label for="category">Category</label>
                    <input id="category" type="text" name="category" value="{{ old('category', $product->category) }}">
                </div>
                <div class="form-group">
                    <label for="price">Price</label>
                    <input id="price" type="number" name="price" value="{{ old('price', $product->price) }}" min="0" required>
                </div>
                <div class="form-group">
                    <label for="stock">Stock</label>
                    <input id="stock" type="number" name="stock" value="{{ old('stock', $product->stock) }}" min="0" required>
                </div>
                <div class="form-group full-width product-type-field">
                    <label for="productType">Product Type</label>
                    <select id="productType" name="has_variants" data-product-type-select>
                        <option value="0" @selected(! old('has_variants', $product->variant_label !== null))>Simple product</option>
                        <option value="1" @selected(old('has_variants', $product->variant_label !== null))>Product with variants</option>
                    </select>
                    <p>Variant stock replaces the general stock when enabled.</p>
                </div>
                <section class="form-group full-width product-variant-builder" data-product-variant-builder hidden aria-labelledby="variant-builder-title">
                    <div class="product-variant-builder-heading">
                        <div><h2 id="variant-builder-title">Product Variants</h2><p>Manage each choice independently. Removing an existing row safely deactivates it.</p></div>
                        <button type="button" data-add-product-variant><i class="fas fa-plus" aria-hidden="true"></i> Add Variant</button>
                    </div>
                    <label for="variantLabel">Choice Label</label>
                    <input id="variantLabel" type="text" name="variant_label" value="{{ old('variant_label', $product->variant_label) }}" maxlength="60" placeholder="Example: Size or Model" data-variant-label-input>
                    <div class="product-variant-list" data-product-variant-list></div>
                    <p class="product-gallery-upload-error" data-product-variant-error role="alert"></p>
                    @php
                        $productVariantInitial = old('variants');

                        if ($productVariantInitial === null) {
                            $productVariantInitial = $product->variants->map(function ($variant) {
                                return [
                                    'id' => $variant->id,
                                    'name' => $variant->name,
                                    'sku' => $variant->sku,
                                    'price' => $variant->price,
                                    'stock' => $variant->stock,
                                    'is_active' => $variant->is_active ? 1 : 0,
                                    'image' => $variant->image,
                                ];
                            })->values()->all();
                        }
                    @endphp
                    <script type="application/json" data-product-variant-initial>@json($productVariantInitial)</script>
                </section>
                <div class="form-group full-width">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="6">{{ old('description', $product->description) }}</textarea>
                </div>

                <div class="form-group full-width product-existing-gallery" data-existing-product-gallery>
                    <div class="product-gallery-upload-heading">
                        <div>
                            <label>Current Photos & Videos</label>
                            <p>Select media you want to remove. At least one photo must remain as cover.</p>
                        </div>
                    </div>
                    <div class="product-existing-gallery-grid">
                        @forelse($product->galleryMedia as $media)
                            <label class="product-existing-photo" data-existing-product-photo>
                                @if($media->media_type === 'video')
                                    <img src="{{ $media->thumbnail_url }}" alt="{{ $product->name }} video preview {{ $loop->iteration }}" loading="lazy">
                                    <span class="product-media-type"><i class="fas fa-play" aria-hidden="true"></i> Video</span>
                                @else
                                    <img src="{{ $media->secure_url }}" alt="{{ $product->name }} photo {{ $loop->iteration }}" loading="lazy">
                                @endif
                                <input type="checkbox" name="delete_media_ids[]" value="{{ $media->id }}" data-media-type="{{ $media->media_type }}" data-delete-product-image>
                                <span><i class="fas fa-trash" aria-hidden="true"></i> Remove</span>
                            </label>
                        @empty
                            <div class="product-existing-photo product-existing-photo--legacy">
                                <img src="{{ $product->image }}" alt="{{ $product->name }} primary photo">
                                <span><i class="fas fa-lock" aria-hidden="true"></i> Current primary photo</span>
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="form-group full-width product-gallery-upload-field">
                    <div class="product-gallery-upload-heading">
                        <div>
                            <label for="productImages">Add Photos or Videos</label>
                            <p>Upload additional angles or short product videos, up to 8 media total.</p>
                        </div>
                        <span data-product-image-count>{{ $product->gallery_images->count() }} / 8 photos</span>
                    </div>
                    <input type="file" name="product_images[]" id="productImages" accept="image/jpeg,image/png,image/webp,video/mp4,video/webm" multiple data-product-images-input data-max-files="8" data-existing-count="{{ $product->galleryMedia->count() ?: $product->gallery_items->count() }}" data-existing-image-count="{{ $product->gallery_images->count() }}" aria-describedby="product-images-help">
                    <p id="product-images-help" class="product-gallery-upload-help">Photos use JPG, PNG, or WebP. Videos use MP4 or WebM and do not autoplay.</p>
                    <p class="product-gallery-upload-error" data-product-images-error role="alert"></p>
                    <div class="product-gallery-preview-grid" data-product-images-preview aria-live="polite"></div>
                </div>
            </div>

            <div class="submit-wrapper">
                <button type="submit" class="save-btn"><i class="fas fa-save" aria-hidden="true"></i> Update Product</button>
            </div>
        </form>
    </div>
</div>
@endsection
