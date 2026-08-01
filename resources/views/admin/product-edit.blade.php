@extends('admin.layouts.admin')

@section('content')

<div class="product-form-page">

    <!-- HEADER -->
    <div class="page-top">

        <div>

            <h1>Edit Product</h1>

            <p class="page-subtitle">
                Update merchandise information ✨
            </p>

        </div>

        <a href="/admin/products" class="back-btn">

            <i class="fas fa-arrow-left"></i>

            Back

        </a>

    </div>

    <!-- FORM CARD -->
    <div class="product-form-card">

        <form
            data-cloudinary-direct-upload
            action="/admin/products/{{ $product->id }}"
            method="POST"
            enctype="multipart/form-data">

            @csrf
            @method('PUT')

            <div class="form-grid">

                <!-- PRODUCT NAME -->
                <div class="form-group">

                    <label>Product Name</label>

                    <input
                        type="text"
                        name="name"
                        value="{{ $product->name }}"
                        required>

                </div>

                <!-- CATEGORY -->
                <div class="form-group">

                    <label>Category</label>

                    <input
                        type="text"
                        name="category"
                        value="{{ $product->category }}">

                </div>

                <!-- PRICE -->
                <div class="form-group">

                    <label>Price</label>

                    <input
                        type="number"
                        name="price"
                        value="{{ $product->price }}"
                        required>

                </div>

                <!-- STOCK -->
                <div class="form-group">

                    <label>Stock</label>

                    <input
                        type="number"
                        name="stock"
                        value="{{ $product->stock }}"
                        required>

                </div>

                <!-- DESCRIPTION -->
                <div class="form-group full-width">

                    <label>Description</label>

                    <textarea
                        name="description"
                        rows="6">{{ $product->description }}</textarea>

                </div>

                <!-- CURRENT IMAGE -->
                <div class="form-group full-width">

                    <label>Current Image</label>

                    <div class="current-product-image">

                        @if($product->image)

                            <img
                                id="currentPreview"
                                src="{{ $product->image }}"
                                alt="{{ $product->name }}">

                        @else

                            <img
                                id="currentPreview"
                                src="https://via.placeholder.com/500x500?text=No+Image"
                                alt="No Image">

                        @endif

                    </div>

                </div>

                <div class="form-group full-width">

                    <label>Change Product Image</label>

                    <input
                        type="file"
                        name="image"
                        id="imageInput"
                        accept="image/*">

                </div>

            <!-- BUTTON -->
            <div class="submit-wrapper">

                <button
                    type="submit"
                    class="save-btn">

                    <i class="fas fa-save"></i>

                    Update Product

                </button>

            </div>

        </form>

    </div>

</div>

<script>

const imageInput =
    document.getElementById('imageInput');

const currentPreview =
    document.getElementById('currentPreview');

imageInput.addEventListener('change', function(){

    const file = this.files[0];

    if(file){

        currentPreview.src =
            URL.createObjectURL(file);
    }

});

</script>

@endsection
