@extends('admin.layouts.admin')

@section('content')

<div class="article-form-page">

    <div class="page-top">

        <div>
            <h1>Create Article</h1>

            <p class="page-subtitle">
                Publish new stories & updates ✨
            </p>
        </div>

    </div>

    <div class="article-form-card">

        <form
            action="/admin/articles/store"
            method="POST"
            enctype="multipart/form-data">

            @csrf

            <div class="form-grid">

                <!-- LEFT -->
                <div class="form-left">

                    <div class="form-group">

                        <label>Article Title</label>

                        <input
                            type="text"
                            name="title"
                            required>

                    </div>

                    <div class="form-group">

                        <label>Publish Date</label>

                        <input
                            type="datetime-local"
                            name="published_at">

                    </div>

                    <div class="form-group">

                        <label>Content</label>

                        <textarea
                            rows="12"
                            name="content"
                            required></textarea>

                    </div>

                </div>

                <!-- RIGHT -->
                <div class="form-right">

                    <div class="upload-box">

                        <i class="fas fa-image"></i>

                        <p>Upload Thumbnail</p>

                        <input
                            type="file"
                            name="thumbnail"
                            required>

                    </div>

                </div>

            </div>

            <div class="submit-wrapper">

                <button type="submit" class="save-btn">

                    <i class="fas fa-cloud-upload-alt"></i>

                    Publish Article

                </button>

            </div>

        </form>

    </div>

</div>

@endsection