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
            enctype="multipart/form-data"
            autocomplete="off">

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

                    <div id="comicCount"></div>

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
                            value="{{ old('title') }}"
                            placeholder="Dreamscape"
                            required>
                        
                        <div class="article-stats">

                            <span id="wordCount">
                                0 words
                            </span>

                        </div>

                    </div>

                    <!-- PUBLISH -->
                    <div
                        class="form-group"
                        id="publishDateGroup">

                        <label>Publish Date</label>

                        <input
                            type="datetime-local"
                            name="published_at"
                            value="{{ old('published_at') }}">

                    </div>

                    <!-- ARTICLE -->
                    <div id="articleFields">

                        <div class="form-group">

                            <label>
                                Article Content
                            </label>

                            <div class="block-editor">

                                <div id="blocksContainer">

                                    <div class="editor-empty">

                                        Click "Text Block"
                                        to start writing ✨

                                    </div>

                                </div>

                                <button
                                    type="button"
                                    id="addTextBlock">

                                    + Text Block

                                </button>

                                <button
                                    type="button"
                                    id="addImageBlock">

                                    + Image Block

                                </button>

                            </div>

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
                            >{{ old('description') }}</textarea>

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
                            name="thumbnail"
                            accept="image/*"
                            required>

                        <img
                            id="thumbnailPreview"
                            alt="Thumbnail Preview"
                            style="
                                display:none;
                                width:100%;
                                max-height:250px;
                                object-fit:cover;
                                border-radius:16px;
                                margin-top:15px;
                            ">

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
                            id="comicImagesInput"
                            name="comic_images[]"
                            accept="image/*"
                            multiple>

                        <div id="comicPreviewGrid"></div>
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

<script>

