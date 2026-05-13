@extends('admin.layouts.admin')

@section('content')

<div class="form-page">

    <!-- HEADER -->
    <div class="form-header">

        <div>
            <span class="form-badge">
                EDIT MUSIC
            </span>

            <h1>Edit Music</h1>

            <p>
                Update music information, audio, and cover artwork.
            </p>
        </div>

        <a href="/admin/music" class="back-btn">
            <i class="fas fa-arrow-left"></i>
            Back
        </a>

    </div>

    <!-- FORM -->
    <div class="music-form-card">

        <form>

            <!-- COVER -->
            <div class="form-group">

                <label>Music Cover</label>

                <div class="upload-box">

                    <i class="fas fa-image"></i>

                    <p>Upload new cover image</p>

                    <input type="file">

                </div>

            </div>

            <!-- TITLE -->
            <div class="form-group">

                <label>Music Title</label>

                <input
                    type="text"
                    value="Dreamscape"
                    placeholder="Enter music title">

            </div>

            <!-- ARTIST -->
            <div class="form-group">

                <label>Artist Name</label>

                <input
                    type="text"
                    value="Aanaya"
                    placeholder="Enter artist name">

            </div>

            <!-- CATEGORY -->
            <div class="form-group">

                <label>Category</label>

                <select>
                    <option>Single</option>
                    <option>Album</option>
                    <option>Live Session</option>
                </select>

            </div>

            <!-- DESCRIPTION -->
            <div class="form-group">

                <label>Description</label>

                <textarea rows="5"
                    placeholder="Write description...">Dreamy emotional indie song.</textarea>

            </div>

            <!-- AUDIO -->
            <div class="form-group">

                <label>Replace Audio File</label>

                <div class="upload-box">

                    <i class="fas fa-music"></i>

                    <p>Upload new MP3 file</p>

                    <input type="file">

                </div>

            </div>

            <!-- BUTTON -->
            <div class="form-actions">

                <button type="submit" class="save-btn">

                    <i class="fas fa-floppy-disk"></i>

                    Update Music

                </button>

            </div>

        </form>

    </div>

</div>

@endsection