@extends('admin.layouts.admin')

@section('content')

<!-- HEADER -->
    <div class="form-header">

        <div>

            <span class="form-badge">
                EDIT MUSIC VIDEO
            </span>

            <h1>Edit Music Video</h1>

            <p>
                Update music video information, audio, and cover artwork.
            </p>

        </div>

        <a href="{{ route('admin.music-vidio') }}" class="back-btn">

            <i class="fas fa-arrow-left"></i>

            Back

        </a>

    </div>

<div class="mv-edit-form-card">

    <div class="mv-edit-form-glow"></div>

    <div class="mv-edit-header">

        <div>

            <span class="mv-edit-badge">

                <i class="fas fa-film"></i>

                MUSIC VIDEO EDITOR

            </span>

            <h2 class="mv-edit-form-title">

                Edit Music Video

            </h2>

            <p class="mv-edit-subtitle">

                Update video information, thumbnail,
                featured status, and description.

            </p>

        </div>

    </div>

    @if ($errors->any())

        <div class="mv-edit-form-error">

            <ul>

                @foreach ($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    @endif

    <form
        method="POST"
        action="{{ route('admin.music-vidio.update', $video->id) }}"
        enctype="multipart/form-data">

        @csrf
        @method('PUT')

        <div class="mv-edit-form-grid">

            <!-- TITLE -->
            <div class="mv-edit-form-group">

                <label>Title</label>

                <input
                    type="text"
                    name="title"
                    value="{{ old('title', $video->title) }}"
                    required>

            </div>

            <!-- ARTIST -->
            <div class="mv-edit-form-group">

                <label>Artist</label>

                <input
                    type="text"
                    name="artist"
                    value="{{ old('artist', $video->artist) }}"
                    required>

            </div>

            <!-- THUMBNAIL PREVIEW -->
            @if($video->thumbnail)

                <div class="mv-edit-form-group mv-edit-full-width">

                    <label>

                        Current Thumbnail

                    </label>

                    <div class="mv-edit-preview-thumbnail">

                        <img
                            src="{{ $video->thumbnail }}"
                            alt="{{ $video->title }}">

                    </div>

                </div>

            @endif

            <!-- NEW THUMBNAIL -->
            <div class="mv-edit-form-group">

                <label>

                    Replace Thumbnail

                </label>

                <input
                    type="file"
                    name="thumbnail"
                    accept="image/*">

            </div>

            <!-- NEW VIDEO -->
            <div class="mv-edit-form-group">

                <label>

                    Replace Video

                </label>

                <input
                    type="file"
                    name="video_file"
                    accept="video/*">

            </div>

            <!-- VIDEO PREVIEW -->
            @if($video->video_file)

                <div class="mv-edit-form-group mv-edit-full-width">

                    <label>

                        Current Video

                    </label>

                    <div class="mv-edit-preview-video">

                        <video
                            controls
                            preload="metadata"
                            poster="{{ $video->thumbnail }}">

                            <source
                                src="{{ $video->video_file }}">

                        </video>

                    </div>

                </div>

            @endif

            <!-- DESCRIPTION -->
            <div class="mv-edit-form-group mv-edit-full-width">

                <label>

                    Description

                </label>

                <textarea
                    name="description"
                    rows="8">{{ old('description', $video->description) }}</textarea>

            </div>

            <!-- FEATURED -->
            <div class="mv-edit-form-group">

                <label class="mv-edit-checkbox">

                    <input
                        type="checkbox"
                        name="is_featured"
                        value="1"
                        {{ old('is_featured', $video->is_featured) ? 'checked' : '' }}>

                    <span>

                        Featured Music Video

                    </span>

                </label>

            </div>

        </div>

        <div class="mv-edit-submit-wrapper">

            <button
                type="submit"
                class="mv-edit-save-btn">

                <i class="fas fa-save"></i>

                Update Music Video

            </button>

        </div>

    </form>

</div>

@endsection