@extends('admin.layouts.admin')

@section('content')

<div class="music-management-page">

    <!-- HEADER -->
    <div class="page-top">

        <div>

            <span class="page-badge">
                AANAYA MUSIC PANEL
            </span>

            <h1>Music Management</h1>

            <p class="page-subtitle">
                Manage songs, albums, releases, and streaming links ✨
            </p>

        </div>

        <a href="{{ route('admin.music.create') }}"
           class="pink-btn">

            <i class="fas fa-plus"></i>

            Upload Music

        </a>

    </div>

    <!-- MUSIC GRID -->
    <div class="music-grid-admin">

        @forelse($musics as $music)

        <div class="music-admin-card">

            <div class="music-card-bg"></div>

            <!-- COVER -->
            <div class="music-cover-wrapper">

                <img
                    src="{{ $music->cover_image }}"
                    alt="{{ $music->title }}"
                    class="music-cover">

                <button
                    class="play-btn"
                    onclick="toggleAudio({{ $music->id }})">

                    <i class="fas fa-play"></i>

                </button>

            </div>

            <!-- AUDIO -->
            <audio id="audio-{{ $music->id }}">

                <source
                    src="{{ $music->audio_file }}"
                    type="audio/mpeg">

            </audio>

            <!-- INFO -->
            <div class="music-info">

                <span class="music-tag">
                    Latest Release
                </span>

                <h2>
                    {{ $music->title }}
                </h2>

                <p class="artist-name">
                    {{ $music->artist }}
                </p>

                @if($music->description)

                <div class="music-description">

                    {{ Str::limit($music->description, 120) }}

                </div>

                @endif

                @if($music->release_date)

                <span class="release-date">

                    <i class="fas fa-calendar"></i>

                    {{ \Carbon\Carbon::parse($music->release_date)->format('d M Y') }}

                </span>

                @endif

                <!-- STREAMING LINKS -->
                <div class="stream-links">

                    @if($music->spotify_link)

                    <a
                        href="{{ $music->spotify_link }}"
                        target="_blank"
                        class="spotify-btn">

                        <i class="fab fa-spotify"></i>

                        Spotify

                    </a>

                    @endif

                    @if($music->youtube_link)

                    <a
                        href="{{ $music->youtube_link }}"
                        target="_blank"
                        class="youtube-btn">

                        <i class="fab fa-youtube"></i>

                        YouTube

                    </a>

                    @endif

                </div>

            </div>

            <!-- ACTION -->
            <div class="music-actions">

                <a
                    href="{{ route('admin.music.edit', $music->id) }}"
                    class="edit-btn">

                    <i class="fas fa-pen"></i>

                    Edit

                </a>

                <form
                    action="{{ route('admin.music.destroy', $music->id) }}"
                    method="POST"
                    onsubmit="return confirm('Hapus music ini?')">

                    @csrf
                    @method('DELETE')

                    <button
                        type="submit"
                        class="delete-btn">

                        <i class="fas fa-trash"></i>

                        Delete

                    </button>

                </form>

            </div>

        </div>

        @empty

        <div class="empty-music">

            <i class="fas fa-music"></i>

            <h3>Belum ada music</h3>

            <p>Upload music pertama kamu ✨</p>

        </div>

        @endforelse

    </div>

</div>

<script>

function toggleAudio(id)
{
    const currentAudio =
        document.getElementById(
            'audio-' + id
        );

    document
        .querySelectorAll('audio')
        .forEach(audio => {

            if(audio !== currentAudio)
            {
                audio.pause();
                audio.currentTime = 0;
            }
        });

    if(currentAudio.paused)
    {
        currentAudio.play();
    }
    else
    {
        currentAudio.pause();
    }
}

</script>

@endsection