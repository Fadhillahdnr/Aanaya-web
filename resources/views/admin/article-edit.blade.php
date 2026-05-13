@extends('admin.layouts.admin')

@section('content')

<div class="article-form-page">

    <div class="page-top">

        <div>
            <h1>Edit Article</h1>

            <p class="page-subtitle">
                Update your article ✨
            </p>
        </div>

    </div>

    <div class="article-form-card">

        <form
            action="/admin/articles/{{ $article->id }}"
            method="POST"
            enctype="multipart/form-data">

            @csrf
            @method('PUT')

            <div class="form-grid">

                <!-- LEFT -->
                <div class="form-left">

                    <div class="form-group">

                        <label>Article Title</label>

                        <input
                            type="text"
                            name="title"
                            value="{{ $article->title }}"
                            required>

                    </div>

                    <div class="form-group">

                        <label>Content</label>

                        <textarea
                            rows="12"
                            name="content"
                            required>{{ $article->content }}</textarea>

                    </div>

                </div>

                <!-- RIGHT -->
                <div class="form-right">

                    <img
                        src="{{ asset('uploads/articles/' . $article->thumbnail) }}"
                        class="edit-thumbnail">

                    <div class="upload-box">

                        <i class="fas fa-image"></i>

                        <p>Change Thumbnail</p>

                        <input
                            type="file"
                            name="thumbnail">

                    </div>

                </div>

            </div>

            <div class="submit-wrapper">

                <button type="submit" class="save-btn">

                    <i class="fas fa-save"></i>

                    Update Article

                </button>

            </div>

        </form>

    </div>

</div>

@endsection