document.addEventListener('DOMContentLoaded', () => {

    let blockIndex = 0;

    const container =
        document.getElementById(
            'blocksContainer'
        );

    const previewTitle =
        document.getElementById(
            'previewTitle'
        );

    const previewDescription =
        document.getElementById(
            'previewDescription'
        );

    const comicCount =
        document.getElementById(
            'comicCount'
        );

    const comicImagesInput =
        document.getElementById(
            'comicImagesInput'
        );

    const comicPreviewGrid =
        document.getElementById(
            'comicPreviewGrid'
        );

    comicImagesInput.addEventListener(
        'change',
        function()
        {
            comicPreviewGrid.innerHTML = '';

            Array.from(this.files)
            .forEach(file =>
            {
                const reader =
                    new FileReader();

                reader.onload = e =>
                {
                    comicPreviewGrid
                    .insertAdjacentHTML(
                        'beforeend',
                        `
                        <img
                            src="${e.target.result}"
                            alt="Comic Preview"
                            class="comic-preview-image">
                        `
                    );
                };

                reader.readAsDataURL(file);
            });
        }
    );

    /*
    |--------------------------------------------------------------------------
    | ADD TEXT BLOCK
    |--------------------------------------------------------------------------
    */

    function addTextBlock()
    {
        const empty =
            container.querySelector(
                '.editor-empty'
            );

        if(empty)
        {
            empty.remove();
        }        
        container.insertAdjacentHTML(
            'beforeend',

            `
            <div class="content-block">

                <button
                    type="button"
                    class="remove-block">
                    ×
                </button>

                <input
                    type="hidden"
                    name="blocks[${blockIndex}][type]"
                    value="text">

                <textarea
                    class="article-text-block"
                    name="blocks[${blockIndex}][content]"
                    rows="6"
                    placeholder="Write paragraph...">
                </textarea>

            </div>
            `
        );

        blockIndex++;
    }

    /*
    |--------------------------------------------------------------------------
    | ADD IMAGE BLOCK
    |--------------------------------------------------------------------------
    */

    function addImageBlock()
    {
        container.insertAdjacentHTML(
            'beforeend',

            `
            <div class="content-block">

                <button
                    type="button"
                    class="remove-block">
                    ×
                </button>

                <input
                    type="hidden"
                    name="blocks[${blockIndex}][type]"
                    value="image">

                <input
                    type="file"
                    class="block-image-input"
                    name="blocks[${blockIndex}][image]"
                    accept="image/*">

                <img
                    class="block-image-preview"
                    alt="Block Preview"
                    style="
                        display:none;
                        width:100%;
                        margin-top:15px;
                        border-radius:12px;
                    ">

            </div>
            `
        );

        blockIndex++;
    }

    /*
    |--------------------------------------------------------------------------
    | DEFAULT BLOCK
    |--------------------------------------------------------------------------
    */

    addTextBlock();

    /*
    |--------------------------------------------------------------------------
    | BUTTON EVENTS
    |--------------------------------------------------------------------------
    */

    document
    .getElementById('addTextBlock')
    .addEventListener(
        'click',
        addTextBlock
    );

    document
    .getElementById('addImageBlock')
    .addEventListener(
        'click',
        addImageBlock
    );

    /*
    |--------------------------------------------------------------------------
    | REMOVE BLOCK
    |--------------------------------------------------------------------------
    */

    document.addEventListener(
        'click',
        function(e)
        {
            if(
                e.target.classList.contains(
                    'remove-block'
                )
            )
            {
                e.target
                .closest('.content-block')
                .remove();

                const totalBlocks =
                    container.querySelectorAll(
                        '.content-block'
                    ).length;

                if(totalBlocks === 0)
                {
                    container.innerHTML =
                    `
                    <div class="editor-empty">
                        Click "Text Block"
                        to start writing ✨
                    </div>
                    `;
                }

                updatePreview();
            }
        }
    );

    /*
    |--------------------------------------------------------------------------
    | IMAGE BLOCK PREVIEW
    |--------------------------------------------------------------------------
    */

    document.addEventListener(
        'change',
        function(e)
        {
            if(
                e.target.classList.contains(
                    'block-image-input'
                )
            )
            {
                const file =
                    e.target.files[0];

                if(!file) return;

                const reader =
                    new FileReader();

                reader.onload =
                    function(event)
                    {
                        const preview =
                            e.target
                            .parentElement
                            .querySelector(
                                '.block-image-preview'
                            );

                        preview.src =
                            event.target.result;

                        preview.style.display =
                            'block';
                    };

                reader.readAsDataURL(file);
            }
        }
    );

    /*
    |--------------------------------------------------------------------------
    | COMIC COUNTER
    |--------------------------------------------------------------------------
    */

    const comicInput =
        document.querySelector(
            'input[name="comic_images[]"]'
        );

    if(comicInput)
    {
        comicInput.addEventListener(
            'change',
            function()
            {
                comicCount.innerText =
                    this.files.length +
                    ' images selected';
            }
        );
    }

    /*
    |--------------------------------------------------------------------------
    | THUMBNAIL PREVIEW
    |--------------------------------------------------------------------------
    */

    const thumbInput =
        document.querySelector(
            'input[name="thumbnail"]'
        );

    const thumbPreview =
        document.getElementById(
            'thumbnailPreview'
        );

    thumbInput.addEventListener(
        'change',
        function()
        {
            if(!this.files[0]) return;

            const reader =
                new FileReader();

            reader.onload =
                function(e)
                {
                    thumbPreview.src =
                        e.target.result;

                    thumbPreview.style.display =
                        'block';
                };

            reader.readAsDataURL(
                this.files[0]
            );
        }
    );

    /*
    |--------------------------------------------------------------------------
    | LIVE PREVIEW
    |--------------------------------------------------------------------------
    */

    function updatePreview()
    {
        const title =
            document.querySelector(
                'input[name="title"]'
            ).value;

        previewTitle.innerText =
            title ||
            'Your title will appear here';

        let text = '';

        document
        .querySelectorAll(
            '.article-text-block'
        )
        .forEach(block => {

            text +=
                block.value + ' ';
        });

        const comicDesc =
            document.querySelector(
                'textarea[name="description"]'
            );

        if(comicDesc &&
           comicDesc.value.length)
        {
            text =
                comicDesc.value;
        }

        previewDescription.innerText =
            text.substring(0, 150)
            ||
            'Start writing something dreamy ✨';
        
        const words =
            text
            .trim()
            .split(/\s+/)
            .filter(Boolean);

        document.getElementById(
            'wordCount'
        ).innerText =
            words.length + ' words';
    }

    document.addEventListener(
        'input',
        updatePreview
    );

    /*
    |--------------------------------------------------------------------------
    | CATEGORY TOGGLE
    |--------------------------------------------------------------------------
    */

    const articleCard =
        document.getElementById(
            'articleCard'
        );

    const comicCard =
        document.getElementById(
            'comicCard'
        );

    const articleFields =
        document.getElementById(
            'articleFields'
        );

    const comicFields =
        document.getElementById(
            'comicFields'
        );

    const comicBox =
        document.getElementById(
            'comicBox'
        );

    const thumbnailBox =
        document.getElementById(
            'thumbnailBox'
        );

    const publishDateGroup =
        document.getElementById(
            'publishDateGroup'
        );

    function toggleContentType(type)
    {
        articleCard.classList.remove(
            'active-category'
        );

        comicCard.classList.remove(
            'active-category'
        );

        if(type === 'comic')
        {
            articleFields.style.display =
                'none';

            comicFields.style.display =
                'block';

            comicBox.style.display =
                'flex';

            thumbnailBox.style.display =
                'flex';

            publishDateGroup.style.display =
                'none';

            comicCard.classList.add(
                'active-category'
            );
        }
        else
        {
            articleFields.style.display =
                'block';

            comicFields.style.display =
                'none';

            comicBox.style.display =
                'none';

            thumbnailBox.style.display =
                'flex';

            publishDateGroup.style.display =
                'block';

            articleCard.classList.add(
                'active-category'
            );
        }

        updatePreview();
    }

    document
    .querySelectorAll(
        'input[name="category"]'
    )
    .forEach(input => {

        input.addEventListener(
            'change',
            () =>
            toggleContentType(
                input.value
            )
        );

    });

    toggleContentType('article');

    updatePreview();

});

</script>

@endsection