<article class="music-release" data-music-release="{{ $music->id }}" data-music-reveal>
    <span class="music-release__number">{{ str_pad($releaseNumber, 2, '0', STR_PAD_LEFT) }}</span>
    <div class="music-release__cover" data-cover-tilt>
        <x-media-image :src="$music->cover_image" :alt="'Cover artwork '.$music->title"
            :width="720" :height="720" crop="fill"
            sizes="(max-width: 767px) 78vw, 34vw" />
    </div>
    <div class="music-release__content">
        <span data-playing-label hidden>Now playing</span>
        <h3>{{ $music->title }}</h3>
        <p>{{ $music->artist }}</p>
        @if ($music->release_date)
            <time datetime="{{ $music->release_date }}">{{ \Carbon\Carbon::parse($music->release_date)->format('Y') }}</time>
        @endif
        @if ($music->description)<p class="music-release__description">{{ $music->description }}</p>@endif
        <div class="music-actions">
            @if ($music->audio_file)
                <button type="button" class="music-play-action" data-play-track="{{ $music->id }}"><span aria-hidden="true">▶</span> Play</button>
            @endif
            @if ($music->spotify_link)<a href="{{ $music->spotify_link }}" target="_blank" rel="noopener noreferrer">Spotify ↗</a>@endif
            @if ($music->youtube_link)<a href="{{ $music->youtube_link }}" target="_blank" rel="noopener noreferrer">YouTube ↗</a>@endif
        </div>
    </div>
</article>
