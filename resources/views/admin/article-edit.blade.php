@extends('admin.layouts.admin')

@section('content')

<div class="edit-article-form-page">

    <!-- ===================================================== -->
    <!-- HEADER -->
    <!-- ===================================================== -->

    <div class="edit-page-top">

        <div class="edit-page-top-content">

            <span class="edit-page-badge">

                <i class="fas fa-pen-nib"></i>

                EDIT CONTENT

            </span>

            <h1>
                Edit {{ ucfirst($article->category) }}
            </h1>

            <p class="edit-page-subtitle">
                Update your article, comic, thumbnail, and content information easily.
            </p>

        </div>

        <a href="/admin/articles" class="back-btn">

            <i class="fas fa-arrow-left"></i>

            Back

        </a>
    </div>

    <!-- ===================================================== -->
    <!-- FORM CARD -->
    <!-- ===================================================== -->

    <div class="edit-article-form-card">

        @if ($errors->any())

            <div class="edit-error-alert">

                <ul>

                    @foreach ($errors->all() as $error)

                        <li>{{ $error }}</li>

                    @endforeach

                </ul>

            </div>

        @endif

        <form
            action="/admin/articles/{{ $article->id }}"
            method="POST"
            enctype="multipart/form-data">

            @csrf
            @method('PUT')

            <div class="edit-form-grid">

                <!-- LEFT -->
                <div class="edit-form-left">

                    <!-- CATEGORY -->
                    <div class="edit-form-group">

                        <label class="edit-group-label">
                            Content Type
                        </label>

                        <div class="edit-category-selector">

                            <!-- ARTICLE -->
                            <label
                                class="edit-category-card {{ $article->category === 'article' ? 'edit-active-category' : '' }}"
                                id="edit-articleCard">

                                <input
                                    type="radio"
                                    name="category"
                                    value="article"
                                    {{ $article->category === 'article' ? 'checked' : '' }}>

                                <div class="edit-category-icon">

                                    <i class="fas fa-newspaper"></i>

                                </div>

                                <div class="edit-category-text">

                                    <h3>
                                        Article
                                    </h3>

                                    <p>
                                        Blog, news, stories, tutorials.
                                    </p>

                                </div>

                            </label>

                            <!-- COMIC -->
                            <label
                                class="edit-category-card {{ $article->category === 'comic' ? 'edit-active-category' : '' }}"
                                id="edit-comicCard">

                                <input
                                    type="radio"
                                    name="category"
                                    value="comic"
                                    {{ $article->category === 'comic' ? 'checked' : '' }}>

                                <div class="edit-category-icon">

                                    <i class="fas fa-images"></i>

                                </div>

                                <div class="edit-category-text">

                                    <h3>
                                        Comic
                                    </h3>

                                    <p>
                                        Visual storytelling panels.
                                    </p>

                                </div>

                            </label>

                        </div>

                    </div>

                    <!-- TITLE -->
                    <div class="edit-form-group">

                        <label>
                            Content Title
                        </label>

                        <input
                            type="text"
                            name="title"
                            value="{{ old('title', $article->title) }}"
                            placeholder="Enter your content title..."
                            required>

                    </div>

                    <!-- PUBLISH DATE -->
                    <div
                        class="edit-form-group"
                        id="edit-publishDateGroup"
                        style="{{ $article->category === 'comic' ? 'display:none;' : '' }}">

                        <label>
                            Publish Date
                        </label>

                        <input
                            type="datetime-local"
                            name="published_at"
                            value="{{ $article->published_at ? $article->published_at->format('Y-m-d\TH:i') : '' }}">

                    </div>

                    <!-- ARTICLE -->
                    <div
                        id="edit-articleFields"
                        style="{{ $article->category === 'comic' ? 'display:none;' : '' }}">

                        <div class="edit-form-group">

                            <label>
                                Article Content
                            </label>

                            <textarea
                                rows="14"
                                name="content"
                                placeholder="Write your article here...">{{ old('content', $article->content) }}</textarea>

                        </div>

                    </div>

                    <!-- COMIC -->
                    <div
                        id="edit-comicFields"
                        style="{{ $article->category === 'comic' ? 'display:block;' : 'display:none;' }}">

                        <div class="edit-form-group">

                            <label>
                                Comic Description
                            </label>

                            <textarea
                                rows="8"
                                name="description"
                                placeholder="Describe your comic...">{{ old('description', $article->description) }}</textarea>

                        </div>

                    </div>

                </div>

                <!-- RIGHT -->
                <div class="edit-form-right">

                    @if($article->thumbnail)

                        <div class="edit-preview-card">

                            <div class="edit-preview-header">

                                <h3>
                                    Current Thumbnail
                                </h3>

                            </div>

                            <div class="edit-thumbnail-preview">

                                <img
                                    src="{{ Str::startsWith($article->thumbnail, 'http')
                                        ? $article->thumbnail
                                        : asset('uploads/articles/' . $article->thumbnail) }}"
                                    alt="{{ $article->title }}">

                            </div>

                        </div>

                    @endif

                    <!-- THUMB -->
                    <div
                        class="edit-upload-box"
                        id="edit-thumbnailBox">

                        <div class="edit-upload-icon">

                            <i class="fas fa-image"></i>

                        </div>

                        <h3>
                            Change Thumbnail
                        </h3>

                        <p>
                            JPG, PNG, WEBP
                        </p>

                        <input
                            type="file"
                            name="thumbnail"
                            accept="image/*">

                    </div>

                    <!-- COMIC UPLOAD -->
                    <div
                        class="edit-upload-box"
                        id="edit-comicBox"
                        style="{{ $article->category === 'comic' ? 'flex' : 'display:none;' }}">

                        <div class="edit-upload-icon">

                            <i class="fas fa-images"></i>

                        </div>

                        <h3>
                            Upload Comic Panels
                        </h3>

                        <p>
                            Multiple images supported
                        </p>

                        <input
                            type="file"
                            name="comic_images[]"
                            multiple
                            accept="image/*">

                    </div>

                    <!-- COMIC PREVIEW -->
                    @if(
                        $article->category === 'comic'
                        &&
                        $article->comicImages->count()
                    )

                        <div class="edit-preview-card">

                            <div class="edit-preview-header">

                                <h3>
                                    Comic Panels
                                </h3>

                                <span>
                                    {{ $article->comicImages->count() }} Images
                                </span>

                            </div>

                            <div class="edit-comic-preview-grid">

                                @foreach($article->comicImages as $comic)

                                    <div class="edit-comic-preview-card">

                                        <img
                                            src="{{ Str::startsWith($comic->image, 'http')
                                                ? $comic->image
                                                : asset('uploads/comics/' . $comic->image) }}"
                                            alt="Comic">

                                    </div>

                                @endforeach

                            </div>

                        </div>

                    @endif

                </div>

            </div>

            <!-- BUTTON -->
            <div class="edit-submit-wrapper">

                <button
                    type="submit"
                    class="edit-save-btn">

                    <i class="fas fa-save"></i>

                    Update Content

                </button>

            </div>

        </form>

    </div>

