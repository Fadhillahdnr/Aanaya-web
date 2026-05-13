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

        <a href="/admin/music/create" class="pink-btn">

            <i class="fas fa-plus"></i>

            Upload Music

        </a>

    </div>

    <!-- MUSIC GRID -->
    <div class="music-grid-admin">

        @foreach($musics as $music)

        <div class="music-admin-card">

            <!-- Glow -->
            <div class="music-card-bg"></div>

            <!-- COVER -->
            <div class="music-cover-wrapper">

                <img
                    src="{{ asset($music->cover_image) }}"
                    alt="{{ $music->title }}"
                    class="music-cover">

                <!-- PLAY BUTTON -->
                <button class="play-btn"
                        onclick="toggleAudio({{ $music->id }})">

                    <i class="fas fa-play"></i>

                </button>

            </div>

            <!-- AUDIO -->
            <audio id="audio-{{ $music->id }}">
                <source
                    src="{{ asset($music->audio_file) }}"
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

                <p>
                    {{ $music->artist }}
                </p>

                @if($music->release_date)

                <span class="release-date">

                    <i class="fas fa-calendar"></i>

                    {{ \Carbon\Carbon::parse($music->release_date)->format('d M Y') }}

                </span>

                @endif

            </div>

            <!-- ACTION -->
            <div class="music-actions">

                <a href="{{ route('admin.music.edit', $music->id) }}"
                   class="edit-btn">

                    <i class="fas fa-pen"></i>

                    Edit

                </a>

                <form
                    action="{{ route('admin.music.destroy', $music->id) }}"
                    method="POST">

                    @csrf
                    @method('DELETE')

                    <button class="delete-btn">

                        <i class="fas fa-trash"></i>

                        Delete

                    </button>

                </form>

            </div>

        </div>

        @endforeach

    </div>

</div>

<!-- AUDIO SCRIPT -->
<script>

function toggleAudio(id)
{
    const audio = document.getElementById('audio-' + id);

    if(audio.paused)
    {
        document.querySelectorAll('audio').forEach(a => {
            a.pause();
        });

        audio.play();
    }
    else
    {
        audio.pause();
    }
}

</script>

@endsection