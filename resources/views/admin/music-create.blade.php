@extends('admin.layouts.admin')

@section('content')
<main class="music-upload-page">
    <header class="music-upload-hero">
        <div class="music-upload-hero__copy">
            <span class="music-upload-eyebrow">
                <i class="fas fa-wave-square" aria-hidden="true"></i>
                Release Studio
            </span>
            <h1>Upload New Music</h1>
            <p>Shape the story, artwork, and listening experience for Aanaya's next release.</p>
        </div>

        <a href="{{ route('admin.music') }}" class="music-upload-back">
            <i class="fas fa-arrow-left" aria-hidden="true"></i>
            <span>Back to Music</span>
        </a>
    </header>

    @if ($errors->any())
        <section class="music-upload-alert" role="alert" aria-labelledby="music-upload-errors">
            <i class="fas fa-circle-exclamation" aria-hidden="true"></i>
            <div>
                <strong id="music-upload-errors">Please review the release details</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </section>
    @endif

    <form
        method="POST"
        action="{{ route('admin.music.store') }}"
        enctype="multipart/form-data"
        class="music-upload-form"
        data-cloudinary-direct-upload
        data-music-upload-form>
        @csrf

        <div class="music-upload-layout">
            <section class="music-upload-card music-upload-details" aria-labelledby="music-details-title">
                <header class="music-upload-card__header">
                    <span class="music-upload-step">01</span>
                    <div>
                        <h2 id="music-details-title">Track Details</h2>
                        <p>The information listeners will see across the website.</p>
                    </div>
                </header>

                <div class="music-upload-fields">
                    <div class="music-upload-field">
                        <label for="music-title">Music title <span aria-hidden="true">*</span></label>
                        <input
                            id="music-title"
                            type="text"
                            name="title"
                            value="{{ old('title') }}"
                            maxlength="255"
                            placeholder="Example: Unfold"
                            autocomplete="off"
                            aria-describedby="music-title-help"
                            required>
                        <small id="music-title-help">Use the official release title.</small>
                        @error('title')<p class="music-upload-error" role="alert">{{ $message }}</p>@enderror
                    </div>

                    <div class="music-upload-fields__row">
                        <div class="music-upload-field">
                            <label for="music-artist">Artist <span aria-hidden="true">*</span></label>
                            <input
                                id="music-artist"
                                type="text"
                                name="artist"
                                value="{{ old('artist', 'Aanaya') }}"
                                maxlength="255"
                                autocomplete="organization"
                                required>
                            @error('artist')<p class="music-upload-error" role="alert">{{ $message }}</p>@enderror
                        </div>

                        <div class="music-upload-field">
                            <label for="music-release-date">Release date</label>
                            <input
                                id="music-release-date"
                                type="date"
                                name="release_date"
                                value="{{ old('release_date') }}">
                            @error('release_date')<p class="music-upload-error" role="alert">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div class="music-upload-field">
                        <label for="music-description">Release story</label>
                        <textarea
                            id="music-description"
                            name="description"
                            rows="7"
                            placeholder="Share the feeling, inspiration, or story behind this release…">{{ old('description') }}</textarea>
                        <small>Optional, but helpful for listeners discovering the song.</small>
                        @error('description')<p class="music-upload-error" role="alert">{{ $message }}</p>@enderror
                    </div>
                </div>
            </section>

            <div class="music-upload-side">
                <section class="music-upload-card music-upload-assets" aria-labelledby="music-assets-title">
                    <header class="music-upload-card__header">
                        <span class="music-upload-step">02</span>
                        <div>
                            <h2 id="music-assets-title">Release Assets</h2>
                            <p>Preview both files before uploading directly to Cloudinary.</p>
                        </div>
                    </header>

                    <div class="music-upload-picker">
                        <div class="music-upload-picker__heading">
                            <label for="music-cover">Cover artwork <span aria-hidden="true">*</span></label>
                            <small>JPG, PNG, WebP · max 5 MB</small>
                        </div>

                        <label class="music-upload-dropzone music-upload-dropzone--cover" for="music-cover">
                            <input
                                id="music-cover"
                                type="file"
                                name="cover_image"
                                accept="image/jpeg,image/png,image/webp"
                                data-music-cover-input
                                required>
                            <span class="music-upload-dropzone__icon"><i class="fas fa-image" aria-hidden="true"></i></span>
                            <span class="music-upload-dropzone__copy">
                                <strong>Choose cover artwork</strong>
                                <small>Square artwork gives the best result.</small>
                            </span>
                            <span class="music-upload-browse">Browse</span>
                        </label>

                        <div class="music-upload-cover-preview" data-music-cover-preview hidden>
                            <img src="" alt="Selected cover artwork preview" data-music-cover-image>
                            <div>
                                <strong data-music-cover-name></strong>
                                <small data-music-cover-meta></small>
                            </div>
                            <button type="button" data-music-clear-cover aria-label="Remove selected cover artwork">
                                <i class="fas fa-xmark" aria-hidden="true"></i>
                            </button>
                        </div>
                        @error('uploaded_media.cover_image')<p class="music-upload-error" role="alert">{{ $message }}</p>@enderror
                    </div>

                    <div class="music-upload-picker music-upload-picker--audio">
                        <div class="music-upload-picker__heading">
                            <label for="music-audio">Audio file <span aria-hidden="true">*</span></label>
                            <small>MP3, WAV, M4A · max 20 MB</small>
                        </div>

                        <label class="music-upload-dropzone" for="music-audio">
                            <input
                                id="music-audio"
                                type="file"
                                name="audio_file"
                                accept="audio/mpeg,audio/mp3,audio/wav,audio/x-wav,audio/mp4,audio/x-m4a"
                                data-music-audio-input
                                required>
                            <span class="music-upload-dropzone__icon"><i class="fas fa-music" aria-hidden="true"></i></span>
                            <span class="music-upload-dropzone__copy">
                                <strong>Choose master audio</strong>
                                <small>Use the final mastered version.</small>
                            </span>
                            <span class="music-upload-browse">Browse</span>
                        </label>

                        <div class="music-upload-audio-preview" data-music-audio-preview hidden>
                            <div class="music-upload-audio-preview__meta">
                                <span class="music-upload-audio-preview__icon"><i class="fas fa-headphones" aria-hidden="true"></i></span>
                                <span>
                                    <strong data-music-audio-name></strong>
                                    <small data-music-audio-meta></small>
                                </span>
                                <button type="button" data-music-clear-audio aria-label="Remove selected audio file">
                                    <i class="fas fa-xmark" aria-hidden="true"></i>
                                </button>
                            </div>
                            <audio controls preload="metadata" data-music-audio-player aria-label="Selected audio preview"></audio>
                        </div>
                        @error('uploaded_media.audio_file')<p class="music-upload-error" role="alert">{{ $message }}</p>@enderror
                    </div>
                </section>

                <section class="music-upload-card music-upload-links" aria-labelledby="music-links-title">
                    <header class="music-upload-card__header music-upload-card__header--compact">
                        <span class="music-upload-step">03</span>
                        <div>
                            <h2 id="music-links-title">Listen Elsewhere</h2>
                            <p>Add external destinations when they are ready.</p>
                        </div>
                    </header>

                    <div class="music-upload-link-grid">
                        <div class="music-upload-field">
                            <label for="music-spotify"><i class="fab fa-spotify" aria-hidden="true"></i> Spotify link</label>
                            <input id="music-spotify" type="url" name="spotify_link" value="{{ old('spotify_link') }}" placeholder="https://open.spotify.com/…" inputmode="url">
                            @error('spotify_link')<p class="music-upload-error" role="alert">{{ $message }}</p>@enderror
                        </div>
                        <div class="music-upload-field">
                            <label for="music-youtube"><i class="fab fa-youtube" aria-hidden="true"></i> YouTube link</label>
                            <input id="music-youtube" type="url" name="youtube_link" value="{{ old('youtube_link') }}" placeholder="https://youtube.com/…" inputmode="url">
                            @error('youtube_link')<p class="music-upload-error" role="alert">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </section>
            </div>
        </div>

        <footer class="music-upload-actions">
            <p><i class="fas fa-shield-heart" aria-hidden="true"></i> Files upload securely to Cloudinary after you select Save Music.</p>
            <a href="{{ route('admin.music') }}" class="music-upload-cancel">Cancel</a>
            <button type="submit" class="music-upload-submit">
                <i class="fas fa-cloud-arrow-up" aria-hidden="true"></i>
                <span>Save Music</span>
            </button>
        </footer>
    </form>