</div>

<script>

const editArticleCard =
    document.getElementById('edit-articleCard');

const editComicCard =
    document.getElementById('edit-comicCard');

const editArticleFields =
    document.getElementById('edit-articleFields');

const editComicFields =
    document.getElementById('edit-comicFields');

const editComicBox =
    document.getElementById('edit-comicBox');

const editPublishDateGroup =
    document.getElementById('edit-publishDateGroup');

const editCategoryInputs =
    document.querySelectorAll(
        'input[name="category"]'
    );

function editToggleContentType(type)
{
    if(type === 'comic')
    {
        editArticleFields.style.display =
            'none';

        editComicFields.style.display =
            'block';

        editComicBox.style.display =
            'flex';

        editPublishDateGroup.style.display =
            'none';

        editArticleCard.classList.remove(
            'edit-active-category'
        );

        editComicCard.classList.add(
            'edit-active-category'
        );
    }
    else
    {
        editArticleFields.style.display =
            'block';

        editComicFields.style.display =
            'none';

        editComicBox.style.display =
            'none';

        editPublishDateGroup.style.display =
            'block';

        editArticleCard.classList.add(
            'edit-active-category'
        );

        editComicCard.classList.remove(
            'edit-active-category'
        );
    }
}

editCategoryInputs.forEach(input => {

    input.addEventListener('change', () => {

        editToggleContentType(
            input.value
        );

    });

});

editToggleContentType(
    document.querySelector(
        'input[name="category"]:checked'
    ).value
);

</script>

@endsection