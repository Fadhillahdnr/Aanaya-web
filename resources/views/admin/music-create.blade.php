@extends('admin.layouts.admin')

@section('content')

<div class="music-page-admin">

    <!-- HEADER -->
    <div class="form-header">

        <div>

            <span class="form-badge">
                UPLOAD MUSIC
            </span>

            <h1>Upload New Music</h1>

            <p>
                Add new music releases to the website.
            </p>

        </div>

        <a href="{{ route('admin.music') }}" class="back-btn">

            <i class="fas fa-arrow-left"></i>

            Back

        </a>

    </div>

    <!-- SUCCESS -->
    @if(session('success'))

        <div class="success-alert-admin">

            {{ session('success') }}

        </div>

    @endif

    <!-- ERROR -->
    @if($errors->any())

        <div class="error-alert-admin">

            <ul>

                @foreach($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    @endif

    <!-- FORM CARD -->
    <div class="music-form-card-admin">

        <div class="form-glow-admin"></div>

        <h2 class="form-title-admin">

            Upload New Track

        </h2>

        <form
            method="POST"
            action="{{ route('admin.music.store') }}"
            enctype="multipart/form-data">

            @csrf

            <div class="form-grid-admin">

                <!-- LEFT -->
                <div class="form-left-admin">

                    <!-- TITLE -->
                    <div class="form-group-admin">

                        <label>

                            Music Title

                        </label>

                        <input
                            type="text"
                            name="title"
                            value="{{ old('title') }}"
                            placeholder="Dreamscape"
                            required>

                    </div>

                    <!-- ARTIST -->
                    <div class="form-group-admin">

                        <label>

                            Artist

                        </label>

                        <input
                            type="text"
                            name="artist"
                            value="{{ old('artist', 'Aanaya') }}"
                            required>

                    </div>

                    <!-- RELEASE DATE -->
                    <div class="form-group-admin">

                        <label>

                            Release Date

                        </label>

                        <input
                            type="date"
                            name="release_date"
                            value="{{ old('release_date') }}">

                    </div>

                    <!-- DESCRIPTION -->
                    <div class="form-group-admin">

                        <label>

                            Description

                        </label>

                        <textarea
                            name="description"
                            rows="6"
                            placeholder="Write something about this release...">{{ old('description') }}</textarea>

                    </div>

                </div>

                <!-- RIGHT -->
                <div class="form-right-admin">

                    <!-- COVER IMAGE -->
                    <div class="upload-box-admin">

                        <img
                            id="cover-preview"
                            class="cover-preview-admin"
                            style="display:none;">

                        <i class="fas fa-image"></i>

                        <p id="cover-text">

                            Upload Cover Image

                        </p>

                        <input
                            type="file"
                            name="cover_image"
                            accept="image/*"
                            onchange="previewCover(event)"
                            required>

                    </div>

                    <!-- AUDIO -->
                    <div class="upload-box-admin">

                        <i class="fas fa-music"></i>

                        <p id="audio-name">

                            Upload Audio File

                        </p>

                        <input
                            type="file"
                            name="audio_file"
                            accept="audio/*"
                            onchange="showAudioName(event)"
                            required>

                    </div>

                    <!-- SPOTIFY -->
                    <div class="form-group-admin">

                        <label>

                            Spotify Link

                        </label>

                        <input
                            type="url"
                            name="spotify_link"
                            value="{{ old('spotify_link') }}"
                            placeholder="https://open.spotify.com/...">

                    </div>

                    <!-- YOUTUBE -->
                    <div class="form-group-admin">

                        <label>

                            YouTube Link

                        </label>

                        <input
                            type="url"
                            name="youtube_link"
                            value="{{ old('youtube_link') }}"
                            placeholder="https://youtube.com/...">

                    </div>

                </div>

            </div>

            <!-- BUTTON -->
            <div class="submit-wrapper-admin">

                <button
                    type="submit"
                    class="save-btn-admin">

                    <i class="fas fa-cloud-upload-alt"></i>

                    Save Music

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

    const text =
        document.getElementById(
            'cover-text'
        );

    const file =
        event.target.files[0];

    if(file)
    {
        preview.src =
            URL.createObjectURL(file);

        preview.style.display =
            'block';

        text.innerText =
            file.name;
    }
}

function showAudioName(event)
{
    const file =
        event.target.files[0];

    if(file)
    {
        document.getElementById(
            'audio-name'
        ).innerText =
            file.name;
    }
}

</script>

@endsection