@extends('admin.layouts.admin')

@section('content')

<div class="gallery-form-page">

    <div class="page-top">

        <div>

            <h1>Edit Gallery</h1>

            <p class="page-subtitle">
                Update gallery photo ✨
            </p>

        </div>

    </div>

    <div class="gallery-form-card">

        <form
            data-cloudinary-direct-upload
            action="/admin/gallery/{{ $gallery->id }}"
            method="POST"
            enctype="multipart/form-data">

            @csrf
            @method('PUT')

            <div class="form-grid">

                <div class="form-group">

                    <label>Title</label>

                    <input
                        type="text"
                        name="title"
                        value="{{ $gallery->title }}">

                </div>

                <div class="form-group full-width">

                    <label>Description</label>

                    <textarea
                        name="description"
                        rows="5">{{ $gallery->description }}</textarea>

                </div>

                <div class="form-group full-width">

                    <label>Current Photo</label>

                    <div class="current-gallery-image">

                        <img
                            src="{{ filter_var($gallery->image, FILTER_VALIDATE_URL) ? $gallery->image : asset('uploads/gallery/' . $gallery->image) }}">

                    </div>

                </div>

                <div class="form-group full-width">

                    <label>Change Photo</label>

                    <input
                        type="file"
                        name="image">

                </div>

            </div>

            <div class="submit-wrapper">

                <button
                    type="submit"
                    class="save-btn">

                    <i class="fas fa-save"></i>

                    Update Gallery

                </button>

            </div>

        </form>

    </div>

</div>

@endsection
