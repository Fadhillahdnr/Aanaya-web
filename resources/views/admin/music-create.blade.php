@extends('admin.layouts.admin')

@section('content')

<div class="music-page">

    <!-- HEADER -->
    <div class="page-top">

        <div>
            <h1>Music Management</h1>

            <p class="page-subtitle">
                Upload new songs, albums, and manage releases ✨
            </p>
        </div>

        <a href="/admin/music/create" class="pink-btn">

            <i class="fas fa-plus"></i>

            Upload Music

        </a>

    </div>

    <!-- SUCCESS MESSAGE -->
    @if(session('success'))

        <div class="success-alert">
            {{ session('success') }}
        </div>

    @endif

    <!-- ERROR -->
    @if ($errors->any())

        <div class="error-alert">

            <ul>

                @foreach ($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    @endif

    <!-- FORM -->
    <div class="music-form-card">

        <div class="form-glow"></div>

        <h2 class="form-title">
            Upload New Track
        </h2>

        <form method="POST"
            action="{{ route('admin.music.store') }}"
            enctype="multipart/form-data">

            @csrf

            <div class="form-grid">

                <!-- LEFT -->
                <div class="form-left">

                    <!-- TITLE -->
                    <div class="form-group">

                        <label>Music Title</label>

                        <input
                            type="text"
                            name="title"
                            placeholder="Dreamscape"
                            required>

                    </div>

                    <!-- ARTIST -->
                    <div class="form-group">

                        <label>Artist</label>

                        <input
                            type="text"
                            name="artist"
                            value="Aanaya"
                            required>

                    </div>

                    <!-- RELEASE -->
                    <div class="form-group">

                        <label>Release Date</label>

                        <input
                            type="date"
                            name="release_date">

                    </div>

                    <!-- DESCRIPTION -->
                    <div class="form-group">

                        <label>Description</label>

                        <textarea
                            name="description"
                            rows="6"
                            placeholder="Write something about this release..."></textarea>

                    </div>

                </div>

                <!-- RIGHT -->
                <div class="form-right">

                    <!-- COVER -->
                    <div class="upload-box">

                        <i class="fas fa-image"></i>

                        <p>Upload Cover</p>

                        <input
                            type="file"
                            name="cover_image"
                            accept="image/*"
                            required>

                    </div>

                    <!-- AUDIO -->
                    <div class="upload-box">

                        <i class="fas fa-music"></i>

                        <p>Upload Audio</p>

                        <input
                            type="file"
                            name="audio_file"
                            accept="audio/*"
                            required>

                    </div>

                    <!-- SPOTIFY -->
                    <div class="form-group">

                        <label>Spotify Link</label>

                        <input
                            type="text"
                            name="spotify_link"
                            placeholder="https://spotify.com/...">

                    </div>

                    <!-- YOUTUBE -->
                    <div class="form-group">

                        <label>YouTube Link</label>

                        <input
                            type="text"
                            name="youtube_link"
                            placeholder="https://youtube.com/...">

                    </div>

                </div>

            </div>

            <!-- BUTTON -->
            <div class="submit-wrapper">

                <button type="submit" class="save-btn">

                    <i class="fas fa-cloud-upload-alt"></i>

                    Save Music

                </button>

            </div>

        </form>

    </div>

</div>

@endsection