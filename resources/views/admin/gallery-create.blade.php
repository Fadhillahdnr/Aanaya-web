@extends('admin.layouts.admin')

@section('content')

<div class="gallery-form-page">

    <div class="page-top">

        <div>

            <h1>Upload Gallery</h1>

            <p class="page-subtitle">
                Add new concert & aesthetic photos ✨
            </p>

        </div>

    </div>

    <div class="gallery-form-card">

        <form
            data-cloudinary-direct-upload
            action="/admin/gallery/store"
            method="POST"
            enctype="multipart/form-data">

            @csrf

            <div class="form-grid">

                <div class="form-group">

                    <label>Title</label>

                    <input
                        type="text"
                        name="title">

                </div>

                <div class="form-group full-width">

                    <label>Description</label>

                    <textarea
                        name="description"
                        rows="5"></textarea>

                </div>

                <div class="form-group full-width">

                    <label>Upload Photo</label>

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

                    Upload Photo

                </button>

            </div>

        </form>

    </div>

</div>

@endsection
