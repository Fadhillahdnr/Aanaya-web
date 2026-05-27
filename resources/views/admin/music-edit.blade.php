@extends('admin.layouts.admin')

@section('content')

<div class="form-page">

    <!-- HEADER -->
    <div class="form-header">

        <div>

            <span class="form-badge">
                EDIT MUSIC
            </span>

            <h1>Edit Music</h1>

            <p>
                Update music information, audio, and cover artwork.
            </p>

        </div>

        <a href="/admin/music" class="back-btn">

            <i class="fas fa-arrow-left"></i>

            Back

        </a>

    </div>

    <!-- FORM -->
    <div class="music-form-card">

        <form
            method="POST"
            action="{{ route('admin.music.update', $music->id) }}"
            enctype="multipart/form-data">

            @csrf
            @method('PUT')

            <!-- COVER -->
            <div class="form-group">

                <label>Current Cover</label>

                <div class="current-image-preview">

                    <img
                        src="{{ asset($music->cover_image) }}"
                        alt="{{ $music->title }}">

                </div>

            </div>

            <!-- UPLOAD NEW COVER -->
            <div class="form-group">

                <label>Replace Cover</label>

                <div class="upload-box">

                    <i class="fas fa-image"></i>

                    <p>Upload new cover image</p>

                    <input
                        type="file"
                        name="cover_image">

                </div>

            </div>

            <!-- TITLE -->
            <div class="form-group">

                <label>Music Title</label>

                <input
                    type="text"
                    name="title"
                    value="{{ old('title', $music->title) }}"
                    placeholder="Enter music title">

            </div>

            <!-- ARTIST -->
            <div class="form-group">

                <label>Artist Name</label>

                <input
                    type="text"
                    name="artist"
                    value="{{ old('artist', $music->artist) }}"
                    placeholder="Enter artist name">

            </div>

            <!-- RELEASE DATE -->
            <div class="form-group">

                <label>Release Date</label>

                <input
                    type="date"
                    name="release_date"
                    value="{{ old('release_date', $music->release_date) }}">

            </div>

            <!-- DESCRIPTION -->
            <div class="form-group">

                <label>Description</label>

                <textarea
                    name="description"
                    rows="5"
                    placeholder="Write description...">{{ old('description', $music->description) }}</textarea>

            </div>

            <!-- CURRENT AUDIO -->
            <div class="form-group">

                <label>Current Audio</label>

                <audio controls style="width:100%;">

                    <source
                        src="{{ asset($music->audio_file) }}"
                        type="audio/mpeg">

                </audio>

            </div>

            <!-- REPLACE AUDIO -->
            <div class="form-group">

                <label>Replace Audio File</label>

                <div class="upload-box">

                    <i class="fas fa-music"></i>

                    <p>Upload new MP3 file</p>

                    <input
                        type="file"
                        name="audio_file">

                </div>

            </div>

            <!-- SPOTIFY -->
            <div class="form-group">

                <label>Spotify Link</label>

                <input
                    type="text"
                    name="spotify_link"
                    value="{{ old('spotify_link', $music->spotify_link) }}"
                    placeholder="https://spotify.com/...">

            </div>

            <!-- YOUTUBE -->
            <div class="form-group">

                <label>YouTube Link</label>

                <input
                    type="text"
                    name="youtube_link"
                    value="{{ old('youtube_link', $music->youtube_link) }}"
                    placeholder="https://youtube.com/...">

            </div>

            <!-- BUTTON -->
            <div class="form-actions">

                <button type="submit" class="save-btn">

                    <i class="fas fa-floppy-disk"></i>

                    Update Music

                </button>

            </div>

        </form>

    </div>

</div>

@endsection