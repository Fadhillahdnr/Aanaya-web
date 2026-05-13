@extends('admin.layouts.admin')

@section('content')

<div class="gallery-page">

    <!-- HEADER -->
    <div class="page-top">

        <div>

            <h1>Gallery</h1>

            <p class="page-subtitle">
                Upload dreamy moments & concert memories ✨
            </p>

        </div>

        <a href="/admin/gallery/create"
           class="pink-btn">

            <i class="fas fa-plus"></i>

            Upload Photo

        </a>

    </div>

    <!-- GRID -->
    <div class="gallery-grid">

        @forelse($galleries as $gallery)

            <div class="gallery-card">

                <!-- IMAGE -->
                <div class="gallery-image-wrapper">

                    <img
                        src="{{ asset('uploads/gallery/' . $gallery->image) }}"
                        alt="{{ $gallery->title }}">

                    <!-- OVERLAY -->
                    <div class="gallery-overlay">

                        <h2>
                            {{ $gallery->title ?? 'Aanaya Gallery' }}
                        </h2>

                        <p>
                            {{ $gallery->description }}
                        </p>

                        <div class="gallery-actions">

                            <a href="/admin/gallery/{{ $gallery->id }}/edit"
                               class="edit-btn">

                                <i class="fas fa-pen"></i>

                            </a>

                            <form
                                action="/admin/gallery/{{ $gallery->id }}"
                                method="POST">

                                @csrf
                                @method('DELETE')

                                <button
                                    type="submit"
                                    class="delete-btn">

                                    <i class="fas fa-trash"></i>

                                </button>

                            </form>

                        </div>

                    </div>

                </div>

            </div>

        @empty

            <div class="empty-gallery">

                <i class="fas fa-image"></i>

                <h2>No Photos Yet</h2>

                <p>
                    Upload your first dreamy gallery ✨
                </p>

            </div>

        @endforelse

    </div>

</div>

@endsection