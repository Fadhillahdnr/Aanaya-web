@extends('admin.layouts.admin')

@section('content')

<div class="music-form-card">

    <div class="form-glow"></div>

    <h2 class="form-title">
        Upload Music Video
    </h2>

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
                    required>

            </div>

            <div class="form-group">

                <label>Artist</label>

                <input
                    type="text"
                    name="artist"
                    value="Aanaya">

            </div>

            <div class="form-group">

                <label>Thumbnail</label>

                <input
                    type="file"
                    name="thumbnail">

            </div>

            <div class="form-group">

                <label>Video File</label>

                <input
                    type="file"
                    name="video_file"
                    required>

            </div>

            <div class="form-group full-width">

                <label>Description</label>

                <textarea
                    name="description"
                    rows="5"></textarea>

            </div>

            <div class="form-group">

                <label>

                    <input
                        type="checkbox"
                        name="is_featured">

                    Set as featured video

                </label>

            </div>

        </div>

        <div class="submit-wrapper">

            <button type="submit" class="save-btn">

                <i class="fas fa-cloud-upload-alt"></i>

                Upload MV

            </button>

        </div>

    </form>

</div>

@endsection