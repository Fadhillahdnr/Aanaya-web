@extends('admin.layouts.admin')

@section('content')
<div class="mv-create-page">
    <header class="mv-create-header">
        <div class="mv-create-heading">
            <span class="mv-create-eyebrow"><i class="fas fa-clapperboard" aria-hidden="true"></i> Music Video Studio</span>
            <h1>Upload Music Video</h1>
            <p>Publish a new visual story with clear details, a polished cover, and direct Cloudinary upload.</p>
        </div>

        <a href="{{ route('admin.music-vidio') }}" class="mv-create-back">
            <i class="fas fa-arrow-left" aria-hidden="true"></i>
            <span>Back to Videos</span>
        </a>
    </header>

    @if ($errors->any())
        <div class="mv-create-alert" role="alert" aria-labelledby="mv-create-error-title">
            <i class="fas fa-circle-exclamation" aria-hidden="true"></i>
            <div>
                <strong id="mv-create-error-title">Please check your video details</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form
        method="POST"
        action="{{ route('admin.music-vidio.store') }}"
        enctype="multipart/form-data"
        class="mv-create-form"
        data-cloudinary-direct-upload
        data-mv-create-form>
        @csrf

        <div class="mv-create-layout">
            <section class="mv-create-card mv-create-details" aria-labelledby="mv-details-title">
                <div class="mv-create-card-heading">
                    <span class="mv-create-step">01</span>
                    <div>
                        <h2 id="mv-details-title">Video Details</h2>
                        <p>Information viewers will see alongside the music video.</p>
                    </div>
                </div>

                <div class="mv-create-fields">
                    <div class="mv-create-field">
                        <label for="mv-title">Video title <span aria-hidden="true">*</span></label>
                        <input
                            id="mv-title"
                            type="text"
                            name="title"
                            value="{{ old('title') }}"
                            maxlength="255"
                            placeholder="Example: Unfold — Official Music Video"
                            autocomplete="off"
                            required>
                        <small>Use the official title that viewers will recognize.</small>
                        @error('title')<p class="mv-create-field-error" role="alert">{{ $message }}</p>@enderror
                    </div>

                    <div class="mv-create-field">
                        <label for="mv-artist">Artist <span aria-hidden="true">*</span></label>
                        <input
                            id="mv-artist"
                            type="text"
                            name="artist"
                            value="{{ old('artist', 'Aanaya') }}"
                            maxlength="255"
                            autocomplete="organization"
                            required>
                        <small>Pre-filled for Aanaya, but you can adjust it for collaborations.</small>
                        @error('artist')<p class="mv-create-field-error" role="alert">{{ $message }}</p>@enderror
                    </div>

                    <div class="mv-create-field">
                        <label for="mv-description">Description</label>
                        <textarea
                            id="mv-description"
                            name="description"
                            rows="7"
                            placeholder="Tell the story behind this music video…">{{ old('description') }}</textarea>
                        <small>Add context, credits, or a short note about the visual concept.</small>
                        @error('description')<p class="mv-create-field-error" role="alert">{{ $message }}</p>@enderror
                    </div>

                    <label class="mv-create-featured" for="mv-featured">
                        <input
                            id="mv-featured"
                            type="checkbox"
                            name="is_featured"
                            value="1"
                            @checked(old('is_featured'))>
                        <span class="mv-create-check" aria-hidden="true"><i class="fas fa-check"></i></span>
                        <span>
                            <strong>Feature this video</strong>
                            <small>Give this release priority in highlighted video sections.</small>
                        </span>
                    </label>
                </div>
            </section>

            <section class="mv-create-card mv-create-media" aria-labelledby="mv-media-title">
                <div class="mv-create-card-heading">
                    <span class="mv-create-step">02</span>
                    <div>
                        <h2 id="mv-media-title">Visual Assets</h2>
                        <p>Select files locally, review them, then upload directly to Cloudinary.</p>
                    </div>
                </div>

                <div class="mv-create-upload-group">
                    <div class="mv-create-upload-heading">
                        <div>
                            <label for="mv-thumbnail">Cover thumbnail</label>
                            <span>Recommended</span>
                        </div>
                        <small>JPG, PNG, or WebP · up to 5 MB</small>
                    </div>

                    <label class="mv-create-dropzone" for="mv-thumbnail" data-mv-dropzone="thumbnail">
                        <input
                            id="mv-thumbnail"
                            type="file"
                            name="thumbnail"
                            accept="image/jpeg,image/png,image/webp"
                            aria-describedby="mv-thumbnail-help"
                            data-mv-thumbnail-input>
                        <span class="mv-create-dropzone-icon"><i class="fas fa-image" aria-hidden="true"></i></span>
                        <span class="mv-create-dropzone-copy">
                            <strong>Choose thumbnail</strong>
                            <small id="mv-thumbnail-help">Use a clear 16:9 image for the best presentation.</small>
                        </span>
                        <span class="mv-create-browse">Browse</span>
                    </label>

                    <div class="mv-create-preview mv-create-image-preview" data-mv-thumbnail-preview hidden>
                        <img src="" alt="Selected thumbnail preview" data-mv-thumbnail-image>
                        <div>
                            <strong data-mv-thumbnail-name></strong>
                            <small data-mv-thumbnail-meta></small>
                        </div>
                        <button type="button" data-mv-clear-thumbnail aria-label="Remove selected thumbnail">
                            <i class="fas fa-xmark" aria-hidden="true"></i>
                        </button>
                    </div>
                    @error('uploaded_media.thumbnail')<p class="mv-create-field-error" role="alert">{{ $message }}</p>@enderror
                </div>

                <div class="mv-create-upload-group mv-create-video-group">
                    <div class="mv-create-upload-heading">
                        <div>
                            <label for="mv-video-file">Video file <span aria-hidden="true">*</span></label>
                            <span class="is-required">Required</span>
                        </div>
                        <small>MP4 or WebM · up to 150 MB · maximum 1080p and 3 minutes</small>
                    </div>

                    <label class="mv-create-dropzone mv-create-video-dropzone" for="mv-video-file" data-mv-dropzone="video">
                        <input
                            id="mv-video-file"
                            type="file"
                            name="video_file"
                            accept="video/mp4,video/webm"
                            aria-describedby="mv-video-help"
                            data-mv-video-input
                            data-video-max-bytes="{{ config('media.video_max_bytes') }}"
                            data-video-max-duration="{{ config('media.video_max_duration') }}"
                            data-video-max-long-edge="{{ config('media.video_max_long_edge') }}"
                            data-video-max-short-edge="{{ config('media.video_max_short_edge') }}"
                            required>
                        <span class="mv-create-dropzone-icon"><i class="fas fa-video" aria-hidden="true"></i></span>
                        <span class="mv-create-dropzone-copy">
                            <strong>Choose music video</strong>
                            <small id="mv-video-help">Videos upload in resilient 10 MB parts directly to Cloudinary.</small>
                        </span>
                        <span class="mv-create-browse">Browse</span>
                    </label>

                    <div class="mv-create-preview mv-create-video-preview" data-mv-video-preview hidden>
                        <video controls playsinline preload="metadata" data-mv-video-player aria-label="Selected music video preview"></video>
                        <div class="mv-create-video-meta">
                            <span>
                                <strong data-mv-video-name></strong>
                                <small data-mv-video-meta></small>
                            </span>
                            <button type="button" data-mv-clear-video>
                                <i class="fas fa-rotate" aria-hidden="true"></i>
                                Choose another
                            </button>
                        </div>
                    </div>
                    @error('uploaded_media.video_file')<p class="mv-create-field-error" role="alert">{{ $message }}</p>@enderror
                </div>

                <aside class="mv-create-note">
                    <i class="fas fa-shield-heart" aria-hidden="true"></i>
                    <p><strong>Safe resumable upload.</strong> Video is sent in small parts directly to Cloudinary. You can pause, resume, or retry without restarting the whole file.</p>
                </aside>
            </section>
        </div>

        <footer class="mv-create-actions">
            <p><i class="fas fa-circle-info" aria-hidden="true"></i> Keep this page open until the upload and saving process finishes.</p>
            <div>
                <a href="{{ route('admin.music-vidio') }}" class="mv-create-cancel">Cancel</a>
                <button type="submit" class="mv-create-submit">
                    <i class="fas fa-cloud-arrow-up" aria-hidden="true"></i>
                    <span>Upload Music Video</span>
                </button>
            </div>
        </footer>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('[data-mv-create-form]');
    if (!form) return;

    const formatBytes = (bytes) => {
        if (!bytes) return '0 MB';
        const units = ['B', 'KB', 'MB', 'GB'];
        const unitIndex = Math.min(Math.floor(Math.log(bytes) / Math.log(1024)), units.length - 1);
        return `${(bytes / (1024 ** unitIndex)).toFixed(unitIndex > 1 ? 1 : 0)} ${units[unitIndex]}`;
    };

    let thumbnailObjectUrl;
    let videoObjectUrl;
    const thumbnailInput = form.querySelector('[data-mv-thumbnail-input]');
    const thumbnailPreview = form.querySelector('[data-mv-thumbnail-preview]');
    const thumbnailImage = form.querySelector('[data-mv-thumbnail-image]');
    const videoInput = form.querySelector('[data-mv-video-input]');
    const videoPreview = form.querySelector('[data-mv-video-preview]');
    const videoPlayer = form.querySelector('[data-mv-video-player]');

    thumbnailInput?.addEventListener('change', () => {
        const file = thumbnailInput.files?.[0];
        if (!file) return;
        if (thumbnailObjectUrl) URL.revokeObjectURL(thumbnailObjectUrl);
        thumbnailObjectUrl = URL.createObjectURL(file);
        thumbnailImage.src = thumbnailObjectUrl;
        form.querySelector('[data-mv-thumbnail-name]').textContent = file.name;
        form.querySelector('[data-mv-thumbnail-meta]').textContent = formatBytes(file.size);
        thumbnailPreview.hidden = false;
    });

    form.querySelector('[data-mv-clear-thumbnail]')?.addEventListener('click', () => {
        thumbnailInput.value = '';
        thumbnailPreview.hidden = true;
        thumbnailImage.removeAttribute('src');
        if (thumbnailObjectUrl) URL.revokeObjectURL(thumbnailObjectUrl);
        thumbnailObjectUrl = undefined;
        thumbnailInput.focus();
    });

    videoInput?.addEventListener('change', () => {
        const file = videoInput.files?.[0];
        if (!file) return;
        if (videoObjectUrl) URL.revokeObjectURL(videoObjectUrl);
        videoObjectUrl = URL.createObjectURL(file);
        videoPlayer.src = videoObjectUrl;
        form.querySelector('[data-mv-video-name]').textContent = file.name;
        form.querySelector('[data-mv-video-meta]').textContent = formatBytes(file.size);
        videoPreview.hidden = false;
        videoPlayer.load();
    });

    form.querySelector('[data-mv-clear-video]')?.addEventListener('click', () => {
        videoPlayer.pause();
        videoInput.value = '';
        videoPreview.hidden = true;
        videoPlayer.removeAttribute('src');
        videoPlayer.load();
        if (videoObjectUrl) URL.revokeObjectURL(videoObjectUrl);
        videoObjectUrl = undefined;
        videoInput.focus();
    });

    window.addEventListener('pagehide', () => {
        if (thumbnailObjectUrl) URL.revokeObjectURL(thumbnailObjectUrl);
        if (videoObjectUrl) URL.revokeObjectURL(videoObjectUrl);
    });
});
</script>
@endsection
