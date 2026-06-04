@extends('admin.layouts.admin')

@section('content')

<div class="music-form-card">

    <div class="form-glow"></div>

    <h2 class="form-title">
        Upload Music Video
    </h2>

    @if ($errors->any())

    <div class="alert alert-danger">

        <ul>

            @foreach ($errors->all() as $error)

                <li>{{ $error }}</li>

            @endforeach

        </ul>

    </div>

    @endif

    <form method="POST"
          action="{{ route('admin.music-vidio.store') }}"
          enctype="multipart/form-data">

        @csrf

        <div class="form-grid">

            <div class="form-group">

                <label>Title</label>

                <input
                    type="text"
                    name="title"
                    value="{{ old('title') }}"
                    required>

            </div>

            <div class="form-group">

                <label>Artist</label>

                <input
                    type="text"
                    name="artist"
                    value="{{ old('artist') }}"
                    value="Aanaya"
                    required>

            </div>

            <div class="form-group">

                <label>Thumbnail</label>

                <input
                    type="file"
                    name="thumbnail"
                    value="{{ old('thumbnail') }}"
                    accept="image/*"
                    required>

            </div>

            <div class="form-group">

                <label>Video File</label>

                <input
                    type="file"
                    name="video_file"
                    value="{{ old('video_file') }}"
                    accept="video/*"
                    required>

            </div>

            <div class="form-group full-width">

                <label>Description</label>

                <textarea name="description" rows="5">
                {{ old('description') }}
                </textarea>

            </div>

            <div class="form-group">

                <label>

                    <input
                        type="checkbox"
                        name="is_featured"
                        value="1"
                        {{ old('is_featured') ? 'checked' : '' }}>

                    Set as featured video

                </label>

            </div>

        </div>

        <div class="submit-wrapper">

            <button
                type="submit"
                class="save-btn"
                id="uploadBtn">

                <i class="fas fa-cloud-upload-alt"></i>

                Upload MV

            </button>

        </div>

    </form>

</div>

<script>
document.querySelector('form')
.addEventListener('submit', function() {

    const btn =
        document.getElementById('uploadBtn');

    btn.disabled = true;

    btn.innerHTML =
        '<i class="fas fa-spinner fa-spin"></i> Uploading...';
});
</script>

@endsection