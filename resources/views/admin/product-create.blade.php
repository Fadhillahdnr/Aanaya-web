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

        @if ($errors->any())
            <div class="alert alert-danger mb-4">

                <ul>

                    @foreach ($errors->all() as $error)

                        <li>{{ $error }}</li>

                    @endforeach

                </ul>

            </div>
        @endif

        <form
            data-cloudinary-direct-upload
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
                        value="{{ old('name') }}"
                        required>

                </div>

                <div class="form-group">

                    <label>Category</label>

                    <input
                        type="text"
                        name="category"
                        value="{{ old('category') }}"
                        placeholder="T-Shirt, Hoodie, Album, etc">

                </div>

                <div class="form-group">

                    <label>Price</label>

                    <input
                        type="number"
                        name="price"
                        value="{{ old('price') }}"
                        min="0"
                        required>

                </div>

                <div class="form-group">

                    <label>Stock</label>

                    <input
                        type="number"
                        name="stock"
                        value="{{ old('stock') }}"
                        min="0"
                        required>

                </div>

                <div class="form-group full-width">

                    <label>Description</label>

                    <textarea
                        name="description"
                        rows="6">{{ old('description') }}</textarea>

                </div>

                <div class="form-group full-width">

                    <label>Product Image</label>

                    <input
                        type="file"
                        name="image"
                        id="imageInput"
                        accept="image/*"
                        required>

                </div>

                <div class="form-group full-width">

                    <img
                        id="previewImage"
                        style="
                            display:none;
                            width:250px;
                            border-radius:20px;
                            margin-top:15px;
                            box-shadow:0 10px 25px rgba(0,0,0,.12);
                        ">

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

<script>

const imageInput = document.getElementById('imageInput');
const previewImage = document.getElementById('previewImage');

imageInput.addEventListener('change', function(){

    const file = this.files[0];

    if(file){

        previewImage.src =
            URL.createObjectURL(file);

        previewImage.style.display =
            'block';
    }

});

</script>

@endsection
