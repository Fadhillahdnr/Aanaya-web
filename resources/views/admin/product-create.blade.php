@extends('admin.layouts.admin')

@section('content')
<div class="product-form-page">
    <div class="page-top">
        <div>
            <h1>Add Product</h1>
            <p class="page-subtitle">Create merchandise with a complete product gallery.</p>
        </div>
        <a href="/admin/products" class="back-btn"><i class="fas fa-arrow-left" aria-hidden="true"></i> Back</a>
    </div>

    <div class="product-form-card">
        @if ($errors->any())
            <div class="alert alert-danger mb-4" role="alert">
                <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        <form data-cloudinary-direct-upload action="/admin/products/store" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-grid">
                <div class="form-group">
                    <label for="name">Product Name</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required>
                </div>
                <div class="form-group">
                    <label for="category">Category</label>
                    <input id="category" type="text" name="category" value="{{ old('category') }}" placeholder="T-Shirt, Hoodie, Album, etc">
                </div>
                <div class="form-group">
                    <label for="price">Price</label>
                    <input id="price" type="number" name="price" value="{{ old('price') }}" min="0" required>
                </div>
                <div class="form-group">
                    <label for="stock">Stock</label>
                    <input id="stock" type="number" name="stock" value="{{ old('stock', 0) }}" min="0" required>
                </div>
                <div class="form-group full-width product-type-field">
                    <label for="productType">Product Type</label>
                    <select id="productType" name="has_variants" data-product-type-select>
                        <option value="0" @selected(! old('has_variants'))>Simple product</option>
                        <option value="1" @selected(old('has_variants'))>Product with variants</option>
                    </select>
                    <p>Use variants for choices such as Size, Model, Edition, or Color.</p>
                </div>
                <section class="form-group full-width product-variant-builder" data-product-variant-builder hidden aria-labelledby="variant-builder-title">
                    <div class="product-variant-builder-heading">
                        <div><h2 id="variant-builder-title">Product Variants</h2><p>Each choice has its own stock, optional price, SKU, and photo.</p></div>
                        <button type="button" data-add-product-variant><i class="fas fa-plus" aria-hidden="true"></i> Add Variant</button>
                    </div>
                    <label for="variantLabel">Choice Label</label>
                    <input id="variantLabel" type="text" name="variant_label" value="{{ old('variant_label') }}" maxlength="60" placeholder="Example: Size or Model" data-variant-label-input>
                    <div class="product-variant-list" data-product-variant-list></div>
                    <p class="product-gallery-upload-error" data-product-variant-error role="alert"></p>
                    <script type="application/json" data-product-variant-initial>@json(old('variants', []))</script>
                </section>
                <div class="form-group full-width">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="6">{{ old('description') }}</textarea>
                </div>
                <div class="form-group full-width product-gallery-upload-field">
                    <div class="product-gallery-upload-heading">
                        <div>
                            <label for="productImages">Product Photos & Videos</label>
                            <p>Select up to 8 media. At least one photo is required as the product cover.</p>
                        </div>
                        <span data-product-image-count>0 / 8 selected</span>
                    </div>
                    <input type="file" name="product_images[]" id="productImages" accept="image/jpeg,image/png,image/webp,video/mp4,video/webm" multiple required data-product-images-input data-max-files="8" data-existing-count="0" data-existing-image-count="0" aria-describedby="product-images-help">
                    <p id="product-images-help" class="product-gallery-upload-help">Photos: JPG, PNG, WebP. Videos: MP4 or WebM, maximum 3 minutes. Upload goes directly to Cloudinary.</p>
                    <p class="product-gallery-upload-error" data-product-images-error role="alert"></p>
                    <div class="product-gallery-preview-grid" data-product-images-preview aria-live="polite"></div>
                </div>
            </div>

            <div class="submit-wrapper">
                <button type="submit" class="save-btn"><i class="fas fa-cloud-upload-alt" aria-hidden="true"></i> Save Product</button>
            </div>
        </form>
    </div>
</div>
@endsection
