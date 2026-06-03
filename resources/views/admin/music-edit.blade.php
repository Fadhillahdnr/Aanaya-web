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
                        src="{{ $music->cover_image }}"
                        alt="{{ $music->title }}">

                </div>

            </div>

            <!-- UPLOAD NEW COVER -->
            <div class="form-group">

                <label>Replace Cover</label>

                <div class="upload-box">

                    <img
                        id="cover-preview"
                        style="
                            display:none;
                            width:100%;
                            max-height:250px;
                            object-fit:cover;
                            border-radius:15px;
                            margin-bottom:15px;
                        ">

                    <i class="fas fa-image"></i>

                    <p>Upload new cover image</p>

                    <input
                        type="file"
                        name="cover_image"
                        accept="image/*"
                        onchange="previewCover(event)">

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
                        src="{{ $music->audio_file }}"
                        type="audio/mpeg">

                </audio>

            </div>

            <!-- REPLACE AUDIO -->
            <div class="form-group">

                <label>Replace Audio File</label>

                <div class="upload-box">

                    <i class="fas fa-music"></i>

                    <p id="audio-name">

                        Upload new MP3 file

                    </p>

                    <input
                        type="file"
                        name="audio_file"
                        accept="audio/*"
                        onchange="showAudioName(event)">

                </div>

            </div>

            <!-- SPOTIFY -->
            <div class="form-group">

                <label>Spotify Link</label>

                <input
                    type="url"
                    name="spotify_link"
                    value="{{ old('spotify_link', $music->spotify_link) }}"
                    placeholder="https://open.spotify.com/...">

                @if($music->spotify_link)

                    <div class="stream-links">

                        <a
                            href="{{ $music->spotify_link }}"
                            target="_blank"
                            class="spotify-btn">

                            <i class="fab fa-spotify"></i>

                            Open Spotify

                        </a>

                    </div>

                @endif

            </div>

            <!-- YOUTUBE -->
            <div class="form-group">

                <label>YouTube Link</label>

                <input
                    type="url"
                    name="youtube_link"
                    value="{{ old('youtube_link', $music->youtube_link) }}"
                    placeholder="https://youtube.com/...">

                @if($music->youtube_link)

                    <div class="stream-links">

                        <a
                            href="{{ $music->youtube_link }}"
                            target="_blank"
                            class="youtube-btn">

                            <i class="fab fa-youtube"></i>

                            Open YouTube

                        </a>

                    </div>

                @endif

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

<script>

function previewCover(event)
{
    const preview =
        document.getElementById(
            'cover-preview'
        );

    preview.src =
        URL.createObjectURL(
            event.target.files[0]
        );

    preview.style.display =
        'block';
}

function showAudioName(event)
{
    document.getElementById(
        'audio-name'
    ).innerText =
        event.target.files[0].name;
}

</script>

@endsection