</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('[data-music-upload-form]');
    if (!form) return;

    const formatBytes = (bytes) => {
        const units = ['B', 'KB', 'MB', 'GB'];
        const index = Math.min(Math.floor(Math.log(Math.max(bytes, 1)) / Math.log(1024)), units.length - 1);
        return `${(bytes / (1024 ** index)).toFixed(index > 1 ? 1 : 0)} ${units[index]}`;
    };

    let coverUrl;
    let audioUrl;
    const coverInput = form.querySelector('[data-music-cover-input]');
    const coverPreview = form.querySelector('[data-music-cover-preview]');
    const coverImage = form.querySelector('[data-music-cover-image]');
    const audioInput = form.querySelector('[data-music-audio-input]');
    const audioPreview = form.querySelector('[data-music-audio-preview]');
    const audioPlayer = form.querySelector('[data-music-audio-player]');

    coverInput?.addEventListener('change', () => {
        const file = coverInput.files?.[0];
        if (!file) return;
        if (coverUrl) URL.revokeObjectURL(coverUrl);
        coverUrl = URL.createObjectURL(file);
        coverImage.src = coverUrl;
        form.querySelector('[data-music-cover-name]').textContent = file.name;
        form.querySelector('[data-music-cover-meta]').textContent = formatBytes(file.size);
        coverPreview.hidden = false;
    });

    form.querySelector('[data-music-clear-cover]')?.addEventListener('click', () => {
        coverInput.value = '';
        coverPreview.hidden = true;
        coverImage.removeAttribute('src');
        if (coverUrl) URL.revokeObjectURL(coverUrl);
        coverUrl = undefined;
        coverInput.focus();
    });

    audioInput?.addEventListener('change', () => {
        const file = audioInput.files?.[0];
        if (!file) return;
        if (audioUrl) URL.revokeObjectURL(audioUrl);
        audioUrl = URL.createObjectURL(file);
        audioPlayer.src = audioUrl;
        form.querySelector('[data-music-audio-name]').textContent = file.name;
        form.querySelector('[data-music-audio-meta]').textContent = formatBytes(file.size);
        audioPreview.hidden = false;
    });

    form.querySelector('[data-music-clear-audio]')?.addEventListener('click', () => {
        audioPlayer.pause();
        audioInput.value = '';
        audioPreview.hidden = true;
        audioPlayer.removeAttribute('src');
        audioPlayer.load();
        if (audioUrl) URL.revokeObjectURL(audioUrl);
        audioUrl = undefined;
        audioInput.focus();
    });

    window.addEventListener('beforeunload', () => {
        if (coverUrl) URL.revokeObjectURL(coverUrl);
        if (audioUrl) URL.revokeObjectURL(audioUrl);
    });
});
</script>
@endsection
