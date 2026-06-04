@extends('admin.layouts.admin')

@section('content')

<div class="mv-page">

<!-- =========================================
     PAGE HEADER
========================================= -->
<div class="mv-page-top">

    <div class="mv-page-heading">

        <span class="mv-page-badge">

            <i class="fas fa-film"></i>

            AANAYA VISUAL UNIVERSE

        </span>

        <h1>
            Music Videos
        </h1>

        <p class="mv-page-subtitle">
            Manage cinematic music videos, dreamy visuals,
            and featured releases for the Aanaya universe.
        </p>

    </div>

    <a href="/admin/mv/create" class="mv-pink-btn">

        <i class="fas fa-cloud-upload-alt"></i>

        Upload MV

    </a>

</div>

<!-- =========================================
     VIDEO GRID
========================================= -->
@if($videos->count())

    <div class="mv-grid">

        @foreach($videos as $video)

            <div class="mv-card">

                <!-- FEATURED -->
                @if($video->is_featured)

                    <div class="mv-featured">

                        <i class="fas fa-star"></i>

                        Featured

                    </div>

                @endif

                <!-- VIDEO -->
                <div class="mv-video-wrapper">

                    <video
                        preload="metadata"
                        controls
                        poster="{{ $video->thumbnail ? $video->thumbnail : '' }}">

                        <source
                            src="{{ $video->video_file }}"
                            type="video/mp4">

                    </video>

                    <div class="mv-overlay"></div>

                    <div class="mv-play-icon">

                        <i class="fas fa-play"></i>

                    </div>

                    <div class="mv-floating-label">

                        <i class="fas fa-wave-square"></i>

                        DREAMY VISUAL

                    </div>

                </div>

                <!-- CONTENT -->
                <div class="mv-content">

                    <div class="mv-top-meta">

                        <div class="mv-badge">

                            <i class="fas fa-compact-disc"></i>

                            MUSIC VIDEO

                        </div>

                        <span class="mv-date">

                            {{ $video->created_at->format('d M Y') }}

                        </span>

                    </div>

                    <h2>
                        {{ $video->title }}
                    </h2>

                    <div class="mv-artist">

                        <i class="fas fa-microphone"></i>

                        <span>
                            {{ $video->artist }}
                        </span>

                    </div>

                    @if($video->description)

                        <p>
                            {{ Str::limit($video->description, 120) }}
                        </p>

                    @else

                        <p>
                            Emotional visuals, cinematic atmosphere,
                            and dreamy storytelling from Aanaya.
                        </p>

                    @endif

                    <!-- ACTIONS -->
                    <div class="mv-actions">

                        <a href="{{ $video->video_file }}"
                        target="_blank"
                        class="mv-btn edit">
                            <i class="fas fa-play"></i>
                            Watch
                        </a>

                        <a href="{{ route('admin.music-vidio.edit', $video->id) }}"
                        class="mv-btn edit">
                            <i class="fas fa-edit"></i>
                            Edit
                        </a>

                        <form
                            action="{{ route('admin.music-vidio.destroy', $video->id) }}"
                            method="POST"
                            onsubmit="return confirm('Delete this music video?')">

                            @csrf
                            @method('DELETE')

                            <button
                                type="submit"
                                class="mv-btn delete">

                                <i class="fas fa-trash"></i>
                                Delete

                            </button>

                        </form>

                    </div>

                </div>

            </div>

        @endforeach

    </div>

@else

    <!-- =========================================
         EMPTY STATE
    ========================================= -->
    <div class="mv-empty">

        <div class="mv-empty-icon">

            <i class="fas fa-photo-film"></i>

        </div>

        <h2>
            No Music Videos Yet
        </h2>

        <p>
            Upload your first cinematic music video
            and start building the dreamy visual universe
            of Aanaya.
        </p>

        <a href="/admin/mv/create"
           class="mv-pink-btn">

            <i class="fas fa-plus"></i>

            Upload First MV

        </a>

    </div>

@endif

</div>

@endsection
