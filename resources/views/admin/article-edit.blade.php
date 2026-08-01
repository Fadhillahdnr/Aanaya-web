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
            data-cloudinary-direct-upload
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
                        <p class="edit-field-help">Changing content type removes blocks or panels belonging to the previous format.</p>

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

                    <div class="edit-article-stats">
                        <span id="editWordCount">
                            0 words
                        </span>
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

                    <div
                        id="edit-articleFields"
                        style="{{ $article->category === 'comic' ? 'display:none;' : '' }}">

                        <div class="edit-form-group">

                            <div class="edit-editor-heading">
                                <div>
                                    <label>Article Content</label>
                                    <p class="edit-field-help">Manage each block and choose what happens to existing images.</p>
                                </div>
                                <span class="edit-editor-tip"><i class="fas fa-layer-group"></i> Block editor</span>
                            </div>

                            <div class="block-editor">

                                <div id="editBlocksContainer">

                                    @if($article->blocks->count())

                                        {{-- blocks --}}

                                    @else

                                        <div class="edit-empty-state">

                                            Click "Text Block"
                                            to start writing ✨

                                        </div>

                                    @endif

                                    @foreach( $article->blocks ->sortBy('sort_order') as $index => $block )

                                        @if($block->type === 'text')

                                            <div class="edit-content-block">

                                                <div class="edit-block-kind"><i class="fas fa-align-left"></i> Text block</div>

                                                <button
                                                    type="button"
                                                    class="edit-remove-block">
                                                    ×
                                                </button>

                                                <input
                                                    type="hidden"
                                                    name="blocks[{{ $index }}][id]"
                                                    value="{{ $block->id }}">

                                                <input
                                                    type="hidden"
                                                    name="blocks[{{ $index }}][type]"
                                                    value="text">

                                                <textarea
                                                    class="edit-article-text-block"
                                                    name="blocks[{{ $index }}][content]"
                                                    rows="6">{{ $block->content }}</textarea>

                                            </div>

                                        @endif

                                        @if($block->type === 'image')

                                            <div class="edit-content-block">

                                                <div class="edit-block-kind"><i class="fas fa-image"></i> Image block</div>

                                                <button
                                                    type="button"
                                                    class="edit-remove-block">
                                                    ×
                                                </button>

                                                <input
                                                    type="hidden"
                                                    name="blocks[{{ $index }}][id]"
                                                    value="{{ $block->id }}">

                                                <input
                                                    type="hidden"
                                                    name="blocks[{{ $index }}][type]"
                                                    value="image">

                                                <label for="block-action-{{ $block->id }}">Tindakan gambar</label>
                                                <select
                                                    id="block-action-{{ $block->id }}"
                                                    name="blocks[{{ $index }}][action]"
                                                    class="edit-media-action">
                                                    <option value="keep" selected>Pertahankan</option>
                                                    <option value="replace">Ganti</option>
                                                    <option value="delete">Hapus</option>
                                                </select>

                                                <img
                                                    src="{{ $block->image }}"
                                                    class="edit-block-image-preview"
                                                    style="
                                                        width:100%;
                                                        border-radius:12px;
                                                        margin-bottom:15px;
                                                    ">

                                                <input
                                                    type="file"
                                                    name="blocks[{{ $index }}][image]"
                                                    multiple
                                                    accept="image/*"
                                                    class="edit-block-image-input">

                                            </div>

                                        @endif

                                    @endforeach

                                </div>

                                <div class="edit-block-toolbar">
                                    <button type="button" id="editAddTextBlock"><i class="fas fa-align-left"></i> Add text</button>
                                    <button type="button" id="editAddImageBlock"><i class="fas fa-image"></i> Add images</button>
                                </div>

                            </div>

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

                        <p>Leave empty to retain the current thumbnail. JPG, PNG, or WEBP.</p>

                        <input
                            type="file"
                            id="editThumbnailInput"
                            name="thumbnail"
                            class="edit-friendly-file-input"
                            accept="image/*">

                        <img
                            id="editThumbnailPreview"
                            src="{{ Str::startsWith($article->thumbnail, 'http')
                                ? $article->thumbnail
                                : asset('uploads/articles/' . $article->thumbnail) }}"
                            alt="Thumbnail Preview"
                            style="
                                width:100%;
                                max-height:250px;
                                object-fit:cover;
                                border-radius:16px;
                                margin-top:15px;
                            ">

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

                        <p>Add several panels at once. New panels are appended after existing ones.</p>

                        <input
                            type="file"
                            name="comic_images[]"
                            class="edit-friendly-file-input"
                            multiple
                            accept="image/*">

                        <div id="editComicPreviewGrid"></div>

                    </div>

                    <div class="edit-live-preview">

                        <span class="edit-preview-badge">
                            LIVE PREVIEW
                        </span>

                        <h2 id="editPreviewTitle">

                            {{ $article->title }}

                        </h2>

                        <p id="editPreviewDescription">

                            @if($article->category === 'comic')

                                {{ Str::limit($article->description, 180) }}

                            @else

                                {{ Str::limit(
                                    $article->blocks
                                        ->where('type','text')
                                        ->pluck('content')
                                        ->implode(' '),
                                    180
                                ) }}

                            @endif

                        </p>

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

                                        <label for="comic-action-{{ $comic->id }}">Tindakan panel</label>
                                        <select
                                            id="comic-action-{{ $comic->id }}"
                                            name="comic_actions[{{ $comic->id }}]"
                                            class="edit-media-action comic-media-action">
                                            <option value="keep" selected>Pertahankan</option>
                                            <option value="replace">Ganti</option>
                                            <option value="delete">Hapus</option>
                                        </select>

                                        <input
                                            type="file"
                                            name="comic_replacements[{{ $comic->id }}]"
                                            accept="image/*"
                                            class="comic-replacement-input">

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

