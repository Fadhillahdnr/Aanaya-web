@extends('admin.layouts.admin')

@section('content')

<div class="products-page">

    <!-- HEADER -->
    <div class="page-top">

        <div>
            <h1>Merchandise</h1>

            <p class="page-subtitle">
                Manage official Aanaya merchandise ✨
            </p>
        </div>

        <a href="/admin/products/create"
           class="pink-btn">

            <i class="fas fa-plus"></i>

            Add Product

        </a>

    </div>

    <!-- GRID -->
    <div class="product-grid">

        @forelse($products as $product)

            <div class="product-card">

                <!-- IMAGE -->
                <div class="product-image-wrapper">

                    @if($product->image)

                        <img
                            src="{{ $product->image }}"
                            alt="{{ $product->name }}"
                            loading="lazy">

                    @else

                        <img
                            src="https://via.placeholder.com/500x500?text=No+Image"
                            alt="No Image">

                    @endif

                </div>

                <!-- CONTENT -->
                <div class="product-content">

                    <span class="product-category">
                        {{ $product->category ?? 'Merchandise' }}
                    </span>

                    <h2>
                        {{ $product->name }}
                    </h2>

                    <p class="product-price">
                        Rp {{ number_format($product->price, 0, ',', '.') }}
                    </p>

                    <div class="product-stock">

                        <i class="fas fa-box"></i>

                        Stock:
                        {{ $product->stock }}

                    </div>

                    <!-- ACTION -->
                    <div class="card-actions">

                        <a href="/admin/products/{{ $product->id }}/edit"
                           class="edit-btn">

                            <i class="fas fa-pen"></i>

                            Edit

                        </a>

                        <form
                            action="/admin/products/{{ $product->id }}"
                            method="POST"
                            onsubmit="return confirm('Delete this product?')">

                            @csrf
                            @method('DELETE')

                            <button
                                type="submit"
                                class="delete-btn">

                                <i class="fas fa-trash"></i>

                                Delete

                            </button>

                        </form>

                    </div>

                </div>

            </div>

        @empty

            <div class="empty-product">

                <i class="fas fa-shirt"></i>

                <h2>No Product Yet</h2>

                <p>
                    Upload your first merchandise ✨
                </p>

            </div>

        @endforelse

    </div>

</div>

@endsection