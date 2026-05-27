@extends('admin.layouts.admin')

@section('content')

<div class="article-form-page">

    <!-- HEADER -->
    <div class="page-top">

        <div>

            <span class="page-badge">
                AANAYA CONTENT PANEL
            </span>

            <h1>Create Content</h1>

            <p class="page-subtitle">
                Publish dreamy articles & visual comics ✨
            </p>

        </div>

        <a href="/admin/articles" class="back-btn">

            <i class="fas fa-arrow-left"></i>

            Back

        </a>

    </div>

    <!-- FORM CARD -->
    <div class="article-form-card">

        <!-- ERROR -->
        @if ($errors->any())

            <div class="error-box">

                <ul>

                    @foreach ($errors->all() as $error)

                        <li>{{ $error }}</li>

                    @endforeach

                </ul>

            </div>

        @endif

        <form
            action="/admin/articles/store"
            method="POST"
            enctype="multipart/form-data">

            @csrf

            <!-- CATEGORY -->
            <div class="category-grid">

                <!-- ARTICLE -->
                <label
                    class="category-card active-category"
                    id="articleCard">

                    <input
                        type="radio"
                        name="category"
                        value="article"
                        checked>

                    <div class="category-icon">
                        <i class="fas fa-newspaper"></i>
                    </div>

                    <h3>Article</h3>

                    <p>
                        Stories, updates, news
                    </p>

                </label>

                <!-- COMIC -->
                <label
                    class="category-card"
                    id="comicCard">

                    <input
                        type="radio"
                        name="category"
                        value="comic">

                    <div class="category-icon">
                        <i class="fas fa-images"></i>
                    </div>

                    <h3>Comic</h3>

                    <p>
                        Multiple image storytelling
                    </p>

                </label>

            </div>

            <!-- GRID -->
            <div class="form-grid">

                <!-- LEFT -->
                <div class="form-left">

                    <!-- TITLE -->
                    <div class="form-group">

                        <label>Title</label>

                        <input
                            type="text"
                            name="title"
                            placeholder="Dreamscape"
                            required>

                    </div>

                    <!-- PUBLISH -->
                    <div
                        class="form-group"
                        id="publishDateGroup">

                        <label>Publish Date</label>

                        <input
                            type="datetime-local"
                            name="published_at">

                    </div>

                    <!-- ARTICLE -->
                    <div id="articleFields">

                        <div class="form-group">

                            <label>
                                Article Content
                            </label>

                            <textarea
                                rows="12"
                                name="content"
                                placeholder="Write dreamy story..."
                            ></textarea>

                        </div>

                    </div>

                    <!-- COMIC -->
                    <div
                        id="comicFields"
                        style="display:none;">

                        <div class="form-group">

                            <label>
                                Comic Description
                            </label>

                            <textarea
                                rows="8"
                                name="description"
                                placeholder="Describe your comic..."
                            ></textarea>

                        </div>

                    </div>

                </div>

                <!-- RIGHT -->
                <div class="form-right">

                    <!-- ARTICLE THUMB -->
                    <div
                        class="upload-box"
                        id="thumbnailBox">

                        <i class="fas fa-image"></i>

                        <p>
                            Upload Thumbnail
                        </p>

                        <input
                            type="file"
                            name="thumbnail">

                    </div>

                    <!-- COMIC IMAGES -->
                    <div
                        class="upload-box"
                        id="comicBox"
                        style="display:none;">

                        <i class="fas fa-images"></i>

                        <p>
                            Upload Comic Images
                            <br>
                            Multiple allowed
                        </p>

                        <input
                            type="file"
                            name="comic_images[]"
                            multiple>

                    </div>

                    <!-- PREVIEW -->
                    <div class="live-preview">

                        <span class="preview-badge">
                            LIVE PREVIEW
                        </span>

                        <h2 id="previewTitle">
                            Your title will appear here
                        </h2>

                        <p id="previewDescription">
                            Start writing something dreamy ✨
                        </p>

                    </div>

                </div>

            </div>

            <!-- BUTTON -->
            <div class="submit-wrapper">

                <button
                    type="submit"
                    class="save-btn">

                    <i class="fas fa-cloud-upload-alt"></i>

                    Publish Content

                </button>

            </div>

        </form>

    </div>

</div>

<!-- JS -->
<script>

/*
|--------------------------------------------------------------------------
| ELEMENTS
|--------------------------------------------------------------------------
*/

const articleCard =
    document.getElementById('articleCard');

const comicCard =
    document.getElementById('comicCard');

const articleFields =
    document.getElementById('articleFields');

const comicFields =
    document.getElementById('comicFields');

const thumbnailBox =
    document.getElementById('thumbnailBox');

const comicBox =
    document.getElementById('comicBox');

const publishDateGroup =
    document.getElementById('publishDateGroup');

const categoryInputs =
    document.querySelectorAll(
        'input[name="category"]'
    );

const titleInput =
    document.querySelector(
        'input[name="title"]'
    );

const contentTextarea =
    document.querySelector(
        'textarea[name="content"]'
    );

const descriptionTextarea =
    document.querySelector(
        'textarea[name="description"]'
    );

const previewTitle =
    document.getElementById('previewTitle');

const previewDescription =
    document.getElementById('previewDescription');

/*
|--------------------------------------------------------------------------
| TOGGLE CATEGORY
|--------------------------------------------------------------------------
*/

function toggleContentType(type)
{
    articleCard.classList.remove(
        'active-category'
    );

    comicCard.classList.remove(
        'active-category'
    );

    /*
    |--------------------------------------------------------------------------
    | COMIC
    |--------------------------------------------------------------------------
    */

    if(type === 'comic')
    {
        comicFields.style.display = 'block';

        articleFields.style.display = 'none';

        comicBox.style.display = 'flex';

        /*
        |----------------------------------------------------------------------
        | THUMBNAIL TETAP TAMPIL
        |----------------------------------------------------------------------
        */

        thumbnailBox.style.display = 'flex';

        publishDateGroup.style.display = 'none';

        comicCard.classList.add(
            'active-category'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | ARTICLE
    |--------------------------------------------------------------------------
    */

    else
    {
        comicFields.style.display = 'none';

        articleFields.style.display = 'block';

        comicBox.style.display = 'none';

        thumbnailBox.style.display = 'flex';

        publishDateGroup.style.display = 'block';

        articleCard.classList.add(
            'active-category'
        );
    }
}

/*
|--------------------------------------------------------------------------
| CATEGORY CHANGE
|--------------------------------------------------------------------------
*/

categoryInputs.forEach(input => {

    input.addEventListener('change', () => {

        toggleContentType(input.value);

    });

});

/*
|--------------------------------------------------------------------------
| TITLE PREVIEW
|--------------------------------------------------------------------------
*/

titleInput.addEventListener('input', function()
{
    previewTitle.innerText =
        this.value ||
        'Your title will appear here';
});

/*
|--------------------------------------------------------------------------
| ARTICLE PREVIEW
|--------------------------------------------------------------------------
*/

contentTextarea.addEventListener('input', function()
{
    previewDescription.innerText =
        this.value.substring(0, 120)
        ||
        'Start writing something dreamy ✨';
});

/*
|--------------------------------------------------------------------------
| COMIC PREVIEW
|--------------------------------------------------------------------------
*/

descriptionTextarea.addEventListener('input', function()
{
    previewDescription.innerText =
        this.value.substring(0, 120)
        ||
        'Start writing something dreamy ✨';
});

/*
|--------------------------------------------------------------------------
| INIT
|--------------------------------------------------------------------------
*/

toggleContentType('article');

</script>

@endsection