document.addEventListener('DOMContentLoaded', () => {

    const editArticleCard =
        document.getElementById(
            'edit-articleCard'
        );

    const editComicCard =
        document.getElementById(
            'edit-comicCard'
        );

    const editArticleFields =
        document.getElementById(
            'edit-articleFields'
        );

    const editComicFields =
        document.getElementById(
            'edit-comicFields'
        );

    const editComicBox =
        document.getElementById(
            'edit-comicBox'
        );

    const editPublishDateGroup =
        document.getElementById(
            'edit-publishDateGroup'
        );

    const editBlocksContainer =
    document.getElementById(
        'editBlocksContainer'
    );

    let editBlockIndex = Date.now();

    /*
    |--------------------------------------------------------------------------
    | CATEGORY TOGGLE
    |--------------------------------------------------------------------------
    */

    function addEditTextBlock()
    {
        document
            .querySelector('.edit-empty-state')
            ?.remove();

        editBlocksContainer.insertAdjacentHTML(
            'beforeend',

            `
            <div class="edit-content-block">

                <div class="edit-block-kind"><i class="fas fa-align-left"></i> Text block</div>

                <button
                    type="button"
                    class="edit-remove-block">
                    ×
                </button>

                <input
                    type="hidden"
                    name="blocks[${editBlockIndex}][type]"
                    value="text">

                <textarea
                    class="edit-article-text-block"
                    name="blocks[${editBlockIndex}][content]"
                    rows="6"
                    placeholder="Write paragraph...">
                </textarea>

            </div>
            `
        );

        editBlockIndex++;

        updateEditWordCount();
    }

    function addEditImageBlock()
    {
        document
            .querySelector('.edit-empty-state')
            ?.remove();
        editBlocksContainer.insertAdjacentHTML(
            'beforeend',

            `
            <div class="edit-content-block">

                <div class="edit-block-kind"><i class="fas fa-images"></i> Image block · multiple allowed</div>

                <button
                    type="button"
                    class="edit-remove-block">
                    ×
                </button>

                <input
                    type="hidden"
                    name="blocks[${editBlockIndex}][type]"
                    value="image">

                <input
                    type="file"
                    class="edit-block-image-input"
                    name="blocks[${editBlockIndex}][image]"
                    multiple
                    accept="image/*">

                <img
                    src=""
                    class="edit-block-image-preview"
                    style="
                        display:none;
                        width:100%;
                        margin-top:15px;
                        border-radius:12px;
                    "
                    alt="">

            </div>
            `
        );

        editBlockIndex++;
    }

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

        updateLivePreview();
    }

    document
    .getElementById(
        'editAddTextBlock'
    )
    .addEventListener(
        'click',
        addEditTextBlock
    );

    document
    .getElementById(
        'editAddImageBlock'
    )
    .addEventListener(
        'click',
        addEditImageBlock
    );

    document
    .querySelectorAll(
        'input[name="category"]'
    )
    .forEach(input => {

        input.addEventListener(
            'change',
            () =>
            editToggleContentType(
                input.value
            )
        );

    });

    document.addEventListener(
    'click',
    function(e)
    {
        if(
            e.target.classList.contains(
                'edit-remove-block'
            )
        )
        {
            const block =
                e.target.closest(
                    '.edit-content-block'
                );

            if(block)
            {
                block.remove();
            }

            updateEditWordCount();
            updateLivePreview();
        }
    });

    function syncMediaAction(select) {
        const card = select.closest('.edit-content-block, .edit-comic-preview-card');
        const replacementInput = card?.querySelector('.edit-block-image-input, .comic-replacement-input');
        if (!card || !replacementInput) return;

        card.dataset.mediaAction = select.value;
        replacementInput.hidden = select.value !== 'replace';
        if (select.value !== 'replace') replacementInput.value = '';
    }

    document.querySelectorAll('.edit-media-action').forEach(syncMediaAction);

    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('edit-media-action')) {
            syncMediaAction(e.target);
        }

        if (e.target.classList.contains('edit-block-image-input') && e.target.files.length) {
            const action = e.target.closest('.edit-content-block')?.querySelector('.edit-media-action');
            if (action) {
                action.value = 'replace';
                syncMediaAction(action);
            }
        }

        if (e.target.classList.contains('comic-replacement-input') && e.target.files.length) {
            const action = e.target.closest('.edit-comic-preview-card')?.querySelector('.comic-media-action');
            if (action) {
                action.value = 'replace';
                syncMediaAction(action);
            }
        }
    });

    /*
    |--------------------------------------------------------------------------
    | THUMBNAIL PREVIEW
    |--------------------------------------------------------------------------
    */

    const thumbnailInput =
        document.querySelector(
            'input[name="thumbnail"]'
        );

    const thumbnailPreview =
        document.getElementById(
            'editThumbnailPreview'
        );

    if(
        thumbnailInput &&
        thumbnailPreview
    )
    {
        thumbnailInput.addEventListener(
            'change',
            function()
            {
                if(!this.files[0])
                {
                    return;
                }

                const reader =
                    new FileReader();

                reader.onload =
                    function(e)
                    {
                        thumbnailPreview.src =
                            e.target.result;

                        thumbnailPreview.style.display =
                            'block';
                    };

                reader.readAsDataURL(
                    this.files[0]
                );
            }
        );
    }

    /*
    |--------------------------------------------------------------------------
    | COMIC IMAGE PREVIEW
    |--------------------------------------------------------------------------
    */

    const comicInput =
        document.querySelector(
            'input[name="comic_images[]"]'
        );

    const comicPreviewGrid =
        document.getElementById(
            'editComicPreviewGrid'
        );

    if(
        comicInput &&
        comicPreviewGrid
    )
    {
        comicInput.addEventListener(
            'change',
            function()
            {
                comicPreviewGrid.innerHTML =
                    '';

                Array
                .from(this.files)
                .forEach(file => {

                    const reader =
                        new FileReader();

                    reader.onload =
                        function(e)
                        {
                            comicPreviewGrid
                            .insertAdjacentHTML(
                                'beforeend',

                                `
                                <img
                                    src="${e.target.result}"
                                    class="edit-comic-preview-image">
                                `
                            );
                        };

                    reader.readAsDataURL(
                        file
                    );
                });
            }
        );
    }

    /*
    |--------------------------------------------------------------------------
    | REMOVE BLOCK
    |--------------------------------------------------------------------------
    */

    document.addEventListener(
        'change',
        function(e)
        {
            if(
                e.target.classList.contains(
                    'edit-block-image-input'
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
                                '.edit-block-image-preview'
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
    | LIVE PREVIEW
    |--------------------------------------------------------------------------
    */

    const previewTitle =
        document.getElementById(
            'editPreviewTitle'
        );

    const previewText =
        document.getElementById(
            'editPreviewDescription'
        );

    const wordCounter =
        document.getElementById(
            'editWordCount'
        );

    function updateEditWordCount()
    {
        let text = '';

        document
        .querySelectorAll(
            '.edit-article-text-block'
        )
        .forEach(block =>
        {
            text +=
                block.value + ' ';
        });

        const words =
            text
            .trim()
            .split(/\s+/)
            .filter(Boolean);

        const counter =
            document.getElementById(
                'editWordCount'
            );

        if(counter)
        {
            counter.innerText =
                words.length + ' words';
        }
    }

    document.addEventListener(
        'input',
        updateEditWordCount
    );

    updateEditWordCount();

    function updateLivePreview()
    {
        const title =
            document.querySelector(
                'input[name="title"]'
            )?.value || '';

        if(previewTitle)
        {
            previewTitle.innerText =
                title ||
                'Your title will appear here';
        }

        let content = '';

        document
        .querySelectorAll(
            '.edit-article-text-block'
        )
        .forEach(block => {

            content +=
                block.value + ' ';
        });

        const comicDesc =
            document.querySelector(
                'textarea[name="description"]'
            );

        const selectedCategory =
            document.querySelector(
                'input[name="category"]:checked'
            )?.value;

        if(
            selectedCategory === 'comic' &&
            comicDesc
        )
        {
            content = comicDesc.value;
        }

        if(previewText)
        {
            previewText.innerText =
                content.substring(0, 180)
                ||
                'Start writing something dreamy ✨';
        }

        const words =
            content
            .trim()
            .split(/\s+/)
            .filter(Boolean);

        if(wordCounter)
        {
            wordCounter.innerText =
                words.length +
                ' words';
        }
    }

    document.addEventListener(
        'input',
        updateLivePreview
    );

    /*
    |--------------------------------------------------------------------------
    | INITIALIZE
    |--------------------------------------------------------------------------
    */

    const checkedCategory =
        document.querySelector(
            'input[name="category"]:checked'
        );

    if(checkedCategory)
    {
        editToggleContentType(
            checkedCategory.value
        );
    }

    updateLivePreview();

});

</script>

@endsection
