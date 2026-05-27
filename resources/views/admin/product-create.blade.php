@extends('admin.layouts.admin')

@section('content')

<div class="product-form-page">

    <div class="page-top">

        <div>
            <h1>Add Product</h1>

            <p class="page-subtitle">
                Upload new merchandise ✨
            </p>
        </div>

        <a href="/admin/products" class="back-btn">

            <i class="fas fa-arrow-left"></i>

            Back

        </a>

    </div>

    <div class="product-form-card">

        <form
            action="/admin/products/store"
            method="POST"
            enctype="multipart/form-data">

            @csrf

            <div class="form-grid">

                <div class="form-group">

                    <label>Product Name</label>

                    <input
                        type="text"
                        name="name"
                        required>

                </div>

                <div class="form-group">

                    <label>Category</label>

                    <input
                        type="text"
                        name="category">

                </div>

                <div class="form-group">

                    <label>Price</label>

                    <input
                        type="number"
                        name="price"
                        required>

                </div>

                <div class="form-group">

                    <label>Stock</label>

                    <input
                        type="number"
                        name="stock"
                        required>

                </div>

                <div class="form-group full-width">

                    <label>Description</label>

                    <textarea
                        name="description"
                        rows="6"></textarea>

                </div>

                <div class="form-group full-width">

                    <label>Product Image</label>

                    <input
                        type="file"
                        name="image"
                        required>

                </div>

            </div>

            <div class="submit-wrapper">

                <button
                    type="submit"
                    class="save-btn">

                    <i class="fas fa-cloud-upload-alt"></i>

                    Save Product

                </button>

            </div>

        </form>

    </div>

</div>

@endsection